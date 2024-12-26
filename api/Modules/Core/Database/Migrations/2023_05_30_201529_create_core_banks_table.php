<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateCoreBanksTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('core__banks', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });
        $rows = [
            ['name' => 'Ngan hang Cong Thuong Han Quoc (IBK)'],
            ['name' => 'Ngan hang Cong Thuong Han Quoc chi nhanh HCM (IBK HCM)'],
            ['name' => 'Ngan hang Lien Doanh Viet - Nga (VRB)'],
            ['name' => 'Ngan hang NN VA PTNT Viet Nam (AGRIBANK)'],
            ['name' => 'Ngan hang Nonghyup Chi Nhanh Ha Noi (NHB HN)'],
            ['name' => 'Ngan hang TMCP A Chau (ACB)'],
            ['name' => 'Ngan hang TMCP An Binh (ABBANK)'],
            ['name' => 'Ngan hang TMCP Bac A (NASB)'],
            ['name' => 'Ngan hang TMCP Ban Viet (VIETCAPITAL BANK)'],
            ['name' => 'Ngan hang TMCP Bao Viet (BVB)'],
            ['name' => 'Ngan hang TMCP Buu Dien Lien Viet (LPB)'],
            ['name' => 'Ngan hang TMCP Cong Thuong viet Nam (VIETINBANK)'],
            ['name' => 'Ngan hang TMCP Dai Chung Viet Nam (PVCOMBANK)'],
            ['name' => 'Ngan hang TMCP Dai Duong (OCEANBANK)'],
            ['name' => 'Ngan hang TMCP Dau Khi Toan Cau (GPB)'],
            ['name' => 'Ngan hang TMCP Dau Tu Va Phat Trien Viet Nam (BIDV)'],
            ['name' => 'Ngan hang TMCP Dong A (DONGABANK)'],
            ['name' => 'Ngan hang TMCP DONG NAM A (SEABANK)'],
            ['name' => 'Ngan hang TMCP Hang Hai Viet Nam (MSB)'],
            ['name' => 'Ngan hang TMCP Kien Long (KIENLONGBANK)'],
            ['name' => 'Ngan hang TMCP Nam A (NAMABANK)'],
            ['name' => 'Ngan hang TMCP Ngoai Thuong Viet Nam (VIETCOMBANK)'],
            ['name' => 'Ngan hang TMCP Phuong Dong (OCB)'],
            ['name' => 'Ngan hang TMCP PT NHA Dong Bang Song Cuu Long'],
            ['name' => 'Ngan hang TMCP Quan Doi (MB)'],
            ['name' => 'Ngan hang TMCP Quoc Dan (NCB)'],
            ['name' => 'Ngan hang TMCP Quoc Te VIB'],
            ['name' => 'Ngan hang TMCP Sai Gon (SCB)'],
            ['name' => 'Ngan hang TMCP Sai Gon - Ha Noi (SHB)'],
            ['name' => 'Ngan hang TMCP Sai Gon Cong Thuong (SAIGONBANK)'],
            ['name' => 'Ngan hang TMCP Tien Phong (TPBANK)'],
            ['name' => 'Ngan hang TMCP Viet A (VAB)'],
            ['name' => 'Ngan hang TMCP Viet Nam Thinh Vuong (VPBANK)'],
            ['name' => 'Ngan hang TMCP Viet Nam Thinh Vuong - Ngan hang so Cake By VPBANK (CAKE)'],
            ['name' => 'Ngan hang TMCP Viet Nam Thinh Vuong - NH So Ubank By VPBANK (UBANK)'],
            ['name' => 'Ngan hang TMCP Viet Nam Thuong Tin (VIETBANK)'],
            ['name' => 'Ngan hang TMCP Xang Dau Petrolimex (PG BANK)'],
            ['name' => 'Ngan hang TMCP Xuat Nhap Khau Viet Nam (EXIMBANK)'],
            ['name' => 'Ngan hang TNNH Indovina'],
            ['name' => 'Ngan hang TNNH MTV CIMB (CIMB)'],
            ['name' => 'Ngan hang TNNH MTV Hongleong Viet Nam'],
            ['name' => 'Ngan hang TNNH MTV HSBC (Viet Nam)'],
            ['name' => 'Ngan hang TNNH MTV Public Viet Nam (BPVN)'],
            ['name' => 'Ngan hang TNNH MTV Shinhan Viet Nam (SHBVN)'],
            ['name' => 'Ngan hang TNNH MTV Standard Chartered Viet Nam (SCVN)'],
            ['name' => 'Ngan hang TNNH MTV United Overseas Bank (UOB)'],
            ['name' => 'Ngan hang Wooribank'],
            ['name' => 'Ngan hang TMCP Ky Thuong Viet Nam (TECHCOMBANK)'],
            ['name' => 'Ngan hang TMCP Sai Gon Thuong Tin (SACOMBANK)'],
        ];
        DB::table('core__banks')->insert($rows);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('core__banks');
    }
}
