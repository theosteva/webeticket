<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class FixAllowedResourcesSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Role::all() as $role) {
            $allowed = $role->allowed_resources;
            if (is_string($allowed)) {
                // Jika string kosong, jadikan array kosong
                if (trim($allowed) === '') {
                    $role->allowed_resources = [];
                } else {
                    // Jika string, split dengan koma atau jadikan array satu elemen
                    $arr = json_decode($allowed, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($arr)) {
                        $role->allowed_resources = $arr;
                    } else {
                        $role->allowed_resources = array_map('trim', explode(separator: ',', $allowed));
                    }
                }
                $role->save();
            }
        }
    }
} 