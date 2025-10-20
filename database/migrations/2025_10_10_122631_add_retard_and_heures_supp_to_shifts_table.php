<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('shifts', function (Blueprint $table) {
        $table->integer('retard_minutes')->nullable()->after('duree_minutes');
        $table->integer('heures_supp_minutes')->nullable()->after('retard_minutes');
    });
}

public function down(): void
{
    Schema::table('shifts', function (Blueprint $table) {
        $table->dropColumn(['retard_minutes', 'heures_supp_minutes']);
    });
}

};
