<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::firstOrNew(['email' => config('kirestore.admin.email')]);
        $admin->name = config('kirestore.admin.name');
        $admin->password = config('kirestore.admin.password');
        $admin->role = 'admin';
        $admin->save();
    }
}
