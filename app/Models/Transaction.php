<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'group_id',
        'group_name',
        'members',
        'program',
        'type_of_defense',
        'transaction_code',
    ];

    public function group()
    {
        return $this->belongsTo(User::class, 'group_id');
    }
}
