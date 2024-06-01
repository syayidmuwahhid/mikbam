@extends('layouts.app')
@section('judul')
<title>Monitoring Traffic</title>

@section('pagetitle')
<h1>Monitoring Traffic</h1>
@endsection

@section('h2')
<li class="breadcrumb-item">Monitoring Traffic</li>
<li class="breadcrumb-item active">Monitoring Traffic</li>
@endsection


@section('konten')
<section class="section">
   <div class="card">
      <div class="card-body">
         <div class="card-title">
            <form id="monitor_form">
               <div class="row">
                  <div class="col-lg-6">
                     <select name="queue_type" class="form-select">
                        <option value="">Pilih Type Queue</option>
                        <option value="0">Simple Queue/PCQ</option>
                        <option value="1">Queue Tree</option>
                     </select>
                  </div>
                  <div class="col-lg-6">
                     <select name="queue_name" class="form-select">
                     </select>
                  </div>
               </div>
            </form>
         </div>

         <div id="chart_container" style="display: none;">
            <canvas id="myChart"></canvas>
         </div>
      </div>
   </div>
</section>
<livewire:monitoring-traffic.monitoring />
@endsection
@stop