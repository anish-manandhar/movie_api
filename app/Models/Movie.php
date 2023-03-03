<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'release_date',
        'poster',
        'is_published',
    ];

    public function poster(){
        return $this->belongsTo(User::class, 'poster');
    }
}
