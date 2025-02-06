<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Qualification extends Model
{
    use HasFactory , SoftDeletes;

    protected $fillable =
        [
          'titre',
          'annee',
          'mention',
          'institutionReference',
          'fichier',
          'sick_guard_id'
        ];

    public function sickGuard(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SickGuard::class);
    }

}
