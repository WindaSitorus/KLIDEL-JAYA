<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Obat;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Poli;
use App\Models\Queue;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Default Users (Admin, Patient, and ALL Doctors)
        $patientUser = User::updateOrCreate(
            ['email' => 'user@aihealth.com'],
            [
                'name' => 'Rina Anggraini',
                'password' => bcrypt('password'),
                'role' => 'user',
            ]
        );

        $adminUser = User::updateOrCreate(
            ['email' => 'admin@aihealth.com'],
            [
                'name' => 'Amélie Laurent',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        // Doctor 1: Dr. Lina Wijaya (Cardiology)
        $docUserLina = User::updateOrCreate(
            ['email' => 'lina@aihealth.com'],
            [
                'name' => 'Dr. Lina Wijaya',
                'password' => bcrypt('password'),
                'role' => 'doctor',
            ]
        );

        // Doctor 2: Dr. Anaya Kapoor (Neurology) - supports both anaya@ and dokter@
        $docUserAnaya = User::updateOrCreate(
            ['email' => 'anaya@aihealth.com'],
            [
                'name' => 'Dr. Anaya Kapoor, Sp.S',
                'password' => bcrypt('password'),
                'role' => 'doctor',
            ]
        );

        User::updateOrCreate(
            ['email' => 'dokter@aihealth.com'],
            [
                'name' => 'Dr. Anaya Kapoor, Sp.S',
                'password' => bcrypt('password'),
                'role' => 'doctor',
            ]
        );

        // Doctor 3: Dr. Saran Mitchell (Orthopedics)
        $docUserSaran = User::updateOrCreate(
            ['email' => 'saran@aihealth.com'],
            [
                'name' => 'Dr. Saran Mitchell',
                'password' => bcrypt('password'),
                'role' => 'doctor',
            ]
        );

        // Apoteker: Amélie Laurent
        $apotekerUser = User::updateOrCreate(
            ['email' => 'apoteker@aihealth.com'],
            [
                'name' => 'Amélie Laurent',
                'password' => bcrypt('password'),
                'role' => 'apoteker',
            ]
        );

        // 2. Seed Polis (Specialties)
        $poliCardio = Poli::updateOrCreate(
            ['slug' => 'Cardiologist'],
            [
                'nama_poli' => 'Kardiologi',
                'name_en' => 'Heart',
                'deskripsi' => 'Layanan poliklinik spesialis jantung dan pembuluh darah modern.',
            ]
        );

        $poliNeuro = Poli::updateOrCreate(
            ['slug' => 'Neurologist'],
            [
                'nama_poli' => 'Neurologi',
                'name_en' => 'Brain expert',
                'deskripsi' => 'Layanan poliklinik spesialis saraf, otak, dan sistem neurologis.',
            ]
        );

        $poliOrtho = Poli::updateOrCreate(
            ['slug' => 'Orthopedist'],
            [
                'nama_poli' => 'Ortopedi',
                'name_en' => 'Bone',
                'deskripsi' => 'Layanan poliklinik spesialis bedah tulang, sendi, dan muskuloskeletal.',
            ]
        );

        // 3. Seed Doctors & Link to User Accounts
        $docLina = Doctor::updateOrCreate(
            ['nama_dokter' => 'Dr. Lina Wijaya'],
            [
                'user_id' => $docUserLina->id,
                'poli_id' => $poliCardio->id,
                'spesialisasi' => 'Cardiologist',
                'rating' => 4.9,
                'price' => 'Rp 150.000',
                'price_num' => 150000.00,
                'initials' => 'LW',
                'image' => 'images/doctors/doctor-1.png',
                'availability' => '9AM - 8PM',
                'experience' => '10 years',
                'patients' => '3.8k Up',
            ]
        );

        $docAnaya = Doctor::updateOrCreate(
            ['nama_dokter' => 'Dr. Anaya Kapoor'],
            [
                'user_id' => $docUserAnaya->id,
                'poli_id' => $poliNeuro->id,
                'spesialisasi' => 'Neurologist',
                'rating' => 4.8,
                'price' => 'Rp 175.000',
                'price_num' => 175000.00,
                'initials' => 'AK',
                'image' => 'images/doctors/doctor-2.png',
                'availability' => '8AM - 6PM',
                'experience' => '12 years',
                'patients' => '4.6k Up',
            ]
        );

        $docSaran = Doctor::updateOrCreate(
            ['nama_dokter' => 'Dr. Saran Mitchell'],
            [
                'user_id' => $docUserSaran->id,
                'poli_id' => $poliOrtho->id,
                'spesialisasi' => 'Orthopedist',
                'rating' => 4.7,
                'price' => 'Rp 180.000',
                'price_num' => 180000.00,
                'initials' => 'SM',
                'image' => 'images/doctors/doctor-3.png',
                'availability' => '10AM - 7PM',
                'experience' => '15 years',
                'patients' => '5.2k Up',
            ]
        );

        // 4. Seed Schedules
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        foreach ([$docLina, $docAnaya, $docSaran] as $doc) {
            foreach ($days as $day) {
                Schedule::updateOrCreate(
                    [
                        'doctor_id' => $doc->id,
                        'hari' => $day,
                    ],
                    [
                        'jam_mulai' => '08:00',
                        'jam_selesai' => '16:00',
                        'kuota' => 20,
                    ]
                );
            }
        }

        // 5. Seed Patients
        $patientRina = Patient::updateOrCreate(
            ['mrn' => '#GM-0214'],
            [
                'user_id' => $patientUser->id,
                'nik' => '3171045206920003',
                'full_name' => 'Rina Anggraini',
                'dob' => '1992-06-12',
                'gender' => 'Perempuan',
                'blood_type' => 'B+',
                'phone' => '+62 812-5550-1920',
                'address' => 'Jl. Senopati No. 42, Kebayoran Baru, Jakarta Selatan, 12190',
                'allergies' => ['Penisilin', 'Asam Mefenamat'],
                'past_illnesses' => ['Migraine with aura (2024)', 'Gastritis fungsional (2025)'],
                'regular_meds' => ['Vitamin B Kompleks 1x1'],
            ]
        );

        $patientBudi = Patient::updateOrCreate(
            ['mrn' => '#GM-0188'],
            [
                'nik' => '3172081504880001',
                'full_name' => 'Budi Santoso',
                'dob' => '1988-04-15',
                'gender' => 'Laki-laki',
                'blood_type' => 'O+',
                'phone' => '+62 813-8821-4091',
                'address' => 'Jl. Tebet Barat Dalam No. 12, Jakarta Selatan',
                'allergies' => ['Sulfa'],
                'past_illnesses' => ['Hipertensi primer'],
                'regular_meds' => ['Amlodipine 5mg 1x1'],
            ]
        );

        $patientSiti = Patient::updateOrCreate(
            ['mrn' => '#GM-0205'],
            [
                'nik' => '3173095509950004',
                'full_name' => 'Siti Rahmawati',
                'dob' => '1995-09-25',
                'gender' => 'Perempuan',
                'blood_type' => 'A+',
                'phone' => '+62 856-7712-3490',
                'address' => 'Jl. Panglima Polim No. 8, Kebayoran Baru, Jakarta Selatan',
                'allergies' => ['Tidak ada alergi obat'],
                'past_illnesses' => ['Anemia defisiensi besi'],
                'regular_meds' => ['Tablet Tambah Darah 1x1'],
            ]
        );

        $patientDewi = Patient::updateOrCreate(
            ['mrn' => '#GM-0305'],
            [
                'nik' => '3173094812990002',
                'full_name' => 'Dewi Safitri',
                'dob' => '1999-12-08',
                'gender' => 'Perempuan',
                'blood_type' => 'A+',
                'phone' => '+62 856-9122-3344',
                'address' => 'Jl. Cikini Raya No. 12, Menteng, Jakarta Pusat',
                'allergies' => ['Sulfa'],
                'past_illnesses' => ['Tension Headache (2025)'],
                'regular_meds' => [],
            ]
        );

        $patientHendro = Patient::updateOrCreate(
            ['mrn' => '#GM-0102'],
            [
                'nik' => '3174061208650004',
                'full_name' => 'Hendro Prasetyo',
                'dob' => '1965-08-12',
                'gender' => 'Laki-laki',
                'blood_type' => 'AB+',
                'phone' => '+62 811-2233-4455',
                'address' => 'Jl. Bangka IX No. 5, Mampang Prapatan, Jakarta Selatan',
                'allergies' => ['Tidak ada'],
                'past_illnesses' => ['Diabetes Mellitus Tipe 2'],
                'regular_meds' => ['Metformin 500mg 2x1'],
            ]
        );

        // 6. Seed Bookings & Queues (Masing-masing dokter memiliki tepat 1 riwayat selesai)

        // --- DOKTER 1: Dr. Lina Wijaya (Cardiology) ---
        // 1 Riwayat Selesai (Completed & Paid)
        $bookingLinaCompleted = Booking::updateOrCreate(
            ['booking_code' => '#BILL00127'],
            [
                'patient_id' => $patientSiti->id,
                'doctor_id' => $docLina->id,
                'booking_date' => now()->subDays(1)->toDateString(),
                'booking_time' => '09:30 AM',
                'booking_source' => 'online',
                'symptoms' => 'Dada berdebar-debar (palpitasi) dan cepat lelah saat beraktivitas fisik.',
                'duration' => '3 Hari',
                'severity' => 'Sedang',
                'notes' => 'Pasien memerlukan evaluasi ritme jantung dan EKG.',
                'status' => 'completed',
                'tariff_plan' => 'with_medicine',
                'total_amount' => 320000.00,
                'payment_status' => 'paid',
            ]
        );

        Queue::updateOrCreate(
            ['booking_id' => $bookingLinaCompleted->id],
            [
                'queue_number' => 'B-01',
                'queue_date' => now()->toDateString(),
                'time_slot' => '09:30 AM',
                'status' => 'completed',
            ]
        );

        MedicalRecord::updateOrCreate(
            ['booking_id' => $bookingLinaCompleted->id],
            [
                'subjective' => 'Pasien mengeluh jantung berdebar kencang saat lelah atau stres, tidak disertai sesak napas berat ataupun nyeri dada menjalar.',
                'objective' => [
                    'bp' => '125/82',
                    'hr' => '88',
                    'rr' => '18',
                    'temp' => '36.6',
                    'spo2' => '99',
                    'weight' => '52',
                    'height' => '158',
                    'physical_exam' => 'Cor: BJ I-II reguler, murmur (-), gallop (-). EKG 12 lead: Sinus takikardia tanpa kelainan segmen ST.',
                ],
                'assessmen' => [
                    'primary_diagnosis' => 'I49.9 - Cardiac arrhythmia, unspecified (Sinus palpitation ec fatigue)',
                    'secondary_diagnosis' => 'D50.9 - Iron deficiency anemia, unspecified',
                    'clinical_notes' => 'Aritmia fungsional akibat stres dan kelelahan kerja.',
                ],
                'plan' => [
                    'medications' => [
                        ['name' => 'Bisoprolol 2.5mg', 'dose' => '1 tablet', 'frequency' => '1x1 pagi', 'notes' => 'Sesudah makan'],
                        ['name' => 'Suplemen Kalium & Magnesium', 'dose' => '1 tablet', 'frequency' => '1x1 malam', 'notes' => 'Rutin'],
                    ],
                    'clinical_action' => 'Edukasi kurangi konsumsi kopi/kafein tinggi dan istirahat teratur.',
                    'return_date' => '24 Nov 2026',
                ],
            ]
        );

        // 1 Antrean Menunggu untuk Dr. Lina
        $bookingLinaWaiting = Booking::updateOrCreate(
            ['booking_code' => 'BK-LINA-WAIT-01'],
            [
                'patient_id' => $patientDewi->id,
                'doctor_id' => $docLina->id,
                'booking_date' => now()->toDateString(),
                'booking_time' => '10:30 AM',
                'booking_source' => 'online',
                'symptoms' => 'Pemeriksaan rutin tekanan darah dan evaluasi denyut nadi istirahat.',
                'duration' => '1 Minggu',
                'severity' => 'Ringan',
                'status' => 'confirmed',
                'tariff_plan' => 'consultation_only',
                'total_amount' => 150000.00,
                'payment_status' => 'unpaid',
            ]
        );

        Queue::updateOrCreate(
            ['booking_id' => $bookingLinaWaiting->id],
            [
                'queue_number' => 'B-02',
                'queue_date' => now()->toDateString(),
                'time_slot' => '10:30 AM',
                'status' => 'waiting',
            ]
        );

        // --- DOKTER 2: Dr. Anaya Kapoor (Neurology) ---
        // 1 Riwayat Selesai (Completed & Paid)
        $bookingNeuroCompleted = Booking::updateOrCreate(
            ['booking_code' => '#BILL00125'],
            [
                'patient_id' => $patientBudi->id,
                'doctor_id' => $docAnaya->id,
                'booking_date' => now()->subDays(1)->toDateString(),
                'booking_time' => '10:00 AM',
                'booking_source' => 'online',
                'symptoms' => 'Kesemutan menahun di ujung jari tangan kanan dan leher kaku.',
                'duration' => '> 1 Bulan',
                'severity' => 'Sedang',
                'notes' => 'Gejala memburuk saat menyetir motor.',
                'status' => 'completed',
                'tariff_plan' => 'consultation_only',
                'total_amount' => 175000.00,
                'payment_status' => 'paid',
            ]
        );

        Queue::updateOrCreate(
            ['booking_id' => $bookingNeuroCompleted->id],
            [
                'queue_number' => 'A-01',
                'queue_date' => now()->toDateString(),
                'time_slot' => '10:00 AM',
                'status' => 'completed',
            ]
        );

        MedicalRecord::updateOrCreate(
            ['booking_id' => $bookingNeuroCompleted->id],
            [
                'subjective' => 'Pasien mengeluh kesemutan dan kebas di ibu jari, jari telunjuk, dan jari tengah kanan sejak 3 minggu lalu.',
                'objective' => [
                    'bp' => '128/82',
                    'hr' => '76',
                    'rr' => '16',
                    'temp' => '36.5',
                    'spo2' => '98',
                    'weight' => '72',
                    'height' => '170',
                    'physical_exam' => 'Tes Phalen (+), Tes Tinel (+) di pergelangan tangan kanan. Motorik thenar 5/5, atrofi (-).',
                ],
                'assessmen' => [
                    'primary_diagnosis' => 'G56.0 - Carpal tunnel syndrome, mild dextra',
                    'secondary_diagnosis' => 'M54.2 - Cervicalgia',
                    'clinical_notes' => 'Kompresi nervus medianus derajat ringan akibat beban kerja tangan berulang.',
                ],
                'plan' => [
                    'medications' => [
                        ['name' => 'Mecobalamin 500mcg', 'dose' => '1 kapsul', 'frequency' => '3x1 sesudah makan', 'notes' => 'Untuk 14 hari'],
                        ['name' => 'Natrium Diklofenak 50mg', 'dose' => '1 tablet', 'frequency' => '2x1 prn', 'notes' => 'Bila nyeri'],
                    ],
                    'clinical_action' => 'Pemasangan wrist splint saat malam hari dan edukasi peregangan sendi pergelangan.',
                    'return_date' => '25 Nov 2026',
                ],
            ]
        );

        // 1 Antrean Menunggu untuk Dr. Anaya
        $bookingNeuroWaiting = Booking::updateOrCreate(
            ['booking_code' => 'BK-ANAYA-WAIT-01'],
            [
                'patient_id' => $patientHendro->id,
                'doctor_id' => $docAnaya->id,
                'booking_date' => now()->toDateString(),
                'booking_time' => '11:00 AM',
                'booking_source' => 'online',
                'symptoms' => 'Pusing berputar mendadak terutama saat bangun dari posisi tidur.',
                'duration' => '< 24 Jam',
                'severity' => 'Sedang',
                'status' => 'confirmed',
                'tariff_plan' => 'consultation_only',
                'total_amount' => 175000.00,
                'payment_status' => 'unpaid',
            ]
        );

        Queue::updateOrCreate(
            ['booking_id' => $bookingNeuroWaiting->id],
            [
                'queue_number' => 'A-02',
                'queue_date' => now()->toDateString(),
                'time_slot' => '11:00 AM',
                'status' => 'waiting',
            ]
        );

        // --- DOKTER 3: Dr. Saran Mitchell (Orthopedics) ---
        // 1 Riwayat Selesai (Completed & Paid)
        $bookingOrthoCompleted = Booking::updateOrCreate(
            ['booking_code' => '#BILL00124'],
            [
                'patient_id' => $patientRina->id,
                'doctor_id' => $docSaran->id,
                'booking_date' => now()->toDateString(),
                'booking_time' => '14:55 WIB',
                'booking_source' => 'online',
                'symptoms' => 'Nyeri persendian lutut kanan setelah aktivitas lari.',
                'duration' => '1 - 3 Hari',
                'severity' => 'Sedang',
                'notes' => 'Pasien memerlukan evaluasi mobilitas sendi.',
                'status' => 'completed',
                'tariff_plan' => 'with_medicine',
                'total_amount' => 435000.00,
                'payment_status' => 'paid',
            ]
        );

        Queue::updateOrCreate(
            ['booking_id' => $bookingOrthoCompleted->id],
            [
                'queue_number' => 'C-01',
                'queue_date' => now()->toDateString(),
                'time_slot' => '02:30 PM',
                'status' => 'completed',
            ]
        );

        MedicalRecord::updateOrCreate(
            ['booking_id' => $bookingOrthoCompleted->id],
            [
                'subjective' => 'Pasien mengeluh nyeri persendian lutut dextra setelah berolahraga lari. Nyeri terasa menusuk saat ditekuk atau menaiki anak tangga.',
                'objective' => [
                    'bp' => '120/80',
                    'hr' => '82',
                    'rr' => '18',
                    'temp' => '36.8',
                    'spo2' => '99',
                    'weight' => '56',
                    'height' => '162',
                    'physical_exam' => 'Genu dextra: ROM fleksi 120° dengan nyeri tekan pada tendon patella distal. Lachman test (-), anterior drawer (-), McMurray (-).',
                ],
                'assessmen' => [
                    'primary_diagnosis' => 'M25.561 - Pain in right knee (Patellar tendinitis mild)',
                    'secondary_diagnosis' => 'M76.51 - Patellar tendinitis, right knee',
                    'clinical_notes' => 'Strain ligamen patella akibat beban berlebih (overuse injury). Tidak ditemukan tanda ruptur meniskus.',
                ],
                'plan' => [
                    'medications' => [
                        ['name' => 'Natrium Diklofenak 50mg', 'dose' => '1 tablet', 'frequency' => '2x1 sesudah makan', 'notes' => 'Selama 5 hari'],
                        ['name' => 'Glucosamine Sulfate 500mg', 'dose' => '1 kapsul', 'frequency' => '2x1', 'notes' => 'Untuk 30 hari'],
                        ['name' => 'Krim Gel Analgesik', 'dose' => 'Oles tipis', 'frequency' => '3x1 pada sendi lutut', 'notes' => 'Luar'],
                    ],
                    'clinical_action' => 'Protokol RICE (Rest, Ice 15 menit 2x sehari, Compression, Elevation). Hindari olahraga impak tinggi.',
                    'return_date' => '28 Nov 2026',
                ],
            ]
        );

        // 1 Antrean Menunggu untuk Dr. Saran
        $bookingOrthoWaiting = Booking::updateOrCreate(
            ['booking_code' => 'BK-SARAN-WAIT-01'],
            [
                'patient_id' => $patientBudi->id,
                'doctor_id' => $docSaran->id,
                'booking_date' => now()->toDateString(),
                'booking_time' => '03:30 PM',
                'booking_source' => 'online',
                'symptoms' => 'Nyeri pinggang bawah (low back pain) setelah mengangkat beban berat.',
                'duration' => '2 Hari',
                'severity' => 'Sedang',
                'status' => 'confirmed',
                'tariff_plan' => 'consultation_only',
                'total_amount' => 180000.00,
                'payment_status' => 'unpaid',
            ]
        );

        Queue::updateOrCreate(
            ['booking_id' => $bookingOrthoWaiting->id],
            [
                'queue_number' => 'C-02',
                'queue_date' => now()->toDateString(),
                'time_slot' => '03:30 PM',
                'status' => 'waiting',
            ]
        );

        // 8. Seed Payments & Transactions History (Replicating Image 4)
        $paymentData = [
            [
                'booking_id' => $bookingOrthoCompleted->id,
                'patient_id' => $patientRina->id,
                'doctor_id' => $docSaran->id,
                'invoice_number' => '#BILL00124',
                'patient_name' => 'Rina Anggraini',
                'doctor_name' => 'Dr. Saran Mitchell',
                'service_type' => 'Orthopedics',
                'payment_method' => 'cash',
                'tariff_plan' => 'with_medicine',
                'amount' => 435000.00,
                'net_amount' => 435000.00,
                'status' => 'confirmed',
                'paid_at' => now()->subMinutes(25),
            ],
            [
                'booking_id' => null,
                'patient_id' => null,
                'doctor_id' => null,
                'invoice_number' => '47229027',
                'patient_name' => 'Isa Isgenderov',
                'doctor_name' => 'Ahmadov S.',
                'service_type' => 'Dermatology',
                'payment_method' => 'credit_card',
                'tariff_plan' => 'consultation_only',
                'amount' => 180000.00,
                'net_amount' => 180000.00,
                'status' => 'changed',
                'paid_at' => now()->subHours(2),
            ],
            [
                'booking_id' => null,
                'patient_id' => null,
                'doctor_id' => $docSaran->id,
                'invoice_number' => '15287353',
                'patient_name' => 'Murad Mamedli',
                'doctor_name' => 'Asgarov D.',
                'service_type' => 'Optalmology',
                'payment_method' => 'bank_transfer',
                'tariff_plan' => 'with_medicine',
                'amount' => 435000.00,
                'net_amount' => 435000.00,
                'status' => 'confirmed',
                'paid_at' => now()->subHours(3),
            ],
            [
                'booking_id' => null,
                'patient_id' => null,
                'doctor_id' => $docAnaya->id,
                'invoice_number' => '89789765',
                'patient_name' => 'Diana Huseynova',
                'doctor_name' => 'Suleymanova A.',
                'service_type' => 'Radiology',
                'payment_method' => 'qris',
                'tariff_plan' => 'with_medicine',
                'amount' => 350000.00,
                'net_amount' => 350000.00,
                'status' => 'confirmed',
                'paid_at' => now()->subHours(4),
            ],
            [
                'booking_id' => null,
                'patient_id' => null,
                'doctor_id' => $docLina->id,
                'invoice_number' => '19876212',
                'patient_name' => 'Alekber Rzayev',
                'doctor_name' => 'Rzayeva S.',
                'service_type' => 'Gastroenterology',
                'payment_method' => 'cash',
                'tariff_plan' => 'consultation_only',
                'amount' => 120000.00,
                'net_amount' => 120000.00,
                'status' => 'canceled',
                'paid_at' => now()->subHours(5),
            ],
            [
                'booking_id' => $bookingNeuroCompleted->id,
                'patient_id' => $patientBudi->id,
                'doctor_id' => $docAnaya->id,
                'invoice_number' => '#BILL00125',
                'patient_name' => 'Budi Santoso',
                'doctor_name' => 'Dr. Anaya Kapoor',
                'service_type' => 'Neurology',
                'payment_method' => 'qris',
                'tariff_plan' => 'consultation_only',
                'amount' => 175000.00,
                'net_amount' => 175000.00,
                'status' => 'confirmed',
                'paid_at' => now()->subDays(1),
            ],
            [
                'booking_id' => $bookingLinaCompleted->id,
                'patient_id' => $patientSiti->id,
                'doctor_id' => $docLina->id,
                'invoice_number' => '#BILL00127',
                'patient_name' => 'Siti Rahmawati',
                'doctor_name' => 'Dr. Lina Wijaya',
                'service_type' => 'Cardiology',
                'payment_method' => 'credit_card',
                'tariff_plan' => 'with_medicine',
                'amount' => 320000.00,
                'net_amount' => 320000.00,
                'status' => 'confirmed',
                'paid_at' => now()->subDays(2),
            ],
            [
                'booking_id' => null,
                'patient_id' => null,
                'doctor_id' => $docSaran->id,
                'invoice_number' => '#BILL00128',
                'patient_name' => 'Hendro Prasetyo',
                'doctor_name' => 'Dr. Saran Mitchell',
                'service_type' => 'Orthopedics',
                'payment_method' => 'cash',
                'tariff_plan' => 'with_medicine',
                'amount' => 435000.00,
                'net_amount' => 435000.00,
                'status' => 'confirmed',
                'paid_at' => now()->subDays(3),
            ],
        ];

        foreach ($paymentData as $pay) {
            Payment::updateOrCreate(
                ['invoice_number' => $pay['invoice_number']],
                $pay
            );
        }

        // 8. Seed Master Data Obat & Stok Apotek
        $obatData = [
            [
                'kode_obat' => 'PCT-500',
                'nama_obat' => 'Paracetamol 500 mg',
                'kategori' => 'Analgesik',
                'satuan' => 'Tablet',
                'harga' => 12000,
                'stok' => 128,
                'deskripsi' => 'Tablet · strip isi 10. Rak: A-03',
                'is_active' => true,
            ],
            [
                'kode_obat' => 'AMX-500',
                'nama_obat' => 'Amoxicillin 500 mg',
                'kategori' => 'Antibiotik',
                'satuan' => 'Kapsul',
                'harga' => 18000,
                'stok' => 64,
                'deskripsi' => 'Kapsul · strip isi 10. Rak: A-01',
                'is_active' => true,
            ],
            [
                'kode_obat' => 'CTZ-010',
                'nama_obat' => 'Cetirizine 10 mg',
                'kategori' => 'Antihistamin',
                'satuan' => 'Tablet',
                'harga' => 15000,
                'stok' => 96,
                'deskripsi' => 'Tablet · strip isi 10. Rak: A-04',
                'is_active' => true,
            ],
            [
                'kode_obat' => 'OMP-020',
                'nama_obat' => 'Omeprazole 20 mg',
                'kategori' => 'Lambung',
                'satuan' => 'Kapsul',
                'harga' => 25000,
                'stok' => 36,
                'deskripsi' => 'Kapsul · strip isi 10. Rak: B-02',
                'is_active' => true,
            ],
            [
                'kode_obat' => 'VTC-500',
                'nama_obat' => 'Vitamin C 500 mg',
                'kategori' => 'Vitamin',
                'satuan' => 'Tablet',
                'harga' => 45000,
                'stok' => 21,
                'deskripsi' => 'Tablet · botol 30. Rak: B-05',
                'is_active' => true,
            ],
            [
                'kode_obat' => 'MTF-500',
                'nama_obat' => 'Metformin 500 mg',
                'kategori' => 'Antidiabetik',
                'satuan' => 'Tablet',
                'harga' => 22000,
                'stok' => 18,
                'deskripsi' => 'Tablet · strip isi 10. Rak: A-02',
                'is_active' => true,
            ],
            [
                'kode_obat' => 'INS-GLA',
                'nama_obat' => 'Insulin Glargine 100 IU/ml',
                'kategori' => 'Rak Dingin',
                'satuan' => 'Pen',
                'harga' => 185000,
                'stok' => 12,
                'deskripsi' => 'Pen · 3 ml. Rak: KLK-1',
                'is_active' => true,
            ],
            [
                'kode_obat' => 'AMB-SRP',
                'nama_obat' => 'Ambroxol Sirup 15 mg/5 ml',
                'kategori' => 'Batuk & Flu',
                'satuan' => 'Botol',
                'harga' => 16500,
                'stok' => 40,
                'deskripsi' => 'Botol · 60 ml. Rak: B-03',
                'is_active' => true,
            ],
        ];

        foreach ($obatData as $obat) {
            Obat::updateOrCreate(
                ['kode_obat' => $obat['kode_obat']],
                $obat
            );
        }
    }
}
