<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use function Termwind\render;


class Experience extends Model
{
    use HasFactory , SoftDeletes;

    protected $fillable =
        [
            'nomEntreprise',
            'typeEntreprise',
            'nomReferent',
            'numeroReferent',
            'posteReferent',
            'dateDebut',
            'dateFin',
            'poste',
            'description',
            'sick_guard_id'
        ];

    public function sickGuard(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SickGuard::class);
    }

}
