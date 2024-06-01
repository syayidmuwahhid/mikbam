<div>
    @section('judul')
    <title>Internet Gateway</title>

    @section('pagetitle')
    <h1>Internet Gateway</h1>
    @endsection

    @section('h2')
    <li class="breadcrumb-item">Konfigurasi Dasar</li>
    <li class="breadcrumb-item active">Internet Gateway</li>
    @endsection
    @stop

    <section class="section">
        <div class="row">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            Check Reqruitments
                            <!-- <button id="manual" class="float-end btn btn-warning btn-sm">Manual</button> -->
                            <button id="auto" class="float-end btn btn-primary btn-sm" wire:click="$emit('SubmitAuto', `{{ $ig['dhcp']}}`, `{{ $ig['nat'] }}`, `{{ $ig['id'] }}`)">Auto</button>
                        </h5>
                        <div class="row">
                            <div class="col-sm-6">
                                <h6 class="fw-bold">DHCP Client</h6>
                            </div>
                            <div class="col-sm-6">
                                @if(!$ig['dhcp'])
                                <label class='badge bg-warning text-dark'><i class='bi bi-exclamation-triangle'></i> Not Available</label>
                                @else
                                <label class='badge bg-success'><i class='bi bi-check-circle'></i> Connected</label>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <h6 class="fw-bold">IP Address</h6>
                            </div>
                            <div class="col-sm-6">
                                @if(!$ig['ip'])
                                <label class='badge bg-warning text-dark'><i class='bi bi-exclamation-triangle'></i> Not Available</label>
                                @else
                                <label>{{ $ig['ip'] }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <h6 class="fw-bold">DNS</h6>
                            </div>
                            <div class="col-sm-6">
                                @if(empty($ig['dns'][0]))
                                <label class='badge bg-warning text-dark'><i class='bi bi-exclamation-triangle'></i> Not Available</label>
                                @else
                                @foreach($ig['dns'] as $dns)
                                <label>{{ $dns }}</label> <br>
                                @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <h6 class="fw-bold">Gateway</h6>
                            </div>
                            <div class="col-sm-6">
                                @if(empty($ig['gateway']))
                                <label class='badge bg-warning text-dark'><i class='bi bi-exclamation-triangle'></i> Not Available</label>
                                @else
                                <label>{{ $ig['gateway'] }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <h6 class="fw-bold">Firewall NAT</h6>
                            </div>
                            <div class="col-sm-6">
                                @if(!$ig['nat'])
                                <label class='badge bg-warning text-dark'><i class='bi bi-exclamation-triangle'></i> Not Available</label>
                                @else
                                <label class='badge bg-success'><i class='bi bi-check-circle'></i> Connected</label>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <h6 class="fw-bold">Koneksi Internet</h6>
                            </div>
                            <div class="col-sm-6">
                                @if(!$ig['internet'])
                                <label class='badge bg-warning text-dark'><i class='bi bi-exclamation-triangle'></i> Not Available</label>
                                @else
                                <label class='badge bg-success'><i class='bi bi-check-circle'></i> Connected</label>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-body" id="form_body">
                        <h5 class="card-title">Manual (Tanpa DHCP Client)</h5>
                        <form method="post" id="ig_form">
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">IP Address :</label>
                                <div class="col-sm-8">
                                    <input type="text" name="ig_ip" id="ig_ip" class="form-control" placeholder="0.0.0.0/0" required="" value="{{ $ig['ip'] }}" title="Penulisan IP Address Harus Menggunakan kode CIDR [x.x.x.x/x]">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-form-label col-sm-4">DNS :</label>
                                <div class="col-sm-8">
                                    <input type="text" name="ig_dns" id="ig_dns" class="form-control" placeholder="0.0.0.0" required="">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-form-label col-sm-4">Gateway :</label>
                                <div class="col-sm-8">
                                    <input type="text" name="ig_gateway" id="ig_gateway" class="form-control" placeholder="0.0.0.0" required="">
                                </div>
                            </div>
                            <input type="hidden" name="id" class="form-control" value="0">
                            <button type="submit" class="btn btn-primary form-control">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- cek koneksi --}}
    <div wire:poll.keep-alive.2000ms="cekKoneksi"></div>

    @push('script')
    <script>
        window.livewire.on('SubmitAuto', (dhcp, nat, id) => {
            blokUI('Loading...');
            Livewire.emit('submitIG', dhcp, nat, id);
        });
        
        $('#ig_form').submit((e) => {
            e.preventDefault();
            e.stopImmediatePropagation();

            var data = {
                ip: $('#ig_ip').val(),
                dns: $('#ig_dns').val(),
                gw: $('#ig_gateway').val(),
            }
            
            blokUI('Loading...');
            Livewire.emit('submitManual', data);
        });
    </script>
    @endpush
</div>