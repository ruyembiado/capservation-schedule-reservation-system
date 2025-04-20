<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Capstone extends Model
{
    use HasFactory;

    protected $table = 'capstones';
    protected $fillable = [
        'group_id',
        'title',
        'capstone_status',
        'title_status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'group_id');
    }
    
}
