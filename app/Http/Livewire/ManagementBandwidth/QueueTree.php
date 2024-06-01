<?php

namespace App\Http\Livewire\ManagementBandwidth;

use App\Helpers\AnyHelpers;
use Livewire\Component;
use \RouterOS\Query;

class QueueTree extends Component
{
    public $services, $queues, $treeLists, $service_option;

    protected $listeners = ['removeItem' => 'remove', 'store'];

    public function mount()
    {
        $this->services = AnyHelpers::getJson('service_list');
        $this->getQueue();
        $this->service_option = AnyHelpers::unique_multi_array($this->services, 'service_name');
    }
    
    public function render()
    {
        $this->getData();
        session()->put('menu_active', 'management-bandwidth');
        session()->put('sub_menu_active', 'queue-tree');
        return view('livewire.management-bandwidth.queue-tree');
    }
    
    public function store($data)
    {
        try {
            if ($data['type'] == "1") { // ip address
                //mark-conn
                $mc_up = new Query('/ip/firewall/mangle/add');
                $mc_up->equal('comment', $data['nama']);
                $mc_up->equal('chain', 'forward');
                $mc_up->equal('action', 'mark-connection');
                $mc_up->equal('new-connection-mark', 'con-up' . '-' . $data['nama']);
                $mc_up->equal('passthrough', 'yes');
                $mc_up->equal('dst-address', $data['ip']);
                $queue = AnyHelpers::loginSesi()->query($mc_up)->read();

                $mc_down = new Query('/ip/firewall/mangle/add');
                $mc_down->equal('chain', 'forward');
                $mc_down->equal('action', 'mark-connection');
                $mc_down->equal('new-connection-mark', 'con-down' . '-' . $data['nama']);
                $mc_down->equal('passthrough', 'yes');
                $mc_down->equal('src-address', $data['ip']);
                $queue = AnyHelpers::loginSesi()->query($mc_down)->read();

                // mark-packet
                $mp_up = new Query('/ip/firewall/mangle/add');
                $mp_up->equal('chain', 'forward');
                $mp_up->equal('action', 'mark-packet');
                $mp_up->equal('new-packet-mark', $data['nama'] . '-up');
                $mp_up->equal('passthrough', 'no');
                $queue = AnyHelpers::loginSesi()->query($mp_up)->read();

                $mp_down = new Query('/ip/firewall/mangle/add');
                $mp_down->equal('chain', 'forward');
                $mp_down->equal('action', 'mark-packet');
                $mp_down->equal('new-packet-mark', $data['nama'] . '-down');
                $mp_down->equal('passthrough', 'no');

                $queue = AnyHelpers::loginSesi()->query($mp_down)->read();
                $paket_mark_up = $data['nama'] . '-up';
                $paket_mark_down = $data['nama'] . '-down';

            } elseif ($data['type'] == "2") { //domain
                // address list local
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
                $q = new Query('/ip/firewall/mangle/print');
                $q->where('comment', $data['service']);
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

                            //mangle address list
                            $l7 = new Query('/ip/firewall/mangle/add');
                            $l7->equal('comment', $data['service']);
                            $l7->equal('chain', 'prerouting');
                            $l7->equal('protocol', 'tcp');
                            $l7->equal('dst-port', '443');
                            $l7->equal('src-address-list', 'IP_LOCAL');
                            $l7->equal('dst-address-list', '!IP_LOCAL');
                            $l7->equal('layer7-protocol', $s['service_name']);
                            $l7->equal('action', 'add-dst-to-address-list');
                            $l7->equal('address-list', $s['service_name']);
                            $hasil2 = AnyHelpers::loginSesi()->query($l7)->read();

                            $l7u = new Query('/ip/firewall/mangle/add');
                            $l7u->equal('chain', 'prerouting');
                            $l7u->equal('protocol', 'udp');
                            $l7u->equal('dst-port', '443');
                            $l7u->equal('src-address-list', 'IP_LOCAL');
                            $l7u->equal('dst-address-list', '!IP_LOCAL');
                            $l7u->equal('layer7-protocol', $s['service_name']);
                            $l7u->equal('action', 'add-dst-to-address-list');
                            $l7u->equal('address-list', $s['service_name']);
                            $hasil3 = AnyHelpers::loginSesi()->query($l7u)->read();
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

                    //mark-connection
                    $mc_u = new Query('/ip/firewall/mangle/add');
                    $mc_u->equal('src-address-list', 'IP_LOCAL');
                    $mc_u->equal('dst-address-list', $data['service']);
                    $mc_u->equal('action', 'mark-connection');
                    $mc_u->equal('new-connection-mark', 'con-up-' . $data['service']);
                    $mc_u->equal('passthrough', 'yes');
                    $mc_u->equal('chain', 'prerouting');

                    $mc_d = new Query('/ip/firewall/mangle/add');
                    $mc_d->equal('src-address-list', $data['service']);
                    $mc_d->equal('dst-address-list', 'IP_LOCAL');
                    $mc_d->equal('action', 'mark-connection');
                    $mc_d->equal('new-connection-mark', 'con-down-' . $data['service']);
                    $mc_d->equal('passthrough', 'yes');
                    $mc_d->equal('chain', 'prerouting');

                    if ($type == "raw" || $type == "content") {
                        $mc_u->equal('comment', $data['service']);
                    }

                    $queue0 = AnyHelpers::loginSesi()->query($mc_u)->read();
                    $queue1 = AnyHelpers::loginSesi()->query($mc_d)->read();

                    $paket_mark_up = 'paket-up-' . $data['service'];
                    $paket_mark_down = 'paket-down-' . $data['service'];

                    // mark-packet
                    $mp_u = new Query('/ip/firewall/mangle/add');
                    $mp_u->equal('chain', 'forward');
                    $mp_u->equal('connection-mark', 'con-up-' . $data['service']);
                    $mp_u->equal('action', 'mark-packet');
                    $mp_u->equal('new-packet-mark', $paket_mark_up);
                    $mp_u->equal('passthrough', 'no');
                    $queue1 = AnyHelpers::loginSesi()->query($mp_u)->read();

                    $mp_d = new Query('/ip/firewall/mangle/add');
                    $mp_d->equal('chain', 'forward');
                    $mp_d->equal('connection-mark', 'con-down-' . $data['service']);
                    $mp_d->equal('action', 'mark-packet');
                    $mp_d->equal('new-packet-mark', $paket_mark_down);
                    $mp_d->equal('passthrough', 'no');
                    $queue1 = AnyHelpers::loginSesi()->query($mp_d)->read();
                } else {
                    $paket_mark_up = 'paket-up-' . $data['service'];
                    $paket_mark_down = 'paket-down-' . $data['service'];
                }
            }

            // queue tree
            $q_up = new Query('/queue/tree/add');
            $q_up->equal('comment', 'Web-' . $data['nama']);
            $q_up->equal('name', 'up-' . $data['nama']);
            $q_up->equal('parent', $data['parent_up']);
            $q_up->equal('priority', $data['priority_up']);
            $q_up->equal('max-limit', strtoupper($data['max_limit_up']));

            $q_down = new Query('/queue/tree/add');
            $q_down->equal('comment', 'Web-' . $data['nama']);
            $q_down->equal('name', 'down-' . $data['nama']);
            $q_down->equal('parent', $data['parent_down']);
            $q_down->equal('priority', $data['priority_down']);
            $q_down->equal('max-limit', strtoupper($data['max_limit_down']));

            if ($data['type'] != "3") {
                $q_up->equal('packet-mark', $paket_mark_up);
                $q_down->equal('packet-mark', $paket_mark_down);
            }

            if ($data['kategori'] == "Child") {
                $q_up->equal('limit-at', strtoupper($data['limit_at_up']));
                $q_down->equal('limit-at', strtoupper($data['limit_at_down']));
            }
            $queue2 = AnyHelpers::loginSesi()->query($q_up)->query($q_down)->read();
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
                AnyHelpers::saveLog('Berhasil Konfigurasi Queue Tree', 'info');
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
        $this->render();
    }

