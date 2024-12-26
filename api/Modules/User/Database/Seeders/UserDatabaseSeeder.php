<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserDatabaseSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $this->call(RolesTableSeeder::class);

        $path = __DIR__ . '/superadmin.sql';
        DB::unprepared(file_get_contents($path));
    }
}
