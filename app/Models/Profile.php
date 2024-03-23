<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'number',
        'about_me',
        'photo',
        'cv_link',
        'github_link',
        'linkedin_link',
        'twitter_link',

    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
