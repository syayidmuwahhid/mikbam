<?php

namespace App\Http\Livewire\MonitoringTraffic;

use Livewire\Component;
use App\Helpers\AnyHelpers;
use \RouterOS\Query;

class Notifikasi extends Component
{
    public $netwatchList;

    protected $listeners = ['removeItem' => 'remove', 'store'];

    public function mount()
    {
        $this->netwatchList = [];
    }

    public function render()
    {
        session()->put('menu_active', 'monitoring-traffic');
        session()->put('sub_menu_active', 'notifikasi');
        return view('livewire.monitoring-traffic.notifikasi');
    }

    public function store($data)
    {
        try {
            $query = new Query('/tool/netwatch/add');
            $query->equal("host", $data['target']);
            $query->equal("interval", $data['interval']);
            $query->equal("timeout", $data['timeout']);

            if ($data['message_up'] != null || $data['message_down'] != ""
            ) {
                $query->equal("up-script", '/tool fetch url="https://api.telegram.org/bot5102714475:AAHBYIGLVqBjDGlNotHE1X56r0qjv6Y_LQI/sendMessage?chat_id=' . $data['chat_id'] . '&text=' . urlencode($data['message_up']) . '" keep-result=no');
            }
            if ($data['message_down'] != null || $data['message_down'] != "") {
                $query->equal("down-script", '/tool fetch url="https://api.telegram.org/bot5102714475:AAHBYIGLVqBjDGlNotHE1X56r0qjv6Y_LQI/sendMessage?chat_id=' . $data['chat_id'] . '&text=' . urlencode($data['message_down']) . '" keep-result=no');
            }

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
                AnyHelpers::saveLog('Berhasil Menambah Notifikasi Telegram', 'info');
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

    public function getData()
    {
        try {
            //get IP
            $this->netwatchList = AnyHelpers::loginSesi()->query('/tool/netwatch/print')->read();

            for ($i = 0; $i < count($this->netwatchList); $i++) {
                if ($this->netwatchList[$i]['disabled'] == "true") {
                    $this->netwatchList[$i]['ds'] = "Enabled";
                    $this->netwatchList[$i]['dscolor'] = "btn-success";
                    $this->netwatchList[$i]['tr_class'] = "text-warning fst-italic";
                } else {
                    $this->netwatchList[$i]['ds'] = "Disabled";
                    $this->netwatchList[$i]['dscolor'] = "btn-warning";
                    $this->netwatchList[$i]['tr_class'] = "";
                }
                
                //get chat message up
                if(!empty($this->netwatchList[$i]['up-script'])){
                    $chatData = explode(' ', explode('/tool fetch url="https://api.telegram.org/bot5102714475:AAHBYIGLVqBjDGlNotHE1X56r0qjv6Y_LQI/sendMessage?', $this->netwatchList[$i]['up-script'])[1])[0];
                }
                $chatID = !empty($this->netwatchList[$i]['up-script']) ? explode('&', $chatData)[0] : '';
                $messageUP = !empty($this->netwatchList[$i]['up-script']) ? str_replace('+', ' ', substr(explode('=', explode('&', $chatData)[1])[1], 0, -1)) : '';
                
                //get chat message down
                if(!empty($this->netwatchList[$i]['down-script'])){
                    $chatData = explode(' ', explode('/tool fetch url="https://api.telegram.org/bot5102714475:AAHBYIGLVqBjDGlNotHE1X56r0qjv6Y_LQI/sendMessage?', $this->netwatchList[$i]['down-script'])[1])[0];
                }
                $messageDown = !empty($this->netwatchList[$i]['down-script']) ? str_replace('+', ' ', substr(explode('=', explode('&', $chatData)[1])[1], 0, -1))  : '';

                $this->netwatchList[$i]['chat_id'] = explode('=', $chatID)[1];
                $this->netwatchList[$i]['message_up'] = $messageUP;
                $this->netwatchList[$i]['message_down'] = $messageDown;
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('swal:toast', [
                'type' => 'error',
                'title' => 'Kesalahan',
                'text' => $th->getMessage(),
            ]);
            // session()->forget('login');
        }
    }

    public function remove($id)
    {
        try {
            //query
            $query = new Query('/tool/netwatch/remove');
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
                AnyHelpers::saveLog('Berhasil Menghapus data Notifikasi Telegram', 'info');
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
            $query = new Query('/tool/netwatch/' . $stat);
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
                AnyHelpers::saveLog('Berhasil ' . $stat . ' Notifikasi', 'info');
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
}