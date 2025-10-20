<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->constrained()->onDelete('cascade'); // lien avec shift
            $table->string('titre');
            $table->text('description')->nullable();
            $table->time('heure_debut'); // début tâche
            $table->time('heure_fin');   // fin tâche
            $table->enum('statut', ['en attente','en cours','terminée'])->default('en attente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
