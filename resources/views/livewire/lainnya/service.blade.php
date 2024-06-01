<div>
    @section('judul')
    <title>Service List</title>

    @section('pagetitle')
    <h1>Service</h1>
    @endsection

    @section('h2')
    <li class="breadcrumb-item">Lainnya</li>
    <li class="breadcrumb-item active">Service</li>
    @endsection
    @stop

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            Service List
                            <button class="float-end btn btn-primary" class="btn btn-primary" onclick="modalAdd()">Add</button>
                        </h5>
                        <div class="table-responsive">
                            <table id="tabel_service" class="table table-striped" style="width:100%">
                                <thead>
                                    <th class="text-center">#</th>
                                    <th>Service Name</th>
                                    <th>Domain</th>
                                    <th>Protocol</th>
                                    <th>Port</th>
                                    <th>Type</th>
                                    <th class="text-center"> </th>
                                </thead>
                                <tbody>
                                    @php($no = 1)
                                    @foreach($services as $service)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $service['service_name'] }}</td>
                                        <td>{{ $service['domain'] }}</td>
                                        <td>{{ $service['protocol'] }}</td>
                                        <td>{{ $service['port'] }}</td>
                                        <td>{{ $service['type'] }}</td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm"><i class="bi bi-trash" wire:click="$emit('remove', `{{ $service['service_name'] }}`, `{{ $service['id'] }}`)"></i></button>
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
        window.livewire.on('remove', (name, id) => {
            Swal.fire({
                icon: 'warning',
                title: 'Yakin Untuk Hapus ?',
                text: name,
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
            $('#ModalFormLabel').html('Tambah Service');

            var html = '';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Service Name :</label></div>' +
                '<div class="col-sm-8"><input type="text" name="service_name" id="service_name" class="form-control" required title="penulisan nama service name harus sama jika memiliki lebih dari 1 data"></div>' +
                '</div>';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Domain :</label></div>' +
                '<div class="col-sm-8"><input name="service_domain" id="service_domain" class="form-control" title="jika memilih tipe service layer 7 maka domain berisi regexp, sedangkan jika content maka berisi url dari aplikasi atau kontennya"></div>' +
                '</div>';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Protocol :</label></div>' +
                '<div class="col-sm-8"><input name="service_protocol" id="service_protocol" class="form-control" title="protocol dapat diisi atau tidak disesuaikan dengan servisnya"></div>' +
                '</div>';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Port :</label></div>' +
                '<div class="col-sm-8"><input name="service_port" id="service_port" class="form-control"title="port dapat diisi atau tidak disesuaikan dengan servisnya"></div>' +
                '</div>';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Type :</label></div>' +
                '<div class="col-sm-8"><select name="service_type" id="service_type" class="form-select"><option>raw</option><option>layer7</option><option>content</option></select></div>' +
                '</div>';
            $('.modal-body').empty().append(html);

            $('#ModalForm').modal('show');

            $('#modal_submit').submit(function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();

                blokUI('Loading...');
                var data = {
                    service_name: $('#service_name').val(),
                    domain: $('#service_domain').val(),
                    ip_address: null,
                    protocol: $('#service_protocol').val(),
                    port: $('#service_port').val(),
                    type: $('#service_type').val(),
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