<?php

namespace App\Http\Controllers\SickGuard\Workflow;

use App\Http\Controllers\Controller;
use App\Http\Requests\SickGuard\WorkFlow\Disponibilite\SickGuardCreateDisponibiliteRequest;
use App\Http\Requests\SickGuard\WorkFlow\Disponibilite\SickGuardDeleteDisponibiliteRequest;
use App\Models\Disponibilite;
use Illuminate\Http\Request;

class SickGuardGestionDisponibiliteController extends Controller
{
    public function list( Request $request ): \Illuminate\Http\JsonResponse
    {
        try {
            $sickGuard = $request->user('sickguard');
            $disponibilites = $sickGuard->disponibilites;

            $disponibilitesFormatees = [];

            foreach ($disponibilites as $disponibilite)
            {
                $date = \DateTime::createFromFormat('d-m-Y', $disponibilite['date']);

                $annee = $date->format('Y');
                $mois = strtolower(strftime('%B', $date->getTimestamp()));
                $jourMois = $date->format('d');
                $jourSemaine = strtolower(strftime('%A', $date->getTimestamp()));
                $disponibilite['jour'] = $jourSemaine;

                if (!isset($disponibilitesFormatees['annee'][$annee])) {
                    $disponibilitesFormatees['annee'][$annee] = [];
                }
                if (!isset($disponibilitesFormatees['annee'][$annee][$mois])) {
                    $disponibilitesFormatees['annee'][$annee][$mois] = [];
                }
                if (!isset($disponibilitesFormatees['annee'][$annee][$mois][$jourMois])) {
                    $disponibilitesFormatees['annee'][$annee][$mois][$jourMois] = [];
                }

                $disponibilitesFormatees['annee'][$annee][$mois][$jourMois][] = $disponibilite;
            }

            foreach ($disponibilitesFormatees['annee'] as $annee => $moisData) {
                foreach ($moisData as $mois => $jours) {
                    foreach ($jours as $jourMois => $dispos) {
                        usort($disponibilitesFormatees['annee'][$annee][$mois][$jourMois], function($a, $b) {
                            return $a['debut'] - $b['debut'];
                        });
                    }
                }
            }

            return response()->json(
                [
                    'status' => 200,
                    'data' => [
                        'disponibilites' => $disponibilitesFormatees,
                    ]
                ], 200);

        }catch (\Exception $e){
            return response()->json(
                [
                    'status' => 500,
                    'error' => $e->getMessage(),
                    'message' => 'Erreut interne'
                ],500);
        }

    }

    public function create(SickGuardCreateDisponibiliteRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validated();


            $validated['sick_guard_id'] = $request->user('sickguard')->id;
            $validated['fin'] = $validated['debut'] + 1;

            $existingAvailability = Disponibilite::where('sick_guard_id', $validated['sick_guard_id'])
                ->where('date', $validated['date'])
                ->where('debut', $validated['debut'])
                ->exists();

            if ($existingAvailability) {
                return response()->json([
                    'status' => 400,
                    'message' => 'La disponibilité existe déjà pour cette date et cette heure.',
                ], 400);
            }

            Disponibilite::create($validated);

            return response()->json([
                'status' => 200,
                'message' => 'Disponibilité créée avec succès.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Une erreur s\'est produite lors de la création de la disponibilité.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function delete(SickGuardDeleteDisponibiliteRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validated();
            $sickGuardId = $request->user('sickguard')->id;

            $disponibilite = Disponibilite::where('sick_guard_id', $sickGuardId)
                ->where('date', $validated['date'])
                ->where('debut', $validated['debut']);

            if (!$disponibilite->exists()) {
                return response()->json([
                    'status' => 404,
                    'message' => 'La disponibilité spécifiée n\'existe pas.',
                ], 404);
            }

            $disponibilite = $disponibilite->first();

            $disponibilite->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Disponibilité supprimée avec succès.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Une erreur s\'est produite lors de la suppression de la disponibilité.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
