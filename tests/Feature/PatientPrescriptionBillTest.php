<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Obat;
use App\Models\Queue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientPrescriptionBillTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /**
     * Test that medicines and prices prescribed by doctor appear in the patient bill view.
     */
    public function test_medicines_and_prices_prescribed_by_doctor_appear_in_patient_view(): void
    {
        $doctor = Doctor::with('poli')->first();
        $doctorUser = User::where('role', 'doctor')->first();

        // 1. Create a booking for this doctor
        $booking = Booking::create([
            'booking_code' => 'BK-TEST-MED-01',
            'patient_id' => 1,
            'doctor_id' => $doctor->id,
            'booking_date' => now()->toDateString(),
            'booking_time' => '09:00 AM',
            'symptoms' => 'Radang sendi lutut dan nyeri',
            'status' => 'confirmed',
            'tariff_plan' => 'consultation_only',
            'total_amount' => (float) $doctor->price_num,
            'payment_status' => 'unpaid',
        ]);

        $queue = Queue::create([
            'booking_id' => $booking->id,
            'queue_number' => 'A-99',
            'queue_date' => now()->toDateString(),
            'time_slot' => '09:00 AM',
            'status' => 'waiting',
        ]);

        $queueKey = 'Q-'.str_pad((string) $queue->id, 3, '0', STR_PAD_LEFT);

        $obat1 = Obat::where('kode_obat', 'PCT-500')->first();
        $obat2 = Obat::where('kode_obat', 'AMX-500')->first();

        $prescribedMeds = [
            [
                'obat_id' => $obat1->id,
                'kode_obat' => $obat1->kode_obat,
                'name' => 'Paracetamol 500 mg Khusus',
                'quantity' => 2,
                'satuan' => 'Strip',
                'harga' => 15000.00,
                'dose' => '1 tablet',
                'frequency' => '3x1 sehari',
                'notes' => 'Sesudah makan',
            ],
            [
                'obat_id' => $obat2->id,
                'kode_obat' => $obat2->kode_obat,
                'name' => 'Amoxicillin 500 mg Paten',
                'quantity' => 3,
                'satuan' => 'Strip',
                'harga' => 25000.00,
                'dose' => '1 kapsul',
                'frequency' => '3x1 sehari habiskan',
                'notes' => 'Antibiotik',
            ],
        ];

        // 2. Doctor saves SOAP with medicines
        $soapResponse = $this->actingAs($doctorUser)->post(route('doctor.saveSoap', $queueKey), [
            'subjective' => 'Pasien merasakan nyeri lutut akut.',
            'primary_diagnosis' => 'M25.5 - Nyeri Sendi',
            'medications_json' => json_encode($prescribedMeds),
            'therapy_plan' => 'Pemberian obat anti nyeri dan antibiotik profilaksis.',
        ]);

        $soapResponse->assertRedirect();

        // Total expected = consultation fee + (2 * 15000) + (3 * 25000) = fee + 30000 + 75000 = fee + 105000
        $expectedMedicineCost = 105000.00;
        $expectedTotal = (float) $doctor->price_num + $expectedMedicineCost;

        // 3. Check /appointment-status endpoint returns real bill data
        $statusResponse = $this->getJson(route('appointment.status'));
        $statusResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('mode', 'after')
            ->assertJsonPath('total_amount', (int) $expectedTotal)
            ->assertJsonPath('bill.medicine_cost', (int) $expectedMedicineCost)
            ->assertJsonPath('bill.medications.0.name', $obat1->nama_obat)
            ->assertJsonPath('bill.medications.0.harga', 15000)
            ->assertJsonPath('bill.medications.1.name', $obat2->nama_obat)
            ->assertJsonPath('bill.medications.1.harga', 25000);

        // 4. Check home page renders prescribed medicines and prices
        $homeResponse = $this->get(route('home'));
        $homeResponse->assertOk()
            ->assertSee($obat1->nama_obat)
            ->assertSee($obat2->nama_obat)
            ->assertSee(number_format($expectedTotal, 0, ',', '.'))
            ->assertSee(number_format($expectedMedicineCost, 0, ',', '.'));
    }
}
