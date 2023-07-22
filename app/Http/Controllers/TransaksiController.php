<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;
use Auth;
use DB;
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
        $tipe = [];
        foreach ($data as $key => $value) 
        {
           $transaksiItem = DB::table('transaksi_item')->where('transaksi_id',$value->id)->get();
           foreach ($transaksiItem as $transaksiItemKey => $transaksiItemValue) 
           {
                $item = DB::table('material')->where('id',$transaksiItemValue->material_id)->first();
                if($item)
                {
                    $material[$value->id][$transaksiItemKey]['material'] = $item->nama;
                    $material[$value->id][$transaksiItemKey]['subtotal'] = $transaksiItemValue->total / $transaksiItemValue->qty;
                    $material[$value->id][$transaksiItemKey]['total'] = $transaksiItemValue->total;
                    $material[$value->id][$transaksiItemKey]['qty'] = $transaksiItemValue->qty;
                }  
           }
           $materialStock = DB::table('material_stock')->where('transaksi_id',$value->id)->first();
           $tipe[$value->id] = null;
           if($materialStock)
           {
                $tipe[$value->id] = $materialStock->tipe;
           } 
        }
        return view('transaksi.index',compact('data','material','tipe'));
    }

    public function create()
    {
        $material =DB::table('material')->get();
        return view('transaksi.create',compact('material'));
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $createdAt = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        $trsId = DB::table('transaksi')->insertGetId([
                'tanggal'=>$request->tanggal,
                'grandtotal'=>0,
                'qty'=>0,
                'created_at'=>$createdAt
            ]);
        if($request->material_id)
        {
            if($request->tipe == 'out')
            {
                foreach ($request->material_id as $key => $value) 
                {
                    $check = DB::table('material')->where('id',$value)->first();
                    if($check)
                    {
                        if($check->stock < $request->qty[$value])
                        {
                           return redirect()->back()->with('error','Gagal menambahkan data transaksi penjualan material '.$check->nama.' stock penjualan yang dimasukkan melebihi stock saat ini, Silahkan tambahkan transaksi pembelian terlebih dahulu terhadap material '.$check->nama.' ');
                        }
                    }
                }
            }
            $grandTotal = 0;
            $qtyTotal = 0;
            
            foreach ($request->material_id as $key => $value) 
            {
                if($request->qty[$value] <= 0)
                {
                    return redirect()->back()->with('error','Gagal menambahkan data qty material '.$check->nama.' masih 0, Silahkan tambahkan qty terlebih dahulu terhadap material '.$check->nama.' ');
                }
                $item = DB::table('material')->where('id',$value)->first();
                if($item)
                {
                    $total = $request->qty[$value] * $item->harga;
                    $grandTotal += $total;
                    $qtyTotal += $request->qty[$value];
                    DB::table('transaksi_item')->insert([
                        'transaksi_id'=>$trsId,
                        'material_id'=>$value,
                        'qty'=>$request->qty[$value],
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
        $transaksiItem = DB::table('transaksi_item')->where('transaksi_id',$id)->get();
        $materialData = [];
        foreach ($transaksiItem as $key => $value) 
        {
            array_push($materialSelected, $value->material_id);
            $materialData[$key]['qty'] = $value->qty;
        }
        $materialStock = DB::table('material_stock')->where('transaksi_id',$id)->first();
        return view('transaksi.edit',compact('data','material','materialSelected','materialStock','materialData'));
    }

    public function update(Request $request,$id)
    {
        if($request->material_id)
        {
            foreach ($request->material_id as $key => $value)
            {
                if($request->qty[$value] <= 0)
                {
                    $check = DB::table('material')->where('id',$value)->first();
                    if($check)
                    {
                        return redirect()->back()->with('error','Gagal mengubah data qty material '.$check->nama.' masih 0, Silahkan tambahkan qty terlebih dahulu terhadap material '.$check->nama.' ');
                    }
                }
            }
        }
        $updatedAt = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        DB::table('transaksi')->where('id',$id)->update([
            'tanggal'=>$request->tanggal,
            'grandtotal'=>0,
            'qty'=>0,
            'updated_at'=>$updatedAt
        ]);
        DB::table('transaksi_item')->where('transaksi_id',$id)->delete();
        DB::table('material_stock')->where('transaksi_id',$id)->delete();
        if($request->material_id)
        {
            if($request->tipe == 'out')
            {
                foreach ($request->material_id as $key => $value) 
                {
                    $check = DB::table('material')->where('id',$value)->first();
                    if($check)
                    {
                        if($check->stock < $request->qty[$value])
                        {
                            return redirect()->back()->with('error','Gagal mengubah data transaksi penjualan material '.$check->nama.' stock penjualan yang dimasukkan melebihi stock saat ini, Silahkan tambahkan transaksi pembelian terlebih dahulu terhadap material '.$check->nama.' ');
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
                    $total = $request->qty[$value] * $item->harga;
                    $grandTotal += $total;
                    $qtyTotal += $request->qty[$value];
                    DB::table('transaksi_item')->insert([
                        'transaksi_id'=>$id,
                        'material_id'=>$value,
                        'qty'=>$request->qty[$value],
                        'total'=>$total,
                        'created_at'=>$updatedAt
                    ]);
                }
            }
            DB::table('material_stock')->insert([
                'transaksi_id'=>$id,
                'qty'=>$qtyTotal,
                'tipe'=>$request->tipe,
                'created_at'=>$updatedAt
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
