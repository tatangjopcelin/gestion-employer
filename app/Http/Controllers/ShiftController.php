<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ShiftController extends Controller
{
    /**
     * Pointage début (Arrivé)
     */
    public function pointageDebut(Request $request, Shift $shift)
    {
        // Vérification que l'employé pointe son propre shift
        if ($shift->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        // Empêche double pointage
        if ($shift->heure_pointage_debut) {
            return response()->json(['message' => 'Vous avez déjà pointé le début'], 422);
        }

        // Enregistrement de l'heure actuelle
        $shift->update(['heure_pointage_debut' => now()->format('H:i')]);

        return response()->json([
            'message' => 'Pointage début enregistré',
            'heure_pointage_debut' => $shift->heure_pointage_debut
        ]);
    }

    /**
     * Pointage fin (Terminé) + calcul durée, retard et heures sup
     */
    

public function pointageFin(Request $request, Shift $shift)
{
    // Vérifie que l'utilisateur correspond au shift
    if ($shift->user_id !== $request->user()->id) {
        return response()->json(['message' => 'Non autorisé'], 403);
    }

    // Vérifie que le pointage début a été fait
    if (!$shift->heure_pointage_debut) {
        return response()->json(['message' => 'Vous devez d’abord pointer le début'], 422);
    }

    // Vérifie si le pointage fin a déjà été fait
    if ($shift->heure_pointage_fin) {
        return response()->json(['message' => 'Vous avez déjà pointé la fin'], 422);
    }

    try {
        // Heures prévues et pointées
        $heureDebutPrevue = Carbon::parse($shift->heure_debut);
        $heureFinPrevue = Carbon::parse($shift->heure_fin);
        $heureDebut = Carbon::parse($shift->heure_pointage_debut);
        $heureFin = Carbon::now();

        // Durée réelle en minutes
        $duree = $heureDebut->diffInMinutes($heureFin);

        // Retard (arrivée après l'heure prévue)
        $retard = $heureDebut->gt($heureDebutPrevue)
            ? $heureDebutPrevue->diffInMinutes($heureDebut)
            : 0;

        // Heures supplémentaires (départ après l'heure prévue)
        $heuresSupp = $heureFin->gt($heureFinPrevue)
            ? $heureFinPrevue->diffInMinutes($heureFin)
            : 0;

        // Mise à jour du shift avec types corrects
        $shift->update([
            'heure_pointage_fin' => $heureFin->format('H:i:s'), // format TIME correct
            'duree_minutes' => (int) $duree,
            'retard_minutes' => (int) $retard,
            'heures_supp_minutes' => (int) $heuresSupp,
        ]);

        // Retour JSON
        return response()->json([
            'message' => 'Pointage fin enregistré',
            'heure_pointage_fin' => $shift->heure_pointage_fin,
            'duree_minutes' => (int) $duree,
            'retard_minutes' => (int) $retard,
            'heures_supp_minutes' => (int) $heuresSupp,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Erreur lors du pointage fin',
            'error' => $e->getMessage(),
        ], 500);
    }
}





}
