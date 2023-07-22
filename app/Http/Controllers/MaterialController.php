<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;
use Auth;
use DB;
class MaterialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data = DB::table('material')->get();
        return view('material.index',compact('data'));
    }

    public function create()
    {
        return view('material.create');
    }

    public function store(Request $request)
    {
        $createdAt = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        DB::table('material')->insert([
            'nama'=>$request->nama,
            'harga'=>$request->harga,
            'created_at'=>$createdAt
        ]);

        return redirect('material')->with('success','Berhasil membuat data material');
    }

    public function edit($id)
    {
        $data = DB::table('material')->where('id',$id)->first();
        if(!$data)
        {
            return redirect('material')->with('error','Gagal mendapatkan data material');
        }
        return view('material.edit',compact('data'));
    }

    public function update(Request $request,$id)
    {
        $updatedAt = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        DB::table('material')->where('id',$id)->update([
            'nama'=>$request->nama,
            'harga'=>$request->harga,
            'updated_at'=>$updatedAt
        ]);
        return redirect('material')->with('success','Berhasil mengubah data material');
    }

    public function delete($id)
    {
        DB::table('material')->where('id',$id)->delete();
        return redirect('material')->with('success','Berhasil menghapus data material');
    }
}