<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_id',
        'titre',
        'description',
        'heure_debut',
        'heure_fin',
        'statut',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
