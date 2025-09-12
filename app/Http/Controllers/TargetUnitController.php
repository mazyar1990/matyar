<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SourceFile;
use App\Models\ArabicSourceUnit;
use App\Models\FarsiSourceUnit;
use App\Models\TargetUnit;


class TargetUnitController extends Controller
{
    public function store(Request $request) {
        // Validate the request
        $validatedData = $request->validate([
            'text' => 'required|string', // text must be a non-empty string
            'fileId' => 'required|numeric', // fileId must be a number
            'sUnitId' => 'required|numeric', // sUnitId must be a number
        ]);
        
        // Access validated data
        $text = $validatedData['text'];
        $fileId = $validatedData['fileId'];
        $sUnitInternalId = $validatedData['sUnitId'];

        $sourceFile = SourceFile::find($fileId);
        $lang = $sourceFile->lang;

        //reconstructing the source html
        if($lang == 'ar') {
            $sUnit = new ArabicSourceUnit();
            $sourceTable = 'arabic_source_units';
            $targetTable = 'farsi_target_units';
        } elseif ($lang == 'fa') {
            $sUnit = new FarsiSourceUnit();
            $sourceTable = 'farsi_source_units';
            $targetTable = 'arabic_target_units';
        }

        $sUnitId = $sUnit->where('source_file', $fileId)  
                            ->where('internal_id', $sUnitInternalId)  
                            ->pluck('id')  
                            ->first();
        
        $targetUnit = new TargetUnit();
        $targetUnit->setTableName($targetTable);

        try {  
            $newUnit = $targetUnit->create([  
                'source_unit' => $sUnitId,  
                'text' => $text  
            ]);  
        
            // Return a successful response  
            return response()->json([  
                'success' => true,  
                'message' => 'Insertion successful.',  
                'data' => $newUnit // Optionally return the created object  
            ], 201); // 201 Created  
        } catch (\Exception $e) {  
            // Return an error response  
            return response()->json([  
                'success' => false,  
                'message' => 'Insertion failed: ' . $e->getMessage()  
            ], 500); // 500 Internal Server Error  
        }

    
    }

}