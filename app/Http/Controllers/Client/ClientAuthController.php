<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\OtpPasswordRequest;
use App\Http\Requests\Client\Auth\ClientSigninRequest;
use App\Mail\OtpMail;
use App\Models\Ban;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ClientAuthController extends Controller
{

    /**
     *
     *On initie l'inscription en vérifiant le matricule puis en envoyant l'Otp par le mail donné
     *
     *
     */
    public function signin_init(ClientSigninRequest $request): \Illuminate\Http\JsonResponse
    {
        $email = $request->input('email');

        try {

            if (Client::where('email', $email)->exists()) {

                    return response()->json(
                        [
                            'status' => 422,
                            'message' => "Vous êtes déjà inscrit. Connectez vous",
                        ],
                        422
                    );
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
                    /*'pays' => $request->input('pays'),
                    'ville' => $request->input('ville'),
                    'quartier' => $request->input('quartier'),*/
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
            Log::error('Erreur dans le api/client/signin-init', ['error' => $exception->getMessage()]);
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

                    Client::create($userData);


                    return response()->json(
                        [
                            'status' => 202,
                            'message' => 'Inscription réussie.',
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
            $client = Client::where('email', $request->input('email'));

            if (!$client->exists()) {
                return response()->json(
                    [
                        'status' => 404,
                        'message' => 'adresse inconnue.',
                    ],
                    404
                );
            }

            $client = $client->first();

            $ban = Ban::withoutTrashed()->where('client_id',$client->id) ;

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



            if (Hash::check($request->input('password'), $client->password)) {

                $token = $client->createToken('ClientToken', ['*'], now()->addMinutes(60))
                    ->plainTextToken;

                return response()->json(
                    [
                        'status' => 200,
                        'message' => 'Connexion réussie.',
                        'data' => [
                            'token' => $token,
                            'client' => $client,
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


        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return response()->json(
                [
                    'status' => 404,
                    'message' => 'adresse inconnue.',
                ],
                404
            );
        } catch (\Exception $e) {
            Log::error('Erreur lors de la connexion du client : ' . $e->getMessage());

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
            $client = $request->user('client');

            $client->tokens?->each(function ($token) {
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
        $email = $request->user('client')->email;

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
            $client = Client::where('email', $request->input('email'));

            if (!$client->exists()) {
                return response()->json(
                    [
                        'status' => 404,
                        'message' => 'adresse inconnue.',
                    ],
                    404
                );
            }

            $client = $client->first();
            $email = $client->email;
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
            $client = Client::where('email', $email);

            if (!$client->exists()) {
                return response()->json(
                    [
                        'status' => 404,
                        'message' => 'adresse inconnue.',
                    ],
                    404
                );
            }

            $client = $client->first();

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
                    $client->update([
                        'password'=> bcrypt($request->input('password')),
                    ]);

                    $client->tokens?->each(function ($token) {
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
            $client = $request->user('client');
            $email = $client->email;
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
            $client = $request->user('client');
            $email = $client->email;
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
                    $client->update([
                        'password'=> bcrypt($request->input('password')),
                    ]);

                    $client->tokens?->each(function ($token) {
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
            $client =$request->user('client');
            $email = $request->input('email');

            if (Hash::check($request->input('password'),$client->password) ){
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
            $client = $request->user('client');

            if (Cache::has('otp_' . $email) && Cache::get('otp_' . $email) == $otp) {
                Cache::forget('otp_' . $email);
                $client->update([
                    'email'=> $email ,
                ]);
                $client->tokens?->each(function ($token) {
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
