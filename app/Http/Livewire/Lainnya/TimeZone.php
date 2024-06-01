<?php

namespace App\Http\Livewire\Lainnya;

use Livewire\Component;
use App\Helpers\AnyHelpers;

class TimeZone extends Component
{
    public $timezones;

    protected $listeners = ['removeItem' => 'remove', 'store'];

    public function mount()
    {
        $this->timezones = AnyHelpers::getJson('time_zone');
    }

    public function render()
    {
        session()->put('menu_active', 'lainnya');
        session()->put('sub_menu_active', 'time-zone');
        return view('livewire.lainnya.time-zone');
    }

    public function store($data)
    {
        try {
            $data['created_at'] = date('Y-m-d H:i:s');

            //cek data
            $cek = AnyHelpers::getJson('time_zone', ['key' => 'negara', 'value' => $data['negara']]);
            if (count($cek) == 0) {
                AnyHelpers::insertJson('time_zone', $data);
                AnyHelpers::saveLog('Berhasil Menyimpan data Time Zone', 'error');
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
                    'text' => 'Data Time Zone dengan Negara tersebut sudah Ada',
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

    public function remove($id)
    {
        try {
            AnyHelpers::deleteJson('time_zone', ["key" => "negara", "value" => $id]);
            AnyHelpers::saveLog('Berhasil Menghapus data Time Zone', 'info');
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