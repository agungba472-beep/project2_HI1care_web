<?php

namespace Database\Seeders;

use App\Models\Faskes;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FaskesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('faskes')->truncate();

        $data = [
            // ─── RUMAH SAKIT ───
            [
                'nama' => 'RSUD Kelas B Kabupaten Subang',
                'alamat' => 'Jl. Brigjen Katamso No.37, Dangdeur, Kec. Subang',
                'kontak' => '0260411421', 'tipe' => 'Rumah Sakit', 'layanan' => 'Klinik VCT, Terapi ARV, Viral Load, PDP',
                'latitude' => '-6.5681146', 'longitude' => '107.7661608',
            ],
            [
                'nama' => 'RS Hamori Subang',
                'alamat' => 'Jl. Raya Sukamelang No.1, Kec. Subang',
                'kontak' => '02604240888', 'tipe' => 'Rumah Sakit', 'layanan' => 'Skrining HIV, Konseling, Rawat Inap',
                'latitude' => '-6.541200', 'longitude' => '107.771500',
            ],
            [
                'nama' => 'RS Karisma Pamanukan',
                'alamat' => 'Jl. Raya Pamanukan No.98, Kec. Pamanukan',
                'kontak' => '0260552345', 'tipe' => 'Rumah Sakit', 'layanan' => 'Konseling Sukarela, Pemeriksaan Darah',
                'latitude' => '-6.286000', 'longitude' => '107.813000',
            ],
            [
                'nama' => 'RS Rayhan Subang',
                'alamat' => 'Jl. Raya Cipeundeuy, Kec. Cipeundeuy',
                'kontak' => '0260461111', 'tipe' => 'Rumah Sakit', 'layanan' => 'Layanan Rujukan Medis, Pemeriksaan Dasar',
                'latitude' => '-6.452000', 'longitude' => '107.604000',
            ],
            [
                'nama' => 'RS PTPN VIII Subang',
                'alamat' => 'Jl. Otto Iskandardinata, Kec. Subang',
                'kontak' => '0260411133', 'tipe' => 'Rumah Sakit', 'layanan' => 'Perawatan Medis Umum, Cek Lab',
                'latitude' => '-6.570000', 'longitude' => '107.760000',
            ],
            [
                'nama' => 'RS Mutiara Hati Subang',
                'alamat' => 'Jl. Raya Tanjungpura No. 14, Kec. Pagaden',
                'kontak' => '0260450555', 'tipe' => 'Rumah Sakit', 'layanan' => 'Pemeriksaan HIV Terpadu, Rawat Jalan',
                'latitude' => '-6.485123', 'longitude' => '107.798456',
            ],
            [
                'nama' => 'RS Indosehat 2003',
                'alamat' => 'Jl. Raya Dawuan, Kec. Subang',
                'kontak' => '0260412333', 'tipe' => 'Rumah Sakit', 'layanan' => 'Pemeriksaan Dasar, Skrining Penyakit Menular',
                'latitude' => '-6.550000', 'longitude' => '107.765000',
            ],
            [
                'nama' => 'RS Pamanukan Medical Center (PMC)',
                'alamat' => 'Jl. Raya Rancasari No.1, Kec. Pamanukan',
                'kontak' => '0260551555', 'tipe' => 'Rumah Sakit', 'layanan' => 'Konseling PDP, Perawatan Rawat Inap',
                'latitude' => '-6.284500', 'longitude' => '107.811200',
            ],
            [
                'nama' => 'RSAU Lanud Suryadarma Kalijati',
                'alamat' => 'Komplek Lanud Suryadarma, Kec. Kalijati',
                'kontak' => '0260460021', 'tipe' => 'Rumah Sakit', 'layanan' => 'Pemeriksaan Kesehatan, Rujukan Lanjutan',
                'latitude' => '-6.530000', 'longitude' => '107.670000',
            ],

            // ─── PUSKESMAS WILAYAH TENGAH ───
            [
                'nama' => 'Puskesmas Subang',
                'alamat' => 'Kecamatan Subang, Kabupaten Subang',
                'kontak' => '0260412123', 'tipe' => 'Puskesmas', 'layanan' => 'Skrining HIV, Konseling Dasar',
                'latitude' => '-6.565000', 'longitude' => '107.762000',
            ],
            [
                'nama' => 'Puskesmas Sukarahayu',
                'alamat' => 'Jl. Karanganyar, Kec. Subang',
                'kontak' => '0260412211', 'tipe' => 'Puskesmas', 'layanan' => 'Konseling VCT, Terapi ARV Dasar',
                'latitude' => '-6.566838', 'longitude' => '107.761066',
            ],
            [
                'nama' => 'Puskesmas Cikalapa', // Data Tambahan Spesial!
                'alamat' => 'Jl. Emo Kurniatmadja, Pasirkareumbi, Kec. Subang',
                'kontak' => '0260413456', 'tipe' => 'Puskesmas', 'layanan' => 'Edukasi IMS, Tes HIV Cepat',
                'latitude' => '-6.554200', 'longitude' => '107.761500',
            ],
            [
                'nama' => 'Puskesmas Ciereng',
                'alamat' => 'Ciereng, Kec. Subang',
                'kontak' => '0260415551', 'tipe' => 'Puskesmas', 'layanan' => 'Konseling HIV Ibu Hamil',
                'latitude' => '-6.578000', 'longitude' => '107.755000',
            ],
            [
                'nama' => 'Puskesmas Wanareja',
                'alamat' => 'Wanareja, Kec. Subang',
                'kontak' => '0260415552', 'tipe' => 'Puskesmas', 'layanan' => 'Penyuluhan Pencegahan HIV',
                'latitude' => '-6.580000', 'longitude' => '107.770000',
            ],

            // ─── PUSKESMAS WILAYAH SELATAN ───
            [
                'nama' => 'Puskesmas Jalancagak',
                'alamat' => 'Jl. Raya Jalancagak No.1, Kec. Jalancagak',
                'kontak' => '0260470115', 'tipe' => 'Puskesmas', 'layanan' => 'VCT Bergerak, Pengambilan ARV',
                'latitude' => '-6.671100', 'longitude' => '107.679200',
            ],
            [
                'nama' => 'Puskesmas Kasomalang',
                'alamat' => 'Kasomalang, Kabupaten Subang',
                'kontak' => '0260471111', 'tipe' => 'Puskesmas', 'layanan' => 'Skrining HIV Komunitas',
                'latitude' => '-6.680000', 'longitude' => '107.710000',
            ],
            [
                'nama' => 'Puskesmas Cisalak',
                'alamat' => 'Cisalak, Kabupaten Subang',
                'kontak' => '0260472222', 'tipe' => 'Puskesmas', 'layanan' => 'Konseling Dasar, Rujukan',
                'latitude' => '-6.690000', 'longitude' => '107.740000',
            ],
            [
                'nama' => 'Puskesmas Tanjungsiang',
                'alamat' => 'Tanjungsiang, Kabupaten Subang',
                'kontak' => '0260473333', 'tipe' => 'Puskesmas', 'layanan' => 'Edukasi HIV Terpadu',
                'latitude' => '-6.720000', 'longitude' => '107.800000',
            ],
            [
                'nama' => 'Puskesmas Sagalaherang',
                'alamat' => 'Sagalaherang, Kabupaten Subang',
                'kontak' => '0260474444', 'tipe' => 'Puskesmas', 'layanan' => 'Pemeriksaan Darah Rutin',
                'latitude' => '-6.650000', 'longitude' => '107.640000',
            ],
            [
                'nama' => 'Puskesmas Serangpanjang',
                'alamat' => 'Serangpanjang, Kabupaten Subang',
                'kontak' => '0260475555', 'tipe' => 'Puskesmas', 'layanan' => 'Skrining IMS Dasar',
                'latitude' => '-6.630000', 'longitude' => '107.610000',
            ],
            [
                'nama' => 'Puskesmas Cijambe',
                'alamat' => 'Cijambe, Kabupaten Subang',
                'kontak' => '0260476666', 'tipe' => 'Puskesmas', 'layanan' => 'Tes HIV Cepat',
                'latitude' => '-6.600000', 'longitude' => '107.730000',
            ],
            [
                'nama' => 'Puskesmas Cibogo',
                'alamat' => 'Cibogo, Kabupaten Subang',
                'kontak' => '0260477777', 'tipe' => 'Puskesmas', 'layanan' => 'Konseling PDP Terpadu',
                'latitude' => '-6.540000', 'longitude' => '107.800000',
            ],

            // ─── PUSKESMAS WILAYAH TENGAH-BARAT & TIMUR ───
            [
                'nama' => 'Puskesmas Kalijati',
                'alamat' => 'Jl. Raya Kalijati No.10, Kec. Kalijati',
                'kontak' => '0260461223', 'tipe' => 'Puskesmas', 'layanan' => 'Konseling Kelompok, Distribusi ARV',
                'latitude' => '-6.542000', 'longitude' => '107.674000',
            ],
            [
                'nama' => 'Puskesmas Dawuan',
                'alamat' => 'Dawuan, Kabupaten Subang',
                'kontak' => '0260462334', 'tipe' => 'Puskesmas', 'layanan' => 'Skrining Dasar',
                'latitude' => '-6.550000', 'longitude' => '107.700000',
            ],
            [
                'nama' => 'Puskesmas Purwadadi',
                'alamat' => 'Jl. Raya Purwadadi No.45, Kec. Purwadadi',
                'kontak' => '0260460112', 'tipe' => 'Puskesmas', 'layanan' => 'Skrining HIV Mandiri, Rujukan Faskes',
                'latitude' => '-6.461500', 'longitude' => '107.682100',
            ],
            [
                'nama' => 'Puskesmas Cipeundeuy',
                'alamat' => 'Cipeundeuy, Kabupaten Subang',
                'kontak' => '0260460555', 'tipe' => 'Puskesmas', 'layanan' => 'Tes HIV Cepat, Edukasi',
                'latitude' => '-6.450000', 'longitude' => '107.600000',
            ],
            [
                'nama' => 'Puskesmas Pagaden',
                'alamat' => 'Jl. Raya Pagaden No. 12, Kec. Pagaden',
                'kontak' => '0260450111', 'tipe' => 'Puskesmas', 'layanan' => 'Pemeriksaan HIV Ibu Hamil, Obat ARV',
                'latitude' => '-6.483912', 'longitude' => '107.800123',
            ],
            [
                'nama' => 'Puskesmas Pagaden Barat',
                'alamat' => 'Pagaden Barat, Kabupaten Subang',
                'kontak' => '0260451222', 'tipe' => 'Puskesmas', 'layanan' => 'Konseling Pencegahan HIV',
                'latitude' => '-6.490000', 'longitude' => '107.760000',
            ],
            [
                'nama' => 'Puskesmas Cipunagara',
                'alamat' => 'Cipunagara, Kabupaten Subang',
                'kontak' => '0260452333', 'tipe' => 'Puskesmas', 'layanan' => 'Pemeriksaan Darah VCT',
                'latitude' => '-6.430000', 'longitude' => '107.820000',
            ],
            [
                'nama' => 'Puskesmas Compreng',
                'alamat' => 'Compreng, Kabupaten Subang',
                'kontak' => '0260453444', 'tipe' => 'Puskesmas', 'layanan' => 'Penyuluhan Masyarakat Terpadu',
                'latitude' => '-6.370000', 'longitude' => '107.830000',
            ],

            // ─── PUSKESMAS WILAYAH UTARA (PANTURA) ───
            [
                'nama' => 'Puskesmas Pamanukan',
                'alamat' => 'Jl. Ion Martasahara No.2, Kec. Pamanukan',
                'kontak' => '0260551044', 'tipe' => 'Puskesmas', 'layanan' => 'Layanan PDP, Distribusi ARV, VCT',
                'latitude' => '-6.281986', 'longitude' => '107.816431',
            ],
            [
                'nama' => 'Puskesmas Legonkulon',
                'alamat' => 'Legonkulon, Kabupaten Subang',
                'kontak' => '0260552111', 'tipe' => 'Puskesmas', 'layanan' => 'Skrining HIV Dasar',
                'latitude' => '-6.230000', 'longitude' => '107.800000',
            ],
            [
                'nama' => 'Puskesmas Pusakanagara',
                'alamat' => 'Pusakanagara, Kabupaten Subang',
                'kontak' => '0260553222', 'tipe' => 'Puskesmas', 'layanan' => 'Edukasi IMS, Tes HIV Cepat',
                'latitude' => '-6.250000', 'longitude' => '107.850000',
            ],
            [
                'nama' => 'Puskesmas Pusakajaya',
                'alamat' => 'Pusakajaya, Kabupaten Subang',
                'kontak' => '0260554333', 'tipe' => 'Puskesmas', 'layanan' => 'Konseling Pra-Nikah, VCT',
                'latitude' => '-6.260000', 'longitude' => '107.880000',
            ],
            [
                'nama' => 'Puskesmas Sukasari',
                'alamat' => 'Sukasari, Kabupaten Subang',
                'kontak' => '0260555444', 'tipe' => 'Puskesmas', 'layanan' => 'Konseling Dasar, Rujukan',
                'latitude' => '-6.300000', 'longitude' => '107.830000',
            ],
            [
                'nama' => 'Puskesmas Tambakdahan',
                'alamat' => 'Tambakdahan, Kabupaten Subang',
                'kontak' => '0260556555', 'tipe' => 'Puskesmas', 'layanan' => 'Skrining Kesehatan Kelompok Rentan',
                'latitude' => '-6.350000', 'longitude' => '107.800000',
            ],
            [
                'nama' => 'Puskesmas Binong',
                'alamat' => 'Binong, Kabupaten Subang',
                'kontak' => '0260557666', 'tipe' => 'Puskesmas', 'layanan' => 'Pemeriksaan HIV dan ARV Terbatas',
                'latitude' => '-6.400000', 'longitude' => '107.780000',
            ],
            [
                'nama' => 'Puskesmas Blanakan',
                'alamat' => 'Blanakan, Kabupaten Subang',
                'kontak' => '0260558777', 'tipe' => 'Puskesmas', 'layanan' => 'Edukasi Pesisir, VCT Mobile',
                'latitude' => '-6.250000', 'longitude' => '107.650000',
            ],
            [
                'nama' => 'Puskesmas Patokbeusi',
                'alamat' => 'Patokbeusi, Kabupaten Subang',
                'kontak' => '0260559888', 'tipe' => 'Puskesmas', 'layanan' => 'Konseling VCT, Pemeriksaan Lab',
                'latitude' => '-6.350000', 'longitude' => '107.650000',
            ],
            [
                'nama' => 'Puskesmas Pabuaran',
                'alamat' => 'Pabuaran, Kabupaten Subang',
                'kontak' => '0260560999', 'tipe' => 'Puskesmas', 'layanan' => 'Konseling dan Rujukan Lanjutan',
                'latitude' => '-6.400000', 'longitude' => '107.600000',
            ],
            [
                'nama' => 'Puskesmas Ciasem',
                'alamat' => 'Ciasem, Kabupaten Subang',
                'kontak' => '0260561000', 'tipe' => 'Puskesmas', 'layanan' => 'Pengambilan Obat ARV, Skrining',
                'latitude' => '-6.320000', 'longitude' => '107.700000',
            ],
        ];

        foreach ($data as $item) {
            Faskes::create($item);
        }
    }
}