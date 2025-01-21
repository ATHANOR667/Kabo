<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Ban;
use App\Models\Client;
use App\Models\SickGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class AdminManageSickGuardController extends Controller
{

    public function sickGuardList(): \Illuminate\Http\JsonResponse
    {
        try {
            $sickGuard = SickGuard::withTrashed()->get();

            $selfRejected = $sickGuard->whereNotNull('deleted_at');
            $pending = $sickGuard->where('status', 'pending')
                ->filter(function ($item) {
                    return !Ban::withoutTrashed()->where('sick_guard_id', $item->id)->exists();
                });
            $accepted = $sickGuard->where('status', 'accepted')
                ->filter(function ($item) {
                    return !Ban::withoutTrashed()->where('sick_guard_id', $item->id)->exists();
                });
            $rejected = $sickGuard->where('status', 'rejected')
                ->filter(function ($item) {
                    return !Ban::withoutTrashed()->where('sick_guard_id', $item->id)->exists();
                });
            $banned = Ban::withoutTrashed()
                ->whereNotNull('sick_guard_id')
                ->get()
                ->map(function ($ban) {
                    $sickGuardData = $ban->sickGuard->toArray() ;
                    $motifData = ['motif' => $ban->motif];
                    return collect($sickGuardData)->merge($motifData);
                });


            return response()->json([
                'status' => 200,
                'data' => [
                    'selfRejected' => $selfRejected,
                    'accepted' => $accepted,
                    'rejected' => $rejected,
                    'pending' => $pending ,
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


    public function sickGuardAccept(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $sickguard = SickGuard::withoutTrashed()->where('id',$request->input('id'));


            if (!$sickguard->exists()) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Profil non trouvé.',
                ]);
            }
            $sickguard = $sickguard->first();


            $sickguard->update(['status' => 'accepted' ,'admin_id' => $request->user('admin')->id]);
            $sickguard->save();

            return response()->json([
                'status' => 200,
                'message' => 'Le profil de ' . $sickguard->nom . ' ' . $sickguard->prenom . ' a été accepté.',
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 500,
                'message' => 'Erreur interne.',
                'error' => $exception->getMessage(),
            ]);
        }
    }


    public function sickGuardReject(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $sickguard = SickGuard::withoutTrashed()->where('id',$request->input('id'));


            if (!$sickguard->exists()) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Profil non trouvé.',
                ]);
            }
            $sickguard = $sickguard->first();


            $sickguard->update(['status' => 'rejected' , 'admin_id' => $request->user('admin')->id]);
            $sickguard->save();

            return response()->json([
                'status' => 200,
                'message' => 'Le profil de ' . $sickguard->nom . ' ' . $sickguard->prenom . ' a été rejeté.',
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 500,
                'message' => 'Erreur interne.',
                'error' => $exception->getMessage(),
            ]);
        }
    }


    public function sickGuardBan( Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $sickguard = SickGuard::withoutTrashed()->where('id',$request->input('id'));


            if (!$sickguard->exists()) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Profil non trouvé.',
                ]);
            }

            $sickguard = $sickguard->first();


            $sickguard->ban($request->input('motif'),$request->user('admin')->id);

            return response()->json([
                'status' => 200,
                'message' =>  $sickguard->nom . ' ' . $sickguard->prenom . ' a été banni.',
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 500,
                'message'  => $exception->getMessage(),
            ]);
        }
    }

    public function sickGuardUnBan(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $sickguard = SickGuard::withoutTrashed()->where('id',$request->input('id'));

            if (!$sickguard->exists()) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Profil non trouvé.',
                ]);
            }

            $sickguard = $sickguard->first();
            $sickguard->unban();

            return response()->json([
                'status' => 200,
                'message' =>  $sickguard->nom . ' ' . $sickguard->prenom . ' a été retiré de la liste des bannis.',
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 500,
                'message'  => $exception->getMessage(),
            ]);
        }
    }

    public function sickGuardBanList(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $sickguard = SickGuard::withoutTrashed()->where('id',$request->input('id'));


            if (!$sickguard->exists()) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Profil non trouvé.',
                ]);
            }
            $sickguard = $sickguard->first();


            return response()->json(
                [
                    'status' => 200,
                    'historique des bans du garde malade' => $sickguard->banList()->map(function ($ban) {
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
