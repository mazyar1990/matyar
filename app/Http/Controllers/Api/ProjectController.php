<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;  

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate(['name' => 'required|string']);
        
        try {
            $project = auth()->user()
                ->projects()
                ->where('name', '=', $request->name)
                ->firstOrFail();
            
            // Get files with only id and name
            $filesForThisProject = DB::table('source_files')
                ->where('project_id', $project->id)
                ->select('id', 'name')
                ->get();
                    
            return response()->json([
                'success' => true,
                'project' => [
                    'id' => $project->id,
                    'name' => $project->name,
                    'subject' => $project->subject,
                    'files' => $filesForThisProject
                ]
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found',
                'subject' => null
            ], 404);
        }
    }
}
