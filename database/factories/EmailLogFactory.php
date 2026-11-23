<?php

namespace Database\Factories;

use App\Models\EmailLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmailLog>
 */
class EmailLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_konsultasi' => ConsultationFactory::new(),
            'jenis' => 'konfirmasi_klien',
            'ke_email' => fake()->safeEmail(),
            'subjek' => 'Konsultasi Anda sudah kami terima',
            'status' => 'terjadwal',
            'jadwal_kirim' => now(),
        ];
    }
}
