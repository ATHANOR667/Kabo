<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;




class SickGuard extends Model
{
    use HasFactory , HasApiTokens , Notifiable , SoftDeletes;



    protected $fillable = [
        'status',
        'accepted',
        'active',
        'password',
        'email',
        'sexe',
        'nom',
        'prenom',
        'dateNaissance',
        'lieuNaissance',
        'telephone',
        'pays',
        'ville',
        'quartier',
        'photoProfil',
        'pieceIdentite',
        'admin_id'
    ];
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];


    public function admin(){
        return $this->belongsTo(Admin::class);
    }
    /**
     * @throws \Exception
     */
    public function ban(string $motif = null , $admin): bool|\Illuminate\Http\JsonResponse
    {
        try {
            $existingBan = Ban::withoutTrashed()->where('sick_guard_id', $this->id)->first();

            if ($existingBan) {
                throw new \Exception('Le garde malade est déjà banni.', 400);
            }

            Ban::create([
                'sick_guard_id' => $this->id,
                'motif' => $motif,
                'admin_id' => $admin,
            ]);

            return true;
        } catch (\Exception $e) {

            throw new \Exception( $e->getMessage(), 400);
        }
    }


    /**
     * @throws \Exception
     */
    public function unban(): bool|\Illuminate\Http\JsonResponse
    {
        try {
            $ban = Ban::withoutTrashed()->where('sick_guard_id', $this->id)->first();

            if (!$ban) {
                throw new \Exception('Aucun ban en cours pour ce garde malade', 400);
            }

            $ban->delete();
            return true;
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 400);
        }
    }

    /**
     * @throws \Exception
     */
    public function banList(): array|\Illuminate\Database\Eloquent\Collection|\Illuminate\Http\JsonResponse
    {
        try {
            return Ban::withTrashed()
                ->where('sick_guard_id', $this->id)
                ->get();
        } catch (\Exception $e) {

            throw new \Exception('Une erreur est survenue ', 400);

        }
    }

}
