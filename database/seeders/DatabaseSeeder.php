<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Director;
use App\Models\CreditCard;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $directorsData = [
            ['name' => 'JEFFRY HARYADI', 'position' => 'DIREKTUR UTAMA', 'card' => '5534-7901-0070-6704'],
            ['name' => 'SRI AININ MUKTIRIZKA', 'position' => 'DIREKTUR SDM DAN HUKUM', 'card' => '5534-7901-0078-5708'],
            ['name' => 'HELMI IMAM SATRIYONO', 'position' => 'DIREKTUR KEUANGAN', 'card' => '5534-7901-0070-6506'],
            ['name' => 'KHAIDIR ABDURRAHMAN', 'position' => 'DIREKTUR INVESTASI', 'card' => '5534-7901-0085-2706'],
        ];

        foreach ($directorsData as $d) {
            $director = Director::firstOrCreate(
                ['name' => $d['name']],
                [
                    'position' => $d['position'],
                    'slug' => Str::slug($d['name'])
                ]
            );

            CreditCard::firstOrCreate(
                ['card_number' => $d['card']],
                ['director_id' => $director->id]
            );
        }
    }
}