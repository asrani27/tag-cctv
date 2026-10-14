<?php

namespace App\Models;

use Database\Factories\KelurahanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelurahan extends Model
{
    /** @use HasFactory<KelurahanFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kelurahans';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'kecamatan_id',
        'kode',
        'name',
    ];

    /**
     * Get the kecamatan that owns this kelurahan.
     */
    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }

    /**
     * Get all survey locations in this kelurahan.
     */
    public function surveys(): HasMany
    {
        return $this->hasMany(SurveyLocation::class, 'kelurahan', 'name');
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
