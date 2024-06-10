<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Participation extends Model
{
    use HasFactory;

    protected $fillable = ['participant_id', 'string_id'];
    
    // Get the date in presentable mode
    public function getFormattedCreatedAtAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->format('F j, Y, g:i a');
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function randomString()
    {
        return $this->belongsTo(RandomString::class, 'string_id');
    }
    
    public function detail()
    {
        return $this->hasOne(ParticipationDetail::class);
    }

}


