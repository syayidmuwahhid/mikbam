  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        @php($kondisi = request()->session()->get('menu_active') == "dashboard")
        @php($active = (!$kondisi) ? "collapsed" : "")
        <a class="nav-link {{$active}}" href="{{ route('dashboard') }}">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <div id="menuu">
        <li class="nav-item">
          @if(request()->session()->get('menu_active') == "konfigurasi-dasar")
          @php($show = "show")
          @php($active = "")
          @else
          @php($show = "")
          @php($active = "collapsed")
          @endif
          <a class="nav-link {{$active}}" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-menu-button-wide"></i><span>Konfigurasi Dasar</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="components-nav" class="nav-content collapse {{$show}}" data-bs-parent="#sidebar-nav">
            <li>
              @php($kondisi = request()->session()->get('sub_menu_active') == "ip-address")
              @php($active = ($kondisi) ? "active" : "")
              <a href="{{ route('ip-address') }}" class="{{$active}}">
                <i class="bi bi-circle"></i><span>IP Address</span>
              </a>
            </li>
            <li>
              @php($kondisi = request()->session()->get('sub_menu_active') == "internet-gateway")
              @php($active = ($kondisi) ? "active" : "")
              <a href="{{ route('internet-gateway') }}" class="{{$active}}">
                <i class="bi bi-circle"></i><span>Internet Gateway</span>
              </a>
            </li>
            <!-- <li>
            <a href="components-accordion.html">
              <i class="bi bi-circle"></i><span>Bridge Port</span>
            </a>
          </li> -->
            <li>
              @php($kondisi = request()->session()->get('sub_menu_active') == "dhcp-server")
              @php($active = ($kondisi) ? "active" : "")
              <a href="{{ route('dhcp-server') }}" class="{{$active}}">
                <i class="bi bi-circle"></i><span>DHCP Server</span>
              </a>
            </li>
          </ul>
        </li><!-- End Components Nav -->

        <li class="nav-item">
          @if(request()->session()->get('menu_active') == "management-bandwidth")
          @php($show = "show")
          @php($active = "")
          @else
          @php($show = "")
          @php($active = "collapsed")
          @endif
          <a class="nav-link {{$active}}" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-layout-text-window-reverse"></i><span>Manajemen Bandwidth</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="tables-nav" class="nav-content collapse {{$show}}" data-bs-parent="#sidebar-nav">
            <li>
              @php($kondisi = request()->session()->get('sub_menu_active') == "simple-queue")
              @php($active = ($kondisi) ? "active" : "")
              <a href="{{ route('simple-queue') }}" class="{{$active}}">
                <i class="bi bi-circle"></i><span>Simple Queue</span>
              </a>
            </li>
            <li>
              @php($kondisi = request()->session()->get('sub_menu_active') == "queue-tree")
              @php($active = ($kondisi) ? "active" : "")
              <a href="{{ route('queue-tree') }}" class="{{$active}}">
                <i class="bi bi-circle"></i><span>Queue Tree</span>
              </a>
            </li>
            <li>
              @php($kondisi = request()->session()->get('sub_menu_active') == "pcq")
              @php($active = ($kondisi) ? "active" : "")
              <a href="{{ route('pcq') }}" class="{{$active}}">
                <i class="bi bi-circle"></i><span>Perconnection Queue</span>
              </a>
            </li>
          </ul>
        </li><!-- End Tables Nav -->

        @if(request()->session()->get('menu_active') == "monitoring-traffic")
        @php($show = "show")
        @php($active = "")
        @else
        @php($show = "")
        @php($active = "collapsed")
        @endif
        <li class="nav-item">
          <a class="nav-link collapsed {{$active}}" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-bar-chart"></i><span>Monitoring Traffic</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="charts-nav" class="nav-content collapse {{$show}}" data-bs-parent="#sidebar-nav">
            <li>
              @php($kondisi = request()->session()->get('sub_menu_active') == "prioritas-traffic")
              @php($active = ($kondisi) ? "active" : "")
              <a href="{{ route('prioritas-traffic') }}" class="{{$active}}">
                <i class="bi bi-circle"></i><span>Prioritas Traffic</span>
              </a>
            </li>
            <li>
              @php($kondisi = request()->session()->get('sub_menu_active') == "drop-traffic")
              @php($active = ($kondisi) ? "active" : "")
              <a href="{{ route('drop-traffic') }}" class="{{$active}}">
                <i class="bi bi-circle"></i><span>Drop Traffic</span>
              </a>
            </li>
            <li>
              @php($kondisi = request()->session()->get('sub_menu_active') == "monitoring-traffic")
              @php($active = ($kondisi) ? "active" : "")
              <a href="{{ route('monitoring') }}" class="{{$active}}">
                <i class="bi bi-circle"></i><span>Monitoring Traffic</span>
              </a>
            </li>
            <li>
              @php($kondisi = request()->session()->get('sub_menu_active') == "notifikasi")
              @php($active = ($kondisi) ? "active" : "")
              <a href="{{ route('notifikasi') }}" class="{{$active}}">
                <i class="bi bi-circle"></i><span>Notifikasi</span>
              </a>
            </li>
          </ul>
        </li><!-- End Charts Nav -->

        <li class="nav-item">
          @if(request()->session()->get('menu_active') == "lainnya")
          @php($show = "show")
          @php($active = "")
          @else
          @php($show = "")
          @php($active = "collapsed")
          @endif
          <a class="nav-link {{$active}}" data-bs-target="#icons-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-gem"></i><span>Lainnya</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="icons-nav" class="nav-content collapse {{$show}}" data-bs-parent="#sidebar-nav">

            <li>
              <a href="#" id="identity">
                <i class="bi bi-circle"></i><span>Identity</span>
                @push('script')
                <script type="text/javascript">
                  $("#identity").click(function(e) {
                    e.preventDefault();
                    $('#ModalFormLabel').html('Change Identity');
                    var html = '';
                    html += '<div class="row mb-3">' +
                      '<div class="col-sm-4"><label class="col-form-label">Identity :</label></div>' +
                      '<div class="col-sm-8"><input type="text" name="identity" class="form-control" required></div>' +
                      '</div>';
                    $('.modal-body').empty().append(html);

                    //get identity
                    blokUI('Loading . . .');
                    $.post("{{ route('get-identity') }}", {
                      _token: $('#token').val()
                    }, (data) => {
                      if (data.code == 200) {
                        $('input[name=identity]').val(data.data);
                      } else {
                        notif('error', 'Kesalahan', data.msg);
                        $('#ModalForm').modal('hide');
                      }
                      $.unblockUI();
                    });
                    $('#ModalForm').modal('show');

                    $('#modal_submit').submit(function(e) {
                      e.preventDefault();
                      e.stopImmediatePropagation();

                      blokUI('Loading...');
                      $.post("{{ route('set-identity') }}", {
                        _token: $('#token').val(),
                        identity: $('input[name=identity]').val()
                      }, (data) => {
                        if (data.code == 200) {
                          notif('success', 'Berhasil Merubah Identity');
                          $('#ModalForm').modal('hide');
                        } else {
                          notif('error', 'Kesalahan', data.msg);
                        }
                        $.unblockUI();
                      });
                    });
                  });
                </script>
                @endpush
              </a>
            </li>
            <li>
              @php($kondisi = request()->session()->get('sub_menu_active') == "akun")
              @php($active = ($kondisi) ? "active" : "")
              <a href="{{ route('akun') }}" class="{{$active}}">
                <i class="bi bi-circle"></i><span>Akun</span>
              </a>
            </li>
            <li>
              <a href="#" id="clock">
                <i class="bi bi-circle"></i><span>Clock</span>
              </a>
              @push('script')
              <script type="text/javascript">
                $("#clock").click(function(e) {
                  e.preventDefault();
                  $('#ModalFormLabel').html('Clock');
                  var html = '';
                  html += '<div class="row mb-3">' +
                    '<div class="col-sm-4"><label class="col-form-label">Time :</label></div>' +
                    '<div class="col-sm-8"><input readonly type="text" name="time" id="time" class="form-control" required></div>' +
                    '</div>';
                  html += '<div class="row mb-3">' +
                    '<div class="col-sm-4"><label class="col-form-label">Date :</label></div>' +
                    '<div class="col-sm-8"><input readonly type="text" name="date" id="date" class="form-control" required></div>' +
                    '</div>';
                  html += '<div class="row mb-3">' +
                    '<div class="col-sm-4"><label class="col-form-label">Time Zone :</label></div>' +
                    '<div class="col-sm-8"><input readonly type="text" name="time_zone" id="time_zone" class="form-control" required><span><i>*Pastikan Router Terkonesi Internet</i></span></div>' +
                    '</div>';
                  $('.modal-body').empty().append(html);

                  //get clock
                  let clockInterval = setInterval(function() {
                    $.post("{{ route('get-clock') }}", {
                      _token: $('#token').val()
                    }, (data) => {
                      if (data.code == 200) {
                        $('#time').val(data.data[0].time);
                        $('#date').val(data.data[0].date);
                        $('#time_zone').val(data.data[0].time_zone_name);
                      } else {
                        notif('error', 'Kesalahan', data.msg);
                        $('#ModalForm').modal('hide');
                      }
                    });
                  }, 900);
                  $('#ModalForm').modal('show');
                  $('#ModalForm').on('hidden.bs.modal', function() {
                    clearInterval(clockInterval);
                    $('button[type=submit]').html('save');
                  });
                  $('button[type=submit]').html('Automatically');

                  <?= "var negaras = " . json_encode(\App\Helpers\Anyhelpers::getJson('time_zone')) . ";\n"; ?>

                  var negara = new Array;
                  negaras.forEach(element => {
                    negara[element.negara] = element.negara;
                  });

                  $('#modal_submit').submit(function(e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();

                    Swal.fire({
                      title: 'Pilih Lokasi',
                      input: 'select',
                      inputOptions: negara,
                      inputPlaceholder: 'Pilih Negara',
                      showCancelButton: true,
                      inputValidator: (value) => {
                        return new Promise((resolve) => {
                          if (value !== '') {
                            blokUI('Loading...');
                            $.post("{{ route('set-clock') }}", {
                              _token: $('#token').val(),
                              negara: value
                            }, (data) => {
                              if (data.code == 200) {
                                notif('success', 'Berhasil Merubah Clock');
                                // $('#ModalForm').modal('hide');
                              } else {
                                notif('error', 'Kesalahan', data.msg);
                              }
                              $.unblockUI();
                            });
                            resolve();
                          } else {
                            resolve('Silakan Pilih Negara Terlebih Dahulu')
                          }
                        })
                      }
                    });
                  });

                });
              </script>
              @endpush
            </li>
            <li>
              <a href="#" id="backup">
                <i class="bi bi-circle"></i><span>Backup</span>
              </a>
              @push('script')
              <script type="text/javascript">
                $("#backup").click(function(e) {
                  e.preventDefault();

                  $('#ModalFormLabel').html('Backup Konfigurasi');
                  var html = '';
                  html += '<div class="row mb-3">' +
                    '<div class="col-sm-4"><label class="col-form-label">Encryption :</label></div>' +
                    '<div class="col-sm-8"><select class="form-select" name="enkripsi" id="enkripsi"><option value="0">no</option><option value="1">aes-sha256</option></select></div>' +
                    '</div>';
                  html += '<div class="row mb-3" style="display:none;" id="pass_container">' +
                    '<div class="col-sm-4"><label class="col-form-label">Password :</label></div>' +
                    '<div class="col-sm-8"><input type="password" name="encrypt_password" class="form-control"></div>' +
                    '</div>';
                  $('.modal-body').empty().append(html);

                  $('#enkripsi').change(() => {
                    if ($('#enkripsi').val() == 1) {
                      $('#pass_container').show();
                      $('input[name=encrypt_password]').attr('required', '');
                    } else {
                      $('#pass_container').hide();
                      $('input[name=encrypt_password]').removeAttr('required');
                    }
                  });

                  $('#ModalForm').modal('show');

                  $('#modal_submit').submit(function(e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();

                    let encryp = $("select[name=enkripsi]").val();
                    let pass = $("input[name=encrypt_password]").val();
                    window.open("{{ route('backup') }}?auth=" + encryp + "&pass=" + pass, "_blank");
                  });
                });
              </script>
              @endpush
            </li>
            <li>
              <a href="#" id="restore">
                <i class="bi bi-circle"></i><span>Restore</span>
              </a>
              @push('script')
              <script type="text/javascript">
                $("#restore").click(function(e) {
                  e.preventDefault();

                  $('#ModalFormLabel').html('Restore Konfigurasi');
                  var html = '';
                  html += '<div class="row mb-3">' +
                    '<div class="col-sm-4"><label class="col-form-label">Upload File :</label></div>' +
                    '<div class="col-sm-8"><input type="file" name="restore_file" class="form-control" accept=".backup"></div>' +
                    '</div>';
                  html += '<div class="row mb-3">' +
                    '<div class="col-sm-4"><label class="col-form-label">Password :</label></div>' +
                    '<div class="col-sm-8"><input type="password" name="restore_password" title="kosongkan jika file tidak dienkripsi" class="form-control"></div>' +
                    '</div>';
                  $('.modal-body').empty().append(html);

                  $('#ModalForm').modal('show');

                  $('#modal_submit').submit(function(e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    console.log('ok');

                    blokUI('Loading...');
                    let form = new FormData();
                    let file = $("input[name=restore_file]")[0].files;
                    form.append('_token', $('#token').val());
                    form.append('pass', $("input[name=restore_password]").val());
                    form.append('file', file[0]);
                    $.ajax({
                      url: "{{ route('restore') }}",
                      data: form,
                      contentType: false,
                      processData: false,
                      dataType: "json",
                      method: "post",
                      success: function(data) {
                        if (data.code == 200) {
                          notif('success', 'Berhasil', data.msg);
                          $('#ModalForm').modal('hide');
                          window.location.href='/logout';
                        } else {
                          notif('error', 'Kesalahan', data.msg);
                        }
                        $.unblockUI();
                      },
                      error: function(data) {
                        notif('error', 'Kesalahan', data);
                      },
                    });

                  });
                });
              </script>
              @endpush
            </li>
            <li>
              @php($kondisi = request()->session()->get('sub_menu_active') == "daftar-router")
              @php($active = ($kondisi) ? "active" : "")
              <a href="{{ route('daftar-router') }}" class="{{ $active }}">
                <i class="bi bi-circle"></i><span>Daftar Router</span>
              </a>
            </li>
            <li>
              @php($kondisi = request()->session()->get('sub_menu_active') == "service")
              @php($active = ($kondisi) ? "active" : "")
              <a href="{{ route('service') }}" class="{{$active}}">
                <i class="bi bi-circle"></i><span>Service</span>
              </a>
            </li>
            <li>
              @php($kondisi = request()->session()->get('sub_menu_active') == "time-zone")
              @php($active = ($kondisi) ? "active" : "")
              <a href="{{ route('time-zone') }}" class="{{$active}}">
                <i class="bi bi-circle"></i><span>Time Zone</span>
              </a>
            </li>
            <li>
              <a href="{{ route('logout') }}">
                <i class="bi bi-circle"></i><span>Logout</span>
              </a>
            </li>
          </ul>
        </li><!-- End Icons Nav -->
      </div>

      <!-- <li class="nav-heading">Pages</li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="users-profile.html">
          <i class="bi bi-person"></i>
          <span>Profile</span>
        </a> -->
      <!--</li>-->
      <!-- End Profile Page Nav -->

    </ul>

    @push('script')
    <script type="text/javascript">
      $(document).on("keydown", ":input:not(textarea)", function(event) {
        return event.key != "Enter";
      });
    </script>
    @endpush
  </aside><!-- End Sidebar-->