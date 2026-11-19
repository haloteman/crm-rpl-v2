<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Service;
use App\Models\StatusDefinition;
use App\Models\SystemSetting;
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

        $layanans = [
            ['Desain Logo', 'Logo yang mewakili identitas usaha Anda.'],
            ['Desain Kaos', 'Desain kaos untuk komunitas, acara, atau merchandise.'],
            ['Editing Video', 'Penyuntingan video untuk promosi dan media sosial.'],
            ['Editing Foto', 'Retouch dan penyuntingan foto produk maupun acara.'],
            ['Poster', 'Poster informatif dan menarik untuk berbagai kebutuhan.'],
            ['Flyer', 'Flyer promosi yang ringkas dan mudah dibaca.'],
            ['Banner', 'Banner cetak maupun digital untuk acara dan promosi.'],
        ];

        foreach ($layanans as $urutan => [$nama, $ringkasan]) {
            Service::create([
                'name' => $nama,
                'slug' => Str::slug($nama),
                'short_description' => $ringkasan,
                'workflow_steps' => ['Konsultasi kebutuhan', 'Pembayaran DP', 'Pengerjaan desain', 'Revisi', 'Hasil akhir'],
                'sort_order' => $urutan + 1,
            ]);
        }

        // nomor contoh, ganti dengan nomor WhatsApp asli perusahaan
        SystemSetting::create(['setting_key' => 'wa_admin', 'setting_value' => '6285143334354', 'description' => 'Nomor WhatsApp Admin']);
        SystemSetting::create(['setting_key' => 'wa_owner', 'setting_value' => '6285143334354', 'description' => 'Nomor WhatsApp Owner']);

        // kode, label internal, label untuk customer, status berikutnya yang boleh
        $statusPesanan = [
            ['menunggu_konsultasi', 'Menunggu Konsultasi', 'Menunggu Konsultasi', ['menunggu_dp', 'dibatalkan']],
            ['menunggu_dp', 'Menunggu DP', 'Menunggu Pembayaran DP', ['dp_diterima', 'dibatalkan']],
            ['dp_diterima', 'DP Diterima', 'DP Diterima', ['dikerjakan']],
            ['dikerjakan', 'Dikerjakan', 'Sedang Dikerjakan', ['revisi', 'selesai']],
            ['revisi', 'Revisi', 'Revisi', ['dikerjakan', 'selesai']],
            ['selesai', 'Selesai', 'Selesai', []],
            ['dibatalkan', 'Dibatalkan', 'Dibatalkan', []],
        ];

        foreach ($statusPesanan as $urutan => [$kode, $label, $labelCustomer, $berikutnya]) {
            StatusDefinition::create([
                'scope' => 'order',
                'code' => $kode,
                'label' => $label,
                'customer_label' => $labelCustomer,
                'sort_order' => $urutan + 1,
                'allowed_next' => $berikutnya,
                'is_final' => empty($berikutnya),
            ]);
        }
    }
}
