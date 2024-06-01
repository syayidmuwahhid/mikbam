<?php

namespace App\Http\Livewire\KonfigurasiDasar;

use Livewire\Component;
use App\Helpers\AnyHelpers;
use \RouterOS\Query;

class IpAddress extends Component
{
    public $ipList, $interfaces;

    protected $listeners = ['removeItem' => 'remove', 'getInterface', 'storeIP'];

    public function mount()
    {
        $this->ipList = [];
        $this->getInterface();
    }

    public function render()
    {
        session()->put('menu_active', 'konfigurasi-dasar');
        session()->put('sub_menu_active', 'ip-address');
        return view('livewire.konfigurasi-dasar.ip-address');
    }

    public function getData()
    {
        try {
            //get IP
            $this->ipList = AnyHelpers::loginSesi()->query('/ip/address/print')->read();

            for ($i=0; $i < count($this->ipList); $i++) { 
                if (substr($this->ipList[$i]['interface'], 0, 1) == "*") {
                    $this->ipList[$i]['dy'] = "I";
                    $this->ipList[$i]['label'] = "invalid";
                } else if ($this->ipList[$i]['dynamic'] == "true" && $this->ipList[$i]['invalid'] == "false") {
                    $this->ipList[$i]['dy'] = "D";
                    $this->ipList[$i]['label'] = "Dynamic";
                } else if ($this->ipList[$i]['dynamic'] == "false" && $this->ipList[$i]['invalid'] == "false") {
                    $this->ipList[$i]['dy'] = "S";
                    $this->ipList[$i]['label'] = "Static";
                } else if ($this->ipList[$i]['dynamic'] == "false" && $this->ipList[$i]['invalid'] == "true") {
                    $this->ipList[$i]['dy'] = "I";
                    $this->ipList[$i]['label'] = "invalid";
                }

                if ($this->ipList[$i]['invalid'] == "true" || substr($this->ipList[$i]['interface'], 0, 1) == "*") {
                    $this->ipList[$i]['interface'] = "unknown";
                    $this->ipList[$i]['lineClass'] = "text-danger fst-italic";
                } else {
                    $this->ipList[$i]['interface'] = $this->ipList[$i]['interface'];
                }

                if ($this->ipList[$i]['disabled'] == "true") {
                    $this->ipList[$i]['ds'] = "Enabled";
                    $this->ipList[$i]['dscolor'] = "btn-success";
                    $this->ipList[$i]['tr_class'] = "text-warning fst-italic";
                } else {
                    $this->ipList[$i]['ds'] = "Disabled";
                    $this->ipList[$i]['dscolor'] = "btn-warning";
                    $this->ipList[$i]['tr_class'] = "";
                }
            }

        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('swal:toast', [
                'type' => 'error',
                'title' => 'Kesalahan',
                'text' => 'Koneksi Terputus Silakan Login Kembali',
            ]);
            session()->forget('login');
        }
    }

    public function endisable($id, $stat) {
        try {
            $this->dispatchBrowserEvent('blockUI', ['msg' => 'Loading . . . ']);
            if ($stat == "Disabled") {
                $stat = "disable";
            } else {
                $stat = "enable";
            }

            //query
            $query = new Query('/ip/address/' . $stat);
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
                AnyHelpers::saveLog('Berhasil ' . $stat . ' IP Address', 'info');
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
    
    public function remove($id) {
        try {
            //query
            $query = new Query('/ip/address/remove');
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
                AnyHelpers::saveLog('Berhasil Menghapus IP Address', 'info');
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

    public function getInterface()
    {
        try {
            //query
            $query = new Query('/interface/print');
            $this->interfaces = AnyHelpers::loginSesi()->query($query)->read();

        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('swal:toast', [
                'type' => 'error',
                'title' => 'Kesalahan',
                'text' => $th->getMessage(),
            ]);
        }
    }

    public function storeIP($ip, $interface)
    {
        try {
            //cek interface
            $query = new Query('/ip/address/print');
            $query->where('interface', $interface);
            $query->where('disabled', 'no');
            $cek = AnyHelpers::loginSesi()->query($query)->read();

            if ($cek == null) {
                //query
                $query = new Query('/ip/address/add');
                $query->equal('address', $ip);
                $query->equal('interface', $interface);

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
                    AnyHelpers::saveLog('Sukses Menambah IP Address ( ' . $ip . ' )', 'info');
                    $this->dispatchBrowserEvent('swal:toast', [
                        'type' => 'success',
                        'title' => 'Berhasil',
                        'text' => '',
                    ]);
                }
            } else {
                AnyHelpers::saveLog('Gagal Menambah IP', 'error');
                $this->dispatchBrowserEvent('swal:toast', [
                    'type' => 'error',
                    'title' => 'Kesalahan',
                    'text' => 'Interface Sudah Memiliki IP Address',
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