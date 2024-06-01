<?php

namespace App\Http\Livewire\Lainnya;

use App\Helpers\AnyHelpers;
use Livewire\Component;
use Illuminate\Support\Facades\Crypt;

class DaftarRouter extends Component
{
    public $routers;

    public function mount()
    {
        $this->routers = AnyHelpers::getJson('router_list');
    }

    protected $listeners = ['removeItem' => 'remove', 'store'];

    public function render()
    {
        session()->put('menu_active', 'lainnya');
        session()->put('sub_menu_active', 'daftar-router');
        return view('livewire.lainnya.daftar-router');
    }

    public function store($data)
    {
        try {
            //cek data
            $cek = AnyHelpers::getJson('router_list', ["key" => "ip", "value" => $data['ip']]);

            if (count($cek) == 0) {
                $resp = AnyHelpers::loginRouter($data['ip'], $data['username'], Crypt::encryptString($data['password']), $data['port']);

                if ($resp["code"] == 200) {
                    $result = array(
                        'identity' => $resp["data"],
                        'ip' => $data['ip'],
                        'username' => $data['username'],
                        'password' => Crypt::encryptString($data['password']),
                        'port' => $data['port'],
                        'last_active' => null,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => null
                    );
                } else {
                    $result = array(
                        'identity' => null,
                        'ip' => $data['ip'],
                        'username' => $data['username'],
                        'password' => Crypt::encryptString($data['password']),
                        'port' => $data['port'],
                        'last_active' => null,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => null
                    );
                }
                AnyHelpers::insertJson('router_list', $result);
                AnyHelpers::saveLog('Berhasil Menambah data Router', 'info');
                $this->dispatchBrowserEvent('swal:toast', [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'text' => '',
                ]);
                
                $this->dispatchBrowserEvent('reload');
            } else {
                $this->dispatchBrowserEvent('swal:toast', [
                    'type' => 'error',
                    'title' => 'Kesalahan',
                    'text' => 'Data Router sudah Ada',
                ]);
            }
        } catch (\Throwable $th) {
            AnyHelpers::saveLog($th->getMessage(), 'error');
            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'error',
                'title' => 'Kesalahan',
                'text' => $th->getMessage(),
            ]);
        }
        $this->dispatchBrowserEvent('unBlockUI');
    }

    public function remove($data) {
        try {
            AnyHelpers::deleteJson('router_list', ["key" => "ip", "value" => $data]);
            AnyHelpers::saveLog('Berhasil Menghapus data Router', 'info');
            $this->dispatchBrowserEvent('swal:toast', [
                'type' => 'success',
                'title' => 'Berhasil',
                'text' => '',
            ]);
        } catch (\Throwable $th) {
            AnyHelpers::saveLog($th->getMessage(), 'error');
            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'error',
                'title' => 'Kesalahan',
                'text' => $th->getMessage(),
            ]);
        }
        $this->dispatchBrowserEvent('reload');
        $this->dispatchBrowserEvent('unBlockUI');
    }
}