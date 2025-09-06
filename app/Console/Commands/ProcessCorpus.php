<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProcessCorpus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'corpus:process {corpusTitle}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process a corpus by reading source and target files and storing them in the database.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $corpusTitle = $this->argument('corpusTitle');

        // Create the table
        $this->createTable($corpusTitle);

        // Read and store source and target units
        $sourcePath = storage_path("app/private/corpora/{$corpusTitle}/source.txt");
        $targetPath = storage_path("app/private/corpora/{$corpusTitle}/target.txt");

        $this->readAndStorePairs($sourcePath, $targetPath, $corpusTitle);

        $this->info("Corpus {$corpusTitle} processed successfully.");
        return 0;
    }

    protected function createTable($tableName)
    {
        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function ($table) {
                $table->increments('id');
                $table->text('source_unit');
                $table->text('target_unit');
                $table->timestamps();
            });
            $this->info("Table {$tableName} created successfully.");
        } else {
            $this->info("Table {$tableName} already exists.");
        }
    }

    protected function readAndStorePairs($sourcePath, $targetPath, $tableName)
    {
        // Check if both files exist
        if (!file_exists($sourcePath)) {
            $this->error("Source file not found: {$sourcePath}");
            return;
        }
        if (!file_exists($targetPath)) {
            $this->error("Target file not found: {$targetPath}");
            return;
        }

        // Open both files for reading
        $sourceFile = fopen($sourcePath, 'r');
        $targetFile = fopen($targetPath, 'r');

        // Read lines from both files simultaneously
        while (($sourceLine = fgets($sourceFile)) !== false && ($targetLine = fgets($targetFile)) !== false) {
            // Trim newlines and whitespace
            $sourceLine = trim($sourceLine);
            $targetLine = trim($targetLine);

            // Insert the pair into the table
            DB::table($tableName)->insert([
                'source_unit' => $sourceLine,
                'target_unit' => $targetLine,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Close the files
        fclose($sourceFile);
        fclose($targetFile);

        $this->info("Data from source and target files has been stored in {$tableName}.");
    }
}