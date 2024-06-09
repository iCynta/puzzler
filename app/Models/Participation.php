<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participation extends Model
{
    use HasFactory;

    protected $fillable = ['participant_id', 'string_id'];

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function randomString()
    {
        return $this->belongsTo(RandomString::class, 'string_id');
    }
    
    public function participationDetail()
    {
        return $this->hasOne(ParticipationDetail::class);
    }
}


