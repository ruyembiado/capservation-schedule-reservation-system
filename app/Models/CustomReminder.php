<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomReminder extends Model
{
    use HasFactory;

    protected $table = 'custom_reminders';

    protected $fillable = [
        'title_status',
        'message',
        'group_id',
        'defense_stage',
        'schedule_datetime',
    ];

    public function group()
    {
        return $this->belongsTo(User::class, 'group_id');
    }
}
