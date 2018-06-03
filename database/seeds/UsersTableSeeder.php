<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 创建超级管理员
        User::insert([
            'name'       => '吉富德地产',
            'email'      => 'jifude@qq.com',
            'phone'      => '18376662410',
            'remake'     => '超级管理员',
            'identify'   => 1,
            'password'   => bcrypt('jfd168'),
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString()
        ]);
    }
}
