<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EncryptedFiles extends Model
{
    protected $fillable = [
        'fs_name',
        'name',
        'added_by',
    ];
}
