@extends('layouts.main')

@section('css')

@endsection
@section('content')
<div class="content-wrapper">
   <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>ABC Analyze</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">ABC Analyze</a></li>
              <li class="breadcrumb-item active">Tambah</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Filter Transaksi</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              
                <div class="card-body">
                  @if($message=Session::get('error'))
                    <div class="alert alert-danger" role="alert">
                        <div class="alert-text">{{ucwords($message)}}</div>
                    </div>
                  @endif
                  <form action="{{url('material_analyze')}}">
                    <div class="row">
                      <div class="col-sm-5">
                        <div class="form-group">
                          <label>Tanggal Awal</label>
                          <input type="date" name="tanggal_awal" value="{{$request->tanggal_awal}}" required class="form-control">
                        </div>
                      </div>
                      <div class="col-sm-5">
                        <div class="form-group">
                          <label>Tanggal Awal</label>
                          <input type="date" name="tanggal_akhir" value="{{$request->tanggal_akhir}}" required class="form-control">
                        </div>
                      </div>
                      <div class="col-sm-2">
                        <div class="form-group pt-2">
                          <label></label>
                          <input type="submit" value="Analyze" class="btn btn-primary form-control">
                        </div>
                      </div>
                    </div>
                  </form>

                  <div class="row">
                    <div class="col-sm-4">
                      <p align="center"><strong>Rekap Penjualan</strong></p>
                      <table  class="table table-bordered table-striped">
                       <tr>
                         <td>Total Pendapatan : </td>
                         <td>Rp.{{number_format($data['total_nilai_penjualan'])}}</td>
                       </tr>
                       <tr>
                         <td>Total Material Terjual : </td>
                         <td>{{$data['total_barang_terjual']}}</td>
                       </tr> 
                       <tr>
                         <td>Total Transaksi : </td>
                         <td>{{$data['total_transaksi']}}</td>
                       </tr>  
                      </table>
                    </div>
                    <div class="col-sm-4">
                      <p align="center"><strong>Pembagian Kategori Berdasarkan Total Pendaptan</strong></p>
                      <table  class="table table-bordered table-striped">
                       <tr>
                         <td>Kategori A (50%): </td>
                         <td>Rp.{{number_format($data['a'])}}</td>
                       </tr>
                       <tr>
                         <td>Kategori B (30%): </td>
                         <td>Rp.{{number_format($data['b'])}} </td>
                       </tr> 
                       <tr>
                         <td>Kategori C (20%): </td>
                         <td>Rp.{{number_format($data['c'])}} </td>
                       </tr>  
                      </table>
                    </div>
                    <div class="col-sm-4">
                      <p align="center"><strong>Penjelasan Pembagian Kategori</strong></p>
                      <table  class="table table-bordered table-striped">
                       <tr>
                         <td>Kategori A (50%): </td>
                         <td>Berisi barang-barang yang memiliki nilai penjualan tinggi dan persediaan yang perlu diawasi dengan ketat</td>
                       </tr>
                       <tr>
                         <td>Kategori B (30%): </td>
                         <td>Berisi barang-barang dengan nilai penjualan menengah dan persediaan yang memerlukan pengawasan yang moderat.</td>
                       </tr> 
                       <tr>
                         <td>Kategori C (20%): </td>
                         <td>berisi barang-barang dengan nilai penjualan rendah dan persediaan yang memerlukan pengawasan yang lebih longgar.</td>
                       </tr>  
                      </table>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-sm-12">
                      <p align="center"><strong>Hasil Analisa</strong></p>
                      <table  class="table table-bordered table-striped">
                        <thead>
                          <tr>
                            <th>Ranking</th>
                            <th>Nama Material</th>
                            <th>Nilai Penjualan</th>
                            <th>Persentase Nilai Penjualan</th>
                            <th>Persentase Kumulatif</th>
                            <th>Persentase Kumulatif Akumulatif</th>
                            <th>Kategori</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($data['data'] as $key => $item)
                          <tr>
                              <td>{{$key+1}}</td>
                              <td>{{$item['nama']}}</td>
                              <td>Rp.{{number_format($item['nilaiPenjualan'])}}</td>
                              <td>{{$item['persentase_nilai_penjualan']}}%</td>
                              <td>{{$item['kumulatif']}}%</td>
                              <td>{{$item['kumulatif_akumulatif']}}%</td>
                              <td>{{$item['kategori']}}</td>
                          </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <!-- /.card-body -->

               
              
            </div>
            <!-- /.card -->

          </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
@endsection
@section('script')
<script src="{{url('assets/plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>

@endsection