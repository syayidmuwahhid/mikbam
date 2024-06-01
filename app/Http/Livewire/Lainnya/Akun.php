<?php

namespace App\Http\Livewire\Lainnya;

use Livewire\Component;
use App\Helpers\AnyHelpers;
use \RouterOS\Query;

class Akun extends Component
{
    public $userList;

    protected $listeners = ['removeItem' => 'remove', 'store'];

    public function mount()
    {
        $this->userList = [];
    }

    public function render()
    {
        session()->put('menu_active', 'lainnya');
        session()->put('sub_menu_active', 'akun');
        return view('livewire.lainnya.akun');
    }

    public function getData()
    {
        try {
            //get users
            $this->userList = AnyHelpers::loginSesi()->query('/user/print')->read();

            for ($i = 0; $i < count($this->userList); $i++) {
                if ($this->userList[$i]['disabled'] == "true") {
                    $this->userList[$i]['ds'] = "Enabled";
                    $this->userList[$i]['dscolor'] = "btn-success";
                    $this->userList[$i]['tr_class'] = "text-warning fst-italic";
                } else {
                    $this->userList[$i]['ds'] = "Disabled";
                    $this->userList[$i]['dscolor'] = "btn-warning";
                    $this->userList[$i]['tr_class'] = "";
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
            $query = new Query('/user/' . $stat);
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
                AnyHelpers::saveLog('Berhasil ' . $stat . ' Akun', 'error');
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

    public function remove($id)
    {
        try {
            //query
            $query = new Query('/user/remove');
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
                AnyHelpers::saveLog('Berhasil Menghapus User', 'info');
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

    public function store($data)
    {
        try {
            //query
            $query = new Query('/user/add');
            $query->equal('name', $data['name']);
            $query->equal('password', $data['password_akun']);
            $query->equal('group', strtolower($data['level_akun']));
            $query->equal('address', $data['allowed_address']);
            $query->equal('disabled', 'no');

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
                AnyHelpers::saveLog('Berhasil Menambah User', 'info');
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