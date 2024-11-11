<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LanguageFile extends Model
{
    protected $fillable = [
        'language_id',
        'path_file',
        'type',
        'md5_path_file',
    ];
    // Define the inverse relationship with Language
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
