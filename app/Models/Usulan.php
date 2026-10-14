<?php

namespace App\Models;

use Database\Factories\UsulanFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Usulan extends Model
{
    /** @use HasFactory<UsulanFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usulans';

    /**
     * Pilihan opsi untuk Nama Lokasi / Titik Usulan.
     *
     * @var list<string>
     */
    public const NAMA_LOKASI_OPTIONS = [
        'Gang',
        'Jalan',
        'Fasum',
    ];

    /**
     * Pilihan opsi untuk Kategori Titik.
     *
     * @var list<string>
     */
    public const KATEGORI_TITIK_OPTIONS = [
        'Persimpangan',
        'Pintu Masuk RT',
        'Rawan Kriminalitas',
        'Fasos-Fasum',
    ];

    /**
     * Pilihan opsi untuk Infrastruktur Penyangga.
     *
     * @var list<string>
     */
    public const INFRASTRUKTUR_PENYANGGA_OPTIONS = [
        'Tiang PLN',
        'Tiang Telkom',
        'Bangunan Posyandu/Poskamling',
        'Belum Ada',
    ];

    /**
     * Pilihan opsi untuk Sumber Daya Listrik.
     *
     * @var list<string>
     */
    public const SUMBER_LISTRIK_OPTIONS = [
        'Ada',
        'Tidak',
    ];

    /**
     * Pilihan opsi untuk Ketersediaan CCTV Eksisting.
     *
     * @var list<string>
     */
    public const CCTV_EKSISTING_OPTIONS = [
        'Sudah Ada',
        'Belum',
    ];

    /**
     * Status Usulan.
     *
     * @var list<string>
     */
    public const STATUS_OPTIONS = [
        'Diajukan',
        'Disetujui',
        'Ditolak',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'latitude',
        'longitude',
        'kelurahan',
        'kecamatan',
        'rt',
        'nama_ketua_rt',
        'no_hp',
        'nama_lokasi',
        'kategori_titik',
        'infrastruktur_penyangga',
        'sumber_daya_listrik',
        'cctv_eksisting',
        'keterangan',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    /**
     * Check if usulan location has valid coordinates.
     */
    public function hasCoordinates(): bool
    {
        return ! is_null($this->latitude) && ! is_null($this->longitude);
    }

    /**
     * Scope for querying usulans with valid coordinates.
     */
    public function scopeWithCoordinates(Builder $query): Builder
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }

    /**
     * Scope for filtering usulans based on request parameters and user permissions.
     *
     * @param  array<string, mixed>  $filters
     */
    public function scopeApplyFilters(Builder $query, array $filters, ?User $user = null): Builder
    {
        if ($user && ! $user->isSuperAdmin()) {
            $query->where('user_id', $user->id);
        } elseif (! empty($filters['user_id'])) {
            if ($filters['user_id'] === 'legacy') {
                $query->whereNull('user_id');
            } else {
                $query->where('user_id', $filters['user_id']);
            }
        }

        if (! empty($filters['search'])) {
            $search = '%'.$filters['search'].'%';
            $query->where(function (Builder $q) use ($search) {
                $q->where('nama_ketua_rt', 'like', $search)
                    ->orWhere('no_hp', 'like', $search)
                    ->orWhere('rt', 'like', $search)
                    ->orWhere('kelurahan', 'like', $search)
                    ->orWhere('nama_lokasi', 'like', $search)
                    ->orWhere('kategori_titik', 'like', $search)
                    ->orWhere('infrastruktur_penyangga', 'like', $search)
                    ->orWhere('keterangan', 'like', $search);
            });
        }

        if (! empty($filters['kelurahan'])) {
            $query->where('kelurahan', $filters['kelurahan']);
        }

        if (! empty($filters['kategori_titik'])) {
            $query->where('kategori_titik', $filters['kategori_titik']);
        }

        if (! empty($filters['nama_lokasi'])) {
            $query->where('nama_lokasi', $filters['nama_lokasi']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }

    /**
     * Get the user who created this usulan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the associated Kelurahan model.
     */
    public function kelurahanModel(): BelongsTo
    {
        return $this->belongsTo(Kelurahan::class, 'kelurahan', 'name');
    }
}
