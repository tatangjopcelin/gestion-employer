<?php
namespace App\Http\Controllers;
use App\Models\Task;
use App\Models\Shift;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'shift_id' => 'required|exists:shifts,id',
            'titre' => 'required|string',
            'description' => 'nullable|string',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'statut' => 'nullable|in:en attente,en cours,terminée',
        ]);

        $task = Task::create($request->all());

        return response()->json([
            'message' => 'Tâche créée',
            'task' => $task
        ], 201);
    }

    public function index(Shift $shift)
    {
        return response()->json($shift->tasks);
    }

    public function update(Request $request, Task $task)
    {
        $task->update($request->all());
        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(['message' => 'Tâche supprimée']);
    }
}
