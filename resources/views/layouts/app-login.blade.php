<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta content="width=device-width, initial-scale=1.0" name="viewport">

   <title>Login - MIKBAM</title>
   <meta content="" name="description">
   <meta content="" name="keywords">

   <!-- Favicons -->
   <link href="{{ asset('/img/favicon.png') }}" rel="icon">
   <link href="{{ asset('/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

   <!-- Google Fonts -->
   <link href="{{ asset('/css/opensansfont.css') }}" rel="stylesheet">

   <!-- Vendor CSS Files -->
   <link href="{{ asset('/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
   <link href="{{ asset('/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
   <link href="{{ asset('/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
   <link href="{{ asset('/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
   <link href="{{ asset('/vendor/quill/quill.snow.css') }}" rel="stylesheet">
   <link href="{{ asset('/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
   <link href="{{ asset('/vendor/simple-datatables/style.css') }}" rel="stylesheet">

   <link href="{{ asset('/plugins/bootstrap-sweetalert/dist/sweetalert2.min.css') }}" rel="stylesheet">
   <link href="{{ asset('/plugins/bootstrap-sweetalert/dist/toast.css') }}" rel="stylesheet">

   <!-- Template Main CSS File -->
   <link href="{{ asset('/css/style.css') }}" rel="stylesheet">

   <script src="{{asset('/js/jquery-3.6.0.min.js')}}"></script>
   <script src="{{ asset('/plugins/block-ui/jquery.blockUI.js') }} "></script>
   @livewireStyles

   <!-- =======================================================
  * Template Name: NiceAdmin - v2.1.0
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

   <main>
      <div class="container">

         <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
            <div class="container">
               {{ $slot }}
            </div>

         </section>

      </div>
   </main><!-- End #main -->

   <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

   <!-- Vendor JS Files -->
   <!-- <script src="{{ asset('/vendor/bootstrap/js/bootstrap.bundle.js') }}"></script> -->
   <!-- <script src="{{ asset('/vendor/php-email-form/validate.js') }}"></script> -->
   <!-- <script src="{{ asset('/vendor/quill/quill.min.js') }}"></script> -->
   <script src="{{ asset('/vendor/tinymce/tinymce.min.js') }}"></script>
   <!-- <script src="{{ asset('/vendor/simple-datatables/simple-datatables.js') }}"></script> -->
   <!-- <script src="{{ asset('/vendor/chart.js/chart.min.js') }}"></script> -->
   <!-- <script src="{{ asset('/vendor/apexcharts/apexcharts.min.js') }}"></script> -->
   <!-- <script src="{{ asset('/vendor/echarts/echarts.min.js') }}"></script> -->

   <!-- Template Main JS File -->
   <script src="{{ asset('/js/main.js') }}"></script>
   <script src="{{ asset('/plugins/bootstrap-sweetalert/dist/sweetalert2.all.min.js')}}"></script>
   <script src="{{ asset('/plugins/core/global.js')}}"></script>
   @livewireScripts


</body>

</html>