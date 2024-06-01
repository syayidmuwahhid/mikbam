<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Helpers\AnyHelpers;

class Dashboard extends Component
{
    public $ip, $username, $password, $port;
    public $title, $pagetitle, $clock, $resource, $interface, $log;

    protected $listener = [
        'tes'
    ];

    public function mount()
    {
        $this->title = 'Dashboard - NiceAdmin Bootstrap Template';
        $this->pagetitle = 'Dashboard';
        $this->ip = session()->get('login')['ip'];
        $this->username = session()->get('login')['username'];
        $this->password = session()->get('login')['password'];
        $this->port = session()->get('login')['port'];
        $this->resource['platform'] = null;
        $this->resource['board-name'] = null;
        $this->resource['version'] = null;
        $this->resource['cpu'] = null;
        $this->resource['cpu-load'] = null;
        $this->resource['cpu-frequency'] = null;
        $this->resource['free-hdd-space'] = null;
        $this->resource['total-hdd-space'] = null;
        $this->resource['free-memory'] = null;
        $this->resource['total-memory'] = null;
        $this->resource['model'] = null;
        $this->log = [];
        $this->getData();

    }

    public function render()
    {
        $this->getData();
        session()->put('menu_active', 'dashboard');
        session()->put('sub_menu_active', '');
        return view('livewire.dashboard');
    }

    public function getData()
    {
        try {
            $login = AnyHelpers::login($this->ip, $this->username, $this->password, $this->port);
            
            //get waktu
            $getClock = $login->query('/system/clock/print')->read();
            $this->clock['hari'] = date('l', strtotime($getClock[0]['date']));
            $this->clock['time'] = $getClock[0]['time'];
            $this->clock['tgl'] = $getClock[0]['date'];
            
            //get resource
            $getResource = $login->query('/system/resource/print')->read();
            $this->resource['platform'] = $getResource[0]['platform'];
            $this->resource['board-name'] = $getResource[0]['board-name'];
            $this->resource['version'] = $getResource[0]['version'];
            $this->resource['cpu'] = $getResource[0]['cpu'];
            $this->resource['cpu-load'] = $getResource[0]['cpu-load'];
            $this->resource['cpu-frequency'] = $getResource[0]['cpu-frequency'];
            $this->resource['free-hdd-space'] = round($getResource[0]['free-hdd-space'] / 1049000, 1);
            $this->resource['total-hdd-space'] = round($getResource[0]['total-hdd-space'] / 1049000, 1);
            $this->resource['free-memory'] = round($getResource[0]['free-memory'] / 1049000, 1);
            $this->resource['total-memory'] = round($getResource[0]['total-memory'] / 1049000, 1);
            
            //get routerboard
            $getRouterboard = $login->query('/system/routerboard/print')->read();
            $this->resource['model'] = isset($getRouterboard[0]['model']) ? $getRouterboard[0]['model'] : 'RouterOS';
            
            //get interface
            $getInterface = $login->query('/interface/print')->read();
            $this->interface = $getInterface;

            //getlog
            $log = AnyHelpers::getJson('log');
            $logs = array();
            for ($i=count($log)-1; $i >= 0; $i--) { 
                if (explode(' ', $log[$i]['created_at'])[0] == date('Y-m-d')) {
                    $data_log = array(
                        'waktu' => date('H:i:s', strtotime($log[$i]['created_at'])),
                        'deskripsi' => $log[$i]['deskripsi'],
                        'status' => $log[$i]['status']
                    );
                    array_push($logs, $data_log);
                }
                if (count($logs) == 5) {
                    break;
                }
            }
            $this->log = $logs;

        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('swal:toast', [
                'type' => 'error',
                'title' => 'Kesalahan',
                'text' => $th->getMessage(),
            ]);
            // session()->forget('login');
        }
    }

    public function tes()
    {
        dd('ok');
        // return response()->json('ok');
    }
}
