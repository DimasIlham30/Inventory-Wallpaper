@extends('layouts.main')

@section('css')
  <link rel="stylesheet" href="{{url('assets/pluginsdatatables-bs4/css/dataTables.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{url('assets/pluginsdatatables-responsive/css/responsive.bootstrap4.min.css')}}">
@endsection
@section('content')
<div class="content-wrapper">
   <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Transaksi</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Transaksi</a></li>
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
                <h3 class="card-title">Tambah Data Transaksi</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" action="{{url('transaksi/store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                  @if($message=Session::get('error'))
                    <div class="alert alert-danger" role="alert">
                        <div class="alert-text">{{ucwords($message)}}</div>
                    </div>
                  @endif
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" required class="form-control">
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label>Tipe</label>
                       <select class="form-control" name="tipe" required>
                         <option disabled selected>Pilih Tipe Transaksi</option>
                         <option value="in">Pembelian</option>
                         <option value="out">Penjualan</option>
                       </select>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-12">
                      <p align="center">List Material</p>
                      <table id="example1" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                          <th>#</th>
                          <th>Nama</th>
                          <th>Harga</th>
                          <th>Qty</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($material as $key => $item)
                        <tr>
                          <td>
                            <input type="checkbox" name="material_id[{{$item->id}}]" value="{{$item->id}}">
                          </td>
                          <td>{{$item->nama}}</td>
                          <td>Rp.{{number_format($item->harga)}}</td>
                          <td>
                           <input type="number" name="qty[{{$item->id}}]" class="form-control" value="0">
                          </td>
                        </tr>    
                        @endforeach            
                      </table>
                    </div>
                  </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer" align="right">
                  <button type="reset" class="btn btn-default">Reset</button>
                  &nbsp;&nbsp;
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </form>
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
<script src="{{url('assets/pluginsdatatables/jquery.dataTables.min.js')}}"></script>
<script src="{{url('assets/pluginsdatatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{url('assets/pluginsdatatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{url('assets/pluginsdatatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script type="text/javascript">
    $(function () {
    $("#example1").DataTable({
      "responsive": true,
      "autoWidth": false,
      "paging": false
    });
  });
</script>
@endsection