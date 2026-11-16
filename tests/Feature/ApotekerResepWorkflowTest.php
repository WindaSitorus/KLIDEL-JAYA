<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Obat;
use App\Models\Queue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApotekerResepWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test doctor can search medicines from the obats database.
     */
    public function test_doctor_can_search_obat_from_database(): void
    {
        $doctor = User::where('role', 'doctor')->first();

        $response = $this->actingAs($doctor)->getJson(route('doctor.obat.search', ['q' => 'Paracetamol']));

        $response->assertOk();
        $response->assertJsonFragment([
            'kode_obat' => 'PCT-500',
            'nama_obat' => 'Paracetamol 500 mg',
        ]);
    }

    /**
     * Test doctor prescribes medicine with frequency and it enters Apoteker as 'perlu_diproses'.
     */
    public function test_doctor_prescribes_medicine_and_enters_apoteker_as_perlu_diproses(): void
    {
        $doctor = User::where('role', 'doctor')->first();
        $apoteker = User::where('email', 'apoteker@aihealth.com')->first();
        $queue = Queue::whereHas('booking', fn ($q) => $q->where('doctor_id', $doctor->doctor->id))->first();
        $queueKey = 'Q-'.str_pad((string) $queue->id, 3, '0', STR_PAD_LEFT);

        $obat = Obat::where('kode_obat', 'PCT-500')->first();

        $medications = [
            [
                'obat_id' => $obat->id,
                'kode_obat' => $obat->kode_obat,
                'name' => $obat->nama_obat,
                'quantity' => 10,
                'satuan' => 'Strip',
                'dose' => '1 tablet',
                'frequency' => '3x1 sehari sesudah makan',
                'notes' => 'Bila demam / nyeri',
            ],
        ];

        // 1. Doctor saves SOAP with structured medications
        $saveResponse = $this->actingAs($doctor)->post(route('doctor.saveSoap', $queueKey), [
            'subjective' => 'Pasien demam dan sakit kepala sejak 2 hari.',
            'bp' => '120/80',
            'hr' => '80',
            'rr' => '18',
            'temp' => '38.2',
            'spo2' => '99',
            'physical_exam' => 'Faring hiperemis ringan.',
            'primary_diagnosis' => 'R50.9 - Demam Akut',
            'secondary_diagnosis' => '',
            'clinical_notes' => 'Minum air putih banyak dan istirahat.',
            'medications_json' => json_encode($medications),
            'therapy_plan' => 'Paracetamol 500 mg — 10 Strip · (3x1 sehari sesudah makan, 1 tablet)',
            'return_date' => '3 hari lagi',
        ]);

        $saveResponse->assertRedirect();
        $saveResponse->assertSessionHas('success');

        // Verify medical record plan status
        $booking = Booking::with('medicalRecord')->find($queue->booking_id);
        $this->assertNotNull($booking->medicalRecord);
        $this->assertSame('perlu_diproses', $booking->medicalRecord->plan['prescription_status']);
        $this->assertCount(1, $booking->medicalRecord->plan['medications']);
        $this->assertSame('3x1 sehari sesudah makan', $booking->medicalRecord->plan['medications'][0]['frequency']);

        // 2. Apoteker dashboard displays the prescription in 'Perlu Diproses'
        $apotekerResponse = $this->actingAs($apoteker)->get(route('apoteker.dashboard'));
        $apotekerResponse->assertOk();
        $apotekerResponse->assertSee('PERLU DIPROSES');
        $apotekerResponse->assertSee('Paracetamol 500 mg');
        $apotekerResponse->assertSee('3x1 sehari sesudah makan');
    }

    /**
     * Test apoteker can ACC prescription and status moves to 'sedang_diracik'.
     */
    public function test_apoteker_can_acc_resep_and_status_becomes_sedang_diracik(): void
    {
        $doctor = User::where('role', 'doctor')->first();
        $apoteker = User::where('email', 'apoteker@aihealth.com')->first();
        $queue = Queue::whereHas('booking', fn ($q) => $q->where('doctor_id', $doctor->doctor->id))->first();
        $queueKey = 'Q-'.str_pad((string) $queue->id, 3, '0', STR_PAD_LEFT);

        $obat = Obat::where('kode_obat', 'AMX-500')->first();

        // Doctor prescribes
        $this->actingAs($doctor)->post(route('doctor.saveSoap', $queueKey), [
            'subjective' => 'Infeksi saluran nafas.',
            'primary_diagnosis' => 'J02 - Faringitis Akut',
            'medications_json' => json_encode([
                [
                    'obat_id' => $obat->id,
                    'kode_obat' => $obat->kode_obat,
                    'name' => $obat->nama_obat,
                    'quantity' => 15,
                    'satuan' => 'Kapsul',
                    'dose' => '1 kapsul',
                    'frequency' => '3x1 sehari sesudah makan (habiskan)',
                ],
            ]),
        ]);

        $bookingId = $queue->booking_id;

        // Apoteker ACC resep
        $accResponse = $this->actingAs($apoteker)->postJson(route('apoteker.resep.acc', $bookingId));

        $accResponse->assertOk();
        $accResponse->assertJson([
            'success' => true,
            'status' => 'sedang_diracik',
        ]);

        $booking = Booking::with('medicalRecord')->find($bookingId);
        $this->assertSame('sedang_diracik', $booking->medicalRecord->plan['prescription_status']);
    }

    /**
     * Test apoteker can complete & dispense prescription, and drug stock is decremented.
     */
    public function test_apoteker_can_complete_resep_and_drug_stock_is_decremented(): void
    {
        $doctor = User::where('role', 'doctor')->first();
        $apoteker = User::where('email', 'apoteker@aihealth.com')->first();
        $queue = Queue::whereHas('booking', fn ($q) => $q->where('doctor_id', $doctor->doctor->id))->first();
        $queueKey = 'Q-'.str_pad((string) $queue->id, 3, '0', STR_PAD_LEFT);

        $obat = Obat::where('kode_obat', 'PCT-500')->first();
        $initialStock = $obat->stok;

        // Doctor prescribes 10 items
        $prescribeQty = 10;
        $this->actingAs($doctor)->post(route('doctor.saveSoap', $queueKey), [
            'subjective' => 'Pusing dan demam.',
            'primary_diagnosis' => 'R51 - Cephalgia',
            'medications_json' => json_encode([
                [
                    'obat_id' => $obat->id,
                    'kode_obat' => $obat->kode_obat,
                    'name' => $obat->nama_obat,
                    'quantity' => $prescribeQty,
                    'satuan' => 'Strip',
                    'dose' => '1 tablet',
                    'frequency' => '3x1 sehari sesudah makan',
                ],
            ]),
        ]);

        $bookingId = $queue->booking_id;

        // 1. Apoteker ACC
        $this->actingAs($apoteker)->postJson(route('apoteker.resep.acc', $bookingId));

        // 2. Apoteker Selesai & Serahkan Obat
        $selesaiResponse = $this->actingAs($apoteker)->postJson(route('apoteker.resep.selesai', $bookingId));

        $selesaiResponse->assertOk();
        $selesaiResponse->assertJson([
            'success' => true,
            'status' => 'selesai',
        ]);

        // Verify status in DB
        $booking = Booking::with('medicalRecord')->find($bookingId);
        $this->assertSame('selesai', $booking->medicalRecord->plan['prescription_status']);

        // Verify drug stock in obats table decremented by prescribeQty
        $obat->refresh();
        $this->assertSame($initialStock - $prescribeQty, $obat->stok);
    }
}
