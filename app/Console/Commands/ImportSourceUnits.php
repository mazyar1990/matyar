<?php

namespace App\Console\Commands;

use App\Models\ArabicSourceUnit;
use App\Models\FarsiSourceUnit;
use Illuminate\Console\Command;

class ImportSourceUnits extends Command
{
    /**
     * The name and signature of the console command.
     * It accepts a required 'language' argument.
     *
     * @var string
     */
    protected $signature = 'matyar:import-source-units {language}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import source units for a specific language (arabic or farsi) into Scout';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $language = $this->argument('language');

        // Determine the correct model class based on the language argument
        $modelClass = match ($language) {
            'arabic' => ArabicSourceUnit::class,
            'farsi'  => FarsiSourceUnit::class,
            default  => null,
        };

        // Validate the input
        if (!$modelClass) {
            $this->error("Invalid language provided. Please use 'arabic' or 'farsi'.");
            return 1; // Return error
        }
        
        $this->info("Starting Scout import for model: {$modelClass}");

        // We can now use Laravel's built-in scout:import command,
        // but pass our specific model class to it.
        $this->call('scout:import', [
            'model' => $modelClass
        ]);
        
        $this->info("Successfully imported all records for {$modelClass}.");
        return 0; // Return success
    }
}

