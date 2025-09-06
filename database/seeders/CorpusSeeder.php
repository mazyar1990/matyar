<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        Subject::create([
            'name' => 'wiki_titles_ar_fa',
            'source_lang' => 'ar',
            'target_lang' => 'fa'
        ]);

        Subject::create([
            'name' => 'wiki_titles_fa_ar',
            'source_lang' => 'fa',
            'target_lang' => 'ar'
        ]);
        
    }
}