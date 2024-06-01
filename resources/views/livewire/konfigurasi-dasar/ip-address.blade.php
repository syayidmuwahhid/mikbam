<div>
    @section('judul')
    <title>IP Address</title>

    @section('pagetitle')
    <h1>IP Address</h1>
    @endsection

    @section('h2')
    <li class="breadcrumb-item">Konfigurasi Dasar</li>
    <li class="breadcrumb-item active">IP Address</li>
    @endsection
    @stop

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            IP Address List
                            <button class="float-end btn btn-primary" onclick="modalAdd()">Add</button>
                        </h5>
                        <div class="table-responsive">
                            <table id="tabel_ip" class="table table-striped" style="width:100%">
                                <thead class="text-center">
                                    <th>#</th>
                                    <th>Flags</th>
                                    <th>Address</th>
                                    <th>Network</th>
                                    <th>Interface</th>
                                    <th>Action</th>
                                </thead>
                                <tbody wire:poll.keep-alive.2000ms="getData">
                                    @php($no = 1)
                                    @foreach ($ipList as $ip)
                                    <tr class="{{ isset($ip['lineClass']) ? $ip['lineClass'] : '' }} {{ $ip['tr_class'] }}">
                                        <td class="text-center">{{ $no++ }}</td>
                                        <td class=" text-center" data-toggle="tooltip" data-placement="top" title="{{ $ip['label'] }}" id='tdflag" + i + "'>{{ $ip['dy'] }}</td>
                                        <td class="text-center">{{ $ip['address'] }}</td>
                                        <td class="text-center">{{ $ip['network'] }}</td>
                                        <td class="text-center">{{ $ip['interface'] }}</td>
                                        <td class="text-center">
                                            <button type='button' wire:click="endisable(`{{ $ip['.id'] }}`, `{{ $ip['ds'] }}`)" class="btn {{ $ip['dscolor'] }} btn-sm">{{ $ip['ds'] }}</button>
                                            <button type='button' wire:click="$emit('remove', `{{ $ip['.id'] }}`, `{{ $ip['address'] }}`)" class='btn btn-danger btn-sm'>Remove</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('script')
    <script>
        window.livewire.on('remove', (id, ip) => {
            Swal.fire({
                icon: 'warning',
                title: 'Yakin Untuk Hapus ?',
                text: ip,
                showCancelButton: true,
                confirmButtonText: 'Yes',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    blokUI('Loading...');
                    Livewire.emit('removeItem', id);
                }
            });
        });

        modalAdd = () => {
            <?= "var interfaces = " . json_encode($interfaces) . ";\n"; ?>
            $('#ModalFormLabel').html('Tambah IP Address');

            var html = '';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">IP Address :</label></div>' +
                '<div class="col-sm-8"><input type="text" name="ip" id="ip" class="form-control" placeholder="0.0.0.0/0" title="Penulisan IP Address menggunakan kode CIDR [x.x.x.x/x]"></div>' +
                '</div>';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Interface :</label></div>' +
                '<div class="col-sm-8"><select name="interface" id="interface" class="form-select"></select></div>' +
                '</div>';
            $('.modal-body').empty().append(html);

            var htmlx = '';
            $.each(interfaces, (i, val) => {
                htmlx += '<option value="' + val.name + '">' + val.name + '</option>';
            });
            $('#interface').empty().append(htmlx);

            $('#ModalForm').modal('show');

            $('#ip').focusout(() => {
                var ip = $('#ip').val().split('/');
                if (ip.length != 2) {
                    notif('error', 'Kesalahan', 'IP Address tidak valid [eg. 192.168.1.1/24]');
                    $('#ip').val('');
                }
            });

            $('#modal_submit').submit(function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();

                blokUI('Loading...');
                Livewire.emit('storeIP', $('#ip').val(), $('#interface').val());
                $('#ModalForm').modal('hide');
            });
        }
        $(document).ready(function() {
            $('#tabel_ip').DataTable();
        });
    </script>
    @endpush
</div>