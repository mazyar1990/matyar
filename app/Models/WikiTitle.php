<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class WikiTitle extends Model
{
    use HasFactory, Searchable;

    /**
     * The table associated with the model.
     * It's good practice to explicitly define this.
     *
     * @var string
     */
    protected $table = 'wiki_titles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'arabic_text',
        'persian_text',
    ];

    /**
     * Get the indexable data array for the model.
     * Meilisearch will use this array to build its search documents.
     *
     * @return array
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => (int) $this->id, // Casting id to integer is recommended for Meilisearch
            'arabic_text' => $this->arabic_text,
            'persian_text' => $this->persian_text,
        ];
    }
}
