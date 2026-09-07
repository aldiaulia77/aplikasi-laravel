<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with(['nasabah:id,nama,kode_nasabah', 'detail.jenisSampah:id,nama'])
            ->latest('tanggal')
            ->latest('id');

        // Nasabah hanya boleh melihat transaksi miliknya sendiri.
        if (Auth::user()->isNasabah()) {
            $query->where('nasabah_id', Auth::user()->nasabah_id);
        }

        if ($request->from) {
            $query->whereDate('tanggal', '>=', $request->from);
        }
        if ($request->to) {
            $query->whereDate('tanggal', '<=', $request->to);
        }

        $transaksi = $query->paginate(15)->withQueryString();

        return view('transaksi.index', compact('transaksi'));
    }
}
