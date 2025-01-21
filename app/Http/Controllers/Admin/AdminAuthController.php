<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OtpPasswordRequest;
use App\Mail\OtpMail;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminAuthController extends Controller
{
    /**
     *
     *On initie l'inscription en vérifiant le matricule puis en envoyant l'Otp par le mail donné
     *
     *
     */
    // Signin initialization (sending OTP)
    public function signin_init(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $admin = Admin::where('matricule', $request->input('matricule'))->first();

            if (!$admin) {
                return response()->json(
                    [
                        'status' => 404,
                        'message' => 'Admin non trouvé.',
                    ],
                    404
                );
            }

            if (!$admin->email) {
                $email = $request->input('email');

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

                $otp = random_int(1000, 9999);
                Cache::put('otp_' . $email, $otp, 600);
                Cache::put('validation_email_' . $otp, $email, 600);
                Cache::put('validation_matricule_' . $otp . '_' . $email, $request->input('matricule'), 600);

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

                Cache::put('last_email_sent_time_' . $email, now(), 300);

                return response()->json(
                    [
                        'status' => 200,
                        'message' => 'E-mail envoyé avec succès.',
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'status' => 400,
                        'message' => "Vous êtes déjà inscrit.",
                    ],
                    400
                );
            }

        } catch (\Exception $exception) {
            Log::error('Erreur dans le api/admin/signin-init', ['error' => $exception->getMessage()]);
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
            $matricule = Cache::get('validation_matricule_' . $otp . '_' . $email);
        } else {
            return response()->json(
                [
                    'status' => 404,
                    'message' => 'Votre OTP  expiré ou incorrect.',
                ]
            );
        }

        try {
            if (Cache::has('otp_' . $email) && Cache::get('otp_' . $email) == $otp) {
                Cache::forget('otp_' . $email);

                $admin = Admin::where('matricule', $matricule)->first();
                if (!$admin) {
                    return response()->json(
                        [
                            'status' => 404,
                            'message' => 'Admin non trouvé.',
                        ],
                        404
                    );
                }

                $admin->update([
                    'password' => bcrypt($request->input('password')),
                    'email' => $email,
                ]);

                return response()->json(
                    [
                        'status' => 200,
                        'message' => 'Inscription complète , vous pouvez maintenant vous connecter.',
                    ],
                    200
                );
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

            $admin = Admin::where('email', $request['email']);

            if (!$admin->exists()) {
                return response()->json(
                    [
                        'status' => 404,
                        'message' => 'adresse inconnue.',
                    ],
                    404
                );
            }

            $admin = $admin->first();

            if (!$admin) {
                return response()->json(
                    [
                        'status' => 404,
                        'message' => 'Adresse email inconnue.',
                    ],
                    404
                );
            }

            if (Hash::check($request['password'], $admin->password)) {
                $token = $admin->createToken('AdminToken', ['*'], now()->addMinutes(60))
                    ->plainTextToken;

                return response()->json(
                    [
                        'status' => 200,
                        'message' => 'Connexion réussie.',
                        'data' => [
                            'token' => $token,
                            'admin' => $admin,
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

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(
                [
                    'status' => 422,
                    'message' => 'Validation échouée.',
                    'errors' => $e->errors(),
                ],
                422
            );
        } catch (\Exception $e) {
            Log::error('Erreur lors de la connexion de l\'admin : ' . $e->getMessage());

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
            $admin = $request->user('admin');

            $admin->tokens?->each(function ($token) {
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
        $email = $request->user('admin')->email;

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
            $admin = Admin::where('email', $request->input('email'));

            if (!$admin->exists()) {
                return response()->json(
                    [
                        'status' => 404,
                        'message' => 'adresse inconnue.',
                    ],
                    404
                );
            }

            $admin = $admin->first();
            $email = $admin->email;
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



    function password_reset_while_dissconnected_process(OtpPasswordRequest $request):string|RedirectResponse
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
            $admin = Admin::where('email', $email);

            if (!$admin->exists()) {
                return response()->json(
                    [
                        'status' => 404,
                        'message' => 'adresse inconnue.',
                    ],
                    404
                );
            }

            $admin = $admin->first();
        }catch (\Exception $e){
            return response()->json(
                [
                    'status' => 500,
                    'message' => 'Operation imposssible , adresse inconnue.',
                    'error' => $e->getMessage(),
                ],
                500
            );
        }



        try {
            if (Cache::has('otp_disconnected_' . $email) ) {

                if(Cache::get('otp_disconnected_' . $email) == $otp){
                    Cache::forget('otp_disconnected_' . $email);
                    $admin->update([
                        'password'=> bcrypt($request->input('password')),
                    ]);

                    $admin->tokens?->each(function ($token) {
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
            $admin = $request->user('admin');
            $email = $admin->email;
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

    function password_reset_while_connected_process(OtpPasswordRequest $request):string|RedirectResponse
    {
        try {
            $admin = $request->user('admin');
            $email = $admin->email;
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
                    $admin->update([
                        'password'=> bcrypt($request->input('password')),
                    ]);

                    $admin->tokens?->each(function ($token) {
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
            $admin =$request->user('admin');
            $email = $request->input('email');

            if (Hash::check($request->input('password'),$admin->password) ){
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
            $admin = $request->user('admin');

            if (Cache::has('otp_' . $email) && Cache::get('otp_' . $email) == $otp) {
                Cache::forget('otp_' . $email);
                $admin->update([
                    'email'=> $email ,
                ]);
                $admin->tokens?->each(function ($token) {
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
