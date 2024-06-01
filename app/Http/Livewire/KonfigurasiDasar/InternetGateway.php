<?php

namespace App\Http\Livewire\KonfigurasiDasar;

use Livewire\Component;
use App\Helpers\AnyHelpers;
use \RouterOS\Query;

class InternetGateway extends Component
{
    public $ig;

    protected $listeners = ['submitIG', 'submitManual'];

    public function mount()
    {
        $this->cekIG();
    }

    public function render()
    {
        session()->put('menu_active', 'konfigurasi-dasar');
        session()->put('sub_menu_active', 'internet-gateway');
        return view('livewire.konfigurasi-dasar.internet-gateway');
    }

    public function cekIG()
    {
        //cekDHCPClient
        $query = new Query('/ip/dhcp-client/print');
        $query->where('interface', "ether1");
        $query->where('status', "bound");
        $cekEth1 = AnyHelpers::loginSesi()->query($query)->read();

        if ($cekEth1 == null) {
            $this->ig["dhcp"] = false;
        } else {
            $this->ig["dhcp"] = true;
        }

        //getIPEth1
        $query = new Query('/ip/address/print');
        $query->where('interface', "ether1");
        $query->where('disabled', "no");
        $ip = AnyHelpers::loginSesi()->query($query)->read();
        if ($ip == null) {
            $this->ig["ip"] = false;
            $this->ig["id"] = false;
        } else {
            $this->ig["ip"] = $ip[0]["address"];
            $this->ig["id"] = $ip[0][".id"];
        }

        //getDNS
        $query = new Query('/ip/dns/print');
        $qdns = AnyHelpers::loginSesi()->query($query)->read();
        if ($qdns[0]["dynamic-servers"] == "" && $qdns[0]["servers"] != "") {
            $rawdns = $qdns[0]["servers"];
            $dns = explode(",", $rawdns);
        } elseif ($qdns[0]["dynamic-servers"] != "" && $qdns[0]["servers"] == "") {
            $rawdns = $qdns[0]["dynamic-servers"];
            $dns = explode(",", $rawdns);
        } elseif ($qdns[0]["dynamic-servers"] != "" && $qdns[0]["servers"] != "") {
            $rawdns = $qdns[0]["dynamic-servers"] . "," . $qdns[0]["servers"];
            $dns = explode(",", $rawdns);
        } else {
            $dns[] = null;
        }
        $this->ig["dns"] = $dns;

        //getGateway
        $query = new Query('/ip/route/print');
        $query->where('dst-address', "0.0.0.0/0");
        $gw = AnyHelpers::loginSesi()->query($query)->read();
        if (empty($gw)) {
            $this->ig["gateway"] = null;
        } else {
            $this->ig["gateway"] = $gw[0]["gateway"];
        }

        //getNat
        $query = new Query('/ip/firewall/nat/print');
        $query->where('action', "masquerade");
        $query->where('chain', "srcnat");
        $nat = AnyHelpers::loginSesi()->query($query)->read();
        if (empty($nat)) {
            $this->ig["nat"] = false;
        } else {
            $this->ig["nat"] = true;
        }

        //cekInternet
        $query = new Query('/ping');
        $query->equal('address', "google.com");
        $query->equal('interface', "ether1");
        $query->equal('count', 4);
        $internet = AnyHelpers::loginSesi()->query($query)->read();
        if (isset($internet["after"]["message"]) || $internet == null) {
            $this->ig["internet"] = false;
        } else {
            $received = 0;
            foreach ($internet as $r) {
                $received += (int) $r["received"];
            }
            if ($received == 0) {
                $this->ig["internet"] = false;
            } else {
                $this->ig["internet"] = true;
            }
        }
    }

    public function submitIG($dhcp, $nat, $id)
    {
        try {
            if (empty($dhcp)) {
                $query = new Query('/ip/dhcp-client/add');
                $query->equal('interface', "ether1");
                $query->equal('disabled', "no");

                $response = AnyHelpers::loginSesi()->query($query)->read();
                if (isset($response["after"]["message"])) {
                    AnyHelpers::saveLog($response["after"]["message"], 'error');
                    $this->dispatchBrowserEvent('swal:toast', [
                        'type' => 'error',
                        'title' => 'Kesalahan',
                        'text' => $response["after"]["message"],
                    ]);

                    $this->dispatchBrowserEvent('unBlockUI');
                    return false;
                } else {
                    sleep(5);
                    if (!empty($id)) {
                        //disableip
                        $query = new Query('/ip/address/disable');
                        $query->equal('.id', $id);
                        $response = AnyHelpers::loginSesi()->query($query)->read();
                    }
                }
            }

            if (empty($nat)) {
                $query = new Query('/ip/firewall/nat/add');
                $query->equal('action', "masquerade");
                $query->equal('out-interface', "ether1");
                $query->equal("chain", "srcnat");

                $nat = AnyHelpers::loginSesi()->query($query)->read();
            }
            AnyHelpers::saveLog('Berhasil Konfigurasi Internet Gateway', 'info');
            $this->dispatchBrowserEvent('swal:toast', [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'text' => 'Berhasil Konfigurasi Internet Gateway',
                ]);
            $this->cekIG();
        
        } catch (\Throwable $th) {
            AnyHelpers::saveLog($th->getMessage(), 'error');
            $this->dispatchBrowserEvent('swal:toast', [
                'type' => 'error',
                'title' => 'Kesalahan',
                'text' => $th->getMessage(),
            ]);
        }
        $this->dispatchBrowserEvent('unBlockUI');
    }

