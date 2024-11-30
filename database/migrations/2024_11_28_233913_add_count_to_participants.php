<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('participants', function (Blueprint $table) {
            $table->integer('count')->after('chosen_by')->default(0);
            $table->string('phone')->after('chosen_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn('count');
            $table->dropColumn('phone');
        });
    }
};
