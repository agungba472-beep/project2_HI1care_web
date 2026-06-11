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
                'latitude' => -6.5570872, 'longitude' => 107.7472842,
            ],
            [
                'nama' => 'RS Hamori Subang',
                'alamat' => 'Jl. Raya Sukamelang No.1, Kec. Subang',
                'kontak' => '02604240888', 'tipe' => 'Rumah Sakit', 'layanan' => 'Skrining HIV, Konseling, Rawat Inap',
                'latitude' => -6.5274519, 'longitude' => 107.7911813,
            ],
            [
                'nama' => 'RS Karisma Pamanukan',
                'alamat' => 'Jl. Raya Pamanukan No.98, Kec. Pamanukan',
                'kontak' => '0260552345', 'tipe' => 'Rumah Sakit', 'layanan' => 'Konseling Sukarela, Pemeriksaan Darah',
                'latitude' => -6.3128642, 'longitude' => 107.8196061,
            ],
            [
                'nama' => 'RS Rayhan Subang',
                'alamat' => 'Jl. Raya Cipeundeuy, Kec. Cipeundeuy',
                'kontak' => '0260461111', 'tipe' => 'Rumah Sakit', 'layanan' => 'Layanan Rujukan Medis, Pemeriksaan Dasar',
                'latitude' => -6.4946388, 'longitude' => 107.5970969,
            ],
            [
                'nama' => 'RS PTPN VIII Subang',
                'alamat' => 'Jl. Otto Iskandardinata, Kec. Subang',
                'kontak' => '0260411133', 'tipe' => 'Rumah Sakit', 'layanan' => 'Perawatan Medis Umum, Cek Lab',
                'latitude' => -6.5680896, 'longitude' => 107.7626985,
            ],
            [
                'nama' => 'RS Mutiara Hati Subang',
                'alamat' => 'Jl. Raya Tanjungpura No. 14, Kec. Pagaden',
                'kontak' => '0260450555', 'tipe' => 'Rumah Sakit', 'layanan' => 'Pemeriksaan HIV Terpadu, Rawat Jalan',
                'latitude' => -6.4722183, 'longitude' => 107.8109313,
            ],
            [
                'nama' => 'RS Indosehat 2003',
                'alamat' => 'Jl. Raya Dawuan, Kec. Subang',
                'kontak' => '0260412333', 'tipe' => 'Rumah Sakit', 'layanan' => 'Pemeriksaan Dasar, Skrining Penyakit Menular',
                'latitude' => -6.5019709, 'longitude' => 107.5802227,
            ],
            [
                'nama' => 'RS Pamanukan Medical Center (PMC)',
                'alamat' => 'Jl. Raya Rancasari No.1, Kec. Pamanukan',
                'kontak' => '0260551555', 'tipe' => 'Rumah Sakit', 'layanan' => 'Konseling PDP, Perawatan Rawat Inap',
                'latitude' => -6.3094357, 'longitude' => 107.8215023,
            ],
            [
                'nama' => 'RSAU Lanud Suryadarma Kalijati',
                'alamat' => 'Komplek Lanud Suryadarma, Kec. Kalijati',
                'kontak' => '0260460021', 'tipe' => 'Rumah Sakit', 'layanan' => 'Pemeriksaan Kesehatan, Rujukan Lanjutan',
                'latitude' => -6.5158350, 'longitude' => 107.6628693,
            ],

            // ─── PUSKESMAS WILAYAH TENGAH ───
            [
                'nama' => 'Puskesmas Subang',
                'alamat' => 'Kecamatan Subang, Kabupaten Subang',
                'kontak' => '0260412123', 'tipe' => 'Puskesmas', 'layanan' => 'Skrining HIV, Konseling Dasar',
                'latitude' => null, 'longitude' => null,
            ],
            [
                'nama' => 'Puskesmas Sukarahayu',
                'alamat' => 'Jl. Karanganyar, Kec. Subang',
                'kontak' => '0260412211', 'tipe' => 'Puskesmas', 'layanan' => 'Konseling VCT, Terapi ARV Dasar',
                'latitude' => -6.5542261, 'longitude' => 107.7693660,
            ],
            [
                'nama' => 'Puskesmas Cikalapa',
                'alamat' => 'Jl. Emo Kurniatmadja, Pasirkareumbi, Kec. Subang',
                'kontak' => '0260413456', 'tipe' => 'Puskesmas', 'layanan' => 'Edukasi IMS, Tes HIV Cepat',
                'latitude' => -6.5790882, 'longitude' => 107.7657479,
            ],
            [
                'nama' => 'Puskesmas Ciereng',
                'alamat' => 'Ciereng, Kec. Subang',
                'kontak' => '0260415551', 'tipe' => 'Puskesmas', 'layanan' => 'Konseling HIV Ibu Hamil',
                'latitude' => null, 'longitude' => null,
            ],
            [
                'nama' => 'Puskesmas Wanareja',
                'alamat' => 'Wanareja, Kec. Subang',
                'kontak' => '0260415552', 'tipe' => 'Puskesmas', 'layanan' => 'Penyuluhan Pencegahan HIV',
                'latitude' => null, 'longitude' => null,
            ],

            // ─── PUSKESMAS WILAYAH SELATAN ───
            [
                'nama' => 'Puskesmas Jalancagak',
                'alamat' => 'Jl. Raya Jalancagak No.1, Kec. Jalancagak',
                'kontak' => '0260470115', 'tipe' => 'Puskesmas', 'layanan' => 'VCT Bergerak, Pengambilan ARV',
                'latitude' => -6.6784478, 'longitude' => 107.6899236,
            ],
            [
                'nama' => 'Puskesmas Kasomalang',
                'alamat' => 'Kasomalang, Kabupaten Subang',
                'kontak' => '0260471111', 'tipe' => 'Puskesmas', 'layanan' => 'Skrining HIV Komunitas',
                'latitude' => -6.6983603, 'longitude' => 107.7370829,
            ],
            [
                'nama' => 'Puskesmas Cisalak',
                'alamat' => 'Cisalak, Kabupaten Subang',
                'kontak' => '0260472222', 'tipe' => 'Puskesmas', 'layanan' => 'Konseling Dasar, Rujukan',
                'latitude' => -6.7158344, 'longitude' => 107.7640706,
            ],
            [
                'nama' => 'Puskesmas Tanjungsiang',
                'alamat' => 'Tanjungsiang, Kabupaten Subang',
                'kontak' => '0260473333', 'tipe' => 'Puskesmas', 'layanan' => 'Edukasi HIV Terpadu',
                'latitude' => -6.7504302, 'longitude' => 107.8083363,
            ],
            [
                'nama' => 'Puskesmas Sagalaherang',
                'alamat' => 'Sagalaherang, Kabupaten Subang',
                'kontak' => '0260474444', 'tipe' => 'Puskesmas', 'layanan' => 'Pemeriksaan Darah Rutin',
                'latitude' => -6.6745526, 'longitude' => 107.6528285,
            ],
            [
                'nama' => 'Puskesmas Serangpanjang',
                'alamat' => 'Serangpanjang, Kabupaten Subang',
                'kontak' => '0260475555', 'tipe' => 'Puskesmas', 'layanan' => 'Skrining IMS Dasar',
                'latitude' => -6.6656427, 'longitude' => 107.6135760,
            ],
            [
                'nama' => 'Puskesmas Tanjung Wangi Cijambe',
                'alamat' => 'Cijambe, Kabupaten Subang',
                'kontak' => '0260476666', 'tipe' => 'Puskesmas', 'layanan' => 'Tes HIV Cepat',
                'latitude' => -6.5948676, 'longitude' => 107.7341906,
            ],
            [
                'nama' => 'Puskesmas Cibogo',
                'alamat' => 'Cibogo, Kabupaten Subang',
                'kontak' => '0260477777', 'tipe' => 'Puskesmas', 'layanan' => 'Konseling PDP Terpadu',
                'latitude' => -6.5590177, 'longitude' => 107.7971278,
            ],

            // ─── PUSKESMAS WILAYAH TENGAH-BARAT & TIMUR ───
            [
                'nama' => 'Puskesmas Kalijati',
                'alamat' => 'Jl. Raya Kalijati No.10, Kec. Kalijati',
                'kontak' => '0260461223', 'tipe' => 'Puskesmas', 'layanan' => 'Konseling Kelompok, Distribusi ARV',
                'latitude' => -6.5193219, 'longitude' => 107.6827910,
            ],
            [
                'nama' => 'Puskesmas Rawalele Dawuan',
                'alamat' => 'Dawuan, Kabupaten Subang',
                'kontak' => '0260462334', 'tipe' => 'Puskesmas', 'layanan' => 'Skrining Dasar',
                'latitude' => -6.5251177, 'longitude' => 107.7147674,
            ],
            [
                'nama' => 'Puskesmas Purwadadi',
                'alamat' => 'Jl. Raya Purwadadi No.45, Kec. Purwadadi',
                'kontak' => '0260460112', 'tipe' => 'Puskesmas', 'layanan' => 'Skrining HIV Mandiri, Rujukan Faskes',
                'latitude' => -6.4363482, 'longitude' => 107.6874909,
            ],
            [
                'nama' => 'Puskesmas Cipeundeuy',
                'alamat' => 'Cipeundeuy, Kabupaten Subang',
                'kontak' => '0260460555', 'tipe' => 'Puskesmas', 'layanan' => 'Tes HIV Cepat, Edukasi',
                'latitude' => -6.4959351, 'longitude' => 107.5718407,
            ],
            [
                'nama' => 'Puskesmas Pagaden',
                'alamat' => 'Jl. Raya Pagaden No. 12, Kec. Pagaden',
                'kontak' => '0260450111', 'tipe' => 'Puskesmas', 'layanan' => 'Pemeriksaan HIV Ibu Hamil, Obat ARV',
                'latitude' => -6.4581680, 'longitude' => 107.8119127,
            ],
            [
                'nama' => 'Puskesmas Pagaden Barat',
                'alamat' => 'Pagaden Barat, Kabupaten Subang',
                'kontak' => '0260451222', 'tipe' => 'Puskesmas', 'layanan' => 'Konseling Pencegahan HIV',
                'latitude' => -6.4644390, 'longitude' => 107.7603801,
            ],
            [
                'nama' => 'Puskesmas Cipunagara',
                'alamat' => 'Cipunagara, Kabupaten Subang',
                'kontak' => '0260452333', 'tipe' => 'Puskesmas', 'layanan' => 'Pemeriksaan Darah VCT',
                'latitude' => -6.4584943, 'longitude' => 107.8839633,
            ],
            [
                'nama' => 'Puskesmas Compreng',
                'alamat' => 'Compreng, Kabupaten Subang',
                'kontak' => '0260453444', 'tipe' => 'Puskesmas', 'layanan' => 'Penyuluhan Masyarakat Terpadu',
                'latitude' => -6.3596145, 'longitude' => 107.8791021,
            ],

            // ─── PUSKESMAS WILAYAH UTARA (PANTURA) ───
            [
                'nama' => 'Puskesmas Pamanukan',
                'alamat' => 'Jl. Ion Martasahara No.2, Kec. Pamanukan',
                'kontak' => '0260551044', 'tipe' => 'Puskesmas', 'layanan' => 'Layanan PDP, Distribusi ARV, VCT',
                'latitude' => -6.2897611, 'longitude' => 107.8208591,
            ],
            [
                'nama' => 'Puskesmas Legonkulon',
                'alamat' => 'Legonkulon, Kabupaten Subang',
                'kontak' => '0260552111', 'tipe' => 'Puskesmas', 'layanan' => 'Skrining HIV Dasar',
                'latitude' => -6.2423173, 'longitude' => 107.8066908,
            ],
            [
                'nama' => 'Puskesmas Pusakanagara',
                'alamat' => 'Pusakanagara, Kabupaten Subang',
                'kontak' => '0260553222', 'tipe' => 'Puskesmas', 'layanan' => 'Edukasi IMS, Tes HIV Cepat',
                'latitude' => -6.2825615, 'longitude' => 107.8760468,
            ],
            [
                'nama' => 'Puskesmas Pusakajaya',
                'alamat' => 'Pusakajaya, Kabupaten Subang',
                'kontak' => '0260554333', 'tipe' => 'Puskesmas', 'layanan' => 'Konseling Pra-Nikah, VCT',
                'latitude' => null, 'longitude' => null,
            ],
            [
                'nama' => 'Puskesmas Sukasari',
                'alamat' => 'Sukasari, Kabupaten Subang',
                'kontak' => '0260555444', 'tipe' => 'Puskesmas', 'layanan' => 'Konseling Dasar, Rujukan',
                'latitude' => -6.1849756, 'longitude' => 106.6423183,
            ],
            [
                'nama' => 'Puskesmas Tambakdahan',
                'alamat' => 'Tambakdahan, Kabupaten Subang',
                'kontak' => '0260556555', 'tipe' => 'Puskesmas', 'layanan' => 'Skrining Kesehatan Kelompok Rentan',
                'latitude' => -6.3493827, 'longitude' => 107.8162220,
            ],
            [
                'nama' => 'Puskesmas Binong',
                'alamat' => 'Binong, Kabupaten Subang',
                'kontak' => '0260557666', 'tipe' => 'Puskesmas', 'layanan' => 'Pemeriksaan HIV dan ARV Terbatas',
                'latitude' => -6.2431044, 'longitude' => 106.5779018,
            ],
            [
                'nama' => 'Puskesmas Blanakan',
                'alamat' => 'Blanakan, Kabupaten Subang',
                'kontak' => '0260558777', 'tipe' => 'Puskesmas', 'layanan' => 'Edukasi Pesisir, VCT Mobile',
                'latitude' => -6.2760585, 'longitude' => 107.6571593,
            ],
            [
                'nama' => 'Puskesmas Patokbeusi',
                'alamat' => 'Patokbeusi, Kabupaten Subang',
                'kontak' => '0260559888', 'tipe' => 'Puskesmas', 'layanan' => 'Konseling VCT, Pemeriksaan Lab',
                'latitude' => -6.3789909, 'longitude' => 107.5923071,
            ],
            [
                'nama' => 'Puskesmas Pabuaran',
                'alamat' => 'Pabuaran, Kabupaten Subang',
                'kontak' => '0260560999', 'tipe' => 'Puskesmas', 'layanan' => 'Konseling dan Rujukan Lanjutan',
                'latitude' => -6.1646349, 'longitude' => 106.0712044,
            ],
            [
                'nama' => 'Puskesmas Ciasem',
                'alamat' => 'Ciasem, Kabupaten Subang',
                'kontak' => '0260561000', 'tipe' => 'Puskesmas', 'layanan' => 'Pengambilan Obat ARV, Skrining',
                'latitude' => -6.3390103, 'longitude' => 107.6645165,
            ],
        ];

        foreach ($data as $item) {
            Faskes::create($item);
        }
    }
}