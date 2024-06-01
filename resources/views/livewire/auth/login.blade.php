<div>
    <div class="row justify-content-center">
        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

            <div class="d-flex justify-content-center py-4">
                <a href="index.html" class="logo d-flex align-items-center w-auto">
                    <!-- <img src="{{ asset('/img/logo.png') }}" alt=""> -->
                    <!-- <span class="d-none d-lg-block">MIKBAM</span> -->
                </a>
            </div><!-- End Logo -->

            <div class="card mb-3">

                <div class="card-body">

                    <div class="pt-4 pb-2">
                        <h5 class="card-title text-center pb-0 fs-4">Mikrotik Bandwidth Management</h5>
                        <p class="text-center small" id="login_title">Login Router</p>
                    </div>

                    <form class="g-3 needs-validation mb-10" wire:submit.prevent="submit">
                        <div class="row g-3" id="ip_container">
                            <div class="col-12">
                                <label class="form-label">IP Address</label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text" id="inputGroupPrepend">@</span>
                                    <input type="text" name="ip" class="form-control" id="ip_address" required wire:model="ip" list="listip" autocomplete="off">
                                    <datalist id="listip">
                                        @foreach($scanlists as $scanlist)
                                        <option value="{{ $scanlist }}">{{ $scanlist }}</option>
                                        @endforeach
                                    </datalist>
                                    <!-- <div class="invalid-feedback">Please enter your IP.</div> -->
                                </div>
                                @error('ip') <span class="error">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-6">
                                <button type="button" class="btn btn-info w-100" wire:click="Scan" wire:loading.remove>Scan</button>
                                <div wire:loading.block wire:target="Scan" class="text-center">
                                    <span class="spinner-border text-primary" style="width:30px; height: 30px;"></span>
                                </div>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-primary w-100" type="button" wire:click="next" wire:loading.remove>Next</button>
                                <div wire:loading.block wire:target="next" class="text-center">
                                    <span class="spinner-border text-primary" style="width:30px; height: 30px;"></span>
                                </div>
                            </div>

                            <br />
                            <h5 class="fw-bold">Scan Result</h5>
                            <ul>
                                @foreach($scanlists as $scanlist)
                                <li wire:click="putIP($scanlist)">{{ $scanlist }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div id="username_container" style="display: none;">
                            <div class="row mb-6">
                                <div class="col-12">
                                    <label class="form-label">Username</label>
                                    <div class="input-group has-validation">
                                        <span class="input-group-text" id="inputGroupPrepend">@</span>
                                        <input type="text" name="username" class="form-control" id="username" required wire:model.defer="username" autocomplete="off">
                                        <div class="invalid-feedback">Username Wajib Diisi</div>
                                    </div>
                                    @error('username') <span class="error">{{ $message }}</span> @enderror
                                </div>
                            </div><br />

                            <div class="row mb-6">
                                <div class="col-12">
                                    <label class="form-label">Password</label>
                                    <div class="input-group has-validation">
                                        <span class="input-group-text" id="inputGroupPrepend">@</span>
                                        <input type="password" name="password" class="form-control" id="password" require wire:model.defer="password" autocomplete="off">
                                        <div class="invalid-feedback">Password Wajib Diisi</div>
                                    </div>
                                    @error('password') <span class="error">{{ $message }}</span> @enderror
                                </div>
                            </div> <br />

                            <div class="row mb-6">
                                <div class="col-12">
                                    <label class="form-label">Port API</label>
                                    <div class="input-group has-validation">
                                        <span class="input-group-text" id="inputGroupPrepend">@</span>
                                        <input type="port" name="port" class="form-control form-control-solid" id="port" required wire:model.defer="port" autocomplete="off">
                                        <div class="invalid-feedback">Port Wajib Diisi</div>
                                    </div>
                                    @error('password') <span class="error">{{ $message }}</span> @enderror
                                </div>
                            </div><br />
                            <div class="row mb-6">
                                <div class="col-6">
                                    <button type="button" class="btn btn-info w-100" wire:click="save" wire:loading.remove>Save</button>
                                    <div wire:loading.block wire:target="save" class="text-center">
                                        <span class="spinner-border text-primary" style="width:30px; height: 30px;"></span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <button class="btn btn-success w-100" type="submit">Login</button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

            <div class="credits">
                <!-- All the links in the footer should remain intact. -->
                <!-- You can delete the links only if you purchased the pro version. -->
                <!-- Licensing information: https://bootstrapmade.com/license/ -->
                <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
                <!-- Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a> -->
                
                <!-- <div wire:poll.keep-alive.5000ms="cek_koneksi">
                    {{--now()--}}
                </div> -->
            </div>

        </div>
    </div>

    <script>
        window.addEventListener('scanKlik', event => {
            $('#scan_container').removeClass('d-none');
        });

        window.addEventListener('doNext', event => {
            $('#ip_container').hide();
            $('#username_container').show();
            $('#login_title').append(' ' + event.detail.ip);
        });
    </script>
</div>