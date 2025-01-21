<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\GestionAdmin\SuperadminCreateAdminRequest;
use App\Http\Requests\SuperAdmin\GestionAdmin\SuperadminDeleteAdminRequest;
use App\Http\Requests\SuperAdmin\GestionAdmin\SuperadminEditAdminRequest;
use App\Models\Admin;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class GestionAdminController extends Controller
{


    public function admin_create(SuperadminCreateAdminRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $data = $request->validated();

            $admin = Admin::create([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'telephone' => $data['telephone'],
                'pays' => $data['pays'],
                'ville' => $data['ville'],
                'photoProfil' => $request->file('photoProfil') ? $request->file('photoProfil')->store('profiles', 'public') : null,
                'pieceIdentite' => $request->file('pieceIdentite') ? $request->file('pieceIdentite')->store('identities', 'public') : null,
            ]);

            return response()->json(
                [
                    'status' => 201,
                    'message' => 'Admin créé avec succès.',
                    'admin' => $admin,
                ],
                201);

        } catch (ValidationException $e) {

            return response()->json(
                [
                    'status' => 422,
                    'message' => 'Échec de la création de l\'admin.',
                    'errors' => $e->errors(),
                ],
                422);
        } catch (\Exception $e) {

            return response()->json(
                [
                    'status' => 500,
                    'message' => 'Erreur lors de la création de l\'admin.',
                    'error' => $e->getMessage(),
                ],
                500);
        }
    }



    public function admin_list(): \Illuminate\Http\JsonResponse
    {
        try {
            $admins = Admin::withoutTrashed()->get()->toArray();
            $admins_deleted = Admin::onlyTrashed()->get()->toArray();

            return response()->json(
                [
                    'status' => '200',
                    'admins' =>$admins,
                    'admins_deleted' => $admins_deleted,
                ],
                200);
        }catch (\Exception $e){
            return response()->json(
                [
                    'status' => 404,
                    'message' => $e->getMessage()
                ],
                404);
        }
    }



    public function admin_edit(SuperadminEditAdminRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $data = $request->validated();
            $admin = Admin::find($data['id']);
            $admin->update(Arr::except($data , 'id'));
            $admin->save();
            return response()->json(
                [
                    'status' => 201,
                    'message ' => 'Admin mis a jour avec succes.',
                    'admin' => $admin,
                ]
                ,201
            );
        }catch (ValidationException $exception) {
            return response()->json(
                [
                    'status' => 422,
                    'message' => 'Erreur de validation',
                    'error' => $exception->getMessage(),
                ]
                ,422
            );
        }
        catch (\Exception $e){
            return response()->json(
                [
                    'status' => 422,
                    'message' => 'Erreur interne',
                    'error' => $e->getMessage(),
                ]
                , 422
            );
        }
    }

    public function admin_delete(SuperadminDeleteAdminRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $data = $request->validated();
            $admin = Admin::find($data['id']);
            $admin->delete();
            return response()->json(
                [
                    'status' => 200,
                    'message' => 'Suppression réussie.',
                ]
                ,200
            );
        }catch (ValidationException $exception) {
            return response()->json(
                [
                    'status' => 422,
                    'message' => 'Erreur de validation',
                    'error' => $exception->getMessage(),
                ]
                ,422
            );
        } catch (\Exception $e){
            return response()->json(
                [
                    'status' => 422,
                    'message' => 'Erreur interne',
                    'error' => $e->getMessage(),
                ]
                ,422
            );
        }
    }
}
