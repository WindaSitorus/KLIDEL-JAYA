<?php

namespace App\Models;

use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'booking_code',
    'patient_id',
    'doctor_id',
    'schedule_id',
    'booking_date',
    'booking_time',
    'booking_source',
    'symptoms',
    'duration',
    'severity',
    'notes',
    'status',
    'tariff_plan',
    'total_amount',
    'payment_status',
])]
class Booking extends Model
{
    /** @use HasFactory<BookingFactory> */
    use HasFactory;

    /**
     * Patient who booked this appointment.
     *
     * @return BelongsTo<Patient, $this>
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Doctor handling this consultation booking.
     *
     * @return BelongsTo<Doctor, $this>
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Schedule slot chosen for this booking.
     *
     * @return BelongsTo<Schedule, $this>
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * Queue ticket generated for this booking.
     *
     * @return HasOne<Queue, $this>
     */
    public function queue(): HasOne
    {
        return $this->hasOne(Queue::class);
    }

    /**
     * Clinical SOAP examination record for this consultation.
     *
     * @return HasOne<MedicalRecord, $this>
     */
    public function medicalRecord(): HasOne
    {
        return $this->hasOne(MedicalRecord::class);
    }

    /**
     * Payment record for this booking.
     *
     * @return HasOne<Payment, $this>
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * All payment transactions for this booking.
     *
     * @return HasMany<Payment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Build the patient-facing bill breakdown from the doctor's examination result.
     *
     * Medicines and prices come from the SOAP plan saved by the doctor, so the
     * patient sees exactly what was prescribed instead of placeholder data.
     *
     * @return array{
     *     booking_id: int,
     *     booking_code: string,
     *     doctor_name: string,
     *     specialty: string,
     *     tariff_plan: string,
     *     consultation_fee: float,
     *     medicine_cost: float,
     *     total_amount: float,
     *     prescription_status: ?string,
     *     medications: list<array{name: string, kode_obat: ?string, satuan: string, quantity: int, harga: float, subtotal: float, dose: string, frequency: string, notes: string}>
     * }
     */
    public function billDetails(): array
    {
        $plan = $this->medicalRecord?->plan ?? [];

        $medications = collect($plan['medications'] ?? [])
            ->filter(fn ($medication): bool => is_array($medication) && ! empty($medication['name']))
            ->map(function (array $medication): array {
                $quantity = max(1, (int) ($medication['quantity'] ?? 1));
                $price = (float) ($medication['harga'] ?? 0);

                return [
                    'name' => (string) $medication['name'],
                    'kode_obat' => $medication['kode_obat'] ?? null,
                    'satuan' => (string) ($medication['satuan'] ?? 'Item'),
                    'quantity' => $quantity,
                    'harga' => $price,
                    'subtotal' => (float) ($medication['subtotal'] ?? $quantity * $price),
                    'dose' => (string) ($medication['dose'] ?? ''),
                    'frequency' => (string) ($medication['frequency'] ?? ''),
                    'notes' => (string) ($medication['notes'] ?? ''),
                ];
            })
            ->values()
            ->all();

        $medicineCost = (float) ($plan['medicine_cost'] ?? array_sum(array_column($medications, 'subtotal')));
        $totalAmount = (float) ($this->total_amount ?? 0);
        $consultationFee = (float) ($plan['consultation_fee']
            ?? $this->doctor?->price_num
            ?? max(0, $totalAmount - $medicineCost));

        if ($totalAmount <= 0) {
            $totalAmount = $consultationFee + $medicineCost;
        }

        $tariffPlan = $this->medicalRecord
            ? (empty($medications) ? 'consultation_only' : 'with_medicine')
            : (string) ($this->tariff_plan ?: 'consultation_only');

        return [
            'booking_id' => $this->id,
            'booking_code' => (string) $this->booking_code,
            'doctor_name' => (string) ($this->doctor?->nama_dokter ?? 'Dokter Pemeriksa'),
            'specialty' => (string) ($this->doctor?->spesialisasi ?? ''),
            'tariff_plan' => $tariffPlan,
            'consultation_fee' => $consultationFee,
            'medicine_cost' => $medicineCost,
            'total_amount' => $totalAmount,
            'prescription_status' => $plan['prescription_status'] ?? null,
            'medications' => $medications,
        ];
    }

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'total_amount' => 'decimal:2',
        ];
    }
}
