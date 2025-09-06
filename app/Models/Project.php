<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject',
        'description',
        'source_language',
        'target_language',
        'status',
        'due_date',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the source files for the project.
     */
    public function sourceFiles(): HasMany
    {
        return $this->hasMany(SourceFile::class); // A project has many source files
    }
}
