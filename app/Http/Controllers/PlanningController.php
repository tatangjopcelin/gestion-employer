<?php

namespace App\Http\Controllers;

use App\Models\Planning;
use App\Models\Shift;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PlanningController extends Controller
{
    /**
     * Liste tous les plannings (admin uniquement)
     */
    public function index()
    {
        $plannings = Planning::with(['shifts.user'])->orderBy('semaine_debut', 'desc')->get();
        return response()->json($plannings);
    }

    /**
     * Créer un planning + ses shifts
     */
public function store(Request $request)
{
    $request->validate([
        'semaine_debut' => 'required|date',
        'semaine_fin' => 'required|date|after_or_equal:semaine_debut',
        'titre' => 'nullable|string',
        'shifts' => 'required|array',
        'shifts.*.user_id' => 'required|exists:users,id',
        'shifts.*.jour' => 'required|string',
        'shifts.*.date_jour' => 'required|date',
        'shifts.*.heure_debut' => 'required|date_format:H:i',
        'shifts.*.heure_fin' => 'required|date_format:H:i',
        'shifts.*.poste' => 'nullable|string',
    ]);

    $planning = Planning::create([
        'semaine_debut' => $request->semaine_debut,
        'semaine_fin' => $request->semaine_fin,
        'titre' => $request->titre,
        'cree_par' => $request->user()->id,
    ]);

    foreach ($request->shifts as $shiftData) {
        if (strtotime($shiftData['heure_fin']) <= strtotime($shiftData['heure_debut'])) {
            return response()->json([
                'message' => "L'heure de fin doit être après l'heure de début pour {$shiftData['jour']}."
            ], 422);
        }

        $planning->shifts()->create($shiftData);
    }

    return response()->json([
        'message' => 'Planning créé avec succès',
        'planning' => $planning->load('shifts.user'),
    ], 201);
}


public function rapportMensuel($mois, $annee)
{
    
    $shifts = Shift::with('user')
    ->whereYear('date_jour', $annee)
    ->whereMonth('date_jour', $mois)
    ->take(50) 
    ->get();


    if ($shifts->isEmpty()) {
        return response()->json(['message' => 'Aucun shift pour ce mois.'], 404);
    }

    // Groupé par utilisateur
    $rapport = $shifts->groupBy('user_id')->map(function ($shiftsParUser) {
        $user = $shiftsParUser->first()->user;

        // Sommes en minutes
        $totalDuree = $shiftsParUser->sum('duree_minutes');
        $totalRetard = $shiftsParUser->sum('retard_minutes');
        $totalHeuresSupp = $shiftsParUser->sum('heures_supp_minutes');

        // Conversion en heures et minutes
        $formatHeuresMinutes = fn($minutes) => [
            'heures' => floor($minutes / 60),
            'minutes' => $minutes % 60,
        ];

        return [
            'employe' => $user->only(['id', 'nom', 'prenom', 'poste']),
            'total_duree' => $formatHeuresMinutes($totalDuree),
            'total_retard' => $formatHeuresMinutes($totalRetard),
            'total_heures_supp' => $formatHeuresMinutes($totalHeuresSupp),
            'shifts_count' => $shiftsParUser->count(),
        ];
    });

    return response()->json($rapport);
}


    /**
     * Afficher un planning précis
     */
    public function show($id)
    {
        $planning = Planning::with(['shifts.user'])->findOrFail($id);
        return response()->json($planning);
    }

    /**
     * Modifier un planning + ses shifts
     */
    public function update(Request $request, $id)
    {
        $planning = Planning::findOrFail($id);

        $request->validate([
            'semaine_debut' => 'sometimes|date',
            'semaine_fin' => 'sometimes|date|after_or_equal:semaine_debut',
            'titre' => 'nullable|string',
            'shifts' => 'array',
        ]);

        $planning->update($request->only(['semaine_debut', 'semaine_fin', 'titre']));

        // Si shifts fournis → on met à jour
        if ($request->has('shifts')) {
            // On supprime les anciens shifts
            $planning->shifts()->delete();

            // On recrée les nouveaux
            foreach ($request->shifts as $shiftData) {
                $planning->shifts()->create($shiftData);
            }
        }

        return response()->json([
            'message' => 'Planning mis à jour avec succès',
            'planning' => $planning->load('shifts.user'),
        ]);
    }

    /**
     * Supprimer un planning (et ses shifts liés)
     */
    public function destroy($id)
    {
        $planning = Planning::findOrFail($id);
        $planning->delete();

        return response()->json(['message' => 'Planning supprimé avec succès']);
    }

    /**
     * Renvoyer les plannings de l'utilisateur connecté (pour employés)
     */
    public function mesPlannings(Request $request)
    {
        $user = $request->user();

        $shifts = Shift::with('planning')
            ->where('user_id', $user->id)
            ->orderBy('jour', 'asc')
            ->get();

        if ($shifts->isEmpty()) {
            return response()->json(['message' => 'Aucun planning pour vous.']);
        }

        return response()->json([
            'user' => $user->only(['id', 'nom', 'prenom', 'poste']),
            'shifts' => $shifts,
        ]);
    }
}
