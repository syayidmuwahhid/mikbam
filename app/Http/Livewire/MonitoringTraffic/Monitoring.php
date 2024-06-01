<?php

namespace App\Http\Livewire\MonitoringTraffic;

use App\Helpers\AnyHelpers;
use Illuminate\Http\Request;
use Livewire\Component;
use \RouterOS\Query;

class Monitoring extends Component
{
    public $trees, $simples, $dataChart;

    // protected $listeners = ['getChart'];

    public function mount()
    {
        $this->getTree();
        $this->getSimple();
    }

    public function render()
    {
        return view('livewire.monitoring-traffic.monitoring');
    }

    public function getTree()
    {
        try {
            $query = new Query("/queue/tree/print");
            $query->where("disabled", "false");
            
            $this->trees = AnyHelpers::loginSesi()->query($query)->read();

        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('swal:toast', [
                'type' => 'error',
                'title' => 'Kesalahan',
                'text' => $th->getMessage(),
            ]);
        }
    }
    
    public function getSimple()
    {
        try {
            $query = new Query("/queue/simple/print");
            $query->where("disabled", "false");
            
            $this->simples= AnyHelpers::loginSesi()->query($query)->read();

        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('swal:toast', [
                'type' => 'error',
                'title' => 'Kesalahan',
                'text' => $th->getMessage(),
            ]);
        }
    }

    public function getChart(Request $rq)
    {
        try {
            $time = AnyHelpers::loginSesi()->query('/system/clock/print')->read();
            $data["time"] = $time[0]["time"];

            if ($rq->type == "0") {
                $query = new Query("/queue/simple/print");
                $query->where('name', $rq->name);
                $queue = AnyHelpers::loginSesi()->query($query)->read();
                $data["rate"] = explode('/', $queue[0]["rate"]);
                $data["upload"] = $data["rate"][0] / 1000;
                $data["download"] = $data["rate"][1] / 1000;
            } else {
                // return response()->json($rq->all());
                $query = new Query('/queue/tree/print');
                $query->where('name', 'up-' . explode('-', $rq->name)[1]);
                $query->where('name', 'down-' . explode('-', $rq->name)[1]);
                $query->operations('|');
                $queue = AnyHelpers::loginSesi()->query($query)->read();
                $data["rate"] = [$queue[0]["rate"], $queue[1]["rate"]];
                $data["upload"] = $queue[0]["rate"] / 1000;
                $data["download"] = $queue[1]["rate"] / 1000;
            }
            return response()->json($data);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('swal:toast', [
                'type' => 'error',
                'title' => 'Kesalahan',
                'text' => $th->getMessage(),
            ]);
        }
    }
}
