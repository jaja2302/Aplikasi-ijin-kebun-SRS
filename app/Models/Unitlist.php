<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unitlist extends Model
{
    use HasFactory;

    protected $table = 'unit';



    protected $fillable = [
        'nama'
    ];
    public $timestamps = false;
}
