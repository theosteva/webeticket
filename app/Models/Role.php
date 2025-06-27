<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'guard_name',
        'allowed_resources',
    ];

    protected $casts = [
        'allowed_resources' => 'array',
    ];
}
