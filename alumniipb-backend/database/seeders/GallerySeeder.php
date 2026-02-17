<?php

namespace Database\Seeders;

use App\Models\Gallery;
use Illuminate\Database\Seeder;

class GallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 10,
                'judul_galery' => 'Seminar AI dan Otomasi 2024',
                'deskripsi' => 'Seminar membahas perkembangan kecerdasan buatan dan dampaknya terhadap industri.',
                'tanggal' => '2024-05-30',
                'kategori' => 'seminar',
                'jumlah_peserta' => 110,
                'foto_kegiatan' => 'default.jpg',
                'lokasi' => 'Depok',
            ],
            [
                'id' => 9,
                'judul_galery' => 'Event Expo Kreatif 2023',
                'deskripsi' => 'Pameran produk kreatif dari pelaku UMKM dan mahasiswa.',
                'tanggal' => '2023-11-22',
                'kategori' => 'event',
                'jumlah_peserta' => 220,
                'foto_kegiatan' => 'default.jpg',
                'lokasi' => 'Malang',
            ],
            [
                'id' => 8,
                'judul_galery' => 'CSR Donor Darah 2023',
                'deskripsi' => 'Aksi sosial donor darah bersama PMI untuk membantu pasien di rumah sakit.',
                'tanggal' => '2023-08-17',
                'kategori' => 'csr',
                'jumlah_peserta' => 130,
                'foto_kegiatan' => 'default.jpg',
                'lokasi' => 'Solo',
            ],
            [
                'id' => 7,
                'judul_galery' => 'Seminar Inovasi Pertanian 2023',
                'deskripsi' => 'Diskusi inovasi dalam sektor pertanian modern berbasis teknologi.',
                'tanggal' => '2023-02-28',
                'kategori' => 'seminar',
                'jumlah_peserta' => 90,
                'foto_kegiatan' => 'default.jpg',
                'lokasi' => 'Bogor',
            ],
            [
                'id' => 6,
                'judul_galery' => 'Event Alumni Gathering 2022',
                'deskripsi' => 'Acara temu alumni lintas angkatan untuk memperkuat jejaring profesional.',
                'tanggal' => '2022-12-03',
                'kategori' => 'event',
                'jumlah_peserta' => 180,
                'foto_kegiatan' => 'default.jpg',
                'lokasi' => 'Jakarta',
            ],
            [
                'id' => 5,
                'judul_galery' => 'CSR Lingkungan Bersih 2022',
                'deskripsi' => 'Kegiatan penanaman pohon dan bersih-bersih sungai bersama masyarakat.',
                'tanggal' => '2022-09-12',
                'kategori' => 'csr',
                'jumlah_peserta' => 120,
                'foto_kegiatan' => 'default.jpg',
                'lokasi' => 'Semarang',
            ],
            [
                'id' => 4,
                'judul_galery' => 'Seminar Digital Marketing 2022',
                'deskripsi' => 'Pelatihan strategi digital marketing modern bagi pelaku bisnis lokal.',
                'tanggal' => '2022-04-20',
                'kategori' => 'seminar',
                'jumlah_peserta' => 100,
                'foto_kegiatan' => 'default.jpg',
                'lokasi' => 'Surabaya',
            ],
            [
                'id' => 3,
                'judul_galery' => 'Event Startup Day 2021',
                'deskripsi' => 'Ajang pertemuan startup muda Indonesia dengan investor dan mentor profesional.',
                'tanggal' => '2021-11-05',
                'kategori' => 'event',
                'jumlah_peserta' => 200,
                'foto_kegiatan' => 'default.jpg',
                'lokasi' => 'Jakarta',
            ],
            [
                'id' => 2,
                'judul_galery' => 'CSR Peduli Pendidikan 2021',
                'deskripsi' => 'Program CSR memberikan bantuan peralatan sekolah untuk siswa kurang mampu.',
                'tanggal' => '2021-07-10',
                'kategori' => 'csr',
                'jumlah_peserta' => 150,
                'foto_kegiatan' => 'default.jpg',
                'lokasi' => 'Yogyakarta',
            ],
            [
                'id' => 1,
                'judul_galery' => 'Seminar Teknologi Hijau 2021',
                'deskripsi' => 'Seminar membahas inovasi teknologi ramah lingkungan untuk masa depan berkelanjutan.',
                'tanggal' => '2021-03-15',
                'kategori' => 'seminar',
                'jumlah_peserta' => 85,
                'foto_kegiatan' => 'default.jpg',
                'lokasi' => 'Bandung',
            ],
            [
                'id' => 15,
                'judul_galery' => 'test kedua',
                'deskripsi' => 'testing mulu',
                'tanggal' => '2020-10-05',
                'kategori' => 'csr',
                'jumlah_peserta' => 150,
                'foto_kegiatan' => 'gallery_photos/IFKxsS1ZSsepX28IoQiMVGv7TBUeiTUtgbQ4cbZ8.jpg',
                'lokasi' => 'Jakarta selatan',
            ],
            [
                'id' => 16,
                'judul_galery' => 'test kedua',
                'deskripsi' => 'testing mulu',
                'tanggal' => '2020-10-05',
                'kategori' => 'csr',
                'jumlah_peserta' => 150,
                'foto_kegiatan' => 'gallery_photos/780wpRKCyC9GW32HSnWM3DsEissYBvoHu5OJJA1h.jpg',
                'lokasi' => 'Jakarta selatan',
            ],
            [
                'id' => 17,
                'judul_galery' => 'test ketiga',
                'deskripsi' => 'testing mulu',
                'tanggal' => '2020-10-05',
                'kategori' => 'csr',
                'jumlah_peserta' => 150,
                'foto_kegiatan' => 'gallery_photos/Ta9xSu5LddYokTJTzknvk2C2MYzbQAuG8hQIhKTS.png',
                'lokasi' => 'Jakarta selatan',
            ],
            [
                'id' => 18,
                'judul_galery' => 'test ketiga',
                'deskripsi' => 'testing mulu',
                'tanggal' => '2020-10-05',
                'kategori' => 'csr',
                'jumlah_peserta' => 150,
                'foto_kegiatan' => 'gallery_photos/EtavRjPl8WBGox3mgxzD7EGDBWYzZ1m5l4AtuvYr.png',
                'lokasi' => 'Jakarta selatan',
            ],
            [
                'id' => 19,
                'judul_galery' => 'test terusss',
                'deskripsi' => 'testing mulu',
                'tanggal' => '2020-10-05',
                'kategori' => 'csr',
                'jumlah_peserta' => 150,
                'foto_kegiatan' => 'gallery_photos/J6jGUNLVudpt9E85oZNXRNVos3p91EkUUVYR0kxk.jpg',
                'lokasi' => 'Jakarta selatan',
            ],
        ];

        foreach ($data as $item) {
            Gallery::updateOrCreate(
                ['id' => $item['id']],
                $item
            );
        }
    }
}

