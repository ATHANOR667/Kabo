<?php

namespace App\Http\Controllers;

use App\Models\Ban;
use App\Models\SickGuard;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;


    /**
     *Permet de verifier le mot de passe d'un garde malade et savoir s'il est banni
     * avant de lui permettre d'effectuer une opération
     *
     * qu'il soit connecté ou pas (vu qu'on vérifie ici meme son ot de passe)
     *
     * Le but est d'utiliser une meme route pour effectuer une opération lorsqu'il est connecté
     * et lorsqu'il n'est pas connecté.
     *
     * Sans avoir à lui donner un token (car ça lui permettrait de faire plusieurs autres choses
     * qu'on ne veut pas forcément.)
     */
    public function sick_guard_verify(Request $request): array
    {
        try {
            $sickGuard = SickGuard::where('email', $request->input('email'));

            if (!$sickGuard->exists()) {
                return
                    [
                        'status' => 404,
                        'message' => 'adresse inconnue.',
                    ];
            }

            $sickGuard = $sickGuard->first();



            $ban = Ban::withoutTrashed()->where('sick_guard_id',$sickGuard->id) ;

            if ($ban->exists()) {
                $ban = $ban->first();
                $message = $ban->motif != null ? 'Vous avez été banni pour : '.$ban->motif : 'Vous avez été banni.' ;
                return
                    [
                        'status' => 401,
                        'message' => $message,
                    ];
            }



            if ($sickGuard->status == 'accepted') {

                if ($sickGuard->active == null){
                    $sickGuard->active = true;
                    $sickGuard->save();
                }

                if (!$sickGuard->active){
                    return
                        [
                            'status' => 403,
                            'message' => 'Vos accèss ont été révoqués',
                        ];

                    }else{
                    if (Hash::check($request->input('password'), $sickGuard->password)) {

                        return
                            [
                                'status' => 200,
                            ];
                            } else {
                        return
                            [
                                'status' => 401,
                                'message' => 'Mot de passe incorrect.',
                            ];
                    }
                }



            }elseif ($sickGuard->status == 'pending'){
                return
                    [
                        'status' => 200,
                    ];
            } else{
                return
                    [
                        'status' => 401,
                        'message' => 'Votre demande d\'inscription  a été refusée',
                    ];
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return
                [
                    'status' => 404,
                    'message' => 'adresse inconnue.',
                ];
        } catch (\Exception $e) {
            Log::error('Erreur lors de la connexion du sickguard : ' . $e->getMessage());

            return
                [
                    'status' => 500,
                    'message' => 'Une erreur interne est survenue.',
                    'error' => $e->getMessage(),
                ];
        }
    }


}
