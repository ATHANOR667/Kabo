<?php

namespace App\Http\Controllers\SickGuard\Profil;

use App\Http\Controllers\Controller;
use App\Http\Requests\SickGuard\Profil\Qualification\SickGuardCreateQualificationRequest;
use App\Http\Requests\SickGuard\Profil\Qualification\SickGuardDeleteQualificationRequest;
use App\Models\Qualification;
use App\Models\SickGuard;
use Illuminate\Http\Request;

class SickGuardGestionQualificationController extends Controller
{
    public function list(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $this->sick_guard_verify($request);

        if ($data['status'] === 200) {
            $sickGuard = SickGuard::where('email', $request->input('email'))->first();
            $qualifications = $sickGuard->qualifications;
            return response()->json(
                [
                    'status' => 200,
                    'data' => [
                        'qualifications' => $qualifications
                    ]
                ],200);
        }else{
            return response()->json($data) ;
        }

    }


    public function create(SickGuardCreateQualificationRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $this->sick_guard_verify($request);

        if ($data['status'] === 200) {
            $sickGuard = SickGuard::where('email', $request->input('email'))->first();

            try {
                $qualificationData = $request->except('email', 'password');

                if ($request->hasFile('fichier')) {
                    $validatedFile = $request->file('fichier');

                    $filePath = $validatedFile->store('qualifications', 'public');

                    $qualificationData['fichier'] = $filePath;
                }

                $qualificationData['sick_guard_id'] = $sickGuard->id;

                $qualification = Qualification::create($qualificationData);

                return response()->json(
                    [
                        'status' => 200,
                        'data' => [
                            'qualification' => $qualification
                        ]
                    ]
                );
            } catch (\Exception $exception) {
                return response()->json(
                    [
                        'status' => 500,
                        'error' => $exception->getMessage(),
                        'message' => 'Erreur lors de la création de la qualification'
                    ]
                );
            }
        } else {
            return response()->json($data);
        }
    }


    public function delete(SickGuardDeleteQualificationRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $this->sick_guard_verify($request);

        if ($data['status'] === 200) {
            $sickGuard = SickGuard::where('email', $request->input('email'))->first();


            try {
                $qualification  = $sickGuard->qualifications()
                    ->where('id', $request->input('id'));

                if (!$qualification->exists()) {
                    return response()->json([
                        'status' => 404,
                        'message' => 'Qualification introuvable'
                    ]);
                }
                $qualification = $qualification->first();
                if($qualification->deleted_at != null)
                {
                    return response()->json([
                        'status' => 500,
                        'message' => 'Qualification déja supprimée'
                    ],500);
                }

                $qualification->delete();
                return response()->json(
                    [
                        'status' => 200,
                        'message' =>  'qualification supprimée avec succèss'
                    ],200);
            }catch (\Exception $exception){
                return response()->json([
                    'status' => 500,
                    'error' => $exception->getMessage() ,
                    'message' => 'Erreur lors de la suppression de la qualification'
                ]);
            }

        }else{
            return response()->json($data) ;
        }

    }

}
