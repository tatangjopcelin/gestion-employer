<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
    'planning_id',
    'user_id',
    'jour',
    'date_jour',
    'heure_debut',
    'heure_fin',
    'poste',
    'heure_pointage_debut', 
    'heure_pointage_fin',   
    'duree_minutes',        
    'retard_minutes',       
    'heures_supp_minutes',  
];


    /**
     * Relation avec le planning
     */
    public function planning()
    {
        return $this->belongsTo(Planning::class);
    }

    /**
     * Relation avec l'employé
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
