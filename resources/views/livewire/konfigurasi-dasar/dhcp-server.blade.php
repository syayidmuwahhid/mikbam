<div>
    @section('judul')
    <title>DHCP Server</title>

    @section('pagetitle')
    <h1>DHCP Server</h1>
    @endsection

    @section('h2')
    <li class="breadcrumb-item">Konfigurasi Dasar</li>
    <li class="breadcrumb-item active">DHCP Server</li>
    @endsection
    @stop

    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    DHCP Server List
                    <!-- <button id="manual" class="float-end btn btn-warning btn-sm">Manual</button> -->
                    <button id="auto" class="float-end btn btn-primary btn-sm" onclick="modalAdd()">Add</button>
                </h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Flags</th>
                                <th>Interface</th>
                                <th>Network</th>
                                <th>Gateway</th>
                                <th>Address Pool</th>
                                <th>Lease Time</th>
                                <!-- <th>Status</th> -->
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody wire:poll.keep-alive.2000ms="getData">
                            @if(!empty($dhcps))
                            @php($no = 1)
                            @foreach($dhcps as $dhcp)
                            <tr class="{{ $dhcp['flag'] == 'I' ? 'text-danger fst-italic' : '' }} {{ $dhcp['tr_class'] }}">
                                <td>{{ $no++ }}</td>
                                <td data-toggle="tooltip" data-placement="top" title="{{ $dhcp['flag_label'] }}">{{ $dhcp['flag'] }}</td>
                                <td>{{ $dhcp['interface'] }}</td>
                                <td>{{ $dhcp['network'] }}</td>
                                <td>{{ $dhcp['gateway'] }}</td>
                                <td>
                                    @foreach(explode(',', $dhcp['pool']) as $pool)
                                    {{ $pool }} <br>
                                    @endforeach
                                </td>
                                <td>{{ $dhcp['lease-time'] }}</td>
                                <td>
                                    <button type="button" wire:click="endisable(`{{ $dhcp['.id'] }}`, `{{ $dhcp['ds'] }}`, `{{ $dhcp['gateway'] }}`)" class="btn {{ $dhcp['dscolor'] }} btn-sm">{{ $dhcp['ds'] }}</button>
                                    <button type="button" wire:click="$emit('remove', `{{ $dhcp['.id'] }}`, `{{ $dhcp['interface'] }}`)" class="btn btn-danger btn-sm">Remove</button>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                            <!-- <tr id="lod"><td colspan="7"><center>..LOADING...</center></td></tr> -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    @push('script')
    <script>
        window.livewire.on('remove', (id, interface, gateway) => {
            Swal.fire({
                icon: 'warning',
                title: 'Yakin Untuk Hapus ?',
                text: interface,
                showCancelButton: true,
                confirmButtonText: 'Yes',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    blokUI('Loading...');
                    Livewire.emit('removeItem', id, gateway);
                }
            });
        });

        modalAdd = () => {
            <?= "var interfaces = " . json_encode($interfaces) . ";\n"; ?>
            $('#ModalFormLabel').html('Tambah DHCP Client');

            var html = '';
            html += '<label>Pilih Interface :</label>';
            html += '<select class="form-select" name="dhcp_int" id="dhcp_int" onchange="intChange()"><option disabled="">Pastikan Interface mendapat IP Address</option></select><br>';
            html += '<label>DHCP Pool : </label>';
            html += '<input type="text" name="dhcp_pool" id="dhcp_pool" class="form-control" required><span>e.g. 192.168.0.1-192.168.0.5</span><br><span>e.g. 192.168.0.10,192.168.0.20-192.168.0.100</span><br><span>e.g. 192.168.0.1-192.168.0.18,192.168.0.20-192.168.0.100</span><br><br><label>DNS Server : </label>';
            html += '<input type="text" name="dhcp_dns" id="dhcp_dns" class="form-control" title="Jika Memiliki lebih dari 1 DNS Server penulisannya dipisahkan oleh koma (,)">';
            $('.modal-body').empty().append(html);

            var htmlx = '';
            $.each(interfaces, (i, val) => {
                htmlx += '<option value="' + val.id + '" data-range="' + val.range + '">' + val.interface + ' [' + val.address + ']</option>';
            });
            $('#dhcp_int').empty().append(htmlx);
            var dnss = '<?= $dns ?>'.split(',');
            var dns = '';
            $.each(dnss, (i,val) => {
                if(val != ''){
                    dns += val;
                    if(i != dnss.length-1){
                        dns += ',';
                    }
                }
            });
            $('#dhcp_dns').val(dns);

            intChange();
            $('#ModalForm').modal('show');

            $('#modal_submit').submit(function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();

                blokUI('Loading...');
                var data = {
                    id: $('#dhcp_int').val(),
                    range: $('#dhcp_pool').val(),
                    dns: $('#dhcp_dns').val(),
                }
                Livewire.emit('storeDHCP', data);
                $('#ModalForm').modal('hide');
            });
        }

        intChange = () => {
            $('#dhcp_pool').val($('#dhcp_int').find('option:selected').data("range"));
        }
    </script>
    @endpush
</div>