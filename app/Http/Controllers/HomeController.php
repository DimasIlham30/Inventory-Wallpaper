<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;
use Auth;
use DB;
class HomeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $material = DB::table('material')->count();
        $transaksi = DB::table('transaksi')->count();
        $itemIn = DB::table('transaksi_item as ti')
                          ->join('transaksi as trs','trs.id','=','ti.transaksi_id')
                          ->join('material_stock as mst','mst.transaksi_id','=','trs.id')
                          ->where('mst.tipe','in')
                          ->sum('ti.total');
        $itemOut = DB::table('transaksi_item as ti')
                          ->join('transaksi as trs','trs.id','=','ti.transaksi_id')
                          ->join('material_stock as mst','mst.transaksi_id','=','trs.id')
                          ->where('mst.tipe','out')
                          ->sum('ti.total');
        return view('home',compact('material','transaksi','itemIn','itemOut'));
    }
}
