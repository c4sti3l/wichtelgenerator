<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        DB::table('users')->insert([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('Start1234'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        $user = User::find(1);
        Bouncer::allow($user)->everything();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        //
    }
};
