<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    
    protected $table = 'settings';

    protected $fillable = [
        'dean_name',
        'dean_email',
        'it_head_name',
        'it_head_email',
        'cs_head_name',
        'cs_head_email',
        'is_head_name',
        'is_head_email',
    ];
}
