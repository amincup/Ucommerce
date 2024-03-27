<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class SliderController extends Controller
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
        $sliders = Slider::all();

        return response()->json($sliders);
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
            'nama_slider' => 'required',
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

        $slider = Slider::create($input);

        return response()->json([
            'data' => $slider
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Slider $slider)
    {
        return response()->json([
            'data' => $slider
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Slider $slider)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Slider $slider)
    {
        //untuk validasi data
        $validator = Validator::make($request->all(), [
            'nama_slider' => 'required',
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
            File::delete('uploads/' . $slider->gambar);

            $gambar = $request->file('gambar');
            $nama_gambar = date('Ymd_His') . "." . $gambar->getClientOriginalExtension();
            $gambar->move('uploads', $nama_gambar);
            $input['gambar'] = $nama_gambar;
        } else {
            unset($input['gambar']);
        }

        $slider->update($input);

        return response()->json([
            'message' => 'success',
            'data' => $slider
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Slider $slider)
    {
        //hapus file di folder
        File::delete('uploads/' . $slider->gambar);
        $slider->delete();

        return response()->json([
            'message' => 'success'
        ]);
    }
}
