<div>
    @section('judul')
    <title>Notifikasi Telegram</title>

    @section('pagetitle')
    <h1>Notifikasi Telegram</h1>
    @endsection

    @section('h2')
    <li class="breadcrumb-item">Monitoring Traffic</li>
    <li class="breadcrumb-item active">Notifikasi Telegram</li>
    @endsection
    @stop

    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Langkah Set-up Notifikasi Telegram</h5>
                <ol>
                    <li>Buat Group Telegram, dan tambahkan user yang akan menerima notifikasi</li>
                    <li>Tambahkan bot Telegram <b>QosMonitoring</b> pada group tersebut</li>
                    <img src="{{asset('/img/addbotpic.PNG')}}">
                    <li>Tambahkan bot Telegram <b>Telegram Bot Raw</b> pada grup untuk mendapatkan chat_id</li>
                    <img src="{{asset('/img/telegrambotraw.PNG')}}">
                    <li>Selanjutnya copy dan simpan <b>chat_id</b></li>
                    <img src="{{asset('/img/chatid.PNG')}}">
                    <li><b>Telegram Bot Raw</b> dapat dihapus kembali (optional)</li>
                </ol>

            </div>
        </div>
    </section>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    Netwatch
                    <button id="netwatch_add" class="float-end btn btn-primary" class="btn btn-primary" onclick="modalAdd()">Add</button>
                </h5>
                <table class="table">
                    <thead class="text-center">
                        <th>#</th>
                        <th>Target</th>
                        <th>Interval</th>
                        <th>Timeout</th>
                        <th>Chat ID</th>
                        <th>Message When UP</th>
                        <th>Message When Down</th>
                        <th>Action</th>
                    </thead>
                    <tbody wire:poll.keep-alive.2000ms="getData" class="text-center">
                        @php($no = 1)
                        @foreach ($netwatchList as $netwatch)
                        <tr class="{{ isset($netwatch['lineClass']) ? $netwatch['lineClass'] : '' }} {{ $netwatch['tr_class'] }}">
                            <td>{{ $no++ }}</td>
                            <td>{{ $netwatch['host'] }}</td>
                            <td>{{ $netwatch['interval'] }}</td>
                            <td>{{ $netwatch['timeout'] }}</td>
                            <td>{{ $netwatch['chat_id'] }}</td>
                            <td>{{ $netwatch['message_up'] }}</td>
                            <td>{{ $netwatch['message_down'] }}</td>
                            <td>
                                <button type='button' wire:click="endisable(`{{ $netwatch['.id'] }}`, `{{ $netwatch['ds'] }}`)" class="btn {{ $netwatch['dscolor'] }} btn-sm">{{ $netwatch['ds'] }}</button>
                                <button type='button' wire:click="$emit('remove', `{{ $netwatch['.id'] }}`, `{{ $netwatch['host'] }}`)" class='btn btn-danger btn-sm'>Remove</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </table>
            </div>
        </div>
    </section>

    @push('script')
    <script>
        window.livewire.on('remove', (id, target) => {
            Swal.fire({
                icon: 'warning',
                title: 'Yakin Untuk Hapus ?',
                text: target,
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
            $('#ModalFormLabel').html('Tambah Netwatch');

            var html = '';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Chat ID :</label></div>' +
                '<div class="col-sm-8"><input type="text" name="chat_id" id="chat_id" class="form-control" placeholder="-123456789" required title="silakan isi chat id sesuai dengan yang diterima dari telegram diawali dengan tanda minus (-)"></div>' +
                '</div>';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Target :</label></div>' +
                '<div class="col-sm-8"><input type="text" name="target_netwatch" id="target_netwatch" class="form-control" placeholder="0.0.0.0" required title="target merupakan ip dari host yang akan di monitoring"></div>' +
                '</div>';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Interval :</label></div>' +
                '<div class="col-sm-8"><input type="text" name="interval_netwatch" id="interval_netwatch" class="form-control" value="00:01:00" required></div>' +
                '</div>';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Timeout(s) :</label></div>' +
                '<div class="col-sm-8"><input type="text" name="timeout" id="timeout" class="form-control" value="1" required></div>' +
                '</div>';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Message When UP:</label></div>' +
                '<div class="col-sm-8"><input type="text" name="message_up" id="message_up" class="form-control" placeholder="host up" title="jika tidak diisi maka tidak akan dikirim pesan ketika host up"></div>' +
                '</div>';
            html += '<div class="row mb-3">' +
                '<div class="col-sm-4"><label class="col-form-label">Message When Down:</label></div>' +
                '<div class="col-sm-8"><input type="text" name="message_down" id="message_down" class="form-control" placeholder="host down" title="jika tidak diisi maka tidak akan dikirim pesan ketika host down"></div>' +
                '</div>';
            $('.modal-body').empty().append(html);

            $('#ModalForm').modal('show');

            $('#modal_submit').submit(function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();

                blokUI('Loading...');
                let formData = {
                    chat_id: $('#chat_id').val(),
                    target: $('#target_netwatch').val(),
                    interval: $('#interval_netwatch').val(),
                    timeout: $('#timeout').val(),
                    message_up: $('#message_up').val(),
                    message_down: $('#message_down').val(),
                };
                Livewire.emit('store', formData);
                $('#ModalForm').modal('hide');
            });
        }
    </script>
    @endpush
</div>