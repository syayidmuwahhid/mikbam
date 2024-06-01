<!DOCTYPE html>
<html>

<head>
  @section('judul')
  @show
  @include('layout.head')
</head>

<body>
  @include('layout.header')
  @include('layout.sidebar')
  <main id="main" class="main">
    <div class="pagetitle">
      @section('pagetitle')
      @show
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          @section('h2')
          @show
        </ol>
      </nav>
    </div><!-- End Page Title -->
    @section('konten')
    @show
    <div wire:poll>
      {{now()}}
    </div>
    <!-- <script>GetIP();</script> -->
  </main>

  @include('sweetalert::alert')
  @include('layout.footer')
  @include('layout.js')

  {{--@if(request()->route()->getName() != "add-router")--}}
  <!-- <script>
    SwitchRouter();
  </script> -->
  {{--@endif--}}

</body>

</html>

<!-- Modal -->
<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modallabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modallabel"></h5>
        <button type="button" class="btn-close"></button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>