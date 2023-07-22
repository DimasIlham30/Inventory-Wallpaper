@extends('layouts.main')

@section('content')
<div class="content-wrapper">
 <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Dashboard</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Info boxes -->
        <div class="row">
          <div class="col-12 col-sm-6 col-md-6">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fa fa-clone"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Total Material</span>
                <span class="info-box-number">
                  {{$material}}
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-12 col-sm-6 col-md-6">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-danger elevation-1"><i class="fa fa-dollar"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Total Transaksi</span>
                <span class="info-box-number">{{$transaksi}}</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->

          <!-- fix for small devices only -->
          <div class="clearfix hidden-md-up"></div>

        </div>
        <!-- /.row -->

        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header" style="text-align: center;">
                <p align="center" class="pt-2" style="margin-bottom: 0;">
                    Selamat Datang Di Sistem Informasi Analisa Penjualan ABC Analisis (SisApelABC)
                </p>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fa fa-minus"></i>
                  </button>

                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div class="row">

                  <div class="col-md-12">
                    <p class="">
                      Sistem Informasi Analisa Penjualan dengan metode ABC Analisis adalah sebuah platform canggih yang mengintegrasikan data penjualan dari berbagai produk secara menyeluruh, dengan tujuan menyediakan wawasan mendalam mengenai performa penjualan berdasarkan kategori produk. Dengan pendekatan metode ABC Analisis, sistem ini secara otomatis mengklasifikasikan produk berdasarkan kontribusinya terhadap total penjualan perusahaan, sehingga memungkinkan para pemangku kepentingan untuk fokus pada produk-produk yang memiliki dampak signifikan terhadap pendapatan perusahaan
                    </p>
                    <p class="">
                      Melalui antarmuka yang user-friendly, pengguna dapat dengan mudah mengakses laporan-laporan visual dan ringkasan hasil analisis, termasuk persentase kontribusi penjualan setiap kategori produk (A, B, atau C), rincian penjualan per produk, dan tren penjualan dari waktu ke waktu. Sistem ini juga memungkinkan pengguna untuk menentukan parameter dan periode waktu yang ingin dianalisis, sehingga memudahkan dalam mengidentifikasi pola-pola penjualan yang berpotensi mempengaruhi strategi bisnis perusahaan.
                    </p>
                    <p class="">
                      Dengan kemampuan untuk menganalisis dan memahami struktur penjualan secara lebih mendalam, sistem ini membantu pengambilan keputusan bisnis menjadi lebih tepat sasaran dan efektif. Hal ini memungkinkan perusahaan untuk mengoptimalkan strategi pemasaran, manajemen stok, dan alokasi sumber daya dengan berlandaskan pada data yang akurat dan real-time, meningkatkan efisiensi operasional serta keuntungan bisnis secara keseluruhan.
                    </p>
                    <!-- /.progress-group -->
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
              </div>
              <!-- ./card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->


      </div><!--/. container-fluid -->
    </section>
    <!-- /.content -->
</div>
@endsection
