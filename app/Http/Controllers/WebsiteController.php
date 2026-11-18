<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\SystemSetting;

class WebsiteController extends Controller
{
    public function beranda()
    {
        return view('website.beranda', [
            'layanans' => Service::where('is_active', true)->orderBy('sort_order')->take(4)->get(),
        ]);
    }

    public function layanan()
    {
        return view('website.layanan', [
            'layanans' => Service::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function detailLayanan($slug)
    {
        $layanan = Service::where('slug', $slug)->where('is_active', true)->firstOrFail();

        return view('website.detail-layanan', [
            'layanan' => $layanan,
            'layananLain' => Service::where('is_active', true)->where('id', '!=', $layanan->id)->orderBy('sort_order')->take(3)->get(),
        ]);
    }

    public function tentangKami()
    {
        return view('website.tentang-kami');
    }

    public function kontak()
    {
        return view('website.kontak', ['waAdmin' => SystemSetting::get('wa_admin')]);
    }
}
