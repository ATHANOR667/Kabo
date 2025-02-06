<?php

namespace App\Http\Controllers\SickGuard\Profil;

use App\Http\Controllers\Controller;
use App\Http\Requests\SickGuard\Profil\Experience\SickGuardCreateExperienceRequest;
use App\Http\Requests\SickGuard\Profil\Experience\SickGuardDeleteExperienceRequest;
use App\Models\Experience;
use App\Models\SickGuard;
use Illuminate\Http\Request;

class SickGuardGestionExperienceController extends Controller
{
    public function list(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $this->sick_guard_verify($request);

        if ($data['status'] === 200) {
            $sickGuard = SickGuard::where('email', $request->input('email'))->first();
            $experiences = $sickGuard->experiences;
            return response()->json(
                [
                    'status' => 200,
                    'data' => [
                        'experiences' => $experiences
                    ]
                ],200);
        }else{
            return response()->json($data) ;
        }

    }


    public function create(SickGuardCreateExperienceRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $this->sick_guard_verify($request);

        if ($data['status'] === 200) {
            $sickGuard = SickGuard::where('email', $request->input('email'))->first();

            try {
                $experienceData = $request->except('email','password');
                $experienceData['sick_guard_id'] = $sickGuard->id ;
                $experience = Experience::create($experienceData);
                return response()->json(
                    [
                        'status' => 200,
                        'data' => [
                            'experience' => $experience
                        ]
                    ]);
            }catch (\Exception $exception){
                return response()->json(
                    [
                        'status' => 200,
                        'error' => $exception->getMessage() ,
                        'message' => 'Erreur lors de la création de la experience'
                    ]
                );
            }


        }else{
            return response()->json($data) ;
        }

    }

    public function delete(SickGuardDeleteExperienceRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $this->sick_guard_verify($request);

        if ($data['status'] === 200) {
            $sickGuard = SickGuard::where('email', $request->input('email'))->first();

            try {
                $experience  = $sickGuard->experiences()
                    ->where('id', $request->input('id'));

                if (!$experience->exists()) {
                    return response()->json([
                        'status' => 404,
                        'message' => 'Experience introuvable'
                    ]);
                }
                $experience = $experience->first();

                if($experience->deleted_at != null)
                {
                    return response()->json([
                        'status' => 500,
                        'message' => 'Expérience déja supprimée'
                    ],500);
                }

                $experience->delete();
                return response()->json(
                    [
                        'status' => 200,
                        'message' =>  'experience supprimée avec succèss'
                    ],200);
            }catch (\Exception $exception){
                return response()->json([
                    'status' => 500,
                    'error' => $exception->getMessage() ,
                    'message' => 'Erreur lors de la suppression de l\'experience'
                ]);
            }

        }else{
            return response()->json($data) ;
        }

    }


}
