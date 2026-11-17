<?php

namespace App\Models;

use Database\Factories\ScheduleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['doctor_id', 'hari', 'jam_mulai', 'jam_selesai', 'kuota'])]
class Schedule extends Model
{
    /** @use HasFactory<ScheduleFactory> */
    use HasFactory;

    /**
     * Doctor assigned to this schedule.
     *
     * @return BelongsTo<Doctor, $this>
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Bookings created under this schedule.
     *
     * @return HasMany<Booking, $this>
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Check if this schedule applies to a given day name (e.g. 'Senin', 'Monday').
     */
    public function appliesToDay(string $dayName): bool
    {
        $hari = strtolower(trim($this->hari));
        $dayName = strtolower(trim($dayName));

        $map = [
            'monday' => 'senin',
            'tuesday' => 'selasa',
            'wednesday' => 'rabu',
            'thursday' => 'kamis',
            'friday' => 'jumat',
            'saturday' => 'sabtu',
            'sunday' => 'minggu',
            'senin' => 'senin',
            'selasa' => 'selasa',
            'rabu' => 'rabu',
            'kamis' => 'kamis',
            'jumat' => 'jumat',
            'sabtu' => 'sabtu',
            'minggu' => 'minggu',
        ];

        $targetDayId = $map[$dayName] ?? $dayName;

        if ($hari === $targetDayId || ($map[$hari] ?? $hari) === $targetDayId) {
            return true;
        }

        if (str_contains($hari, 'senin - jumat') || str_contains($hari, 'senin-jumat')) {
            return in_array($targetDayId, ['senin', 'selasa', 'rabu', 'kamis', 'jumat'], true);
        }

        if (str_contains($hari, 'senin - sabtu') || str_contains($hari, 'senin-sabtu')) {
            return in_array($targetDayId, ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'], true);
        }

        if (str_contains($hari, 'setiap hari') || str_contains($hari, 'all days')) {
            return true;
        }

        return false;
    }

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kuota' => 'integer',
        ];
    }
}
