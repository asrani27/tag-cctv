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


