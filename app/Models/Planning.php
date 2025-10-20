<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Planning extends Model
{
    use HasFactory;

    protected $fillable = [
        'semaine_debut',
        'semaine_fin',
        'titre',
        'cree_par',
    ];

    /**
     * Le créateur du planning (admin)
     */
    public function createur()
    {
        return $this->belongsTo(User::class, 'cree_par');
    }

    /**
     * Les shifts (créneaux) liés à ce planning
     */
    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }
}
