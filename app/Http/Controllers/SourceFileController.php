<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;  
use App\Models\SourceFile;
use App\Models\Project;

use App\Models\SourceUnit;

use DOMDocument;
use DOMText;
use DOMElement;

class SourceFileController extends Controller
{
    //stores the uploaded file and its translation units
    public function store(Request $request)  
    {  
        // Validate the file  
        try {
            $request->validate([
            'project_name' => 'required',
            'lang' => 'required|in:ar,fa',
            'subject' => 'required',
            'file' => 'required|file|mimes:doc,docx,txt|max:10240', // Adjust validation rules as needed  
            ]);  
        } catch(\Illuminate\Validation\ValidationException $e) {
            $translatedErrors = collect($e->validator->errors()->toArray())
            ->map(function ($messages) {
                // Translate each error message
                return array_map(function ($message) {
                    return __($message); // Laravel's translation helper
                }, $messages);
            });
    
            return response()->json(['error' => $translatedErrors], 422);
        }        


        //get the uploaded file and convert it into html
        $file = $request->file('file');
        $path = $file->store('uploads', 'public');
        $absolutePath = realpath($_SERVER['DOCUMENT_ROOT'] . "/storage/app/public/" . $path);
        $mimeType = $file->getMimeType();
        if(strpos($mimeType, 'word')) {
            $outputFile = preg_replace('/\.[^.]+$/', '.html', $absolutePath);
            $nodePath = realpath($_SERVER['DOCUMENT_ROOT'] . '/node/convertToHTML.js');
            exec('node ' . escapeshellarg($nodePath) . ' ' . escapeshellarg($absolutePath) . ' ' . escapeshellarg($outputFile));
            $html = '<html> <head> <meta charset="UTF-8" /> </head> <body>' . file_get_contents($outputFile) . '</body> </html>';
        } elseif (strpos($mimeType,'text')) {
            $html = '<html> <head> <meta charset="UTF-8" /> </head> <body> <pre>' . file_get_contents($path) . '</pre> </body> </html>';
        }

        //extract the text nodes
        $elements = $this->extractTextNodes($html);
        
        //make the template with placeholders for each segment
        $index = 1;
        $finalSegments = [];
        foreach($elements as $element) {
            // Regular expression to split by sentence delimiters (., !, ?) and newlines
            $pattern = '/(?<!\.\.\.)(?<=[.!?])\s+|\n/';
            // Split the text into segments
            $segments = preg_split($pattern, $element['text'], -1, PREG_SPLIT_NO_EMPTY);
            // Trim whitespace from each segment
            $segments = array_map('trim', $segments);
            // Filter out any empty segments
            $segments = array_filter($segments);
            $segments = array_values($segments);
            foreach($segments as $segment) {
                $finalSegments[] = ['text' => $segment, 'type' => $element['tag'], 'sid' => $index];
                if(preg_match('/' . preg_quote($segment, '/') . '/', $html)) {
                    $html = preg_replace('/' . preg_quote($segment, '/') . '/', '<span id="STS_' . $index++ . '"></span>', $html, 1);
                } elseif(preg_match('/' . preg_quote(str_replace('&' , '&amp;', $segment), '/') . '/', $html)) {
                    $html = preg_replace('/' . preg_quote(str_replace('&' , '&amp;', $segment), '/') . '/', '<span id="STS_' . $index++ . '"></span>', $html, 1);
                }
            }            
        }
        
        //Store the file info in the database
        $fileType = $file->getClientOriginalExtension(); // Get the file type  
        $userId = auth()->id(); // Get the authenticated user's ID
        // $fileName = $fileName = $request->file('file')->getClientOriginalName();
        $lang = $request->input('lang');

        $sourceFile = SourceFile::create([
            'name' => $request->input('file_name'),
            'owner' => $userId,
            'type' => $fileType,
            'lang' => $lang,
            'template' => $html,
        ]);

        // Store the file in the file system with name of the fileId and in the folder of its lang
        $fileId = $sourceFile->id; // This is the ID of the newly created record
        exec('move ' . escapeshellarg($absolutePath) . ' ' . escapeshellarg(realpath($_SERVER['DOCUMENT_ROOT'] . '/storage/app/public/uploads/' . $lang . '/') . $fileId));
         
        //Store the segements
        //preparing the appropriate Model for storing the units
        if ($lang == "ar") {
            $tableName = "arabic_source_units";
            $targetlang = "fa";
        } elseif ($lang == "fa") {
            $tableName = "farsi_source_units";
            $targetlang = "ar";
        }
        $sourceUnit = new SourceUnit();
        $sourceUnit->setTableName($tableName);

        foreach($finalSegments as $segment) {
            $sourceUnit->create([
            'source_file' => $fileId,
            'text' => $segment['text'],
            'type' => $segment['type'],
            'internal_id' => $segment['sid'],
            'hash' => sha1($segment['text']),
            ]);
        }

                //save the project if new
        $existingProject = Project::where('name', $request->input('project_name'))
            ->where('user_id', auth()->id())
            ->first();

        if (!$existingProject) {
            Project::create([
                'name' => $request->input('project_name'),
                'subject' => $request->input('subject'),
                'description' => '',
                'source_language' => $lang,
                'target_language' => $targetlang,
                'status' => 'pending',
                'user_id' => auth()->id(),
            ]);
        }

        return response()->json([
            'fileId' => $fileId,
        ]);
    }

