<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

// សម្រាប់ទម្រង់ PHP Attributes របស់ Laravel 11
#[Fillable(['key', 'value'])]
class Setting extends Model
{
    // ឬប្រើប្រាស់ទម្រង់ Property ធម្មតាខាងក្រោម (ដើម្បីធានាសុវត្ថិភាពខ្ពស់)
    protected $fillable = [
        'key',
        'value'
    ];
}
