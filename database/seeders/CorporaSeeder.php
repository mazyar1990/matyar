<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Corpus; // Make sure you have a Corpus model

class CorporaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Option 1: Using Model Factories (Recommended for larger datasets and testing)
        // You can define factories for your Corpus model to generate more varied data.
        // See: https://laravel.com/docs/10.x/database-testing#defining-model-factories

        // Example using factories (if you have them defined):
        // Corpus::factory(10)->create(); // Creates 10 corpora


        // Option 2: Manually inserting data (Good for smaller, fixed datasets)
        Corpus::insert([
            [
                'name' => 'wiki_titles_ar_fa',
                'source_lang' => 'ar',
                'target_lang' => 'fa',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'wiki_titles_fa_ar',
                'source_lang' => 'fa',
                'target_lang' => 'ar',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

    }
}