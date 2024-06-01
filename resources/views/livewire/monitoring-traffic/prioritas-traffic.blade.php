<div>
    @section('judul')
    <title>Prioritas Traffic</title>

    @section('pagetitle')
    <h1>Prioritas Traffic</h1>
    @endsection

    @section('h2')
    <li class="breadcrumb-item">Monitoring Traffic</li>
    <li class="breadcrumb-item active">Prioritas Traffic</li>
    @endsection
    @stop

    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    Tabel Prioritas Traffic
                    <button id="modal_add" class="float-end btn btn-primary" class="btn btn-primary" onclick="modalAdd()">Add</button>
                </h5>
                <table id="tabel_simple" class="table table-striped" style="width:100%">
                    <thead>
                        <th>#</th>
                        <th>Name</th>
                        <th>Service</th>
                        <th class="text-center">Action</th>
                    </thead>
                    <tbody wire:poll.keep-alive.2000ms="getData">
                        @if(!empty($prioritas))
                        @php($no = 1)
                        @foreach($prioritas as $p)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $p['name'] }}</td>
                            <td>{{ explode('-Prioritas', $p['packet-marks'])[1] }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm" wire:click="$emit('remove', `{{ $p['.id'] }}`, `{{ $p['name'] }}`)">Remove</button>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    @push('script')
    <script>
        window.livewire.on('remove', (id, name) => {
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
            <?= "var services = " . json_encode($service_option) . ";\n"; ?>
            $('#ModalFormLabel').html('Set Prioritas Traffic');

            var html = '';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Name :</label></div>' +
                '<div class="col-sm-8"><input type="text" name="prioritas_name" class="form-control" placeholder="" required=""></div>' +
                '</div>';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Service :</label></div>' +
                '<div class="col-sm-8"><select name="prioritas_service" id="prioritas_service" class="form-select" required></select></div>' +
                '</div>';
            $('.modal-body').empty().append(html);

            $.each(services, (i, val) => {
                $('#prioritas_service').append('<option>' + val.service_name + '</option>');
            });

            $('#ModalForm').modal('show');

            $('#modal_submit').submit(function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();

                blokUI('Loading...');
                var data = {
                    name: $('input[name=prioritas_name]').val(),
                    service: $('select[name=prioritas_service]').val(),
                }
                Livewire.emit('store', data);
                $('#ModalForm').modal('hide');
            });
        }
    </script>
    @endpush
</div>