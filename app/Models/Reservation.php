<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservations';
    protected $fillable = [
        'group_id',
        'capstone_title_id',
        'reserve_by',
        'status',
        'panelist_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'group_id');
    }

    public function capstone()
    {
        return $this->belongsTo(Capstone::class, 'capstone_id', 'id');
    }

    public function reserveBy()
    {
        return $this->belongsTo(User::class, 'reserve_by');
    }

    public function schedule()
    {
        return $this->hasOne(Schedule::class, 'reservation_id');
    }
}
