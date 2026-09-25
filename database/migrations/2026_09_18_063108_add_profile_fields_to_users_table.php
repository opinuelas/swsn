<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('cell_number')->after('email');
            $table->string('gender')->after('cell_number');
            $table->string('country')->nullable()->after('gender');
            $table->string('state')->nullable()->after('country');
            $table->string('city')->nullable()->after('state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['cell_number', 'gender', 'country', 'state', 'city']);
        });
    }
};
