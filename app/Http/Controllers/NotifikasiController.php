<?php

namespace App\Http\Controllers;

class NotifikasiController extends Controller
{
    public function index()
    {
        return view('notifikasi.index', [
            'notifikasi' => auth()->user()->notifications()->paginate(10),
        ]);
    }

    public function baca($id)
    {
        $notifikasi = auth()->user()->notifications()->findOrFail($id);
        $notifikasi->markAsRead();

        return redirect()->route('pesanan.show', $notifikasi->data['order_id']);
    }

    public function bacaSemua()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('status', 'Semua pemberitahuan ditandai sudah dibaca');
    }
}
