<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\GameGroup;
use App\Models\GameQuestion;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil admin pembuat
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        if (! $admin) {
            $this->command->warn('Admin tidak ditemukan. Jalankan DatabaseSeeder dasar dulu.');
            return;
        }

        // Ambil 3 siswa pertama sebagai leader
        $leaders = Student::orderBy('id')->limit(3)->get();
        if ($leaders->count() < 3) {
            $this->command->warn('Butuh minimal 3 siswa. Jalankan DatabaseSeeder dasar dulu.');
            return;
        }

        // === Buat Game ===
        $game = Game::create([
            'name'            => 'Kuis Jarimath — Bab Persiapan',
            'status'          => Game::STATUS_DRAFT,
            'created_by'      => $admin->id,
            'session_status'  => Game::SS_LOBBY,
            'current_phase'   => 1,
            'current_q_order' => 0,
        ]);

        // === Buat 3 Kelompok ===
        $groups = [
            ['name' => 'A', 'leader' => $leaders[0], 'color' => '#ffcc33'],
            ['name' => 'B', 'leader' => $leaders[1], 'color' => '#38bdf8'],
            ['name' => 'C', 'leader' => $leaders[2], 'color' => '#ff4757'],
        ];

        foreach ($groups as $g) {
            GameGroup::create([
                'game_id'   => $game->id,
                'name'      => $g['name'],
                'leader_id' => $g['leader']->id,
                'color'     => $g['color'],
                'score'     => 0,
            ]);
        }

        // === Buat 30 Soal (3 fase × 10 soal) ===
        $questions = [
            // ===== FASE 1: Matematika Dasar (10 soal) =====
            ['phase' => 1, 'order' => 1,  'q' => 'Berapakah hasil 7 + 5?', 'a' => '12', 'pts' => 10],
            ['phase' => 1, 'order' => 2,  'q' => 'Berapakah hasil 15 - 8?', 'a' => '7',  'pts' => 10],
            ['phase' => 1, 'order' => 3,  'q' => 'Berapakah hasil 6 × 9?', 'a' => '54', 'pts' => 10],
            ['phase' => 1, 'order' => 4,  'q' => 'Berapakah hasil 72 : 8?', 'a' => '9',  'pts' => 10],
            ['phase' => 1, 'order' => 5,  'q' => 'Berapakah hasil 25 + 17?', 'a' => '42', 'pts' => 10],
            ['phase' => 1, 'order' => 6,  'q' => 'Berapakah hasil 100 - 37?', 'a' => '63', 'pts' => 10],
            ['phase' => 1, 'order' => 7,  'q' => 'Berapakah hasil 11 × 11?', 'a' => '121', 'pts' => 10],
            ['phase' => 1, 'order' => 8,  'q' => 'Berapakah hasil 81 : 9?', 'a' => '9',  'pts' => 10],
            ['phase' => 1, 'order' => 9,  'q' => 'Berapakah hasil 45 + 28?', 'a' => '73', 'pts' => 10],
            ['phase' => 1, 'order' => 10, 'q' => 'Berapakah hasil 9 × 8?',  'a' => '72', 'pts' => 10],

            // ===== FASE 2: Pengetahuan Umum (10 soal) =====
            ['phase' => 2, 'order' => 1,  'q' => 'Ibukota Indonesia adalah?', 'a' => 'Jakarta', 'pts' => 15],
            ['phase' => 2, 'order' => 2,  'q' => 'Planet terdekat dengan matahari?', 'a' => 'Merkurius', 'pts' => 15],
            ['phase' => 2, 'order' => 3,  'q' => 'Hari Kemerdekaan Indonesia tanggal?', 'a' => '17 Agustus', 'pts' => 15],
            ['phase' => 2, 'order' => 4,  'q' => 'Berapa jumlah provinsi di Indonesia saat ini?', 'a' => '38', 'pts' => 15],
            ['phase' => 2, 'order' => 5,  'q' => 'Pencipta lagu Indonesia Raya?', 'a' => 'Wage Rudolf Supratman', 'pts' => 15],
            ['phase' => 2, 'order' => 6,  'q' => 'Benua terbesar di dunia?', 'a' => 'Asia', 'pts' => 15],
            ['phase' => 2, 'order' => 7,  'q' => 'Samudra terbesar di dunia?', 'a' => 'Pasifik', 'pts' => 15],
            ['phase' => 2, 'order' => 8,  'q' => 'Gunung tertinggi di Indonesia?', 'a' => 'Puncak Jaya', 'pts' => 15],
            ['phase' => 2, 'order' => 9,  'q' => 'Bahasa resmi negara Jepang?', 'a' => 'Jepang', 'pts' => 15],
            ['phase' => 2, 'order' => 10, 'q' => 'Lambang negara Indonesia?', 'a' => 'Garuda Pancasila', 'pts' => 15],

            // ===== FASE 3: Sains & Campuran (10 soal) =====
            ['phase' => 3, 'order' => 1,  'q' => 'Rumus kimia untuk air adalah?', 'a' => 'H2O', 'pts' => 20],
            ['phase' => 3, 'order' => 2,  'q' => 'Gas yang dihirup manusia untuk bernapas?', 'a' => 'Oksigen', 'pts' => 20],
            ['phase' => 3, 'order' => 3,  'q' => 'Berapa jumlah tulang pada tubuh manusia dewasa?', 'a' => '206', 'pts' => 20],
            ['phase' => 3, 'order' => 4,  'q' => 'Planet terbesar di tata surya?', 'a' => 'Jupiter', 'pts' => 20],
            ['phase' => 3, 'order' => 5,  'q' => 'Siapa ilmuwan yang menemukan teori relativitas?', 'a' => 'Albert Einstein', 'pts' => 20],
            ['phase' => 3, 'order' => 6,  'q' => 'Satuan energi dalam SI adalah?', 'a' => 'Joule', 'pts' => 20],
            ['phase' => 3, 'order' => 7,  'q' => 'Berapa kecepatan cahaya (km/detik, dibulatkan)?', 'a' => '300000', 'pts' => 20],
            ['phase' => 3, 'order' => 8,  'q' => 'Organ tubuh manusia yang memompa darah?', 'a' => 'Jantung', 'pts' => 20],
            ['phase' => 3, 'order' => 9,  'q' => 'Proses tumbuhan membuat makanan disebut?', 'a' => 'Fotosintesis', 'pts' => 20],
            ['phase' => 3, 'order' => 10, 'q' => 'Berapa jumlah sisi pada segitiga?', 'a' => '3', 'pts' => 20],
        ];

        foreach ($questions as $q) {
            GameQuestion::create([
                'game_id'       => $game->id,
                'phase'         => $q['phase'],
                'order'         => $q['order'],
                'question_text' => $q['q'],
                'answer_key'    => $q['a'],
                'points'        => $q['pts'],
            ]);
        }

        $this->command->info('✅ Game seeder selesai!');
        $this->command->info("   Game: {$game->name} (ID: {$game->id})");
        $this->command->info('   Kelompok: A, B, C (3 leader)');
        $this->command->info('   Soal: 30 (3 fase × 10)');
        $this->command->info('');
        $this->command->info('Leader kelompok:');
        foreach ($groups as $g) {
            $this->command->info("   - Kelompok {$g['name']}: {$g['leader']->name} (ID: {$g['leader']->id_siswa})");
        }
    }
}
