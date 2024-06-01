<?php

namespace App\Http\Livewire\MonitoringTraffic;

use App\Helpers\AnyHelpers;
use Livewire\Component;
use \RouterOS\Query;

class DropTraffic extends Component
{
    public $services, $datas, $service_option;

    protected $listeners = ['store', 'removeItem' => 'remove'];

    public function mount()
    {
        $this->services = AnyHelpers::getJson('service_list');
        $this->service_option = AnyHelpers::unique_multi_array($this->services, 'service_name');
    }

    public function render()
    {
        session()->put('menu_active', 'monitoring-traffic');
        session()->put('sub_menu_active', 'drop-traffic');
        return view('livewire.monitoring-traffic.drop-traffic');
    }

    public function store($data)
    {
        try {
            if (!empty($data['service'])) {
                // get address list for local
                $getip = new Query('/ip/address/print');
                $getip->where('interface', 'ether1');
                $getip->operations('!', "true");
                $hasil = AnyHelpers::loginSesi()->query($getip)->read();
                foreach ($hasil as $h) {
                    $ip = $h["network"] . "/" . explode("/", $h["address"])[1];

                    //cek address list
                    $query = new Query('/ip/firewall/address-list/print');
                    $query->where('list', 'IP_LOCAL');
                    $query->where('address', $ip);
                    $hasil = AnyHelpers::loginSesi()->query($query)->read();
                    if (count($hasil) == 0) {
                        //tambah address list
                        $query = new Query('/ip/firewall/address-list/add');
                        $query->equal('list', 'IP_LOCAL');
                        $query->equal('address', $ip);
                        $tambah = AnyHelpers::loginSesi()->query($query)->read();
                    }
                }

                //cek data
                $q = new Query('/ip/firewall/filter/print');
                $q->where('comment', 'Web Block');
                $q->where('dst-address-list', $data['service']);
                $qfil = AnyHelpers::loginSesi()->query($q)->read();
                if (count($qfil) == 0) {
                    //cek data
                    $q = new Query('/ip/firewall/mangle/print');
                    $q->where('comment', $data['service']);
                    $qres = AnyHelpers::loginSesi()->query($q)->read();

                    $service = AnyHelpers::getJson('service_list', ['key' => 'service_name', 'value' => $data['service']]);
                    $type = null;
                    if (count($qres) == 0) {
                        //add rule blok
                        $lop = 0;
                        foreach ($service as $s) {
                            if ($s['type'] == "raw") {
                                $query = new Query('/ip/firewall/raw/add');
                                if ($lop == 0) {
                                    $query->equal('comment', $data['service']);
                                }
                                $lop++;
                                $query->equal('chain', 'prerouting');
                                $query->equal('protocol', strtolower($s['protocol']));
                                $query->equal('dst-port', $s['port']);
                                $query->equal('src-address-list', 'IP_LOCAL');
                                $query->equal('dst-address-list', '!IP_LOCAL');
                                $query->equal('action', 'add-dst-to-address-list');
                                $query->equal('address-list', $s['service_name']);
                                $hasil = AnyHelpers::loginSesi()->query($query)->read();
                                $type = "raw";
                            } elseif ($s['type'] == "layer7") {
                                //layer 7
                                $query = new Query('/ip/firewall/layer7-protocol/add');
                                $query->equal('name', $s['service_name']);
                                $query->equal('regexp', $s['domain']);
                                $hasil = AnyHelpers::loginSesi()->query($query)->read();
                                $type = "layer7";
                            } elseif ($s['type'] == "content") {
                                $query = new Query('/ip/firewall/raw/add');
                                if ($lop == 0) {
                                    $query->equal('comment', $data['service']);
                                }
                                $lop++;
                                $query->equal('chain', 'prerouting');
                                $query->equal('src-address-list', 'IP_LOCAL');
                                $query->equal('dst-address-list', '!IP_LOCAL');
                                $query->equal('content', $s['domain']);
                                $query->equal('action', 'add-dst-to-address-list');
                                $query->equal('address-list', $s['service_name']);
                                $hasil = AnyHelpers::loginSesi()->query($query)->read();
                                $type = "raw";
                            }
                        }
                    } else {
                        foreach ($service as $s) {
                            $type = $s['type'];
                        }
                    }

                    if ($type == "layer7") {
                        //filter rule
                        $query = new Query('/ip/firewall/filter/add');
                        $query->equal('chain', 'forward');
                        $query->equal('src-address-list', 'IP_LOCAL');
                        $query->equal('layer7-protocol', $data['service']);
                        $query->equal('action', 'drop');
                        $query->equal('comment', 'Web Block');
                    } else {
                        //filter rule
                        $query = new Query('/ip/firewall/filter/add');
                        $query->equal('chain', 'forward');
                        $query->equal('src-address-list', 'IP_LOCAL');
                        $query->equal('dst-address-list', $data['service']);
                        $query->equal('action', 'drop');
                        $query->equal('comment', 'Web Block');
                    }
                    $filter = AnyHelpers::loginSesi()->query($query)->read();
                    if (isset($queue2["after"]["message"])) {
                        AnyHelpers::saveLog($queue2["after"]["message"], 'error');
                        $this->dispatchBrowserEvent('swal:toast', [
                            'type' => 'error',
                            'title' => 'Kesalahan',
                            'text' => $queue2["after"]["message"],
                        ]);
                        $this->dispatchBrowserEvent('unBlockUI');
                        return false;
                    } else {
                        AnyHelpers::saveLog('Berhasil Menambah Drop Traffic', 'info');
                        $this->dispatchBrowserEvent('swal:toast', [
                            'type' => 'success',
                            'title' => 'Berhasil',
                            'text' => '',
                        ]);
                    }
                } else {
                    $this->dispatchBrowserEvent('swal:toast', [
                        'type' => 'error',
                        'title' => 'Kesalahan',
                        'text' => 'Data Sudah Ada',
                    ]);
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
    
    public function getData()
    {
        try {
            //query
            $query = new Query('/ip/firewall/filter/print');
            $query->where('comment', 'Web Block');
            
            $response = AnyHelpers::loginSesi()->query($query)->read();
            
            for ($i=0; $i < count($response); $i++) { 
                // $data["id"] = explode("*", $r[".id"])[1];
                $response[$i]["nama"] = isset($response[$i]["layer7-protocol"]) ? $response[$i]["layer7-protocol"] : $response[$i]["dst-address-list"];
                $response[$i]["status"] =$response[$i]["action"];

                if (isset($response[$i]['disabled'])) {
                    $response[$i]['ds'] = "Enabled";
                    $response[$i]['dscolor'] = "btn-success";
                } else {
                    $response[$i]['ds'] = "Disabled";
                    $response[$i]['dscolor'] = "btn-warning";
                }

                if ($response[$i]['invalid'] == "true") {
                    $response[$i]['dy'] = "I";
                    $response[$i]['label'] = "Invalid";
                    $response[$i]['tr-class'] = "text-danger fst-italic";
                } else {
                    $response[$i]['dy'] = "";
                    $response[$i]['label'] = "";
                    $response[$i]['tr-class'] = "";
                }
            }
            
            $this->datas = $response;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('swal:toast', [
                'type' => 'error',
                'title' => 'Kesalahan',
                'text' => $th->getMessage(),
            ]);
            // session()->forget('login');
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
            $query = new Query('/ip/firewall/filter/' . $stat);
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
                AnyHelpers::saveLog('Berhasil ' . $stat . ' Drop Traffic', 'info');
                $this->dispatchBrowserEvent('swal:toast', [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'text' => '',
                ]);
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

    public function remove($id)
    {
        try {
            //query
            $query = new Query('/ip/firewall/filter/remove');
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
                AnyHelpers::saveLog('Berhasil Menghapus data Drop Traffic', 'info');
                $this->dispatchBrowserEvent('swal:toast', [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'text' => '',
                ]);
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