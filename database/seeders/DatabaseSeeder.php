<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Services\CredentialGenerator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'user.view', 'user.create', 'user.reset-password', 'user.delete', 'user.skills', 'role.manage',
            'customer.view', 'customer.manage', 'password.change',
            'service.manage', 'order.view', 'order.cancel', 'order.create-for', 'consult.note',
            'order.assigned', 'order.own', 'project.assign', 'progress.update',
            'price.set', 'payment.status', 'revenue.manage', 'revenue.view',
            'marketing.view', 'marketing.send', 'dashboard.view',
        ];

        foreach ($permissions as $nama) {
            Permission::create(['name' => $nama]);
        }

        Role::create(['name' => Role::OWNER])->givePermissionTo([
            'user.view', 'user.create', 'user.reset-password', 'user.delete', 'role.manage',
            'customer.view', 'password.change', 'service.manage', 'order.view', 'order.cancel', 'consult.note',
            'price.set', 'payment.status', 'revenue.manage', 'revenue.view', 'marketing.view', 'dashboard.view',
        ]);
        Role::create(['name' => Role::SUPER_ADMIN])->givePermissionTo([
            'user.view', 'user.create', 'user.reset-password', 'user.delete', 'user.skills',
            'password.change', 'dashboard.view',
        ]);
        Role::create(['name' => Role::ADMIN])->givePermissionTo([
            'customer.view', 'customer.manage', 'password.change', 'service.manage', 'order.view',
            'order.cancel', 'order.create-for', 'payment.status', 'marketing.view', 'marketing.send', 'dashboard.view',
        ]);
        Role::create(['name' => Role::KEPALA_STAF_DESAINER])->givePermissionTo([
            'password.change', 'order.view', 'order.assigned', 'project.assign', 'progress.update', 'dashboard.view',
        ]);
        Role::create(['name' => Role::DESAINER])->givePermissionTo([
            'password.change', 'order.assigned', 'progress.update', 'dashboard.view',
        ]);
        Role::create(['name' => Role::CUSTOMER])->givePermissionTo([
            'password.change', 'order.own', 'payment.status', 'dashboard.view',
        ]);

        // akun buat testing, password semua: Password123
        $users = [
            ['name' => 'Owner Media Creative', 'role' => Role::OWNER],
            ['name' => 'Super Admin', 'role' => Role::SUPER_ADMIN],
            ['name' => 'Admin', 'role' => Role::ADMIN],
            ['name' => 'Kepala Staf Desainer', 'role' => Role::KEPALA_STAF_DESAINER],
            ['name' => 'Desainer', 'role' => Role::DESAINER],
        ];

        foreach ($users as $user) {
            $akun = User::create([
                'user_type' => User::INTERNAL,
                'name' => $user['name'],
                'password' => 'Password123',
            ]);

            $akun->internalProfile()->create([
                'nip' => CredentialGenerator::generateNip(),
                'must_change_password' => false,
            ]);

            $akun->assignRole($user['role']);
        }

        $kategori = ['Desain Logo', 'Desain Kaos', 'Editing Video', 'Editing Foto', 'Poster', 'Banner', 'Flyer'];

        foreach ($kategori as $urutan => $nama) {
            ServiceCategory::create([
                'name' => $nama,
                'slug' => Str::slug($nama),
                'sort_order' => $urutan + 1,
            ]);
        }
    }
}
