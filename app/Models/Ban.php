<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ban extends Model
{
    use HasFactory , softDeletes;
    protected $fillable = [
        'motif',
        'sick_guard_id',
        'client_id' ,
        'admin_id'
    ];

    public function admin(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Admin::class)->withTrashed();
    }

    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function sickGuard(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SickGuard::class, 'sick_guard_id');
    }


}
