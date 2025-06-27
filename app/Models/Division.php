<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function users()
    {
        return $this->belongsToMany(\App\Models\User::class);
    }

    public function tickets()
    {
        return $this->belongsToMany(\App\Models\Ticket::class, 'division_ticket');
    }
}
