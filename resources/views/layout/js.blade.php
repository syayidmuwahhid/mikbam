@if(empty(Request()->session()->get('identity')))
<script>
  // let hasReload = false;
  // if(hasReload == false){
  //   hasReload = true;
  //   location.reload();
  // }
</script>
@endif
<script type="text/javascript">
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    type: "post"
  });

  function AjaxForm(form) {
    var response;
    $.ajax({
      url: form.url,
      data: form.form,
      contentType: false,
      processData: false,
      async: false,
      dataType: "json",
      success: function(data) {
        response = data;
      },
      error: function() {
        response = "error";
      },
    });
    return response;
  }

  function AjaxPostData(url) {
    var response;
    $.ajax({
      url: url,
      async: false,
      dataType: "json",
      success: function(data) {
        response = data;
      },
      error: function() {
        response = "error";
      },
    });
    return response;
  }

  function AjaxGetData(url) {
    var response;
    $.ajax({
      url: url,
      async: false,
      type: 'GET',
      dataType: "json",
      success: function(data) {
        response = data;
      },
      error: function() {
        response = "error";
      },
    });
    return response;
  }

  function ResetModal() {
    $("h5.modal-title").empty();
    $("div.modal-body").empty();
    $("div.modal-footer").empty();
    $('#modal').modal('hide');
  }

  function SwitchSucces(data) {
    $("#selectRouter").empty();

    let sesi_ip = "";
    let sesi_identity = "";
    // @if(isset(Request() -> session() -> get('identity')[0]['ip']))
    // sesi_ip = "{{Request()->session()->get('identity')[0]['ip']}}";
    // sesi_identity = "{{Request()->session()->get('identity')[0]['identity']}}";

    // @endif

    $("#selectRouter").append("<option value='" + sesi_ip + "'>" + sesi_identity + " ~ " + sesi_ip + "</option>");

    for (let i = 0; i < data.id_router.length; i++) {
      if (data.status[i] == "Disconnected") {
        continue;
      }
      if (data.ip_address[i] == sesi_ip) {
        continue;
      }
      $("#selectRouter").append("<option value='" + data.ip_address[i] + "'>" + data.identity[i] + " ~ " + data.ip_address[i] + "</option>");
    }

    $("#selectRouter").change(() => {
      let switchForm = new FormData();
      switchForm.append('ip', $("#selectRouter").val());
      let switchData = setTimeout(() => {
        AjaxForm({
          form: switchForm,
          url: "{{--route('switch-router')--}}"
        });
      }, 500);

      if (switchData != "error") {
        let routeName = "{{request()->route()->getName()}}";
        switch (routeName) {
          case "ip-address":
            GetIP();
            break;

          case "internet-gateway":
            cek();
            break;

          case "dhcp-server":
            ActiveAddress();
            GetDhcpServer();
            break;

          case "simple-queue":
            GetSimpleQueue();
            SimpleInterface();
            break;

            // case "queue-tree":

            // break;

          case "pcq":
            GetPCQ();
            PCQInt();
            break;


          default:
            break;
        }
      } else {
        alert('router tidak dapat terhubung.');
        window.reload();
      }

    });
  }

  function SwitchRouter() {
    $("#selectRouter").append("<option>Loading...</option>");
    setTimeout(() => {
      $.ajax({
        url: "{{--route('get-router-list')--}}",
        dataType: "json",
        success: function(data) {
          SwitchSucces(data);
        },
        error: function() {
          alert('Tidak Ada Router yang aktif. silakan tambah terlebih dahulu');
          window.location = "{{--route('add-router')--}}";
        },
      });

    }, 1000);

  }

  // function SwitchRouter() {
  //   $.ajax({
  //     type: "post",
  //     contentType: "application/json; charset=utf-8",
  //     url: "{{--route('get-router-list')--}}",
  //     data: $(this).serialize(),
  //     dataType: "json",
  //     success: function(data) {
  //       for (let z = data.id_router.length - 1; z >= 0; z--) {
  //         if (data.status[z] == "Connected") {
  //           $("select#selectRouter").append("<option value='" + data.ip_address[z] + "'>" + data.identity[z] + " ~ " + data.ip_address[z] + "</option>");
  //         }
  //       }
  //       for (let z = data.id_router.length - 1; z >= 0; z--) {
  //         if (data.status[z] == "Connected") {
  //           continue;
  //         }
  //         $("select#selectRouter").append("<option value='" + data.ip_address[z] + "'>" + data.identity[z] + " ~ " + data.ip_address[z] + "</option>");
  //       }

  //       $('select#selectRouter').on('change', function() {
  //         let ip = this.value;
  //         $.ajax({
  //           type: "post",
  //           contentType: "application/json; charset=utf-8",
  //           url: "{{--route('switch-router')--}}",
  //           data: {
  //             ip: ip
  //           },
  //           dataType: "json",
  //           beforeSend: function() {
  //             // Show image container
  //             $("select#selectRouter").empty();
  //           },
  //           complete: function(data) {
  //             // Hide image container
  //           },
  //           success: function(data) {
  //             SwitchRouter();
  //             console.log(data);
  //           },
  //           error: function(data) {
  //             SwitchRouter();
  //             alert("Tidak Dapat Terhubung");

  //           }
  //         });
  //       });
  //     },
  //     error: function(data) {
  //       alert("Router Tidak ditemukan, Pastikan router sudah mendapatkan IP Address dan terhubung ke jaringan");
  //     }
  //   });
  // }
</script>

<!-- Vendor JS Files -->
<script src="{{asset('vendor/bootstrap/js/bootstrap.bundle.js')}}"></script>
<script src="{{asset('vendor/php-email-form/validate.js')}}"></script>
<script src="{{asset('vendor/quill/quill.min.js')}}"></script>
<script src="{{asset('vendor/tinymce/tinymce.min.js')}}"></script>
<script src="{{asset('vendor/simple-datatables/simple-datatables.js')}}"></script>
<!-- <script src="{{asset('vendor/chart.js/chart.min.js')}}"></script> -->
<script src="{{asset('vendor/apexcharts/apexcharts.min.js')}}"></script>
<script src="{{asset('vendor/echarts/echarts.min.js')}}"></script>

<!-- Template Main JS File -->
<script src="{{asset('js/main.js')}}"></script>

<!-- My JS File -->

<script src="{{ asset('/plugins/bootstrap-sweetalert/dist/sweetalert2.all.min.js')}}"></script>
<script src="{{ asset('/plugins/block-ui/jquery.blockUI.js') }} "></script>