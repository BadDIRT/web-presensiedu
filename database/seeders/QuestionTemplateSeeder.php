<?php

namespace Database\Seeders;

use App\Models\QuestionTemplate;
use App\Models\QuestionTemplateItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuestionTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        if (! $admin) return;

        $template = QuestionTemplate::create([
            'name'        => 'Kuis Campuran Dasar',
            'description' => '30 soal campuran: matematika, pengetahuan umum, sains',
            'created_by'  => $admin->id,
        ]);

        $items = [
            // Fase 1: Matematika
            [1, 1, 'Berapakah hasil 7 + 5?', '12', 10],
            [1, 2, 'Berapakah hasil 15 - 8?', '7', 10],
            [1, 3, 'Berapakah hasil 6 × 9?', '54', 10],
            [1, 4, 'Berapakah hasil 72 : 8?', '9', 10],
            [1, 5, 'Berapakah hasil 25 + 17?', '42', 10],
            [1, 6, 'Berapakah hasil 100 - 37?', '63', 10],
            [1, 7, 'Berapakah hasil 11 × 11?', '121', 10],
            [1, 8, 'Berapakah hasil 81 : 9?', '9', 10],
            [1, 9, 'Berapakah hasil 45 + 28?', '73', 10],
            [1, 10, 'Berapakah hasil 9 × 8?', '72', 10],
            // Fase 2: Pengetahuan Umum
            [2, 1, 'Ibukota Indonesia adalah?', 'Jakarta', 15],
            [2, 2, 'Planet terdekat dengan matahari?', 'Merkurius', 15],
            [2, 3, 'Hari Kemerdekaan Indonesia tanggal?', '17 Agustus', 15],
            [2, 4, 'Berapa jumlah provinsi di Indonesia saat ini?', '38', 15],
            [2, 5, 'Pencipta lagu Indonesia Raya?', 'Wage Rudolf Supratman', 15],
            [2, 6, 'Benua terbesar di dunia?', 'Asia', 15],
            [2, 7, 'Samudra terbesar di dunia?', 'Pasifik', 15],
            [2, 8, 'Gunung tertinggi di Indonesia?', 'Puncak Jaya', 15],
            [2, 9, 'Bahasa resmi negara Jepang?', 'Jepang', 15],
            [2, 10, 'Lambang negara Indonesia?', 'Garuda Pancasila', 15],
            // Fase 3: Sains
            [3, 1, 'Rumus kimia untuk air adalah?', 'H2O', 20],
            [3, 2, 'Gas yang dihirup manusia untuk bernapas?', 'Oksigen', 20],
            [3, 3, 'Berapa jumlah tulang pada tubuh manusia dewasa?', '206', 20],
            [3, 4, 'Planet terbesar di tata surya?', 'Jupiter', 20],
            [3, 5, 'Siapa ilmuwan yang menemukan teori relativitas?', 'Albert Einstein', 20],
            [3, 6, 'Satuan energi dalam SI adalah?', 'Joule', 20],
            [3, 7, 'Berapa kecepatan cahaya (km/detik, dibulatkan)?', '300000', 20],
            [3, 8, 'Organ tubuh manusia yang memompa darah?', 'Jantung', 20],
            [3, 9, 'Proses tumbuhan membuat makanan disebut?', 'Fotosintesis', 20],
            [3, 10, 'Berapa jumlah sisi pada segitiga?', '3', 20],
        ];

        foreach ($items as $item) {
            QuestionTemplateItem::create([
                'template_id'   => $template->id,
                'phase'         => $item[0],
                'order'         => $item[1],
                'question_text' => $item[2],
                'answer_key'    => $item[3],
                'points'        => $item[4],
            ]);
        }

        $this->command->info("Template '{$template->name}' dibuat dengan 30 soal.");
    }
}
