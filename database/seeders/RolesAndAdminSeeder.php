<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $merchantRole = Role::create(['name' => 'merchants']);
        $userRole = Role::create(['name' => 'user']);

        // Create Admin User
        $admin = User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@goumh.com',
            'password' => Hash::make('password'),
            'phone_number' => '081234567890',
            'status' => 'approved',
        ]);
        $admin->assignRole($adminRole);

        // Create Dummy Merchant
        $merchant = User::create([
            'name' => 'Al Barokah Travel',
            'email' => 'travel@goumh.com',
            'password' => Hash::make('password'),
            'phone_number' => '089876543210',
            'business_name' => 'PT Al Barokah Wisata',
            'business_information' => 'Penyelenggara Haji dan Umrah Resmi',
            'status' => 'approved',
        ]);
        $merchant->assignRole($merchantRole);

        // Create Dummy User
        $customer = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '08111222333',
            'status' => 'approved',
        ]);
        $customer->assignRole($userRole);
    }
}
