<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sistema extends Model
{
    protected $fillable = [
        'nome',
        'modulos',
        'status'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    protected $casts = [
        'modulos' => "json",
        'status' => "boolean"
    ];

    use HasFactory;
}
