<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TargetUnit extends Model
{
    // Set the table name dynamically here
    protected $table;

    protected $fillable = [
        'source_unit',  
        'text',
    ];

    public function setTableName($tableName) {
        $this->table = $tableName;
    }
}
