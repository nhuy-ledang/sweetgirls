<?php

namespace Modules\Usr\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsrDatabaseSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $path = __DIR__ . '/superadmin.sql';
        DB::unprepared(file_get_contents($path));
    }
}
