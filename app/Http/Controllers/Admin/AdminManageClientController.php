<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ban;
use App\Models\Client;
use Illuminate\Http\Request;

class AdminManageClientController extends Controller
{

    public function clientList(): \Illuminate\Http\JsonResponse
    {
        try {
            $client = Client::withTrashed()->get();

            $selfRejected = $client->whereNotNull('deleted_at');

            $client = $client->whereNull('deleted_at')
            ->filter(function ($item) {
                return !Ban::withoutTrashed()->where('client_id', $item->id)->exists();
            });


            $banned = Ban::withoutTrashed()
                ->whereNotNull('client_id')
                ->get()
                ->map(function ($ban) {
                    $clientData = $ban->client->toArray() ;
                    $motifData = ['motif' => $ban->motif];
                    return collect($clientData)->merge($motifData);
                });


            return response()->json([
                'status' => 200,
                'data' => [
                    'selfRejected' => $selfRejected,
                    'client' => $client ,
                    'banned' => $banned
                ]
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'status' => 500,
                'message' => 'Erreur interne.',
                'error' => $exception->getMessage()
            ]);
        }
    }





    public function clientBan( Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $client = Client::withoutTrashed()->where('id', $request->input('id'));


            if (!$client->exists()) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Profil non trouvé.',
                ]);
            }

            $client = $client->first();


            $client->ban($request->input('motif'),$request->user('admin')->id);

            return response()->json([
                'status' => 200,
                'message' =>  $client->nom . ' ' . $client->prenom . ' a été banni.',
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 500,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function clientUnBan(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $client = Client::withoutTrashed()->where('id', $request->input('id'));


            if (!$client->exists()) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Profil non trouvé.',
                ]);
            }

            $client = $client->first();

            $client->unban();

            return response()->json([
                'status' => 200,
                'message' =>  $client->nom . ' ' . $client->prenom . ' a été retiré de la liste des bannis.',
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 500,
                'message'  => $exception->getMessage(),
            ]);
        }
    }

    public function clientBanList(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $client = Client::withoutTrashed()->where('id', $request->input('id'));


            if (!$client->exists()) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Profil non trouvé.',
                ]);
            }

            $client = $client->first();

            return response()->json(
                [
                    'status' => 200,
                    'historique des bans du client' => $client->banList()->map(function ($ban) {
                        return [
                            'instigateur' => $ban->admin,
                            'motif' => $ban->motif,
                            'debut' => $ban->created_at->format('d-m-Y'),
                            'fin' => $ban->deleted_at ? $ban->deleted_at->format('d-m-Y') : null
                        ];
                    })
                ]
            );


        } catch (\Exception $exception) {
            return response()->json([
                'status' => 500,
                'message' => 'Erreur interne.',
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