    public function submitManual($rq)
    {
        try {
            //ip
            $query = new Query('/ip/address/print');
            $query->where('interface', "ether1");
            $ip = AnyHelpers::loginSesi()->query($query)->read();
            
            if (empty($ip)) {
                $query = new Query('/ip/address/add');
                $query->equal('address', $rq['ip']);
                $query->equal('interface', "ether1");

                $response = AnyHelpers::loginSesi()->query($query)->read();

                if (isset($response["after"]["message"])) {
                    AnyHelpers::saveLog($response["after"]["message"], 'error');
                    $this->dispatchBrowserEvent('swal:toast', [
                        'type' => 'error',
                        'title' => 'Kesalahan',
                        'text' => $response["after"]["message"],
                    ]);

                    $this->dispatchBrowserEvent('unBlockUI');
                    return false;
                }
            } else {
                if ($rq['ip'] == $ip[0]["address"]) {
                    $this->dispatchBrowserEvent('swal:toast', [
                        'type' => 'success',
                        'title' => 'Berhasil',
                        'text' => 'Tidak Ada Perubahan Pada IP Address',
                    ]);
                } else {
                    $query = new Query('/ip/address/add');
                    $query->equal('address', $rq['ip']);
                    $query->equal('interface', "ether1");

                    $response = AnyHelpers::loginSesi()->query($query)->read();

                    if (isset($response["after"]["message"])) {
                        AnyHelpers::saveLog($response["after"]["message"], 'error');
                        $this->dispatchBrowserEvent('swal:toast', [
                            'type' => 'error',
                            'title' => 'Kesalahan',
                            'text' => $response["after"]["message"],
                        ]);
                        
                        $this->dispatchBrowserEvent('unBlockUI');
                        return false;
                    }
                }
            }

            //dns
            $query = new Query('/ip/dns/set');
            $query->equal('servers', $rq['dns']);
            $dns = AnyHelpers::loginSesi()->query($query)->read();

            if (isset($dns["after"]["message"])) {
                AnyHelpers::saveLog($dns["after"]["message"], 'error');
                $this->dispatchBrowserEvent('swal:toast', [
                    'type' => 'error',
                    'title' => 'Kesalahan',
                    'text' => $dns["after"]["message"],
                ]);

                $this->dispatchBrowserEvent('unBlockUI');
                return false;
            }

            //gateway
            $query = new Query('/ip/route/print');
            $query->where('dst-address', "0.0.0.0/0");
            $query->where('gateway', $rq['gw']);
            $query->where('static', "yes");
            $query->where('active', "yes");

            $cekdhcp = AnyHelpers::loginSesi()->query($query)->read();
            if (empty($cekdhcp)) {
                $query = new Query('/ip/route/add');
                $query->equal('gateway', $rq['gw']);
                $query->equal('dst-address', "0.0.0.0/0");
                $dns = AnyHelpers::loginSesi()->query($query)->read();

                if (isset($response["after"]["message"])) {
                    AnyHelpers::saveLog($response["after"]["message"], 'error');
                    $this->dispatchBrowserEvent('swal:toast', [
                        'type' => 'error',
                        'title' => 'Kesalahan',
                        'text' => $response["after"]["message"],
                    ]);

                    $this->dispatchBrowserEvent('unBlockUI');
                    return false;
                }

            }

            //nat
            //getNat
            $query = new Query('/ip/firewall/nat/print');
            $query->where('action', "masquerade");
            $query->where('chain', "srcnat");
            $nat = AnyHelpers::loginSesi()->query($query)->read();
            if (empty($nat)) {
                $query = new Query('/ip/firewall/nat/add');
                $query->equal('action', "masquerade");
                $query->equal('out-interface', "ether1");
                $query->equal("chain", "srcnat");

                $nat = AnyHelpers::loginSesi()->query($query)->read();

            }
            AnyHelpers::saveLog('Berhasil Konfigurasi Internet Gateway', 'info');
            $this->dispatchBrowserEvent('swal:toast', [
                'type' => 'success',
                'title' => 'Berhasil',
                'text' => 'Berhasil Konfigurasi Internet Gateway',
            ]);
        } catch (\Throwable $th) {
            AnyHelpers::saveLog($th->getMessage(), 'error');
            $this->dispatchBrowserEvent('swal:toast', [
                'type' => 'error',
                'title' => 'Kesalahan',
                'text' => $th->getMessage(),
            ]);
        }
        $this->cekIG();
        $this->dispatchBrowserEvent('unBlockUI');
    }

    public function cekKoneksi()
    {
        try {
            AnyHelpers::loginSesi();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('swal:toast', [
                'type' => 'error',
                'title' => 'Kesalahan',
                'text' => 'Koneksi Terputus SilakanAnyHelpers::loginSesi() Kembali',
            ]);
            session()->forget('login');
        }
    }
}