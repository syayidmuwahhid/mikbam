<div>
    @section('judul')
    <title>Queue Tree</title>

    @section('pagetitle')
    <h1>Queue Tree</h1>
    @endsection

    @section('h2')
    <li class="breadcrumb-item">Manajemen Bandwidth</li>
    <li class="breadcrumb-item active">Queue Tree</li>
    @endsection
    @stop

    <section class="section" id="tambah_queue_tree" style="display: none;">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Tambah Queue Tree</h5>

                <form id="tree_form">
                    <div class="row mb-3">
                        <div class="col-sm-2">
                            <label class="col-form-label">Kategori :</label>
                        </div>
                        <div class="col-sm-10">
                            <select class="form-select" name="kategori">
                                <option>Parent</option>
                                <option>Child</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-2">
                            <label class="col-form-label">Nama :</label>
                        </div>
                        <div class="col-sm-10">
                            <input type="text" name="nama" class="form-control" placeholder="" required="">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-2">
                            <label class="col-form-label">Type Limitasi :</label>
                        </div>
                        <div class="col-sm-10">
                            <select class="form-select" name="type">
                                <option value="">Pilih Tipe Limitasi...</option>
                                <option value="1">IP Address</option>
                                <option value="2">Service</option>
                                <option value="3">None</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3" id="isi_type">

                    </div>

                    <div id="queueform" style="display: none;">
                        <h5>Limitasi</h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <label class="col-form-label">Parent UP :</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <select name="parent_up" id="parent_list_up" class="form-select">
                                            <option value="global">global</option>
                                            @foreach($queues as $que)
                                            <option value="{{ $que['name'] }}">{{ $que['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-6">

                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <label class="col-form-label">Parent Down :</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <select name="parent_down" id="parent_list_down" class="form-select">
                                            <option value="global">global</option>
                                            @foreach($queues as $que)
                                            <option value="{{ $que['name'] }}">{{ $que['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mb-3" id="limit_at_up">
                                    <div class="col-sm-4">
                                        <label class="col-form-label">Limit At UP:</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" name="limit_at_up" class="form-control" placeholder="" title="penulisan dapat diakhiri dengan besar data contoh 1M">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row mb-3" id="limit_at_down">
                                    <div class="col-sm-4">
                                        <label class="col-form-label">Limit At Down:</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" name="limit_at_down" class="form-control" placeholder="" title="penulisan dapat diakhiri dengan besar data contoh 1M">
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <label class="col-form-label">Max Limit UP:</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" name="max_limit_up" class="form-control" placeholder="" title="penulisan dapat diakhiri dengan besar data contoh 1M">
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-6">

                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <label class="col-form-label">Max Limit Down:</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" name="max_limit_down" class="form-control" placeholder="" title="penulisan dapat diakhiri dengan besar data contoh 1M">
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">

                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <label class="col-form-label">Priority UP :</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="number" name="priority_up" class="form-control" max="8" placeholder="1-8" min="1" value="8">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">

                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <label class="col-form-label">Priority Down :</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="number" name="priority_down" class="form-control" min="1" max="8" placeholder="1-8" value="8">
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>

                    <button type="submit" class="btn btn-success form-control">Simpan</button>

                </form>

            </div>
        </div>
    </section>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    Tabel Queue Tree
                    <button type="button" class="float-end btn btn-primary" class="btn btn-primary" onclick="addQueue()">Add</button>
                </h5>
                <table class="table">
                    <thead class="text-center">
                        <th>#</th>
                        <th>Flags</th>
                        <th>Name</th>
                        <th>Parent</th>
                        <th>Limit At</th>
                        <th>Max Limit</th>
                        <th>Priority</th>
                        <th>Action</th>
                    </thead>
                    <!-- <tbody wire:poll.keep-alive.2000ms="getData"> -->
                    <tbody>
                        @if(!empty($treeLists))
                        @php($no = 1)
                        @foreach($treeLists as $treeList)
                        <tr class="{{ $treeList['tr-class'] }}">
                            <td class="text-center">{{ $no++ }}</td>
                            <td data-toggle="tooltip" data-placement="top" title="{{ $treeList['label'] }}">{{ $treeList['dy'] }}</td>
                            <td>{{ $treeList['name'] }}</td>
                            <td>{{ $treeList['parent'] }}</td>
                            <td>{{ $treeList['limit-at'] }}</td>
                            <td>{{ $treeList['max-limit'] }}</td>
                            <td>{{ $treeList['priority'] }}</td>
                            <td class="text-center">
                                <button type="button" class="btn {{ $treeList['dscolor'] }} btn-sm" wire:click="endisable(`{{ $treeList['.id'] }}`, `{{ $treeList['ds'] }}`)">{{ $treeList['ds'] }}</button>
                                <button type="button" class="btn btn-danger btn-sm" wire:click="$emit('remove', `{{ $treeList['.id'] }}`, `{{ $treeList['name'] }}`)">Remove</button>
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

        addQueue = () => {
            $('#tambah_queue_tree').show();
        }

        $(document).ready(function() {
            //display limit at
            if ($("select[name=kategori]").val() == "Parent") {
                $("#limit_at_up").hide();
                $("#limit_at_down").hide();
            } else {
                $("#limit_at_up").show();
                $("#limit_at_down").show();
            }
            $("select[name=kategori]").change(function() {
                if ($("select[name=kategori]").val() == "Parent") {
                    $("#limit_at_up").hide();
                    $("#limit_at_down").hide();
                } else {
                    $("#limit_at_up").show();
                    $("#limit_at_down").show();
                }
            });

            function ResetForm() {
                $("input").val('');
                $("input[name=priority_up]").val('8');
                $("input[name=priority_down]").val('8');
                $("select[name=type]").val('0');
                $("select[name=kategori]").val('Parent');
                $("select[name=service]").val('0');
                $("#queueform").hide();
                $("div#isi_type").empty();
                $("#limit_at_up").hide();
                $("#limit_at_down").hide();
            }

            $("select[name=type]").change(function() {
                $("button[type=submit]").show();
                $("div#isi_type").empty();
                switch ($("select[name=type]").val()) {
                    case "1":
                        IpAddress();
                        break;
                    case "2":
                        $("select[name=service]").empty();
                        Service();
                        break;
                    case "3":
                        None();
                        break;
                    default:
                        $("div#isi_type").empty();
                        $("#queueform").hide();
                        $("button[type=submit]").hide();
                }
                // $("select[name=type]").val('0');
            });

            function None() {
                $("#queueform").show();
            }

            function IpAddress() {
                $("div#isi_type").append("<div class='col-sm-2'>" +
                    "<label class='col-form-label'>IP / Network :</label>" +
                    "</div>" +
                    "<div class='col-sm-10'>" +
                    "<input type='text' name='ip' class='form-control' placeholder='0.0.0.0/0' required='' title='isian dapat berupa ip target ataupun networknya'>" +
                    "</div>"
                );
                $("input[name=ip]").keydown(function() {
                    $("#queueform").show();
                });
            }

            function Service() {
                $("#queueform").show();
                <?= "var services = " . json_encode($service_option) . ";\n"; ?>

                $("div#isi_type").append(
                    "<div class='col-sm-2'>" +
                    "<label class='col-form-label'>Nama Service :</label>" +
                    "</div>" +
                    "<div class='col-sm-10'>" +
                    "<select class='form-select' name='tree_service' id='tree_service'>" +
                    "</select>" +
                    "</div>"
                );
                $.each(services, (i, val) => {
                    $('#tree_service').append('<option>' + val.service_name + '</option>');
                });
                $("select[name=service]").change(function() {
                    if ($("select[name=service]").val() == "0") {
                        // $("div#isi_type").empty()
                        $("#queueform").hide();
                    } else {
                        $("#queueform").show();
                    }
                });
            }
        });

        $('#tree_form').submit(function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            var error_count = 0;
            if (!$('select[name=type]').val()) {
                notif('error', 'Kesalahan', 'Type Limitasi Wajib Dipilih');
                error_count++;
            }
            var data = {
                kategori: $("select[name=kategori]").val(),
                nama: $('input[name=nama]').val(),
                type: $("select[name=type]").val(),
                ip: $('input[name=ip]').val(),
                service: $("select[name=tree_service]").val(),
                parent_up: $('select[name=parent_up]').val(),
                parent_down: $('select[name=parent_down]').val(),
                limit_at_up: $('input[name=limit_at_up]').val(),
                limit_at_down: $('input[name=limit_at_down]').val(),
                max_limit_up: $('input[name=max_limit_up]').val(),
                max_limit_down: $('input[name=max_limit_down]').val(),
                priority_up: $('input[name=priority_up]').val(),
                priority_down: $('input[name=priority_down]').val(),
            }
            if (error_count == 0) {
                // blokUI('Loading...');
                Livewire.emit('store', data);
                $('#tambah_queue_tree').hide();
            }
        });
    </script>
    @endpush
</div>