<div>
    @section('judul')
    <title>Time Zone List</title>

    @section('pagetitle')
    <h1>Time Zone</h1>
    @endsection

    @section('h2')
    <li class="breadcrumb-item">Lainnya</li>
    <li class="breadcrumb-item active">Time Zone</li>
    @endsection
    @stop

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            Time Zone List
                            <button class="float-end btn btn-primary" class="btn btn-primary" onclick="modalAdd()">Add</button>
                        </h5>
                        <div class="table-responsive">
                            <table id="tabel_service" class="table table-striped" style="width:100%">
                                <thead>
                                    <th class="text-center">#</th>
                                    <th>Nama Negara</th>
                                    <th>Domain Negara</th>
                                    <th>Domain Benua</th>
                                    <th>Time Zone</th>
                                    <th class="text-center"> </th>
                                </thead>
                                <tbody>
                                    @php($no = 1)
                                    @foreach($timezones as $timezone)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $timezone['negara'] }}</td>
                                        <td>{{ $timezone['domain_negara'] }}</td>
                                        <td>{{ $timezone['domain_benua'] }}</td>
                                        <td>{{ $timezone['time_zone'] }}</td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm"><i class="bi bi-trash" wire:click="$emit('remove', `{{ $timezone['negara'] }}`)"></i></button>
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
        window.livewire.on('remove', (negara) => {
            Swal.fire({
                icon: 'warning',
                title: 'Yakin Untuk Hapus ?',
                text: negara,
                showCancelButton: true,
                confirmButtonText: 'Yes',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    blokUI('Loading...');
                    Livewire.emit('removeItem', negara);
                }
            });
        });

        modalAdd = () => {
            $('#ModalFormLabel').html('Tambah Service');

            var html = '';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Nama Negara :</label></div>' +
                '<div class="col-sm-8"><input type="text" name="negara" id="negara" class="form-control" required></div>' +
                '</div>';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Domain Negara :</label></div>' +
                '<div class="col-sm-8"><input name="domain_negara" id="domain_negara" class="form-control" required title="domain negara berisi nama domain dari server ntp negara tersebut [id.pool.ntp.org]"></div>' +
                '</div>';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Domain Benua :</label></div>' +
                '<div class="col-sm-8"><input name="domain_benua" id="domain_benua" class="form-control" required title="berisi domain ntp benua [asia.pool.ntp.org]"></div>' +
                '</div>';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Time Zone :</label></div>' +
                '<div class="col-sm-8"><input name="time_zone" id="time_zone" placeholder="Benua/Negara [e.g. Asia/Jakarta] " class="form-control" required></div>' +
                '</div>';
            $('.modal-body').empty().append(html);

            $('#ModalForm').modal('show');

            $('#modal_submit').submit(function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();

                blokUI('Loading...');
                var data = {
                    negara: $('#negara').val(),
                    domain_negara: $('#domain_negara').val(),
                    domain_benua: $('#domain_benua').val(),
                    time_zone: $('#time_zone').val(),
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