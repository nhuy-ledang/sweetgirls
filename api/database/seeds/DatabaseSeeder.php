<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run() {
        $path = __DIR__ . '/seeds.sql';
        DB::unprepared(file_get_contents($path));
    }
}
