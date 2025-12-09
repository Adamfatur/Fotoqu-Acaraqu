<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionGif extends Model
{
    use HasFactory;

    protected $fillable = [
        'photo_session_id',
        'filename',
        's3_path',
        'local_path',
        'file_size',
        'status',
    'progress',
    'step',
        'error_message',
    ];

    public function photoSession()
    {
        return $this->belongsTo(PhotoSession::class);
    }
}
