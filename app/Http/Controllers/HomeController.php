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
        return view('home',compact('material','transaksi'));
    }
}
