<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipationDetail extends Model
{
    use HasFactory;

    protected $table = 'participation_details'; // Define the table name

    protected $fillable = ['participation_id', 'words_scores']; // Define fillable fields

    public function participation()
    {
        return $this->belongsTo(Participation::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
}
