<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->time('heure_pointage_debut')->nullable()->after('heure_fin');
            $table->time('heure_pointage_fin')->nullable()->after('heure_pointage_debut');
            $table->integer('duree_minutes')->nullable()->after('heure_pointage_fin');
        });
    }

    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn(['heure_pointage_debut', 'heure_pointage_fin', 'duree_minutes']);
        });
    }
};
