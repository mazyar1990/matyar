<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description']; // Important: For mass assignment protection

    public function projects() {
        return $this->belongsToMany(Project::class);  // Example: Many-to-many relationship with projects
    }

    
}