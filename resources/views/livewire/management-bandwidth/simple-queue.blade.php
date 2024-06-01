<div>
    @section('judul')
    <title>Simple Queue</title>

    @section('pagetitle')
    <h1>Simple Queue</h1>
    @endsection

    @section('h2')
    <li class="breadcrumb-item">Manajemen Bandwidth</li>
    <li class="breadcrumb-item active">Simple Queue</li>
    @endsection
    @stop

    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    Tabel Simple Queue
                    <button id="modal_add" class="float-end btn btn-primary" class="btn btn-primary" onclick="modalAdd()">Add</button>
                </h5>
                <table id="tabel_simple" class="table table-striped" style="width:100%">
                    <thead>
                        <th>#</th>
                        <th>Flags</th>
                        <th>Name</th>
                        <th>Target</th>
                        <th>Dst</th>
                        <th>Max Limit</th>
                        <th>Parent</th>
                        <th class="text-center">Action</th>
                        <th class="text-center">Move</th>
                    </thead>
                    <tbody wire:poll.keep-alive.2000ms="getData">
                        @if(!empty($SQLists))
                        @php($no = 1)
                        @php($jml = count($SQLists))
                        @foreach($SQLists as $SQList)
                        <tr class="{{ $SQList['tr-class'] }}">
                            <td class="text-center">{{ $no++ }}</td>
                            <td data-toggle="tooltip" data-placement="top" title="{{ $SQList['label'] }}">{{ $SQList['dy'] }}</td>
                            <td>{{ $SQList['name'] }}</td>
                            <td>{{ isset($SQList['target']) ? isset($SQList['target']) : '' }}</td>
                            <td>{{ isset($SQList['dst']) ? $SQList['dst'] : '-' }}</td>
                            <td>{{ $SQList['max-limit'] }}</td>
                            <td>{{ $SQList['parent'] }}</td>
                            <td class="text-center">
                                <button type="button" class="btn {{ $SQList['dscolor'] }} btn-sm" wire:click="endisable(`{{ $SQList['.id'] }}`, `{{ $SQList['ds'] }}`)">{{ $SQList['ds'] }}</button>
                                <button type="button" class="btn btn-danger btn-sm" wire:click="$emit('remove', `{{ $SQList['.id'] }}`, `{{ $SQList['name'] }}`)">Remove</button>
                            </td>
                            <td class="text-center">
                                @php($before = !empty($SQLists[$no-3]['.id']) ? $SQLists[$no-3]['.id'] : '')
                                @php($after = !empty($SQLists[$no-1]['.id']) ? $SQLists[$no-1]['.id'] : '')
                                <button id="btn-up" type="button" class="btn" {{ $no-1 == 1 ? 'disabled' : '' }} wire:click="changeItemPosition(`{{ $before }}`, `{{ $after }}`, `{{ $SQList['.id'] }}`, 'up')"><i class="ri ri-arrow-up-fill"></i></button>
                                <button id="btn-down" type="button" class="btn" {{ $no-1 == $jml ? 'disabled' : '' }} wire:click="changeItemPosition(`{{ $before }}`, `{{ $after }}`, `{{ $SQList['.id'] }}`, 'down')"><i class="ri ri-arrow-down-fill"></i></button>
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
            <?= "var interfaces = " . json_encode($interfaces) . ";\n"; ?>
            <?= "var parents = " . json_encode($parents) . ";\n"; ?>
            $('#ModalFormLabel').html('Tambah Simple Queue');

            var html = '';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Name :</label></div>' +
                '<div class="col-sm-8"><input type="text" name="simple_name" class="form-control" placeholder="" required=""></div>' +
                '</div>';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Target :</label></div>' +
                '<div class="col-sm-8"><input type="text" list="target-int" name="simple_target" class="form-control" required="" autocomplete="off" title="target dapat berisi ip address dari host ataupun interface"><datalist id="target-int"></datalist></div>' +
                '</div>';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Dst :</label></div>' +
                '<div class="col-sm-8"><input type="text" name="simple_dst" placeholder="none" class="form-control" title="dst dapat diisi atau dibiarkan kosong"></div>' +
                '</div>';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Max Limit :</label></div>' +
                '<div class="col-sm-8"><input type="text" name="simple_max_limit" class="form-control" placeholder="Unlimited/Unlimited"><span>e.g. UP/DOWN | 10M/512k | 128/128</span></div>' +
                '</div>';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Parent :</label></div>' +
                '<div class="col-sm-8"><select name="simple_parent" id="parent" class="form-select"></select></div>' +
                '</div>';
            $('.modal-body').empty().append(html);

            var htmlx = '';
            $.each(interfaces, (i, val) => {
                htmlx += '<option value="' + val.name + '">' + val.name + '</option>';
            });
            $('#target-int').empty().append(htmlx);

            var htmlx = '';
            $.each(parents, (i, val) => {
                htmlx += '<option value="' + val.name + '">' + val.name + '</option>';
            });
            $('#parent').empty().append('<option value="none">none</option>').append(htmlx);

            $('#ModalForm').modal('show');

            $('#modal_submit').submit(function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();

                blokUI('Loading...');
                var data = {
                    name: $('input[name=simple_name]').val(),
                    target: $('input[name=simple_target]').val(),
                    dst: $('input[name=simple_dst]').val(),
                    max_limit: $('input[name=simple_max_limit]').val(),
                    parent: $('select[name=simple_parent]').val(),
                }
                Livewire.emit('store', data);
                $('#ModalForm').modal('hide');
            });
        }

        $(document).ready(function() {
            $('#tabel_simple').DataTable();
        });
    </script>
    @endpush
</div>