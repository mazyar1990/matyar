<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    //convert the string sent to it into word doc and sends the doc back to client side to be downloaded
    public function convertToDoc(Request $request)
    {
        // Validate the input
        $request->validate([
            'html' => 'required|string',
        ]);

        // Get the string from the client
        $content = $request->input('html');

        // Save the file temporarily (optional)
        $htmlFile = 'document_' . time();
        $docFile = $htmlFile;

        Storage::disk('local')->put($htmlFile . '.html',  $content);
        
        $absolutePath = realpath($_SERVER['DOCUMENT_ROOT'] . '/Storage/app/private/' . $htmlFile . '.html');        
        $outputFile = realpath($_SERVER['DOCUMENT_ROOT'] . '/Storage/app/private') . DIRECTORY_SEPARATOR . $docFile . '.docx';

        exec('pandoc ' . escapeshellarg($absolutePath) . ' -o ' . escapeshellarg($outputFile) . ' 2>&1', $output, $return_var);
        
        if (file_exists($outputFile)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($outputFile));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($outputFile));
            ob_clean();
            flush();
            readfile($outputFile);
            exit;
        }
    }
}
