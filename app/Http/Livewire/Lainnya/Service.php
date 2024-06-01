<?php

namespace App\Http\Livewire\Lainnya;

use App\Helpers\AnyHelpers;
use Livewire\Component;

class Service extends Component
{
    public $services;

    protected $listeners = ['removeItem' => 'remove', 'store'];

    public function mount()
    {
        $this->services = AnyHelpers::getJson('service_list');
    }

    public function render()
    {
        session()->put('menu_active', 'lainnya');
        session()->put('sub_menu_active', 'service');
        return view('livewire.lainnya.service');
    }

    public function store($data)
    {
        try {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = null;
            
            //get last id
            $getID = AnyHelpers::getJson('service_list');
            AnyHelpers::insertJson('service_list', array_merge(['id' => end($getID)['id'] + 1], $data));
            AnyHelpers::saveLog('Berhasil Menambah data Service', 'info');
            $this->dispatchBrowserEvent('swal:toast', [
                'type' => 'success',
                'title' => 'Berhasil',
                'text' => '',
            ]);
            $this->dispatchBrowserEvent('reload');
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
            AnyHelpers::deleteJson('service_list', ["key" => "id", "value" => $id]);
            AnyHelpers::saveLog('Berhasil Menghapus data Service', 'info');
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
