<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class ArabicSourceUnit extends Model
{
    use HasFactory, Searchable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'arabic_source_units';

    protected $fillable = [
        'source_file',  
        'text',  
        'type',
        'internal_id',
        'hash',
    ];    

    /**
     * Get the name of the index associated with the model for Scout.
     */
    public function searchableAs(): string
    {
        return 'arabic_source_units_index';
    }

    /**
     * Get the indexable data array for the model.
     * This defines what data is sent to Meilisearch.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        // Only the 'id' and 'text' columns will be indexed.
        return [
            'id'   => $this->id,
            'text' => $this->text,
        ];
    }
}

