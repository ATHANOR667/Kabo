<?php

namespace App\Http\Controllers\SickGuard;

use App\Http\Controllers\Controller;
use App\Http\Requests\SickGuard\Auth\SickGuardSigninRequest;
use App\Http\Requests\OtpPasswordRequest;
use App\Mail\DemandeSigninSickGuard;
use App\Mail\OtpMail;
use App\Models\Ban;
use App\Models\SickGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AuthSickGuardController extends Controller
{

    /**
     *
     *On initie l'inscription en vérifiant le matricule puis en envoyant l'Otp par le mail donné
     *
     *
     */
    public function signin_init(SickGuardSigninRequest $request): \Illuminate\Http\JsonResponse
    {
        $email = $request->input('email');

        try {
            if (SickGuard::where('email', $email)->exists()) {
                $sickGuard =  SickGuard::where('email', $email)->first();

                if($sickGuard->accepted){
                    return response()->json(
                        [
                            'status' => 422,
                            'message' => "Vous êtes déjà inscrit. Connectez vous",
                        ],
                        422
                    );
                }elseif ($sickGuard->accepted == null){
                    return response()->json(
                        [
                            'status' => 422,
                            'message' => "Votre demande est encore en cours de traitement. Nous vous répondrons dans les plus bref délais.",
                        ],
                        422
                    );
                }else{
                    return response()->json(
                        [
                            'status' => 422,
                            'message' => "Votre demande a été rejetée ",
                        ],
                        422
                    );
                }

            }

            $lastSentTime = Cache::get('last_email_sent_time_' . $email);
            if ($lastSentTime) {
                $currentTime = now();
                $diffInMinutes = $lastSentTime->diffInMinutes($currentTime);

                if ($diffInMinutes < 5) {
                    return response()->json(
                        [
                            'status' => 429,
                            'message' => 'Veuillez attendre ' . (5 - $diffInMinutes) . ' minutes avant de retenter.',
                        ],
                        429
                    );
                }
            }

            $otp = random_int(1000, 9999);
            $photoProfilPath = $request->file('photoProfil')?->store('profiles', 'public');
            $pieceIdentitePath = $request->file('pieceIdentite')?->store('identities', 'public');

            $data = [
                $otp => [
                    'sexe' => $request->input('sexe'),
                    'nom' => $request->input('nom'),
                    'prenom' => $request->input('prenom'),
                    'dateNaissance' => $request->input('dateNaissance'),
                    'lieuNaissance' => $request->input('lieuNaissance'),
                    'telephone' => $request->input('telephone'),
                    'pays' => $request->input('pays'),
                    'ville' => $request->input('ville'),
                    'quartier' => $request->input('quartier'),
                    'photoProfil' => $photoProfilPath,
                    'pieceIdentite' => $pieceIdentitePath,
                ]
            ];

            try {
                Mail::to($email)->send(new OtpMail($otp));
            } catch (\Exception $e) {
                return response()->json(
                    [
                        'status' => 500,
                        'message' => 'Erreur lors de l\'envoi de l\'email.',
                        'error' => $e->getMessage(),
                    ],
                    500
                );
            }
            Cache::put('otp_' . $email, $data, 600);
            Cache::put('validation_email_' . $otp, $email, 600);
            Cache::put('last_email_sent_time_' . $email, now(), 300);

            return response()->json(
                [
                    'status' => 200,
                    'message' => 'E-mail envoyé avec succès.',
                ],
                200
            );
        } catch (\Exception $exception) {
            Log::error('Erreur dans le api/sickguard/signin-init', ['error' => $exception->getMessage()]);
            return response()->json(
                [
                    'status' => 500,
                    'message' => 'Erreur interne.',
                    'error' => $exception->getMessage(),
                ],
                500
            );
        }
    }


    public function signin_process(OtpPasswordRequest $request): \Illuminate\Http\JsonResponse
    {
        $otp = $request->input('otp');

        if (Cache::has('validation_email_' . $otp)) {
            $email = Cache::get('validation_email_' . $otp);
        } else {
            return response()->json(
                [
                    'status' => 404,
                    'message' => 'Votre OTP a expiré ou est incorrect.',
                ],
                404
            );
        }

        try {
            if (Cache::has('otp_' . $email)) {
                $data = Cache::get('otp_' . $email);

                if (isset($data[$otp])) {
                    $userData = $data[$otp];
                    $userData['email'] = $email;
                    $userData['password'] = bcrypt($request->input('password')) ;

                    Cache::forget('otp_' . $email);
                    Cache::forget('validation_email_' . $otp);

                    SickGuard::create($userData);

                    try {
                        Mail::to($email)->send(new DemandeSigninSickGuard($userData['nom'],$userData['prenom']));
                    }catch (\Exception $exception){}

                    return response()->json(
                        [
                            'status' => 202,
                            'message' => 'Votre demande d\'inscription a bien été prise en compte.',
                        ],
                        200
                    );
                } else {
                    return response()->json(
                        [
                            'status' => 401,
                            'message' => 'OTP incorrect.',
                        ],
                        401
                    );
                }
            } else {
                return response()->json(
                    [
                        'status' => 401,
                        'message' => 'OTP incorrect ou expiré.',
                    ],
                    401
                );
            }
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => 500,
                    'message' => 'Une erreur inattendue est survenue.',
                    'error' => $e->getMessage(),
                ],
                500
            );
        }
    }

    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $sickGuard = SickGuard::where('email', $request->input('email'));

            if (!$sickGuard->exists()) {
                return response()->json(
                    [
                        'status' => 404,
                        'message' => 'adresse inconnue.',
                    ],
                    404
                );
            }

            $sickGuard = $sickGuard->first();



            $ban = Ban::withoutTrashed()->where('sick_guard_id',$sickGuard->id) ;

            if ($ban->exists()) {
                $ban = $ban->first();
                $message = $ban->motif != null ? 'Vous avez été banni pour : '.$ban->motif : 'Vous avez été banni.' ;
                return response()->json(
                    [
                        'status' => 401,
                        'message' => $message,

                    ]
                );
            }



            if ($sickGuard->status == 'accepted') {

                if ($sickGuard->active == null){
                    $sickGuard->active = true;
                    $sickGuard->save();
                }

                if (!$sickGuard->active){
                    return response()->json(
                        [
                            'status' => 403,
                            'message' => 'Vos accèss ont été révoqués',
                        ],
                        403
                    );
                }else{
                    if (Hash::check($request->input('password'), $sickGuard->password)) {

                        $token = $sickGuard->createToken('SickGuardToken', ['*'], now()->addMinutes(60))
                            ->plainTextToken;

                        return response()->json(
                            [
                                'status' => 200,
                                'message' => 'Connexion réussie.',
                                'data' => [
                                    'token' => $token,
                                    'sickguard' => $sickGuard,
                                ]
                            ],
                            200
                        );
                    } else {
                        return response()->json(
                            [
                                'status' => 401,
                                'message' => 'Mot de passe incorrect.',
                            ],
                            401
                        );
                    }
                }



            }elseif ($sickGuard->status == 'pending'){
                return response()->json(
                    [
                        'status' => 401,
                        'message' => 'Votre demande d\'inscription n\'est pas encore traitée',
                    ],
                    401
                );
            } else{
                return response()->json(
                    [
                        'status' => 401,
                        'message' => 'Votre demande d\'inscription  a été refusée',
                    ],
                    401
                );
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return response()->json(
                [
                    'status' => 404,
                    'message' => 'adresse inconnue.',
                ],
                404
            );
        } catch (\Exception $e) {
            Log::error('Erreur lors de la connexion du sickguard : ' . $e->getMessage());

            return response()->json(
                [
                    'status' => 500,
                    'message' => 'Une erreur interne est survenue.',
                    'error' => $e->getMessage(),
                ],
                500
            );
        }
    }


    /**
     *
     *
     * LOGOUT
     *
     *
     */
    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {

        try {
            $sickGuard = $request->user('sickguard');

            $sickGuard->tokens?->each(function ($token) {
                $token->delete();
            });

            return response()->json(
                [
                    'status' => 200,
                    'message' => 'Déconnexion réussie.'
                ]
                , 200
            );
        }catch (\Exception $e)    {
            return response()->json(
                [
                    'status' => 404,
                    'message' => 'Utilisateur déja déconnecté .'
                ]
                , 404
            );
        }

    }

    /**
     *
     *
     *
     *
     * MODIFICATION DES IDENTIFIANTS
     *
     *
     *
     *
     */



    /**
     *
     *
     * MODIFICATION DES IDENTIFIANTS PAR DEFAUT (email et mot de passe)
     *
     *
     */


    public function otp_request(Request $request): \Illuminate\Http\JsonResponse
    {
        $email = $request->user('sickguard')->email;

        $lastSentTime = Cache::get('last_email_sent_time_' . $email);

        if ($lastSentTime) {
            $currentTime = now();
            $diffInMinutes = $lastSentTime->diffInMinutes($currentTime);

            if ($diffInMinutes < 5) {
                return response()->json(
                    [
                        'status' => 400,
                        'message' => 'Veuillez attendre ' . (5 - $diffInMinutes) . ' minutes avant de retenter.',
                    ],
                    400
                );
            }
        }

        try {
            $otp = random_int(1000, 9999);

            Cache::put('otp_' . $email, $otp, 600);
            Mail::to($email)->send(new OtpMail($otp));
            Cache::put('last_email_sent_time_' . $email, now(), 300); // Cache pour 5 minutes

            return response()->json(
                [
                    'status' => 200,
                    'message' => 'E-mail envoyé avec succès.',
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => 500,
                    'message' => 'Une erreur s\'est produite lors de l\'envoi de l\'email. Veuillez réessayer.',
                    'error' => $e->getMessage(),
                ],
                500
            );
        }
    }






    /**
     *
     *
     *MODIFICAION DU MOT DE PASSE D'UN UTILISATEUR QUI N'EST PAS CONNECTE
     *
     *
     *
     */



    function password_reset_while_dissconnected_init(Request $request): \Illuminate\Http\JsonResponse|\Exception
    {
        try {
            $sickGuard = SickGuard::where('email', $request->input('email'));

            if ($sickGuard->exists()) {
                return response()->json(
                    [
                        'status' => 404,
                        'message' => 'adresse inconnue.',
                    ],
                    404
                );
            }

            $sickGuard = $sickGuard->first();

            $email = $sickGuard->email;
        }catch (\Exception $e){
            return response()->json(
                [
                    'status' => 500,
                    'message' => 'Operation imposssible , compte sans adresse',
                    'error' => $e->getMessage(),
                ],
                500
            );
        }

        if ( Cache::has('last_email_sent_time_' . $email)) {
            $lastSentTime = Cache::get('last_email_sent_time_' . $email);
            $currentTime = now();

            $diffInMinutes = $lastSentTime->diffInMinutes($currentTime);
            return response()->json(
                [
                    'status' => 400,
                    'message' => 'Veuillez attendre ' . (5 - $diffInMinutes) . ' minutes avant de retenter.',
                ],
                400
            );
        }

        try {
            $otp = random_int(1000, 9999);

            Mail::to($email)->send(new OtpMail($otp));
            Cache::put('otp_disconnected_' .$email, $otp, 600);
            Cache::put('email_disconnected_' .$otp , $email, 600);
            Cache::put('last_email_sent_time_' . $email, now(), 300);

            return response()->json(
                [
                    'status' => 200,
                    'message' => 'Otp envoyé',
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => 500,
                    'message' => 'Erreur interne' ,
                    'error' => $e->getMessage(),
                ],
                500
            );
        }
    }



    function password_reset_while_dissconnected_process( Request $request):string|RedirectResponse
    {
        $otp = $request->input('otp');

        if(Cache::has('email_disconnected_' . $otp)) {
            $email = Cache::get('email_disconnected_' . $otp);
        }else{
            return response()->json(
                [
                    'status' => 404,
                    'message' => 'Otp expiré ou incorrect.',
                ]
                ,404
            );
        }
        try {
            $sickGuard = SickGuard::where('email', $email);

            if ($sickGuard->exists()) {
                return response()->json(
                    [
                        'status' => 404,
                        'message' => 'adresse inconnue.',
                    ],
                    404
                );
            }

            $sickGuard = $sickGuard->first();

        }catch (\Exception $e){
            return response()->json(
                [
                    'status' => 500,
                    'message' => 'Erreur interne' ,
                    'error' => $e->getMessage(),
                ],
                500
            );
        }



        try {
            if (Cache::has('otp_disconnected_' . $email) ) {

                if(Cache::get('otp_disconnected_' . $email) == $otp){
                    Cache::forget('otp_disconnected_' . $email);
                    $sickGuard->update([
                        'password'=> bcrypt($request->input('password')),
                    ]);

                    $sickGuard->tokens?->each(function ($token) {
                        $token->delete();
                    });

                    return response()->json(
                        [
                            'status' => 200,
                            'message' => 'Mot de passe mis a jour avec succes. Veuillez vous reconnecter.',
                        ],
                        200
                    );
                }else{
                    return response()->json(
                        [
                            'status' => 401,
                            'message' => 'Otp incorrect.',
                        ],
                        401
                    );
                }
            }else{
                return response()->json(
                    [
                        'status' => 400,
                        'message' => 'OTP expiré.',
                    ],
                    400
                );
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(
                [
                    'status' => 500,
                    'message' => 'Une erreur inattendue est survenue.',
                    'error' => $e->getMessage(),
                ],
                500
            );
        }
    }


    /**
     *
     *
     *MODIFICAION DU MOT DE PASSE D'UN UTILISATEUR EST  CONNECTE
     *
     *
     */



    function password_reset_while_connected_init(Request $request):string|RedirectResponse
    {
        try {
            $sickGuard = $request->user('sickguard');
            $email = $sickGuard->email;
        }catch (\Exception $e){
            return response()->json(
                [
                    'status' => 500,
                    'message' => 'Operation imposssible , veuillez vous reconnecter.',
                ],
                500
            );
        }

        if ( Cache::has('last_email_sent_time_' . $email)) {
            $lastSentTime = Cache::get('last_email_sent_time_' . $email);
            $currentTime = now();

            $diffInMinutes = $lastSentTime->diffInMinutes($currentTime);
            return response()->json(
                [
                    'status' => 400,
                    'message' => 'Veuillez attendre ' . (5 - $diffInMinutes) . ' minutes avant de retenter.',
                ],
                400
            );
        }

        try {
            $otp = random_int(1000, 9999);
            Cache::put('otp_' . $email, $otp, 600);
            Mail::to($email)->send(new OtpMail($otp));
            Cache::put('last_email_sent_time_' . $email, now(), 300);

            return response()->json(
                [
                    'status' => 200,
                    'message' => 'Otp envoyé',
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => 500,
                    'message' => 'Erreur interne : ' . $e->getMessage(),
                ],
                500
            );
        }
    }

    function password_reset_while_connected_process( Request $request):string|RedirectResponse
    {
        try {
            $sickGuard = $request->user('sickguard');
            $email = $sickGuard->email;
        }catch (\Exception $e){
            return response()->json(
                [
                    'status' => 500,
                    'message' => 'Operation imposssible , veuillez vous reconnecter.',
                    'error' => $e->getMessage(),
                ],
                500
            );
        }


        $otp = $request->input('otp');

        try {
            if (Cache::has('otp_' . $email) ) {

                if(Cache::get('otp_' . $email) == $otp){
                    Cache::forget('otp_' . $email);
                    $sickGuard->update([
                        'password'=> bcrypt($request->input('password')),
                    ]);

                    $sickGuard->tokens?->each(function ($token) {
                        $token->delete();
                    });

                    return response()->json(
                        [
                            'status' => 200,
                            'message' => 'Mot de passe mis a jour avec succes. Veuillez vous reconnecter.',
                        ],
                        200
                    );
                }else{
                    return response()->json(
                        [
                            'status' => 401,
                            'message' => 'Otp incorrect.',
                        ],
                        401
                    );
                }
            }else{
                return response()->json(
                    [
                        'status' => 400,
                        'message' => 'OTP expiré.',
                    ],
                    400
                );
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(
                [
                    'status' => 500,
                    'message' => 'Une erreur inattendue est survenue.',
                    'error' => $e->getMessage(),
                ],
                500
            );
        }
    }







    /**
     *
     *
     *MODIFICATION DE L'EMAIL
     *
     *
     */


    function email_reset_init(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $sickGuard =$request->user('sickguard');
            $email = $request->input('email');

            if (Hash::check($request->input('password'),$sickGuard->password) ){
                if (session()->has('last_email_sent_time')) {
                    $lastSentTime = session()->get('last_email_sent_time');
                    $currentTime = now();
                    $diffInMinutes = $lastSentTime->diffInMinutes($currentTime);
                    if ($diffInMinutes < 5) {
                        return response()->json(
                            [
                                'status' => 400,
                                'message' => 'Veuillez attendre ' . (5 - $diffInMinutes) . ' minutes avant de retenter.',
                            ],
                            400
                        );
                    }
                }
                try {
                    $otp = random_int(1000, 9999);
                    Cache::put('otp_' . $email, $otp, 600);
                    Mail::to($email)->send(new OtpMail($otp));
                    Cache::put('validation_email_'.$otp, $email);
                    Cache::put('last_email_sent_time', now());
                    return response()->json(
                        [
                            'status' => 200,
                            'message' => 'Otp envoyé avec succès.',
                        ],
                        400
                    );
                } catch (\Exception $e) {
                    return response()->json(
                        [
                            'status' => 400,
                            'message' => 'Erreur interne : ' . $e->getMessage(),
                        ],
                        400
                    );
                }
            } else {
                return response()->json(
                    [
                        'status' => 401,
                        'message' => 'Mot  de passe incorrect.',
                    ],
                    401
                );
            }

        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => 500,
                    'message' => 'Erreur inattendue est survenue : '.$e->getMessage(),
                ],
                500
            );
        }
    }

    public function email_reset_process(Request $request): \Illuminate\Http\JsonResponse
    {
        $otp = $request->input('otp');
        $email = Cache::get('validation_email_'.$otp);
        try {
            $sickGuard = $request->user('sickguard');

            if (Cache::has('otp_' . $email) && Cache::get('otp_' . $email) == $otp) {
                Cache::forget('otp_' . $email);
                $sickGuard->update([
                    'email'=> $email ,
                ]);
                $sickGuard->tokens?->each(function ($token) {
                    $token->delete();
                });

                return response()->json(
                    [
                        'status' => 200,
                        'message' => 'Email du compte modifié avec succes.',
                    ],
                    200
                );
            }else{

                return response()->json(
                    [
                        'status' => 401,
                        'message' => 'OTP incorrect.',
                    ],
                    401
                );
            }
        } catch (\Exception $e) {

            return response()->json(
                [
                    'status' => 500,
                    'message' => 'Erreur inattendue est survenue : '.$e->getMessage(),
                ],
                500
            );
        }

    }


}