    //show the file for the user to be translated
    public function show($fileId) {

        $sourceFile = SourceFile::find($fileId);
        $lang = $sourceFile->lang;
        $template = $sourceFile->template;
        
        //remove the html, head and body elements from the templates and storing them in source and target html to be further changed afterwards
        $sourceHtml = $template;
        $targetHtml = $template;

        //reconstructing the source html
        if($lang == 'ar') {
            $sourceTable = 'arabic_source_units';
            $targetTable = 'farsi_target_units';
        } elseif ($lang == 'fa') {
            $sourceTable = 'farsi_source_units';
            $targetTable = 'arabic_target_units';
        }
        
        $sourceUnits = DB::table($sourceTable)->where(['source_file' => $fileId])->get();
        foreach($sourceUnits as $sourceUnit) {
            $sourceHtml = str_replace('<span id="STS_' . $sourceUnit->internal_id . '"></span>', '<span id="STS_' . $sourceUnit->internal_id . '">'. $sourceUnit->text .'</span>', $sourceHtml);
            $targetUnit = DB::table($targetTable)->where(['source_unit' => $sourceUnit->id])->get()->first();
            if($targetUnit) {
                $targetHtml = str_replace('<span id="STS_' . $sourceUnit->internal_id . '"></span>', '<span class="ts-wrapper translated"><span id="TTS_' . $sourceUnit->internal_id . '">'. $targetUnit->text .'</span><textarea name="TTS_' . $sourceUnit->internal_id . '">'. $targetUnit->text .'</textarea></span>', $targetHtml);
            } else {
                $targetHtml = str_replace('<span id="STS_' . $sourceUnit->internal_id . '"></span>', '<span class="ts-wrapper nontranslated"><span id="TTS_' . $sourceUnit->internal_id . '">'. $sourceUnit->text .'</span><textarea name="TTS_' . $sourceUnit->internal_id . '"></textarea></span>', $targetHtml);
            }
        }

        //Displaying the source and target elements in rows and columns next to each other
        // Load the HTML strings into DOMDocument objects
        $sourceDoc = new DOMDocument();
        $targetDoc = new DOMDocument();

        // Suppress warnings due to malformed HTML (if any)
        libxml_use_internal_errors(true);
        $sourceDoc->loadHTML($sourceHtml);
        $targetDoc->loadHTML($targetHtml);
        libxml_clear_errors();

        // Get the body elements of both documents
        $sourceBody = $sourceDoc->getElementsByTagName('body')->item(0);
        $targetBody = $targetDoc->getElementsByTagName('body')->item(0);

        // Iterate through the child nodes of the source and target bodies
        $sourceNodes = $sourceBody->childNodes;
        $targetNodes = $targetBody->childNodes;

        $finalHtml = '<div class="row">  <div class="col-r w-1/2"> <h1 class="main-headers">' . __('Source Text') . '</h1> </div> <div class="col-l w-1/2"> <h1 class="main-headers">' . __('Target Text') . '</h1> </div> </div>';

        for ($i = 0; $i < $sourceNodes->length; $i++) {
            $sourceNode = $sourceNodes->item($i);
            $targetNode = $targetNodes->item($i);

            // Skip non-element nodes (e.g., text nodes, comments)
            if ($sourceNode->nodeType !== XML_ELEMENT_NODE || $targetNode->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            $finalHtml .= '<div class="row">';

            // Source column
            $finalHtml .= '<div class="col-r w-1/2">';
            $finalHtml .= $sourceDoc->saveHTML($sourceNode);
            $finalHtml .=  '</div>';

            // Target column
            $finalHtml .= '<div class="col-l w-1/2">';
            $finalHtml .= $targetDoc->saveHTML($targetNode);
            $finalHtml .=  '</div>';

            $finalHtml .=  '</div>';
        }

        return view('translate', compact('finalHtml'));
    }

    //extracts the text nodes from an html string
    private function extractTextNodes($html) {
        // Create a new DOMDocument instance
        $dom = new DOMDocument();
    
        // Suppress warnings due to malformed HTML (if any)
        libxml_use_internal_errors(true);
    
        // Load the HTML content
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    
        // Clear any errors
        libxml_clear_errors();
    
        // Initialize an array to store text nodes and their tag types
        $textNodes = [];
    
        // Recursively traverse the DOM tree
        $traverse = function ($node) use (&$traverse, &$textNodes) {
            // Check if the node is a text node
            if ($node instanceof DOMText && trim($node->nodeValue) !== '') {
                // Get the parent element (tag) of the text node
                $parent = $node->parentNode;
    
                // Ensure the parent is an element node (e.g., <p>, <div>, etc.)
                if ($parent instanceof DOMElement) {
                    // Extract the tag name and text content
                    $tagName = $parent->tagName;
                    $textContent = trim($node->nodeValue);
    
                    // Store the data in the array
                    $textNodes[] = [
                        'tag' => $tagName,
                        'text' => $textContent,
                    ];
                }
            }
    
            // Recursively process child nodes
            if ($node->childNodes) {
                foreach ($node->childNodes as $child) {
                    $traverse($child);
                }
            }
        };
    
        // Start traversing from the document element
        $traverse($dom->documentElement);
    
        return $textNodes;
    }
}