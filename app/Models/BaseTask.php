<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseTask extends Model
{
    use HasFactory;

    protected $table = 'base_tasks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id' , 'title' , 'points' , 'complete_state' , 'session_id'
    ];
}
