<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $table = 'schedules';
    protected $fillable = [
        'group_id',
        'reservation_id',
        'schedule_date',
        'schedule_time',
        'schedule_category',
        'schedule_remarks',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'group_id');
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }
}
