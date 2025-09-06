<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SourceUnit extends Model
{
    // Set the table name dynamically here
    protected $table;

    protected $fillable = [
        'source_file',  
        'text',  
        'type',
        'internal_id',
        'hash',
    ];

    public function setTableName($tableName) {
        $this->table = $tableName;
    }
}
