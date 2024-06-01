<div>
    @section('judul')
    <title>Perconnection Queue</title>

    @section('pagetitle')
    <h1>Perconnection Queue</h1>
    @endsection

    @section('h2')
    <li class="breadcrumb-item">Manajemen Bandwidth</li>
    <li class="breadcrumb-item active">Perconnection Queue</li>
    @endsection
    @stop

    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    Tabel Perconnection Queue <span><i>*Urutan Queue dapat diubah pada menu <a href="{{route('simple-queue')}}">Simple Queue</a></i></span>
                    <button id="modal_add" class="float-end btn btn-primary" class="btn btn-primary" onclick="modalAdd()">Add</button>
                </h5>
                <table class="table">
                    <thead>
                        <th class="text-center">#</th>
                        <th>Flags</th>
                        <th>Name</th>
                        <th>Target</th>
                        <th>Dst</th>
                        <th>Max Limit</th>
                        <th>Parent</th>
                        <th class="text-center">Action</th>
                    </thead>
                    <tbody wire:poll.keep-alive.2000ms="getData">
                        {!! empty($PCQs) ? '<tr>
                            <td colspan="9" class="text-center"><em>No Data</em></td>
                        </tr>' : '' !!}
                        @if(!empty($PCQs))
                        @php($no = 1)
                        @php($jml = count($PCQs))
                        @foreach($PCQs as $pcq)
                        <tr class="{{ $pcq['tr-class'] }}">
                            <td class="text-center">{{ $no++ }}</td>
                            <td data-toggle="tooltip" data-placement="top" title="{{ $pcq['label'] }}">{{ $pcq['dy'] }}</td>
                            <td>{{ $pcq['name'] }}</td>
                            <td>{{ $pcq['target'] }}</td>
                            <td>{{ isset($pcq['dst']) ? $pcq['dst'] : '-' }}</td>
                            <td>{{ $pcq['max-limit'] }}</td>
                            <td>{{ $pcq['parent'] }}</td>
                            <td class="text-center">
                                <button type="button" class="btn {{ $pcq['dscolor'] }} btn-sm" wire:click="endisable(`{{ $pcq['.id'] }}`, `{{ $pcq['ds'] }}`)">{{ $pcq['ds'] }}</button>
                                <button type="button" class="btn btn-danger btn-sm" wire:click="$emit('remove', `{{ $pcq['.id'] }}`, `{{ $pcq['name'] }}`)">Remove</button>
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
            $('#ModalFormLabel').html('Tambah Simple PerConnection Queue');

            var html = '';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Name :</label></div>' +
                '<div class="col-sm-8"><input type="text" name="pcq_name" class="form-control" placeholder="" required=""></div>' +
                '</div>';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Target :</label></div>' +
                '<div class="col-sm-8"><input type="text" list="target-int" name="pcq_target" class="form-control" required="" autocomplete="off" title="target dapat berisi ip address dari host ataupun interface"><datalist id="target-int"></datalist></div>' +
                '</div>';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Dst :</label></div>' +
                '<div class="col-sm-8"><input type="text" name="pcq_dst" placeholder="none" class="form-control" title="dst dapat diisi atau dibiarkan kosong"></div>' +
                '</div>';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Rate :</label></div>' +
                '<div class="col-sm-8"><input type="text" name="pcq_rate" class="form-control" placeholder="0/0"><span>e.g. UP/DOWN | 1M/512k | 256/256</span></div>' +
                '</div>';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Max Limit :</label></div>' +
                '<div class="col-sm-8"><input type="text" name="pcq_max_limit" class="form-control" placeholder="Unlimited/Unlimited"><span>e.g. UP/DOWN | 10M/512k | 128/128</span></div>' +
                '</div>';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Parent :</label></div>' +
                '<div class="col-sm-8"><select name="pcq_parent" id="parent" class="form-select"></select></div>' +
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
                    name: $('input[name=pcq_name]').val(),
                    target: $('input[name=pcq_target]').val(),
                    dst: $('input[name=pcq_dst]').val(),
                    max_limit: $('input[name=pcq_max_limit]').val(),
                    rate: $('input[name=pcq_rate]').val(),
                    parent: $('select[name=pcq_parent]').val(),
                }
                Livewire.emit('store', data);
                $('#ModalForm').modal('hide');
            });
        }
    </script>
    @endpush
</div>