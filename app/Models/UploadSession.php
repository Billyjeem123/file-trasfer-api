<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadSession extends Model
{
    use HasFactory;

    protected $fillable = ['token', 'email_to_notify', 'expires_at', 'notified'];

    public function files() {
        return $this->hasMany(UploadFile::class);
    }
}
