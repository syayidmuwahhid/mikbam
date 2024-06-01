<!DOCTYPE html>
<html>

<head>
   @yield('judul')
   <meta charset="utf-8">
   <meta content="width=device-width, initial-scale=1.0" name="viewport">
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <meta content="" name="description">
   <meta content="" name="keywords">

   <!-- Favicons -->
   <link href="{{asset('/img/favicon.png')}}" rel="icon">
   <link href="{{asset('/img/apple-touch-icon.png')}}" rel="apple-touch-icon">

   <!-- Google Fonts -->
   <link href="https://fonts.gstatic.com" rel="preconnect">
   <!-- <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet"> -->
   <link href="{{asset('/css/font.css')}}" rel='stylesheet' type='text/css'>

   <!-- Vendor CSS Files -->
   <link href="{{asset('/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
   <link href="{{asset('/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
   <link href="{{asset('/vendor/remixicon/remixicon.css')}}" rel="stylesheet">
   <link href="{{asset('/vendor/boxicons/css/boxicons.min.css')}}" rel="stylesheet">
   <link href="{{asset('/vendor/quill/quill.snow.css')}}" rel="stylesheet">
   <link href="{{asset('/vendor/quill/quill.bubble.css')}}" rel="stylesheet">
   <!-- <link href="{{asset('/vendor/simple-datatables/style.css')}}" rel="stylesheet"> -->
   <link rel="stylesheet" type="text/css" href="{{ asset('/plugins/datatables/datatables.min.css') }}" />


   <!-- Template Main CSS File -->
   <link href="{{asset('/css/style.css')}}" rel="stylesheet">

   <link href="{{ asset('/plugins/bootstrap-sweetalert/dist/sweetalert2.min.css') }}" rel="stylesheet">
   <link href="{{ asset('/plugins/bootstrap-sweetalert/dist/toast.css') }}" rel="stylesheet">

   <!--=======================================================* Template Name: NiceAdmin - v2.1.0 * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ * Author: BootstrapMade.com * License: https://bootstrapmade.com/license/========================================================-->

   <!-- JQuery -->

   <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" > -->
   <!-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> -->
   <script src="{{asset('/js/jquery-3.6.0.min.js')}}"></script>
   <!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> -->
   <!-- <script type="text/javascript" src="{{asset('/js/jquery.steps.min.js')}}"></script> -->
   <script type="text/javascript" src="{{asset('/js/select2.min.js')}}"></script>
   <!-- <script type="text/javascript" src="{{asset('/js/cookie.js')}}"></script> -->
   <!-- <script src="https://d3js.org/d3.v5.min.js"
        integrity="sha384-HL96dun1KbYEq6UT/ZlsspAODCyQ+Zp4z318ajUPBPSMzy5dvxl6ziwmnil8/Cpd"
        crossorigin="anonymous">
   </script> -->
   <script src="{{asset('vendor/chart.js/chart.min.js')}}"></script>
   @livewireStyles
</head>

<body>
   @include('layout.header')
   @include('layout.sidebar')

   <main id="main" class="main">
      <div class="pagetitle">
         @yield('pagetitle')
         <nav>
            <ol class="breadcrumb">
               <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
               @yield('h2')
            </ol>
         </nav>
      </div><!-- End Page Title -->
      {{ $slot }}
      @yield('konten')

      <input type="hidden" id="token" value="{{ csrf_token() }}" />
      <!-- <script>GetIP();</script> -->

      <!-- Modal -->
      <div class="modal fade" id="ModalForm" tabindex="-1" aria-labelledby="ModalFormLabel" aria-hidden="true">
         <div class="modal-dialog">
            <div class="modal-content">
               <form id="modal_submit">
                  <div class="modal-header">
                     <h5 class="modal-title" id="ModalFormLabel"></h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">

                  </div>
                  <div class="modal-footer">
                     <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                     <button type="submit" class="btn btn-primary">Save</button>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </main>

   <!-- ======= Footer ======= -->
   <footer id="footer" class="footer">
      <div class="copyright">
         &copy; Copyright <strong><span>Syayidul Muwahhid</span></strong>. All Rights Reserved
      </div>
      <div class="credits">
         <!-- All the links in the footer should remain intact. -->
         <!-- You can delete the links only if you purchased the pro version. -->
         <!-- Licensing information: https://bootstrapmade.com/license/ -->
         <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
         Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
      </div>
   </footer><!-- End Footer -->

   <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

   @include('sweetalert::alert')

   <!-- Vendor JS Files -->
   <script src="{{asset('vendor/bootstrap/js/bootstrap.bundle.js')}}"></script>
   <script src="{{asset('vendor/php-email-form/validate.js')}}"></script>
   <script src="{{asset('vendor/quill/quill.min.js')}}"></script>
   <script src="{{asset('vendor/tinymce/tinymce.min.js')}}"></script>
   <!-- <script src="{{asset('vendor/simple-datatables/simple-datatables.js')}}"></script> -->
   <script type="text/javascript" src="{{ asset('/plugins/datatables/datatables.min.js') }}"></script>

   <!-- <script src="{{asset('vendor/chart.js/chart.min.js')}}"></script> -->
   <script src="{{asset('vendor/apexcharts/apexcharts.min.js')}}"></script>
   <script src="{{asset('vendor/echarts/echarts.min.js')}}"></script>

   <!-- Template Main JS File -->
   <script src="{{asset('js/main.js')}}"></script>

   <!-- My JS File -->

   <script src="{{ asset('/plugins/bootstrap-sweetalert/dist/sweetalert2.all.min.js')}}"></script>
   <script src="{{ asset('/plugins/block-ui/jquery.blockUI.js') }} "></script>
   <script src="{{ asset('/plugins/core/global.js')}}"></script>
   @livewireScripts
   @stack('script')

</body>

</html>