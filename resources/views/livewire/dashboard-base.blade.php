@extends('layouts.app')
@section('judul')
<title>Dashboard - MIKBAM</title>

@section('pagetitle')
<h1>Dashboard</h1>
@endsection

@section('h2')
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('konten')
<section class="section dashboard">
   <livewire:dashboard />

   <div class="row">
      <div class="col-lg-8">
         <div class="row">

            <!-- Memory Traffic -->
            <div class="col-lg-6">
               <div class="card">
                  <div class="card-body pb-0">
                     <h5 class="card-title" id="memory_dashboard_title"></h5>

                     <div id="memory_stat" style="min-height: 250px;" class="echart"></div>

                  </div>
               </div>
            </div>
            <!-- End Memory Traffic -->

            <!-- HDD Traffic -->
            <div class="col-lg-6">
               <div class="card">
                  <div class="card-body pb-0">
                     <h5 class="card-title" id="hdd_dashboard_title"></h5>

                     <div id="hdd_stat" style="min-height: 250px;" class="echart"></div>

                  </div>
               </div>
            </div>
            <!-- End HDD Traffic -->

            <!-- Reports -->
            <div class="col-12">
               <div class="card">

                  <div class="filter">
                     <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                     <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <li class="dropdown-header text-start">
                           <h6>Filter</h6>
                        </li>

                        <li><a class="dropdown-item" href="#">Today</a></li>
                        <li><a class="dropdown-item" href="#">This Month</a></li>
                        <li><a class="dropdown-item" href="#">This Year</a></li>
                     </ul>
                  </div>

                  <div class="card-body">
                     <h5 class="card-title">Traffic Ether1</h5>

                     <!-- Line Chart -->
                     <canvas id="traffic_dashboard"></canvas>


                     <!-- End Line Chart -->

                  </div>

               </div>
            </div><!-- End Reports -->


         </div>
      </div>

      <!-- Right side columns -->
      <div class="col-lg-4">



         <!-- Budget Report -->
         <div class="card">
            <div class="card-body pb-0">
               <h5 class="card-title">Interface List </h5>
               <div class="table-responsive">
                  <table class="table">
                     <thead>
                        <th>Name</th>
                        <th>Type</th>
                        <th class="text-center">Status</th>
                     </thead>
                     <tbody id="int_list">
                     </tbody>
                  </table>
               </div>
            </div>
         </div><!-- End Budget Report -->

         <!-- Recent Activity -->
         <div class="card">

            <div class="card-body">
               <h5 class="card-title">Log</h5>

               <div class="activity table-responsive">
                  <table class="table">
                     <thead>
                        <tr>
                           <th>Waktu</th>
                           <th>Deskripsi</th>
                           <th>Status</th>
                        </tr>
                     </thead>
                     <tbody id="log_dashboard">
                     </tbody>
                  </table>
               </div>

            </div>
         </div><!-- End Recent Activity -->

      </div><!-- End Right side columns -->
   </div>

   @endsection
   @stop

   @push('script')
   <script>
      var ctx = document.getElementById('traffic_dashboard').getContext('2d');
      var myChart = new Chart(ctx, {
         type: 'line',
         data: {
            labels: ['00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00'],
            datasets: [{
               label: 'rx rate',
               data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
               backgroundColor: [
                  'rgba(255, 99, 132, 1)'
               ],
               borderColor: [
                  'rgba(255, 99, 132, 1)'
               ]
            }, {
               label: 'tx rate',
               data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
               backgroundColor: [
                  'rgba(75, 192, 192, 1)'
               ],
               borderColor: [
                  'rgba(75, 192, 192, 1)'
               ]
            }]
         },
         options: {
            scales: {
               y: {
                  beginAtZero: true
               }
            },
            borderWidth: 1,
            showLine: true,
            spanGaps: true,
            animation: false,
         }
      });

      setInterval(() => {
         $.post("{{ route('get-trafic-eth1') }}", {
            _token: $('#token').val()
         }, (data) => {
            //add
            myChart.data.labels.push(data.time);
            myChart.data.datasets[0].data.push(data.rx_byte);
            myChart.data.datasets[1].data.push(data.tx_byte);

            //remove first
            if (myChart.data.labels.length > 10) {
               myChart.data.labels.shift();
               myChart.data.datasets.forEach((dataset) => {
                  dataset.data.shift();
               });
            }
            myChart.update();
         });
      }, 1000);
   </script>
   @endpush