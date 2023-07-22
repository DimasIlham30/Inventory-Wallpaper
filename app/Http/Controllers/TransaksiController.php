<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data = DB::table('transaksi')->get();
        $material = [];
        foreach ($data as $key => $value) 
        {
           $transaksiItem = DB::table('transaksi_item')->where('transaksi_id',$value->id)->get();
           foreach ($transaksiItem as $transaksiItemKey => $transaksiItemValue) 
           {
                $item = DB::table('material')->where('id',$transaksiItemValue->material_id)->first();
                if($item)
                {
                    $material[$value->id][$transaksiItemKey]['material'] = $item->soal;
                    $material[$value->id][$transaksiItemKey]['qty'] = $transaksiItemValue->qty;
                }  
           } 
        }
        return view('transaksi.index',compact('data','material'));
    }

    public function create()
    {
        $material =DB::table('material')->get();
        return view('transaksi.create',compact('material'));
    }

    public function store(Request $request)
    {
        $createdAt = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        $trsId = DB::table('transaksi')->insertGetId([
                'tanggal'=>$request->tanggal,
                'grandtotal'=>0,
                'qty'=>0,
                'created_at'=>$createdAt
            ]);
        if($request->material_id)
        {
            if($request->tipe == 'penjualan')
            {
                foreach ($request->material_id as $key => $value) 
                {
                    $check = DB::table('material')->where('id',$value)->first();
                    if($check)
                    {
                        if($check->stock <= 0)
                        {
                            return redirect()->back()->with('error','Gagal menambahkan data transaksi penjualan material '.$check->nama.' masih 0, Silahkan tambahkan transaksi pembelian terlebih dahulu terhadap material '.$check->nama.' ')
                        }
                    }
                }
            }
            $grandTotal = 0;
            $qtyTotal = 0;
            foreach ($request->material_id as $key => $value) 
            {
                $item = DB::table('material')->where('id',$value)->first();
                if($item)
                {
                    $total = $request->qty[$key] * $item->harga;
                    $grandTotal += $total;
                    $qtyTotal += $request->qty[$key];
                    DB::table('transaksi_item')->insert([
                        'transaksi_id'=>$trsId,
                        'material'=>$value,
                        'qty'=>$request->qty[$key],
                        'total'=>$total,
                        'created_at'=>$createdAt
                    ]);
                }
            }
            DB::table('material_stock')->insert([
                'transaksi_id'=>$trsId,
                'qty'=>$qtyTotal,
                'tipe'=>$request->tipe,
                'created_at'=>$createdAt
            ]);
            DB::table('transaksi')->where('id',$trsId)->update(['grandTotal'=>$grandTotal,'qty'=>$qtyTotal]);

            //hitung ulang stock
            foreach ($request->material_id as $key => $value) 
            {
                $itemIn = DB::table('transaksi_item as ti')
                          ->join('transaksi as trs','trs.id','=','ti.transaksi_id')
                          ->join('material_stock as mst','mst.transaksi_id','=','trs.id')
                          ->where('ti.material_id',$value)
                          ->where('mst.tipe','in')
                          ->sum('ti.qty');
                $itemOut = DB::table('transaksi_item as ti')
                          ->join('transaksi as trs','trs.id','=','ti.transaksi_id')
                          ->join('material_stock as mst','mst.transaksi_id','=','trs.id')
                          ->where('ti.material_id',$value)
                          ->where('mst.tipe','out')
                          ->sum('ti.qty');
                $stock = $itemIn - $itemOut;
                DB::table('material')->where('id',$value)->update(['stock'=>$stock]);
            }
            return redirect('transaksi')->with('success','Berhasil membuat data transaksi');
        }else
        {
            return redirect()->back()->with('error','Gagal membuat data transaksi, setidaknya centang salah satu material');
        }
    }

    public function edit($id)
    {
        $data = DB::table('transaksi')->where('id',$id)->first();
        if(!$data)
        {
            return redirect('transaksi_item')->with('error','Gagal mendapatkan data transaksi');
        }
        $material = DB::table('material')->get();
        $materialSelected = [];
        $transaksiItem = DB::table('transaksi_item')->where('paket_id',$id)->get();
        foreach ($transaksiItem as $key => $value) 
        {
            array_push($materialSelected, $value->material_id);
        }
        return view('transaksi.edit',compact('data','material','materialSelected'));
    }

    public function update(Request $request,$id)
    {
        $updatedAt = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        DB::table('transaksi')->where('id',$id)->update([
            'tanggal'=>$request->tanggal,
            'updated_at'=>$updatedAt
        ]);
        DB::table('transaksi_item')->where('transaksi_id',$id)->delete();
        DB::table('material_stock')->where('transaksi_id',$id)->delete();
        if($request->material_id)
        {
            if($request->tipe == 'penjualan')
            {
                foreach ($request->material_id as $key => $value) 
                {
                    $check = DB::table('material')->where('id',$value)->first();
                    if($check)
                    {
                        if($check->stock <= 0)
                        {
                            return redirect()->back()->with('error','Gagal menambahkan data transaksi penjualan material '.$check->nama.' masih 0, Silahkan tambahkan transaksi pembelian terlebih dahulu terhadap material '.$check->nama.' ')
                        }
                    }
                }
            }
            $grandTotal = 0;
            $qtyTotal = 0;
            foreach ($request->material_id as $key => $value) 
            {
                $item = DB::table('material')->where('id',$value)->first();
                if($item)
                {
                    $total = $request->qty[$key] * $item->harga;
                    $grandTotal += $total;
                    $qtyTotal += $request->qty[$key];
                    DB::table('transaksi_item')->insert([
                        'transaksi_id'=>$id,
                        'material'=>$value,
                        'qty'=>$request->qty[$key],
                        'total'=>$total,
                        'created_at'=>$createdAt
                    ]);
                }
            }
            DB::table('material_stock')->insert([
                'transaksi_id'=>$id,
                'qty'=>$qtyTotal,
                'tipe'=>$request->tipe,
                'created_at'=>$createdAt
            ]);
            DB::table('transaksi')->where('id',$id)->update(['grandTotal'=>$grandTotal,'qty'=>$qtyTotal]);

            //hitung ulang stock
            foreach ($request->material_id as $key => $value) 
            {
                $itemIn = DB::table('transaksi_item as ti')
                          ->join('transaksi as trs','trs.id','=','ti.transaksi_id')
                          ->join('material_stock as mst','mst.transaksi_id','=','trs.id')
                          ->where('ti.material_id',$value)
                          ->where('mst.tipe','in')
                          ->sum('ti.qty');
                $itemOut = DB::table('transaksi_item as ti')
                          ->join('transaksi as trs','trs.id','=','ti.transaksi_id')
                          ->join('material_stock as mst','mst.transaksi_id','=','trs.id')
                          ->where('ti.material_id',$value)
                          ->where('mst.tipe','out')
                          ->sum('ti.qty');
                $stock = $itemIn - $itemOut;
                DB::table('material')->where('id',$value)->update(['stock'=>$stock]);
            }
            return redirect('transaksi')->with('success','Berhasil mengubah data transaksi material');
        }else
        {
            return redirect()->back()->with('error','Gagal mengubah data transaksi, setidaknya centang salah satu material');
        }
        
    }

    public function delete($id)
    {
        DB::table('transaksi')->where('id',$id)->delete();
        return redirect('transaksi')->with('success','Berhasil menghapus data transaksi material ');
    }
}
