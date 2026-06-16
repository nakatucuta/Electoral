<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VotanteAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'votante_id',
        'user_id',
        'action',
        'title',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function votante(): BelongsTo
    {
        return $this->belongsTo(Votante::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
