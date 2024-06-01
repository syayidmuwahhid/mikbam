<?php

namespace App\Http\Livewire\KonfigurasiDasar;

use Livewire\Component;
use App\Helpers\AnyHelpers;
use \RouterOS\Query;

class DHCPServer extends Component
{
    public $dhcps, $interfaces, $dns;
    protected $listeners = ['removeItem' => 'remove', 'getInterface', 'storeDHCP'];

    public function mount()
    {
        $this->getInterface();
    }

    public function render()
    {
        session()->put('menu_active', 'konfigurasi-dasar');
        session()->put('sub_menu_active', 'dhcp-server');
        return view('livewire.konfigurasi-dasar.dhcp-server');
    }

    public function getData()
    {
        try {
            $query = new Query('/ip/dhcp-server/print');
            $dhcp = AnyHelpers::loginSesi()->query($query)->read();
            if (!empty($dhcp)) {
                for ($i = 0; $i < count($dhcp); $i++) {
                    if ($dhcp[$i]["invalid"] == "true" && $dhcp[$i]["dynamic"] == "true") {
                        $dhcp[$i]["flag"] = "I";
                        $dhcp[$i]["flag_label"] = "Invalid";
                    } elseif ($dhcp[$i]["invalid"] == "true" && $dhcp[$i]["dynamic"] == "false") {
                        $dhcp[$i]["flag"] = "I";
                        $dhcp[$i]["flag_label"] = "Invalid";
                    } elseif ($dhcp[$i]["invalid"] == "false" && $dhcp[$i]["dynamic"] == "true") {
                        $dhcp[$i]["flag"] = "D";
                        $dhcp[$i]["flag_label"] = "Dynamic";
                    } else {
                        $dhcp[$i]["flag"] = " ";
                        $dhcp[$i]["flag_label"] = "";
                    }

                    //getIP
                    $query = new Query('/ip/address/print');
                    $query->where("interface", $dhcp[$i]["interface"]);
                    // $query->where("disabled", "no");
                    $getip = AnyHelpers::loginSesi()->query($query)->read();

                    $dhcp[$i]["network"] = $getip[0]["network"];
                    $rawip = explode("/", $getip[0]["address"]);
                    $dhcp[$i]["gateway"] = $rawip[0];

                    //getAddressPool
                    $query = new Query('/ip/pool/print');
                    $query->where("name", $dhcp[$i]["address-pool"]);
                    $pool = AnyHelpers::loginSesi()->query($query)->read();
                    $dhcp[$i]["pool"] = $pool[0]["ranges"];

                    if ($dhcp[$i]['disabled'] == 'false') {
                        $dhcp[$i]['ds'] = "Disabled";
                        $dhcp[$i]['dscolor'] = "btn-warning";
                        $dhcp[$i]['tr_class'] = "";
                    } else {
                        $dhcp[$i]['ds'] = "Enabled";
                        $dhcp[$i]['dscolor'] = "btn-success";
                        $dhcp[$i]['tr_class'] = "text-warning fst-italic";
                    }
                }
            }
            $this->dhcps = $dhcp;

        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('swal:toast', [
                'type' => 'error',
                'title' => 'Kesalahan',
                'text' => 'Koneksi Terputus Silakan Login Kembali',
            ]);
            session()->forget('login');
        }
    }

