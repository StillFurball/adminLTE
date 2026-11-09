<?php

namespace Database\Factories;

use App\Models\Tugas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tugas>
 */
class TugasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jenis = fake()->randomElement(['Laporan', 'Kuis', 'Proyek', 'Presentasi']);
        $mataKuliah = fake()->randomElement(['Basis Data', 'Jaringan Komputer', 'PBKK', 'Sistem Operasi']);

        return [
            'judul' => "{$jenis} {$mataKuliah}",
            'tenggat' => fake()->dateTimeBetween('-1 month', '+2 months')->format('Y-m-d'),
        ];
    }
}
