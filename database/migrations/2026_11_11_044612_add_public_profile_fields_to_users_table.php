<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('userid', 15)->nullable()->after('name');
            $table->string('physical_description', 500)->nullable()->after('city');
            $table->string('finding_preference', 500)->nullable()->after('physical_description');
            $table->unsignedInteger('profile_views_count')->default(0)->after('finding_preference');
        });

        DB::table('users')->whereNull('userid')->orderBy('id')->each(function (object $user): void {
            DB::table('users')->where('id', $user->id)->update(['userid' => 'user'.$user->id]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('userid', 15)->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['userid']);
            $table->dropColumn(['userid', 'physical_description', 'finding_preference', 'profile_views_count']);
        });
    }
};
