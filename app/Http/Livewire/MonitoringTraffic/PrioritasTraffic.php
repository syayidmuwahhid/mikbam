<?php

namespace App\Http\Livewire\MonitoringTraffic;

use Livewire\Component;
use App\Helpers\AnyHelpers;
use \RouterOS\Query;

class PrioritasTraffic extends Component
{
    public $services, $service_option, $prioritas;

    protected $listeners = ['removeItem' => 'remove', 'store'];

    public function mount()
    {
        $this->services = $this->services = AnyHelpers::getJson('service_list');
        $this->service_option = AnyHelpers::unique_multi_array($this->services, 'service_name');
    }

    public function render()
    {
        session()->put('menu_active', 'monitoring-traffic');
        session()->put('sub_menu_active', 'prioritas-traffic');
        return view('livewire.monitoring-traffic.prioritas-traffic');
    }

    public function getData()
    {
        try {
            $query = new Query('/queue/simple/print');
            $query->where('comment', 'prioritas');

            $this->prioritas = AnyHelpers::loginSesi()->query($query)->read();

        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('swal:toast', [
                'type' => 'error',
                'title' => 'Kesalahan',
                'text' => $th->getMessage(),
            ]);
        }
        $this->dispatchBrowserEvent('unBlockUI');
    }

    public function store($data)
    {
        try {
            //cek data
            $query = new Query('/queue/simple/print');
            $query->where('comment', 'prioritas');

            $response = AnyHelpers::loginSesi()->query($query)->read();
            
            if (count($response) > 0) {
                $query = new Query('/queue/simple/remove');
                $query->equal('.id', $response[0]['.id']);

                $response = AnyHelpers::loginSesi()->query($query)->read();
            }

            $q = new Query('/ip/firewall/mangle/print');
            $q->where('comment', 'Prioritas ' . $data['service']);
            $qres = AnyHelpers::loginSesi()->query($q)->read();
            if (count($qres) == 0) {
                //add rule blok
                $service = AnyHelpers::getJson('service_list', ['key' => 'service_name', 'value' => $data['service']]);
                $type = null;
                $lop = 0;
                foreach ($service as $s) {
                    if ($s['type'] == "raw") {
                        $query = new Query('/ip/firewall/raw/add');
                        if ($lop == 0) {
                            $query->equal('comment', 'Prioritas ' . $data['service']);
                        }
                        $lop++;
                        $query->equal('chain', 'prerouting');
                        $query->equal('protocol', strtolower($s['protocol']));
                        $query->equal('dst-port', $s['port']);
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

                        //mangle address list
                        $l7 = new Query('/ip/firewall/mangle/add');
                        $l7->equal('comment', 'Prioritas ' . $data['service']);
                        $l7->equal('chain', 'prerouting');
                        $l7->equal('protocol', 'tcp');
                        $l7->equal('dst-port', '443');
                        $l7->equal('dst-address-list', '!IP_LOCAL');
                        $l7->equal('layer7-protocol', $s['service_name']);
                        $l7->equal('action', 'add-dst-to-address-list');
                        $l7->equal('address-list', $s['service_name']);
                        $hasil2 = AnyHelpers::loginSesi()->query($l7)->read();

                    } elseif ($s['type'] == "content") {
                        $query = new Query('/ip/firewall/raw/add');
                        if ($lop == 0) {
                            $query->equal('comment', 'Prioritas ' . $data['service']);
                        }
                        $lop++;
                        $query->equal('chain', 'prerouting');
                        $query->equal('dst-address-list', '!IP_LOCAL');
                        $query->equal('content', $s['domain']);
                        $query->equal('action', 'add-dst-to-address-list');
                        $query->equal('address-list', $s['service_name']);
                        $hasil = AnyHelpers::loginSesi()->query($query)->read();
                        $type = "raw";
                    }
                }

                //mark-connection
                $mc_u = new Query('/ip/firewall/mangle/add');
                $mc_u->equal('dst-address-list', $data['service']);
                $mc_u->equal('action', 'mark-connection');
                $mc_u->equal('new-connection-mark', 'con-Prioritas ' . $data['service']);
                $mc_u->equal('passthrough', 'yes');
                $mc_u->equal('chain', 'prerouting');

                if ($type == "raw" || $type == "content") {
                    $mc_u->equal('comment', 'Prioritas ' . $data['service']);
                }

                $queue0 = AnyHelpers::loginSesi()->query($mc_u)->read();

                $paket_mark = 'paket-' . 'Prioritas ' . $data['service'];

                // mark-packet
                $mp_u = new Query('/ip/firewall/mangle/add');
                $mp_u->equal('chain', 'forward');
                $mp_u->equal('connection-mark', 'con-' . 'Prioritas ' . $data['service']);
                $mp_u->equal('action', 'mark-packet');
                $mp_u->equal('new-packet-mark', $paket_mark);
                $mp_u->equal('passthrough', 'no');
                $queue1 = AnyHelpers::loginSesi()->query($mp_u)->read();

            } else {
                $paket_mark = 'paket-' . 'Prioritas ' . $data['service'];
            }

            $query = new Query('/queue/simple/add');
            $query->equal('name', $data['name']);
            $query->equal('packet-marks', $paket_mark);
            $query->equal('priority', '1');
            $query->equal('comment', 'prioritas');

            $response = AnyHelpers::loginSesi()->query($query)->read();
            
            //get data simple queue
            $query = new Query('/queue/simple/print');
            $response = AnyHelpers::loginSesi()->query($query)->read();

            $queue_1 = $response[0]['.id'];
            
            //get data simple queue prioritas
            $query = new Query('/queue/simple/print');
            $query->where('comment', 'prioritas');
            $response = AnyHelpers::loginSesi()->query($query)->read();
            
            $queue_id = $response[0]['.id'];
            
            if ($queue_1 != $queue_id) {
                $query = new Query('/queue/simple/move');
                $query->equal('.id', $queue_id);
                $query->equal('destination', $queue_1);

                $response = AnyHelpers::loginSesi()->query($query)->read();
            }

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
                AnyHelpers::saveLog('Berhasil Konfigurasi Prioritas Traffic', 'info');
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
            $query = new Query("/queue/simple/remove");
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
                AnyHelpers::saveLog('Berhasil Menghapus data Prioritas Traffic', 'info');
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