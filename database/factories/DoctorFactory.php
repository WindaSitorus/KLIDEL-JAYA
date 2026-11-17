<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Poli;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'poli_id' => Poli::factory(),
            'nama_dokter' => 'Dr. '.fake()->name(),
            'spesialisasi' => 'Spesialis Umum',
            'rating' => fake()->randomFloat(1, 4, 5),
            'price' => 'Rp 180.000',
            'price_num' => 180000.00,
            'initials' => 'DR',
            'image' => 'images/doctors/doctor-1.png',
            'availability' => '9AM - 8PM',
            'experience' => '10 years',
            'patients' => '3.8k Up',
        ];
    }
}
