@extends('layouts.main')

@section('css')
  <link rel="stylesheet" href="{{url('assets/pluginsdatatables-bs4/css/dataTables.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{url('assets/pluginsdatatables-responsive/css/responsive.bootstrap4.min.css')}}">
@endsection
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Transaksi</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Transaksi</a></li>
              <li class="breadcrumb-item active">List</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <div class="row">
              	<div class="col-sm-6 pt-2">
              		<h3 class="card-title">List Data Transaksi</h3>
              	</div>
              	<div class="col-sm-6" align="right">
              		<a href="{{url('transaksi/create')}}" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah Data</a>
              	</div>
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
               @if($message=Session::get('success'))
                    <div class="alert alert-success" role="alert">
                        <div class="alert-text">{{ucwords($message)}}</div>
                    </div>
               @endif
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>No</th>
                  <th>Tanggal</th>
                  <th>Tipe</th>
                  <th>Qty</th>
                  <th>Grand Total</th>
                  <th>Material</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key => $item)
                <tr>
                  <td>{{$key+1}}</td>
                  <td>{{$item->tanggal}}</td>
                  <td>
                    @if($tipe[$item->id] == 'in')
                      <span class="badge badge-primary">Pembelian</span>
                    @else
                      <span class="badge badge-warning">Penjualan</span>
                    @endif
                  </td>
                  <td>{{$item->qty}}</td>
                  <td>Rp.{{number_format($item->grandtotal)}}</td>
                  <td>
                      <ul>
                          @foreach($material[$item->id] as $materialKey => $materialItem)
                            <li>
                                {{$materialItem['material']}} Sejumlah : {{$materialItem['qty']}} x {{number_format($materialItem['subtotal'])}} = <u>{{number_format($materialItem['total'])}}</u>
                            </li>
                          @endforeach
                      </ul>
                  </td>
                  <td>
                  	<a  href="{{url('transaksi/edit/'.$item->id)}}" 
                  	    style="color: black;" 
                  	    class="fa fa-edit btn btn-warning btn-sm"> 
                  		Edit
                  	</a> &nbsp;
                  	<a  href="{{url('transaksi/delete/'.$item->id)}}" 
                  		style="color: black;" 
                  	    class="fa fa-edit btn-danger btn-sm" 	
                  	    onclick="return confirm('Yakin menghapus data?')"> 
                  		Hapus
                  	</a>
                  </td>
                </tr>    
                @endforeach            
              </table>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
@endsection

@section('script')
<script src="{{url('assets/pluginsdatatables/jquery.dataTables.min.js')}}"></script>
<script src="{{url('assets/pluginsdatatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{url('assets/pluginsdatatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{url('assets/pluginsdatatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script type="text/javascript">
	  $(function () {
    $("#example1").DataTable({
      "responsive": true,
      "autoWidth": false,
    });
  });
</script>
@endsection