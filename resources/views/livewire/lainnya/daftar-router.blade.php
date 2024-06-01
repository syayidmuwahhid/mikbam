<div>
    @section('judul')
    <title>Add Router</title>

    @section('pagetitle')
    <h1>Add Router</h1>
    @endsection

    @section('h2')
    <li class="breadcrumb-item active">Add Router</li>
    @endsection
    @stop

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            Router List
                            <button class="float-end btn btn-primary" class="btn btn-primary" onclick="modalAdd()">Add</button>
                        </h5>
                        <div class="table-responsive">
                            <table id="tabel_service" class="table table-striped" style="width:100%">
                                <thead>
                                    <th class="text-center">#</th>
                                    <th>Identity</th>
                                    <th>IP Address</th>
                                    <th>Username</th>
                                    <th>Port</th>
                                    <th>Last Active</th>
                                    <th class="text-center"> </th>
                                </thead>
                                <tbody>
                                    @php($no = 1)
                                    @foreach($routers as $router)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $router['identity'] }}</td>
                                        <td>{{ $router['ip'] }}</td>
                                        <td>{{ $router['username'] }}</td>
                                        <td>{{ $router['port'] }}</td>
                                        <td>{{ $router['last_active'] }}</td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm" wire:click="$emit('remove', `{{ $router['ip'] }}`)"><i class="bi bi-trash"></i></button>
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
        window.livewire.on('remove', (ip) => {
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
                    Livewire.emit('removeItem', ip);
                }
            });
        });

        modalAdd = () => {
            $('#ModalFormLabel').html('Tambah Router');

            var html = '';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">IP Address :</label></div>' +
                '<div class="col-sm-8"><input type="text" name="router_ip" id="router_ip" class="form-control" required title="isi tanpa menambahkan kode CIDR [x.x.x.x]"></div>' +
                '</div>';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Username :</label></div>' +
                '<div class="col-sm-8"><input type="text" name="router_username" id="router_username" class="form-control"></div>' +
                '</div>';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Password :</label></div>' +
                '<div class="col-sm-8"><input type="password" name="router_password" id="router_password" class="form-control"></div>' +
                '</div>';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Port :</label></div>' +
                '<div class="col-sm-8"><input type="number" value="8728" name="router_port" id="router_port" class="form-control"></div>' +
                '</div>';
            $('.modal-body').empty().append(html);

            $('#ModalForm').modal('show');

            $('#modal_submit').submit(function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();

                blokUI('Loading...');
                var data = {
                    ip: $('#router_ip').val(),
                    username: $('#router_username').val(),
                    password: $('#router_password').val(),
                    port: $('#router_port').val(),
                }
                Livewire.emit('store', data);
                $('#ModalForm').modal('hide');
            });
        }

        $(document).ready(function() {
            $('#tabel_service').DataTable();
        });
    </script>
    @endpush
</div>