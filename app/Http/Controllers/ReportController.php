<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => 'index']); //dengan auth:api harus login dulu pake token auth kalo mau post ke postman
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $reports = DB::table('order_details')
            ->join('products', 'products.id', 'order_details.id_produk') //FK disini adalah id_produk
            ->select(DB::raw('count(*) as jumlah_dibeli, nama_barang, harga, SUM(total) as pendapatan, SUM(jumlah) as total_qty'))
            ->whereRaw("date(order_details.created_at) >= '$request->dari'")
            ->whereRaw("date(order_details.created_at) <= '$request->sampai'")
            ->groupBy('id_produk', 'nama_barang', 'harga')
            ->get();

        return response()->json([
            'data' => $reports
        ]);
    }
}
