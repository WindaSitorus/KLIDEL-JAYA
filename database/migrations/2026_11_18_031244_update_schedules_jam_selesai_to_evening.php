<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('schedules')) {
            DB::table('schedules')->update([
                'jam_mulai' => '08:00',
                'jam_selesai' => '21:00',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('schedules')) {
            DB::table('schedules')->update([
                'jam_selesai' => '16:00',
            ]);
        }
    }
};
