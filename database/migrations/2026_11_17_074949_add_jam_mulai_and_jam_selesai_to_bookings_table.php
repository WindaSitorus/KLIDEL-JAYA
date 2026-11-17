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
        Schema::table('bookings', function (Blueprint $table) {
            $table->time('jam_mulai')->nullable()->after('booking_time');
            $table->time('jam_selesai')->nullable()->after('jam_mulai');
            $table->index(['doctor_id', 'booking_date', 'status'], 'idx_bookings_doctor_date_status');
        });

        // Backfill existing bookings so conflict detection and calendar work seamlessly
        $existing = DB::table('bookings')->get();
        foreach ($existing as $b) {
            $start = '09:00:00';
            $end = '10:00:00';

            if (! empty($b->booking_time)) {
                $clean = trim($b->booking_time);
                if (preg_match('/(\d{1,2}:\d{2})\s*(AM|PM)?\s*[-–]\s*(\d{1,2}:\d{2})\s*(AM|PM)?/i', $clean, $m)) {
                    $t1 = $m[1].' '.($m[2] ?? '');
                    $t2 = $m[3].' '.($m[4] ?? $m[2] ?? '');
                    $ts1 = strtotime(trim($t1));
                    $ts2 = strtotime(trim($t2));
                    if ($ts1 && $ts2) {
                        $start = date('H:i:s', $ts1);
                        $end = date('H:i:s', $ts2);
                    }
                } else {
                    $ts = strtotime($clean);
                    if ($ts !== false) {
                        $start = date('H:i:s', $ts);
                        $end = date('H:i:s', strtotime('+1 hour', $ts));
                    }
                }
            }

            DB::table('bookings')->where('id', $b->id)->update([
                'jam_mulai' => $start,
                'jam_selesai' => $end,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('idx_bookings_doctor_date_status');
            $table->dropColumn(['jam_mulai', 'jam_selesai']);
        });
    }
};
