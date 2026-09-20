<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveyLocation extends Model
{
    use HasFactory;

    /**
     * Bootstrap the model and its traits.
     */
    protected static function booted(): void
    {
        static::deleting(function (SurveyLocation $survey) {
            foreach ($survey->photos as $photo) {
                $photo->delete();
            }
        });
    }

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'survey_locations';

    /**
     * Daftar Kecamatan di Kota Banjarmasin.
     */
    public const KECAMATAN_LIST = [
        'Banjarmasin Barat',
        'Banjarmasin Selatan',
        'Banjarmasin Tengah',
        'Banjarmasin Timur',
        'Banjarmasin Utara',
    ];

    /**
     * Daftar Kelurahan berdasarkan Kecamatan di Kota Banjarmasin.
     */
    public const KELURAHAN_BY_KECAMATAN = [
        'Banjarmasin Barat' => [
            'Basirih',
            'Belitung Selatan',
            'Belitung Utara',
            'Kuin Cerucuk',
            'Kuin Selatan',
            'Pelambuan',
            'Telaga Biru',
            'Telawang',
            'Teluk Tiram',
        ],
        'Banjarmasin Selatan' => [
            'Kelayan Barat',
            'Kelayan Dalam',
            'Kelayan Selatan',
            'Kelayan Tengah',
            'Kelayan Timur',
            'Mantuil',
            'Murung Raya',
            'Pekauman',
            'Pemurus Baru',
            'Pemurus Dalam',
            'Tanjung Pagar',
        ],
        'Banjarmasin Tengah' => [
            'Antasan Besar',
            'Gadang',
            'Kelayan Luar',
            'Kertak Baru Ilir',
            'Kertak Baru Ulu',
            'Mawar',
            'Melayu',
            'Pasar Lama',
            'Pekapuran Laut',
            'Seberang Mesjid',
            'Sungai Baru',
            'Teluk Dalam',
        ],
        'Banjarmasin Timur' => [
            'Banua Anyar',
            'Karang Mekar',
            'Kebun Bunga',
            'Kuripan',
            'Pekapuran Raya',
            'Pemurus Luar',
            'Pengambangan',
            'Sungai Bilu',
        ],
        'Banjarmasin Utara' => [
            'Alalak Selatan',
            'Alalak Tengah',
            'Alalak Utara',
            'Antasan Kecil Timur',
            'Kuin Utara',
            'Pangeran',
            'Sungai Andai',
            'Sungai Jingah',
            'Sungai Miai',
            'Surgi Mufti',
        ],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'latitude',
        'longitude',
        'alamat',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'keamanan',
        'akses_wifi',
        'lalin',
        'panic_sensor',
        'jumlah_cctv',
        'jumlah_ap',
        'bandwidth',
        'backhaul',
        'storage',
        'jenis_jalan',
        'tiang',
        'jenis_tiang',
        'tembok',
        'arah_pantau',
        'jarak_pantau',
        'pencahayaan_malam',
        'potensi_silau',
        'penghalang',
        'potensi_vandalisme',
        'luas_area',
        'prakiraan_pengguna_max',
        'tempat_ap_latitude',
        'tempat_ap_longitude',
        'potensi_interferensi',
        'sumber_listrik',
        'posisi_panel_latitude',
        'posisi_panel_longitude',
        'daya_tersedia',
        'proteksi_petir',
        'tersedia_fiber_optik',
        'tersedia_4g_5g',
        'tersedia_link_p2p',
        'jumlah_provider',
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
            'keamanan' => 'boolean',
            'akses_wifi' => 'boolean',
            'lalin' => 'boolean',
            'panic_sensor' => 'boolean',
            'jumlah_cctv' => 'integer',
            'jumlah_ap' => 'integer',
            'luas_area' => 'float',
            'prakiraan_pengguna_max' => 'integer',
            'tempat_ap_latitude' => 'float',
            'tempat_ap_longitude' => 'float',
            'posisi_panel_latitude' => 'float',
            'posisi_panel_longitude' => 'float',
            'daya_tersedia' => 'float',
            'tersedia_fiber_optik' => 'boolean',
            'tersedia_4g_5g' => 'boolean',
            'tersedia_link_p2p' => 'boolean',
            'jumlah_provider' => 'integer',
        ];
    }

    /**
     * Reusable scope for filtering surveys by search, kecamatan, kelurahan, koneksi, user, and date range.
     *
     * Used by both the index listing (with pagination) and the export (without pagination)
     * to guarantee consistent results.
     */
    public function scopeApplyFilters(Builder $query, array $filters, ?User $user = null): Builder
    {
        // Scope by ownership: regular users ONLY see their own data
        if ($user && $user->isSuperAdmin()) {
            $query->with(['user', 'photos']);

            // Filter by user_id (superadmin only)
            $userId = $filters['user_id'] ?? null;
            if ($userId !== null && $userId !== '') {
                if ($userId === 'legacy') {
                    $query->whereNull('user_id');
                } else {
                    $query->where('user_id', $userId);
                }
            }
        } elseif ($user) {
            $query->with('photos')->where('user_id', $user->id);
        }

        // Search alamat, kelurahan, or kecamatan
        $search = trim((string) (! empty($filters['search']) ? $filters['search'] : ($filters['q'] ?? '')));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('alamat', 'like', "%{$search}%")
                    ->orWhere('kelurahan', 'like', "%{$search}%")
                    ->orWhere('kecamatan', 'like', "%{$search}%");
            });
        }

        // Filter Kecamatan
        if (! empty($filters['kecamatan'])) {
            $query->where('kecamatan', $filters['kecamatan']);
        }

        // Filter Kelurahan
        if (! empty($filters['kelurahan'])) {
            $query->where('kelurahan', $filters['kelurahan']);
        }

        // Filter Konektivitas
        $koneksi = strtolower((string) ($filters['koneksi'] ?? ''));
        if ($koneksi !== '') {
            if ($koneksi === 'fiber' || $koneksi === 'fo') {
                $query->where('tersedia_fiber_optik', true);
            } elseif ($koneksi === '4g' || $koneksi === '5g' || $koneksi === 'cellular') {
                $query->where('tersedia_4g_5g', true);
            } elseif ($koneksi === 'p2p') {
                $query->where('tersedia_link_p2p', true);
            }
        }

        // Filter by date range
        if (! empty($filters['tanggal_mulai'])) {
            $query->whereDate('created_at', '>=', $filters['tanggal_mulai']);
        }
        if (! empty($filters['tanggal_akhir'])) {
            $query->whereDate('created_at', '<=', $filters['tanggal_akhir']);
        }

        return $query;
    }

    /**
     * Check if survey location has valid coordinates.
     */
    public function hasCoordinates(): bool
    {
        return ! is_null($this->latitude) && ! is_null($this->longitude);
    }

    /**
     * Scope for querying surveys with valid coordinates.
     */
    public function scopeWithCoordinates(Builder $query): Builder
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }

    /**
     * Get the user who created this survey.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the photos associated with this survey.
     */
    public function photos(): HasMany
    {
        return $this->hasMany(SurveyPhoto::class, 'survey_location_id')->orderBy('sort_order')->orderBy('id');
    }
}