    public function endisable($id, $stat)
    {
        try {
            $this->dispatchBrowserEvent('blockUI', ['msg' => 'Loading . . . ']);
            if ($stat == "Disabled") {
                $stat = "disable";
            } else {
                $stat = "enable";
            }
            
            //query
            $query = new Query('/ip/dhcp-server/' . $stat);
            $query->equal('.id', $id);

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
                AnyHelpers::saveLog('Berhasil ' . $stat . ' DHCP Server', 'info');
                $this->dispatchBrowserEvent('swal:toast', [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'text' => '',
                ]);
            }
            $this->dispatchBrowserEvent('unBlockUI');
        } catch (\Throwable $th) {
            AnyHelpers::saveLog($th->getMessage(), 'error');
            $this->dispatchBrowserEvent('swal:toast', [
                'type' => 'error',
                'title' => 'Kesalahan',
                'text' => $th->getMessage(),
            ]);
        }
    }

    public function remove($id, $gateway)
    {
        try {
            //query
            $query = new Query('/ip/dhcp-server/remove');
            $query->equal('.id', $id);
            
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
                //hapus network dhcp
                $query = new Query('/ip/dhcp-server/network/print');
                $query->where('gateway', $gateway);
                $dhcpn_id = AnyHelpers::loginSesi()->query($query)->read();

                $query = new Query('/ip/dhcp-server/network/remove');
                $query->equal('.id', $dhcpn_id[0][".id"]);
                $response2 = AnyHelpers::loginSesi()->query($query)->read();

                if (isset($response2["after"]["message"])) {
                    AnyHelpers::saveLog($response["after"]["message"], 'error');
                    $this->dispatchBrowserEvent('swal:toast', [
                        'type' => 'error',
                        'title' => 'Kesalahan',
                        'text' => $response["after"]["message"],
                    ]);
                    $this->dispatchBrowserEvent('unBlockUI');
                    return false;
                } else {
                    AnyHelpers::saveLog('Berhasil Menghapus data DHCP Server', 'info');
                    $this->dispatchBrowserEvent('swal:toast', [
                        'type' => 'success',
                        'title' => 'Berhasil',
                        'text' => '',
                    ]);
                }
            }

            $this->dispatchBrowserEvent('unBlockUI');
        } catch (\Throwable $th) {
            AnyHelpers::saveLog($th->getMessage(), 'error');
            $this->dispatchBrowserEvent('swal:toast', [
                'type' => 'error',
                'title' => 'Kesalahan',
                'text' => $th->getMessage(),
            ]);
        }
    }
    
    public function getInterface()
    {
        try {
            $query = new Query('/ip/address/print');
            $query->where('disabled', "no");
            $query->where('interface', "ether1");
            $query->operations('!', "true");
            $int = AnyHelpers::loginSesi()->query($query)->read();

            for ($i = 0; $i < count($int); $i++) {
                $rawip = explode("/", $int[$i]["address"]);
                $prefix = AnyHelpers::prefix("$rawip[0]", $int[0]["network"], $rawip[1]);
                $range = "";
                if (count($prefix["ranges"]) == 1) {
                    $range = $prefix["ranges"][0];
                } else {
                    for ($z = 0; $z < count($prefix["ranges"]); $z++) {
                        if ($z == count($prefix["ranges"]) - 1) {
                            $koma = "";
                        } else {
                            $koma = ",";
                        }
                        $range .= $prefix["ranges"][$z] . $koma;
                    }
                }
                $int[$i]['range'] = $range;
                $int[$i]['id'] = $int[$i]['.id'];
            }

            //dns
            $query = new Query('/ip/dns/print');
            $get_dns = AnyHelpers::loginSesi()->query($query)->read();

            $dns = $get_dns[0]["dynamic-servers"] . "," . $get_dns[0]["servers"];
            $this->dns = $dns == "," ? "" : $dns;
            $this->interfaces = $int;

        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('swal:toast', [
                'type' => 'error',
                'title' => 'Kesalahan',
                'text' => $th->getMessage(),
            ]);
        }
    }

    function storeDHCP($data)
    {
        try{    
            $query = new Query('/ip/address/print');
            $query->where('.id', $data['id']);
            $response_ip = AnyHelpers::loginSesi()->query($query)->read();
            $interface = $response_ip[0]["interface"];
            $rawip = explode("/", $response_ip[0]["address"]);
            $network = $response_ip[0]["network"] . "/" . $rawip[1];
            $gateway = $rawip[0];

            //cekpool
            $query = new Query('/ip/pool/print');
            $query->where('ranges', $data['range']);
            $cek_pool = AnyHelpers::loginSesi()->query($query)->read();

            if (!empty($cek_pool)) {
                $poolName = $cek_pool[0]["name"];
            } else {
                //addpool
                $query = new Query('/ip/pool/add');
                $query->equal('ranges', $data['range']);
                $pool = AnyHelpers::loginSesi()->query($query)->read();

                if (isset($pool["after"]["message"])) {
                    AnyHelpers::saveLog($pool["after"]["message"], 'error');
                    $this->dispatchBrowserEvent('swal:toast', [
                        'type' => 'error',
                        'title' => 'Kesalahan',
                        'text' => $pool["after"]["message"],
                    ]);
                    $this->dispatchBrowserEvent('unBlockUI');
                    return false;
                } else {
                    //getPoolname
                    $query = new Query('/ip/pool/print');
                    $query->where('ranges', $data['range']);
                    $p_name = AnyHelpers::loginSesi()->query($query)->read();
                    $poolName = $p_name[0]["name"];
                }
            }

            //addDHCPNetwork
            $query = new Query('/ip/dhcp-server/network/add');
            $query->equal('address', $network);
            $query->equal('gateway', $gateway);
            $query->equal('dns-server', ($data['dns'] != null) ? $data['dns'] : "");
            $dhcpn = AnyHelpers::loginSesi()->query($query)->read();

            if (isset($dhcpn["after"]["message"])) {
                AnyHelpers::saveLog($dhcpn["after"]["message"], 'error');
                $this->dispatchBrowserEvent('swal:toast', [
                    'type' => 'error',
                    'title' => 'Kesalahan',
                    'text' => $dhcpn["after"]["message"],
                ]);
                $this->dispatchBrowserEvent('unBlockUI');
                return false;
            } else {
                //addDHCPServer
                $query = new Query('/ip/dhcp-server/add');
                $query->equal('interface', $interface);
                $query->equal('lease-time', "1d");
                $query->equal('disabled', "no");
                $query->equal('address-pool', $poolName);
                $dhcp = AnyHelpers::loginSesi()->query($query)->read();

                if (isset($dhcp["after"]["message"])) {
                    AnyHelpers::saveLog($dhcp["after"]["message"], 'error');
                    $this->dispatchBrowserEvent('swal:toast', [
                        'type' => 'error',
                        'title' => 'Kesalahan',
                        'text' => $dhcp["after"]["message"],
                    ]);
                    $this->dispatchBrowserEvent('unBlockUI');
                    return false;
                } else {
                    AnyHelpers::saveLog('Sukses Menambah DHCP Server', 'info');
                    $data["message"] = "Sukses";
                }
            }
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
}