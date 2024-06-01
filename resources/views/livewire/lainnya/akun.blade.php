<div>
    @section('judul')
    <title>Kelola Users</title>

    @section('pagetitle')
    <h1>Kelola Users</h1>
    @endsection

    @section('h2')
    <li class="breadcrumb-item">Lainnya</li>
    <li class="breadcrumb-item active">Kelola Users</li>
    @endsection
    @stop

    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    Tabel Users
                    <button id="modal_add" class="float-end btn btn-primary" class="btn btn-primary" onclick="modalAdd()">Add</button>
                </h5>
                <table class="table">
                    <thead class="text-center">
                        <th>#</th>
                        <th>Name</th>
                        <th>Level</th>
                        <th>Allowed Address</th>
                        <th>Last Login</th>
                        <th>Action</th>
                    </thead>
                    <tbody wire:poll.keep-alive.2000ms="getData" class="text-center">
                        @php($no = 1)
                        @foreach($userList as $user)
                        <tr class="{{ isset($user['lineClass']) ? $user['lineClass'] : '' }} {{ $user['tr_class'] }}">
                            <td>{{ $no++ }}</td>
                            <td>{{ $user['name'] }}</td>
                            <td>{{ $user['group'] }}</td>
                            <td>{{ $user['address'] }}</td>
                            <td>{{ isset($user['last-logged-in']) ? $user['last-logged-in'] : '' }}</td>
                            <td class=" text-center">
                                <button type='button' wire:click="endisable(`{{ $user['.id'] }}`, `{{ $user['ds'] }}`)" class="btn {{ $user['dscolor'] }} btn-sm">{{ $user['ds'] }}</button>
                                <button type='button' wire:click="$emit('remove', `{{ $user['.id'] }}`, `{{ $user['name'] }}`)" class='btn btn-danger btn-sm'>Remove</button>
                            </td>
                        </tr>
                        @endforeach
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
            $('#ModalFormLabel').html('Tambah User');

            var html = '';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Name :</label></div>' +
                '<div class="col-sm-8"><input type="text" name="nama_akun" id="nama_akun" class="form-control" title="isi dengan nama username"></div>' +
                '</div>';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Level :</label></div>' +
                '<div class="col-sm-8"><select name="level_akun" id="level_akun" class="form-select"><option>Read</option><option>Write</option><option>Full</option></select></div>' +
                '</div>';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Allowed Address :</label></div>' +
                '<div class="col-sm-8"><input type="text" name="allowed_address" id="allowed_address" class="form-control" title="jika user dapat mengakses router pada seluruh network jaringan maka kosongkan allowed address"></div>' +
                '</div>';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Password :</label></div>' +
                '<div class="col-sm-8"><input type="text" name="password_akun" id="password_akun" class="form-control"></div>' +
                '</div>';
            $('.modal-body').empty().append(html);

            $('#ModalForm').modal('show');

            $('#modal_submit').submit(function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();

                blokUI('Loading...');
                var formData = {
                    name: $('#nama_akun').val(),
                    level_akun: $('#level_akun').val(),
                    allowed_address: $('#allowed_address').val(),
                    password_akun: $('#password_akun').val()
                };
                Livewire.emit('store', formData);
                $('#ModalForm').modal('hide');
            });
        }
    </script>
    @endpush
</div>