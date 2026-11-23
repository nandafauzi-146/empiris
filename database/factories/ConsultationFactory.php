<?php

namespace Database\Factories;

use App\Models\Consultation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Consultation>
 */
class ConsultationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nomor_konsultasi' => 'KON-'.now()->format('Ymd').'-'.fake()->unique()->numerify('########'),
            'nama' => fake()->name(),
            'email' => fake()->safeEmail(),
            'telepon' => fake()->numerify('08##########'),
            'instansi' => fake()->company(),
            'layanan' => 'Tour & Wisata',
            'tujuan' => fake()->city(),
            'jumlah_peserta' => fake()->numberBetween(1, 100),
            'durasi_hari' => fake()->numberBetween(1, 7),
            'tanggal_rencana' => fake()->dateTimeBetween('+1 week', '+6 months')->format('Y-m-d'),
            'minat' => fake()->sentence(),
            'cerita_kebutuhan' => fake()->paragraph(),
            'status' => 'baru',
        ];
    }
}
