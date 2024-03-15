<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class SubcategoryController extends Controller
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
        $subcategories = Subcategory::all();

        return response()->json($subcategories);
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
            'id_kategori' => 'required',
            'nama_subkategori' => 'required',
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

        $subcategory = Subcategory::create($input);

        return response()->json([
            'data' => $subcategory
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Subcategory $subcategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subcategory $subcategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subcategory $subcategory)
    {
        //untuk validasi data
        $validator = Validator::make($request->all(), [
            'id_kategori' => 'required',
            'nama_subkategori' => 'required',
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
            File::delete('uploads/' . $subcategory->gambar);

            $gambar = $request->file('gambar');
            $nama_gambar = date('Ymd_His') . "." . $gambar->getClientOriginalExtension();
            $gambar->move('uploads', $nama_gambar);
            $input['gambar'] = $nama_gambar;
        } else {
            unset($input['gambar']);
        }

        $subcategory->update($input);

        return response()->json([
            'message' => 'success',
            'data' => $subcategory
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subcategory $subcategory)
    {
        //hapus file di folder
        File::delete('uploads/' . $subcategory->gambar);
        $subcategory->delete();

        return response()->json([
            'message' => 'success'
        ]);
    }
}
