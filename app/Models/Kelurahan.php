<?php

namespace App\Models;

use Database\Factories\KelurahanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
