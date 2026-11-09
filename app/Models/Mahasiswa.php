<?php

namespace App\Models;

use Database\Factories\MahasiswaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['nrp', 'nama', 'departemen', 'dosen_id', 'is_active'])]
class Mahasiswa extends Model
{
    /** @use HasFactory<MahasiswaFactory> */
    use HasFactory;

    /** @var list<string> */
    public const DEPARTEMEN = [
        'Teknik Informatika',
        'Sistem Informasi',
        'Teknologi Informasi',
        'Teknik Elektro',
        'Teknik Komputer',
    ];

    /**
     * @return BelongsTo<Dosen, $this>
     */
    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
