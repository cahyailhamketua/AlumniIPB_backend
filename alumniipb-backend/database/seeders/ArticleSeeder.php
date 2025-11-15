<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use Illuminate\Support\Facades\Hash;
use League\Csv\Reader;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        // Baca file CSV
        $csv = Reader::createFromPath(database_path('seeders/data/articles.csv'), 'r');
        $csv->setHeaderOffset(0); // Baris pertama sebagai header

        // Loop setiap row CSV
        foreach ($csv->getRecords() as $record) {
            Article::create([
                'judul'        => $record['judul'],
                'deskripsi'    => $record['deskripsi'],
                'tanggal'      => $record['tanggal'],
                'kategori'     => $record['kategori'],
                'isi_artikel'  => $record['isi_artikel'],
                'image'        => $record['image'],
            ]);
        }
    }
}


