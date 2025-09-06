<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SourceFile extends Model
{
    protected $fillable = [
        'name',  
        'owner',
        // 'project_id', 
        'type',
        'lang',
        'template', 
    ];

    /**
     * Get the project that owns the source file.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class); // A source file belongs to a project
    }
    

}
