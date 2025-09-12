<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // Don't forget to import Log

class DocumentController extends Controller
{
    /**
     * Converts an HTML string into a Word DOCX file and serves it for download.
     * Requires Pandoc to be installed on the server.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function convertToDoc(Request $request)
    {
        // 1. Validate the input
        $request->validate([
            'html' => 'required|string',
        ]);

        $content = $request->input('html');

        // Generate unique filenames for temporary files
        $timestamp = now()->timestamp;
        $inputFileName = 'temp_document_' . $timestamp . '.html';
        $outputFileName = 'generated_document_' . $timestamp . '.docx';
        $downloadFileName = 'GeneratedDocument_' . date('Ymd_His') . '.docx'; // Name for the client download

        // 2. Store the HTML content temporarily in Laravel's local storage
        // By default, 'local' disk points to storage/app
        try {
            Storage::disk('local')->put($inputFileName, $content);
            $inputPath = Storage::disk('local')->path($inputFileName); // Absolute path to the HTML file
            $outputPath = Storage::disk('local')->path($outputFileName); // Absolute path for the output DOCX file
        } catch (\Exception $e) {
            Log::error("Failed to save temporary HTML file: " . $e->getMessage());
            return response()->json(['error' => 'Server error: Could not save HTML content.'], 500);
        }

        // 3. Execute Pandoc command
        // Ensure 'pandoc' is installed on your server and accessible in the system's PATH.
        // If not in PATH, you might need to specify its full path, e.g., '/usr/local/bin/pandoc'
        $pandocCommand = 'pandoc';
        $command = sprintf('%s %s -o %s 2>&1',
            escapeshellarg($pandocCommand),
            escapeshellarg($inputPath),
            escapeshellarg($outputPath)
        );

        $output = [];
        $return_var = 0;

        Log::info("Executing Pandoc command: " . $command);
        exec($command, $output, $return_var);

        // 4. Handle Pandoc execution result
        if ($return_var !== 0) {
            // Pandoc failed
            Log::error('Pandoc conversion failed.', [
                'command' => $command,
                'output' => implode("\n", $output),
                'return_var' => $return_var,
                'input_file' => $inputPath,
                'output_file_expected' => $outputPath
            ]);

            // Clean up the temporary input HTML file
            Storage::disk('local')->delete($inputFileName);

            return response()->json(['error' => 'Failed to convert HTML to DOCX. Pandoc command failed. Check server logs.'], 500);
        }

        // 5. Check if the output DOCX file was created and has content
        if (Storage::disk('local')->exists($outputFileName) && Storage::disk('local')->size($outputFileName) > 0) {
            // Get the file contents and size
            $fileContents = Storage::disk('local')->get($outputFileName);
            $fileSize = Storage::disk('local')->size($outputFileName);

            // 6. Clean up temporary files immediately
            Storage::disk('local')->delete([$inputFileName, $outputFileName]);

            // 7. Prepare and return the download response using Laravel's response helper
            return response($fileContents, 200)
                ->header('Content-Description', 'File Transfer')
                ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') // Correct MIME type for .docx
                ->header('Content-Disposition', 'attachment; filename="' . $downloadFileName . '"')
                ->header('Content-Transfer-Encoding', 'binary')
                ->header('Expires', '0')
                ->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
                ->header('Pragma', 'public')
                ->header('Content-Length', $fileSize);

        } else {
            // Output file not found or is empty
            Log::error('Pandoc output file not found or is empty after conversion.', [
                'command' => $command,
                'output' => implode("\n", $output),
                'return_var' => $return_var,
                'input_file' => $inputPath,
                'output_file_checked' => $outputPath,
                'file_exists' => Storage::disk('local')->exists($outputFileName),
                'file_size' => Storage::disk('local')->exists($outputFileName) ? Storage::disk('local')->size($outputFileName) : 'N/A'
            ]);

            // Clean up any remaining temporary files
            Storage::disk('local')->delete($inputFileName);
            if (Storage::disk('local')->exists($outputFileName)) {
                 Storage::disk('local')->delete($outputFileName);
            }

            return response()->json(['error' => 'Conversion failed: output file was not created or is empty.'], 500);
        }
    }
}