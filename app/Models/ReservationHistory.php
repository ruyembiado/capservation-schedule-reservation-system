<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationHistory extends Model
{
    use HasFactory;

    protected $table = 'reservation_history';

    protected $fillable = [
        'reservation_id',
        'status',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }
    
}
