<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class WikiTitle extends Model
{

    use HasFactory, Searchable;

    // Meilisearch will use this array to build its search documents.
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'arabic_text' => $this->arabic_text,
            'persian_text' => $this->persian_text,
        ];
    }
    //
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->longText('arabic_text');
            $table->longText('persian_text');
            // timestamps() are optional but good practice
            $table->timestamps();
        });
    }
}
