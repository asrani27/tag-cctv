<?php

namespace App\Models;

use Database\Factories\KecamatanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kecamatan extends Model
{
    /** @use HasFactory<KecamatanFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kecamatans';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'kode',
        'name',
    ];

    /**
     * Get all kelurahan in this kecamatan.
     */
    public function kelurahans(): HasMany
    {
        return $this->hasMany(Kelurahan::class, 'kecamatan_id');
    }

    /**
     * Alias attribute for 'code' in English.
     */
    public function getCodeAttribute(): ?string
    {
        return $this->kode;
    }

    /**
     * Alias mutator for 'code' in English.
     */
    public function setCodeAttribute(?string $value): void
    {
        $this->attributes['kode'] = $value;
    }

    /**
     * Alias attribute for 'nama' in Indonesian.
     */
    public function getNamaAttribute(): ?string
    {
        return $this->name;
    }

    /**
     * Alias mutator for 'nama' in Indonesian.
     */
    public function setNamaAttribute(?string $value): void
    {
        $this->attributes['name'] = $value;
    }
}
