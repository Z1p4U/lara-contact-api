<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favourites', 'contact_id', 'user_id')->withTimestamps();
    }

    protected $fillable = ["name", "country_code", "phone_number", "user_id"];
}
