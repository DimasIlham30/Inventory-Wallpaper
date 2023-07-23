<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;
use Auth;
use DB;
class MaterialAnalayzeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $data = [];
        $data['total_nilai_penjualan'] = 0;
        if(count($request->all()) > 0)
        {
           
            $itemIn = DB::table('transaksi_item as ti')
                          ->join('transaksi as trs','trs.id','=','ti.transaksi_id')
                          ->join('material_stock as mst','mst.transaksi_id','=','trs.id')
                          ->whereBetween('trs.tanggal',[$request->tanggal_awal,$request->tanggal_akhir])
                          ->where('mst.tipe','out')
                          ->sum('ti.total');
            $data['total_nilai_penjualan'] = $itemIn;
            $data['a'] = $itemIn * 50 / 100;
            $data['b'] = $itemIn * 30 / 100;
            $data['c'] = $itemIn * 20 / 100;
            $itemSold = DB::table('transaksi_item as ti')
                          ->join('transaksi as trs','trs.id','=','ti.transaksi_id')
                          ->join('material_stock as mst','mst.transaksi_id','=','trs.id')
                          ->whereBetween('trs.tanggal',[$request->tanggal_awal,$request->tanggal_akhir])
                          ->where('mst.tipe','out')
                          ->sum('ti.qty');
            $data['total_barang_terjual'] = $itemSold;
            $transaksi = $this->getDataTransaksi($request);
            $data['total_transaksi'] = count($transaksi);
            $persenTaseNilaiJual = $this->hitungPersentaseNilaiPernjualan($request);
            $kumulatif = $this->hitungPersentaseKumulatif($persenTaseNilaiJual);
            $kumulatifAkumulatif = $this->hitungPersentaseKumulatifAkumulatif($kumulatif);
            $pengkategorian = $this->pengkategorian($kumulatifAkumulatif,$data);
            $data['data'] = $pengkategorian;
            //dd($data);
        }
        return view('transaksi.analyze',compact('data','request'));
    }

    public function getDataTransaksi($request)
    {
        $arr = [];
        $arr['data'] = [];
        $arr['item'] = [];
        $arr['tipe'] = [];

        $data = DB::table('transaksi')->whereBetween('tanggal',[$request->tanggal_awal,$request->tanggal_akhir])->get();
        $material = [];
        $tipe = [];
        foreach ($data as $key => $value) 
        {
           $transaksiItem = DB::table('transaksi_item')->where('transaksi_id',$value->id)->get();
           foreach ($transaksiItem as $transaksiItemKey => $transaksiItemValue) 
           {
                $item = DB::table('material')->where('id',$transaksiItemValue->material_id)->first();
                if($item)
                {
                    $material[$value->id][$transaksiItemValue->material_id]['material'] = $item->nama;
                    $subtotal = $transaksiItemValue->total / $transaksiItemValue->qty;
                    $material[$value->id][$transaksiItemValue->material_id]['subtotal'] = $subtotal;
                    $material[$value->id][$transaksiItemValue->material_id]['total'] = $transaksiItemValue->total;
                    $material[$value->id][$transaksiItemValue->material_id]['qty'] = $transaksiItemValue->qty;
                }  
           }
           $materialStock = DB::table('material_stock')->where('transaksi_id',$value->id)->first();
           $tipe[$value->id] = null;
           if($materialStock)
           {
                $tipe[$value->id] = $materialStock->tipe;
           } 
        }

        $arr['data'] = json_decode(json_encode($data),true);
        $arr['item'] = $material;
        $arr['tipe'] = $tipe;
        return $arr;
    }

    public function hitungPersentaseNilaiPernjualan($request)
    {
        $materialData = DB::table('material')->select('id','stock','nama')->get();
        $materialData = json_decode(json_encode($materialData),true);
        foreach ($materialData as $materialDataKey => $materialDataValue) 
        {
            //dd($materialDataValue);
            $nilaiPenjualan = DB::table('transaksi_item as ti')
                                  ->join('transaksi as trs','trs.id','=','ti.transaksi_id')
                                  ->join('material_stock as mst','mst.transaksi_id','=','trs.id')
                                  ->where('mst.tipe','out')
                                  ->where('ti.material_id',$materialDataValue['id'])
                                  ->whereBetween('trs.tanggal',[$request->tanggal_awal,$request->tanggal_akhir])
                                  ->sum('ti.total');
            $totalNilaiPenjulan = DB::table('transaksi_item as ti')
                                  ->join('transaksi as trs','trs.id','=','ti.transaksi_id')
                                  ->join('material_stock as mst','mst.transaksi_id','=','trs.id')
                                  ->where('mst.tipe','out')
                                  ->whereBetween('trs.tanggal',[$request->tanggal_awal,$request->tanggal_akhir])
                                  ->sum('ti.total');
            $persenTase = 0;
            if($nilaiPenjualan > 0)
            {
                $persenTase = ($nilaiPenjualan / $totalNilaiPenjulan) * 100;
            }
            $materialData[$materialDataKey]['nilaiPenjualan'] = $nilaiPenjualan;
            //$materialData[$materialDataKey]['totalNilaiPenjulan'] = $totalNilaiPenjulan;
            $materialData[$materialDataKey]['persentase_nilai_penjualan'] = round($persenTase);
        }
        $materialData = $this->array_sort_by_column_desc($materialData,'persentase_nilai_penjualan');
        return $materialData;
    }

    public function hitungPersentaseKumulatif($data)
    {
        $totalPresentasiNilaiJual = array_sum(array_column($data, 'persentase_nilai_penjualan'));

        foreach ($data as $key => $value) 
        {
            $data[$key]['kumulatif'] =0; 
            if($value['persentase_nilai_penjualan'] > 0)
            {
                $data[$key]['kumulatif'] = round($totalPresentasiNilaiJual - $value['persentase_nilai_penjualan']); 
            }
        }
        return $data;
    }

    public function hitungPersentaseKumulatifAkumulatif($data)
    {
        $totalKumulatif = array_sum(array_column($data, 'kumulatif'));

        foreach ($data as $key => $value) 
        {
            $data[$key]['kumulatif_akumulatif'] = 0;
            if($value['kumulatif'] > 0)
            {
                $data[$key]['kumulatif_akumulatif'] = round($totalKumulatif - $value['kumulatif']);
            } 
        }
        return $data;
    }

    function array_sort_by_column_desc(&$arr, $col, $dir = SORT_DESC) {
        $sort_col = array();
        foreach ($arr as $key => $row) {
            $sort_col[$key] = $row[$col];
        }

        array_multisort($sort_col, $dir, $arr);
        return $arr;
    }

    public function pengkategorian($data,$kategori)
    {
        foreach ($data as $key => $value) 
        {
           if($value['nilaiPenjualan'] >= $kategori['a'])
           {
                $data[$key]['kategori'] = 'A';
           }elseif ($value['nilaiPenjualan'] >= $kategori['b']) 
           {
               $data[$key]['kategori'] = 'B';
           }else
           {
               $data[$key]['kategori'] = 'C';
           }
        }

        return $data;
    }
}