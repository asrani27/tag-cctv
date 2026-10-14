<?php

namespace Database\Seeders;

use App\Models\Kecamatan;
use App\Models\Kelurahan;
use Illuminate\Database\Seeder;

class KecamatanSeeder extends Seeder
{
    /**
     * Master data Kecamatan dan Kelurahan khusus Kota Banjarmasin (Kode Kemendagri).
     *
     * @var array<int, array{kode: string, name: string, kelurahans: array<int, array{kode: string, name: string}>}>
     */
    public const DATA = [
        [
            'kode' => '63.71.01',
            'name' => 'Banjarmasin Selatan',
            'kelurahans' => [
                ['kode' => '63.71.01.1001', 'name' => 'Mantuil'],
                ['kode' => '63.71.01.1002', 'name' => 'Kelayan Selatan'],
                ['kode' => '63.71.01.1003', 'name' => 'Pekauman'],
                ['kode' => '63.71.01.1004', 'name' => 'Kelayan Barat'],
                ['kode' => '63.71.01.1005', 'name' => 'Kelayan Tengah'],
                ['kode' => '63.71.01.1006', 'name' => 'Kelayan Dalam'],
                ['kode' => '63.71.01.1007', 'name' => 'Murung Raya'],
                ['kode' => '63.71.01.1008', 'name' => 'Kelayan Timur'],
                ['kode' => '63.71.01.1009', 'name' => 'Tanjung Pagar'],
                ['kode' => '63.71.01.1010', 'name' => 'Pemurus Dalam'],
                ['kode' => '63.71.01.1011', 'name' => 'Pemurus Baru'],
                ['kode' => '63.71.01.1012', 'name' => 'Basirih Selatan'],
            ],
        ],
        [
            'kode' => '63.71.02',
            'name' => 'Banjarmasin Timur',
            'kelurahans' => [
                ['kode' => '63.71.02.1001', 'name' => 'Kuripan'],
                ['kode' => '63.71.02.1002', 'name' => 'Kebun Bunga'],
                ['kode' => '63.71.02.1003', 'name' => 'Karang Mekar'],
                ['kode' => '63.71.02.1004', 'name' => 'Sungai Bilu'],
                ['kode' => '63.71.02.1005', 'name' => 'Sungai Lulut'],
                ['kode' => '63.71.02.1006', 'name' => 'Banua Anyar'],
                ['kode' => '63.71.02.1007', 'name' => 'Pengambangan'],
                ['kode' => '63.71.02.1008', 'name' => 'Pekapuran Raya'],
                ['kode' => '63.71.02.1009', 'name' => 'Pemurus Luar'],
            ],
        ],
        [
            'kode' => '63.71.03',
            'name' => 'Banjarmasin Barat',
            'kelurahans' => [
                ['kode' => '63.71.03.1001', 'name' => 'Belitung Utara'],
                ['kode' => '63.71.03.1002', 'name' => 'Belitung Selatan'],
                ['kode' => '63.71.03.1003', 'name' => 'Pelambuan'],
                ['kode' => '63.71.03.1004', 'name' => 'Telaga Biru'],
                ['kode' => '63.71.03.1005', 'name' => 'Telawang'],
                ['kode' => '63.71.03.1006', 'name' => 'Teluk Tiram'],
                ['kode' => '63.71.03.1007', 'name' => 'Kuin Selatan'],
                ['kode' => '63.71.03.1008', 'name' => 'Kuin Cerucuk'],
                ['kode' => '63.71.03.1009', 'name' => 'Basirih'],
            ],
        ],
        [
            'kode' => '63.71.04',
            'name' => 'Banjarmasin Utara',
            'kelurahans' => [
                ['kode' => '63.71.04.1001', 'name' => 'Alalak Tengah'],
                ['kode' => '63.71.04.1002', 'name' => 'Alalak Utara'],
                ['kode' => '63.71.04.1003', 'name' => 'Alalak Selatan'],
                ['kode' => '63.71.04.1004', 'name' => 'Sungai Jingah'],
                ['kode' => '63.71.04.1005', 'name' => 'Sungai Miai'],
                ['kode' => '63.71.04.1006', 'name' => 'Surgi Mufti'],
                ['kode' => '63.71.04.1007', 'name' => 'Pangeran'],
                ['kode' => '63.71.04.1008', 'name' => 'Antasan Kecil Timur'],
                ['kode' => '63.71.04.1009', 'name' => 'Kuin Utara'],
                ['kode' => '63.71.04.1010', 'name' => 'Sungai Andai'],
            ],
        ],
        [
            'kode' => '63.71.05',
            'name' => 'Banjarmasin Tengah',
            'kelurahans' => [
                ['kode' => '63.71.05.1001', 'name' => 'Kertak Baru Ilir'],
                ['kode' => '63.71.05.1002', 'name' => 'Kertak Baru Ulu'],
                ['kode' => '63.71.05.1003', 'name' => 'Mawar'],
                ['kode' => '63.71.05.1004', 'name' => 'Teluk Dalam'],
                ['kode' => '63.71.05.1005', 'name' => 'Antasan Besar'],
                ['kode' => '63.71.05.1006', 'name' => 'Pasar Lama'],
                ['kode' => '63.71.05.1007', 'name' => 'Seberang Mesjid'],
                ['kode' => '63.71.05.1008', 'name' => 'Gadang'],
                ['kode' => '63.71.05.1009', 'name' => 'Melayu'],
                ['kode' => '63.71.05.1010', 'name' => 'Sungai Baru'],
                ['kode' => '63.71.05.1011', 'name' => 'Pekapuran Laut'],
                ['kode' => '63.71.05.1012', 'name' => 'Kelayan Luar'],
            ],
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::DATA as $item) {
            $kecamatan = Kecamatan::updateOrCreate(
                ['kode' => $item['kode']],
                ['name' => $item['name']]
            );

            foreach ($item['kelurahans'] as $kelurahanData) {
                Kelurahan::updateOrCreate(
                    ['kode' => $kelurahanData['kode']],
                    [
                        'kecamatan_id' => $kecamatan->id,
                        'name' => $kelurahanData['name'],
                    ]
                );
            }
        }
    }
}
