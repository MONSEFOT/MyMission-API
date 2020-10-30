<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    use HasFactory;

    protected $table = 'challenges';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id' , 'hero_instagram' , 'hero_target' , 'points' , 'in_leader_board' , 'is_verefied' , 'user_id' , 'created_at' , 'updated_at',
    ];
}
