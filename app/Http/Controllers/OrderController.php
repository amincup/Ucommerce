<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => 'index']); //dengan auth:api harus login dulu pake token auth kalo mau post ke postman
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::all();

        return response()->json($orders);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //untuk validasi data
        $validator = Validator::make($request->all(), [
            'id_member' => 'required',
        ]);
        //akan return error 422
        if ($validator->fails()) {
            return response()->json(
                ($validator)->errors(),
                422
            );
        }

        $input = $request->all();

        $order = Order::create($input);

        for ($i = 0; $i < count($input['id_produk']); $i++) {
            OrderDetail::create([
                'id_order' => $order['id'],
                'id_produk' => $input['id_produk'][$i],
                'jumlah' => $input['jumlah'][$i],
                'size' => $input['size'][$i],
                'color' => $input['color'][$i],
                'total' => $input['total'][$i],
            ]);
        }

        return response()->json([
            'data' => $order
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //untuk validasi data
        $validator = Validator::make($request->all(), [
            'id_member' => 'required',
        ]);
        //akan return error 422
        if ($validator->fails()) {
            return response()->json(
                ($validator)->errors(),
                422
            );
        }

        $input = $request->all();
        $order->update($input);
        OrderDetail::where('id_order', $order['id'])->delete();

        for ($i = 0; $i < count($input['id_produk']); $i++) {
            OrderDetail::create([
                'id_order' => $order['id'],
                'id_produk' => $input['id_produk'][$i],
                'jumlah' => $input['jumlah'][$i],
                'size' => $input['size'][$i],
                'color' => $input['color'][$i],
                'total' => $input['total'][$i],
            ]);
        }

        return response()->json([
            'message' => 'success',
            'data' => $order
        ]);
    }

    function dikonfirmasi()
    {
        $orders = Order::where('status', 'Dikonfirmasi')->get();

        return response()->json($orders);
    }

    function dikemas()
    {
        $orders = Order::where('status', 'Dikemas')->get();

        return response()->json($orders);
    }

    function dikirim()
    {
        $orders = Order::where('status', 'Dikirim')->get();

        return response()->json($orders);
    }

    function diterima()
    {
        $orders = Order::where('status', 'Diterima')->get();

        return response()->json($orders);
    }

    function selesai()
    {
        $orders = Order::where('status', 'Selesai')->get();

        return response()->json($orders);
    }

    function ubah_status(Request $request, Order $order)
    {
        $order->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'success',
            'data' => $order
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //hapus file di folder
        $order->delete();

        return response()->json([
            'message' => 'success'
        ]);
    }
}
