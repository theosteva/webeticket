<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketCategory extends Model
{
    protected $fillable = [
        'name',
        'urgensi',
        'tipe',
        'sla_hours',
    ];
}
