<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = [
        'name',
        'iso_639_code',
        'flag',
    ];
    // Define the relationship with LanguageFile
    public function files()
    {
        return $this->hasMany(LanguageFile::class);
    }
}
