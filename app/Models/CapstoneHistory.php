<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CapstoneHistory extends Model
{
    use HasFactory;

    protected $table = 'capstone_histories';
    protected $fillable = [
        'capstone_id',
        'user_id',
        'old_capstone_name'
    ];

    public function capstone()
    {
        return $this->belongsTo(Capstone::class, 'capstone_id');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
