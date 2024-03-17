<?php

namespace App\Http\Controllers;

use App\Models\Testimoni;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class TestimoniController extends Controller
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
        $testimonis = Testimoni::all();

        return response()->json($testimonis);
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
            'nama_testimoni' => 'required',
            'deskripsi' => 'required',
            'gambar' => 'required|image|mimes:jpg,jpeg,png,webp'
        ]);
        //akan return error 422
        if ($validator->fails()) {
            return response()->json(
                ($validator)->errors(),
                422
            );
        }

        $input = $request->all();

        if ($request->has('gambar')) {
            $gambar = $request->file('gambar');
            $nama_gambar = date('Ymd_His') . "." . $gambar->getClientOriginalExtension();
            $gambar->move('uploads', $nama_gambar);
            $input['gambar'] = $nama_gambar;
        }

        $testimoni = Testimoni::create($input);

        return response()->json([
            'data' => $testimoni
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Testimoni $testimoni)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Testimoni $testimoni)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Testimoni $testimoni)
    {
        //untuk validasi data
        $validator = Validator::make($request->all(), [
            'nama_testimoni' => 'required',
            'deskripsi' => 'required'
        ]);
        //akan return error 422
        if ($validator->fails()) {
            return response()->json(
                ($validator)->errors(),
                422
            );
        }

        $input = $request->all();

        if ($request->has('gambar')) {
            //hapus file di folder
            File::delete('uploads/' . $testimoni->gambar);

            $gambar = $request->file('gambar');
            $nama_gambar = date('Ymd_His') . "." . $gambar->getClientOriginalExtension();
            $gambar->move('uploads', $nama_gambar);
            $input['gambar'] = $nama_gambar;
        } else {
            unset($input['gambar']);
        }

        $testimoni->update($input);

        return response()->json([
            'message' => 'success',
            'data' => $testimoni
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Testimoni $testimoni)
    {
        //hapus file di folder
        File::delete('uploads/' . $testimoni->gambar);
        $testimoni->delete();

        return response()->json([
            'message' => 'success'
        ]);
    }
}
