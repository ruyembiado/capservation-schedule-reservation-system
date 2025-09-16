<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservations';
    protected $appends = ['capstones', 'capstone_titles'];
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
        return $this->belongsTo(Capstone::class, 'capstone_title_id');
    }

    public function reserveBy()
    {
        return $this->belongsTo(User::class, 'reserve_by');
    }

    public function schedule()
    {
        return $this->hasMany(Schedule::class, 'reservation_id');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'reservation_id');
    }

    public function reservationHistory()
    {
        return $this->hasMany(ReservationHistory::class, 'reservation_id');
    }

    public function getCapstonesAttribute()
    {
        if (!isset($this->attributes['capstones'])) {
            $capstoneIds = $this->parseCapstoneIds($this->capstone_title_id);
            $this->attributes['capstones'] = Capstone::whereIn('id', $capstoneIds)->get();
        }
        return $this->attributes['capstones'];
    }

    public function getCapstoneTitlesAttribute()
    {
        if (!isset($this->attributes['capstone_titles'])) {
            $this->attributes['capstone_titles'] = $this->capstones->pluck('title')->implode(', ');
        }
        return $this->attributes['capstone_titles'];
    }

    protected function parseCapstoneIds($ids)
    {
        if (is_null($ids)) {
            return [];
        }

        if (is_array($ids)) {
            return $ids;
        }

        if (is_string($ids) && str_starts_with($ids, '[')) {
            return json_decode($ids) ?? [];
        }

        return [$ids];
    }

    public function latestSchedule()
    {
        return $this->hasOne(Schedule::class, 'reservation_id')->latestOfMany('created_at');
    }
}
