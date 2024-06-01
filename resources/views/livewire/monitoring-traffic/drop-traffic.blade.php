<div>
    @section('judul')
    <title>Drop Traffic</title>

    @section('pagetitle')
    <h1>Drop Traffic</h1>
    @endsection

    @section('h2')
    <li class="breadcrumb-item">Monitoring Traffic</li>
    <li class="breadcrumb-item active">Drop Traffic</li>
    @endsection
    @stop

    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    Tabel Filter Rules
                    <button id="modal_add" class="float-end btn btn-primary" class="btn btn-primary" onclick="modalAdd()">Add</button>
                </h5>
                <table class="table">
                    <thead>
                        <th class="text-center">#</th>
                        <th>Flags</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </thead>
                    <tbody wire:poll.keep-alive.2000ms="getData">
                        @if(!empty($datas))
                        @php($no = 1)
                        @foreach($datas as $data)
                        <tr class="{{ $data['tr-class'] }}">
                            <td class="text-center">{{ $no++ }}</td>
                            <td data-toggle="tooltip" data-placement="top" title="{{ $data['label'] }}">{{ $data['dy'] }}</td>
                            <td>{{ $data['nama'] }}</td>
                            <td>{{ $data['status'] }}</td>
                            <td class="text-center">
                                <button type="button" class="btn {{ $data['dscolor'] }} btn-sm" wire:click="endisable(`{{ $data['.id'] }}`, `{{ $data['ds'] }}`)">{{ $data['ds'] }}</button>
                                <button type="button" class="btn btn-danger btn-sm" wire:click="$emit('remove', `{{ $data['.id'] }}`, `{{ $data['nama'] }}`)">Remove</button>
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
            $('#ModalFormLabel').html('Tambah Filter Rules');

            var html = '';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Service :</label></div>' +
                '<div class="col-sm-8"><select class="form-select" name="service" id="drop_service"></select></div>' +
                '</div>';
            $('.modal-body').empty().append(html);

            var htmlx = '';
            $.each(services, (i, val) => {
                htmlx += '<option value="' + val.service_name + '">' + val.service_name + '</option>';
            });
            $('#drop_service').empty().append('<option value="">Pilih Service</option>').append(htmlx);

            $('#ModalForm').modal('show');

            $('#modal_submit').submit(function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();


                var error_count = 0;
                if (!$('select[name=service]').val()) {
                    notif('error', 'Kesalahan', 'Service Wajib Dipilih');
                    error_count++;
                }
                var data = {
                    service: $('select[name=service]').val(),
                }

                if (error_count == 0) {
                    blokUI('Loading...');
                    Livewire.emit('store', data);
                    $('#ModalForm').modal('hide');
                }
            });
        }
    </script>
    @endpush

</div>