    public function getQueue()
    {
        $query = new Query('/queue/tree/print');
        $queue = AnyHelpers::loginSesi()->query($query)->read();
        $this->queues = $queue;
    }

    public function getData()
    {
        try {
            $query = new Query('/queue/tree/print');
            $queue = AnyHelpers::loginSesi()->query($query)->read();

            if (count($queue) > 0) {
                for ($i = 0; $i < count($queue); $i++) {

                    $exp = $queue[$i]["max-limit"];
                    $exp2 = $queue[$i]["limit-at"];

                    if ((int)$exp / 1000000 < 1) {
                        if ((int)$exp / 1000 < 1) {
                            if ((int)$exp == 0) {
                                $max_limit = 0;
                            } else {
                                $max_limit = $exp;
                            }
                        } else {
                            $max_limit = ((int)$exp / 1000) . "k";
                        }
                    } else {
                        $max_limit = ((int)$exp / 1000000) . "M";
                    }

                    if ((int)$exp2 / 1000000 < 1) {
                        if ((int)$exp2 / 1000 < 1) {
                            if ((int)$exp2 == 0) {
                                $limit_at = 0;
                            } else {
                                $limit_at = $exp2;
                            }
                        } else {
                            $limit_at = ((int)$exp2 / 1000) . "k";
                        }
                    } else {
                        $limit_at = ((int)$exp2 / 1000000) . "M";
                    }

                    $queue[$i]["max_limit"] = $max_limit;
                    $queue[$i]["limit_at"] = $limit_at;

                    if ($queue[$i]['invalid'] == "true") {
                        $queue[$i]['dy'] = "I";
                        $queue[$i]['label'] = "Invalid";
                        $queue[$i]['tr-class'] = "text-danger fst-italic";
                    } else {
                        $queue[$i]['dy'] = "";
                        $queue[$i]['label'] = "";
                        $queue[$i]['tr-class'] = "";
                    }

                    if ($queue[$i]['disabled'] == "true") {
                        $queue[$i]['ds'] = "Enabled";
                        $queue[$i]['dscolor'] = "btn-success";
                    } else {
                        $queue[$i]['ds'] = "Disabled";
                        $queue[$i]['dscolor'] = "btn-warning";
                    }
                }
            }
            $this->treeLists = $queue;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('swal:toast', [
                'type' => 'error',
                'title' => 'Kesalahan',
                'text' => $th->getMessage(),
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
            $query = new Query("/queue/tree/" . $stat);
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
                AnyHelpers::saveLog('Berhasil ' . $stat . ' Queue Tree', 'error');
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
        $this->render();
    }

    public function remove($id)
    {
        try {
            //query
            //hapus queue tree
            $query = new Query("/queue/tree/remove");
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
                AnyHelpers::saveLog('Berhasil Menghapus data Queue Tree', 'error');
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
        $this->render();
    }
}
