<?php

namespace App\Http\Livewire\ManagementBandwidth;

use App\Helpers\AnyHelpers;
use Livewire\Component;
use \RouterOS\Query;

class SimpleQueue extends Component
{
    public $SQLists, $interfaces, $parents;

    protected $listeners = ['removeItem' => 'remove', 'store'];

    public function mount()
    {
        $this->getInterface();
        $this->getParent();
    }

    public function render()
    {
        session()->put('menu_active', 'management-bandwidth');
        session()->put('sub_menu_active', 'simple-queue');
        return view('livewire.management-bandwidth.simple-queue');
    }

    public function getData()
    {
        try {
            $query = new Query('/queue/simple/print');
            $queue = AnyHelpers::loginSesi()->query($query)->read();

            if (count($queue) > 0) {
                for ($i = 0; $i < count($queue); $i++) {
                    $exp = explode("/", $queue[$i]["max-limit"]);

                    if ((int)$exp[0] / 1000000 < 1) {
                        if ((int)$exp[0] / 1000 < 1) {
                            if ((int)$exp[0] == 0) {
                                $upload = "Unlimited";
                            } else {
                                $upload = $exp[0];
                            }
                        } else {
                            $upload = ((int)$exp[0] / 1000) . "k";
                        }
                    } else {
                        $upload = ((int)$exp[0] / 1000000) . "M";
                    }

                    if ((int)$exp[1] / 1000000 < 1) {
                        if ((int)$exp[1] / 1000 < 1) {
                            if ((int)$exp[1] == 0) {
                                $download = "Unlimited";
                            } else {
                                $download = $exp[1];
                            }
                        } else {
                            $download = ((int)$exp[1] / 1000) . "k";
                        }
                    } else {
                        $download = ((int)$exp[1] / 1000000) . "M";
                    }

                    $queue[$i]["max-limit"] = $upload . "/" . $download;


                    if ($queue[$i]['invalid'] == "true") {  
                        if (isset($queue[$i]['comment'])) {
                            if ($queue[$i]['comment'] == "pcq") {
                                $queue[$i]['dy'] = "I~PCQ";
                                $queue[$i]['label'] = "Invalid, Perconnection Queue";
                            } else {
                                $queue[$i]['dy'] = "I";
                                $queue[$i]['label'] = "Invalid";    
                            }
                        } else {
                            $queue[$i]['dy'] = "I";
                            $queue[$i]['label'] = "Invalid";
                        }
                        $queue[$i]['tr-class'] = "text-danger fst-italic";
                    } else {
                        if(isset($queue[$i]['comment'])){
                            if ($queue[$i]['comment'] == "pcq") {
                                $queue[$i]['dy'] = "PCQ";
                                $queue[$i]['label'] = "Perconnection Queue";
                            } else {
                                $queue[$i]['dy'] = "";
                                $queue[$i]['label'] = "";    
                            }
                        } else {
                            $queue[$i]['dy'] = "";
                            $queue[$i]['label'] = "";
                        }
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
            $this->SQLists = $queue;
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
            $query = new Query("/queue/simple/" . $stat);
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
                AnyHelpers::saveLog('Sukses ' . $stat . ' SImple Queue', 'info');
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
                AnyHelpers::saveLog('Berhasil Menghapus data Simple Queue', 'info');
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

    public function changeItemPosition($before, $after, $selected, $move)
    {
        try {
            $this->dispatchBrowserEvent('blockUI', ['msg' => 'Loading . . . ']);

            if ($move == "up") {
                $from = $selected;
                $to = $before;
            } else {
                $from = $after;
                $to = $selected;
            }
            $query = new Query('/queue/simple/move');
            $query->equal('.id', $from);
            $query->equal('destination', $to);

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
                AnyHelpers::saveLog('Berhasil Merubah Posisi Simple Queue', 'info');
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
    
    public function getParent()
    {
        try {
            //query
            $query = new Query("/queue/simple/print");
            $query->where("disabled", "false");
            $this->parents = AnyHelpers::loginSesi()->query($query)->read();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('swal:toast', [
                'type' => 'error',
                'title' => 'Kesalahan',
                'text' => $th->getMessage(),
            ]);
        }
    }

    function store($data)
    {
        try {
            if ($data['max_limit'] != null) {
                $max_limitRaw = explode("/", $data['max_limit']);

                $u_Raw = $max_limitRaw[0];
                $u_nilai = substr($u_Raw, 0, -1);
                $u_byteSize = substr($u_Raw, -1);
                if ($u_byteSize == "M" || $u_byteSize == "m") {
                    $u_size = 1000000;
                } elseif ($u_byteSize == "K" || $u_byteSize == "k") {
                    $u_size = 1000;
                } else {
                    $u_size = 1;
                    $u_nilai = $u_Raw;
                }
                $upload = $u_nilai * $u_size;

                $d_Raw = $max_limitRaw[1];
                $d_nilai = substr($d_Raw, 0, -1);
                $d_byteSize = substr($d_Raw, -1);
                if ($d_byteSize == "M" || $d_byteSize == "m") {
                    $d_size = 1000000;
                } elseif ($d_byteSize == "K" || $d_byteSize == "k") {
                    $d_size = 1000;
                } else {
                    $d_size = 1;
                    $d_nilai = $d_Raw;
                }
                $download = $d_nilai * $d_size;

                $max_limit = $upload . "/" . $download;
            } else {
                $max_limit = "";
            }
            
            $query = new Query('/queue/simple/add');
            $query->equal('name', $data['name']);
            $query->equal('target', $data['target']);
            $query->equal('max-limit', $max_limit);
            $query->equal('parent', $data['parent']);
            if ($data['dst'] != "") {
                $query->equal('dst', $data['dst']);
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
                AnyHelpers::saveLog('Berhasil Menambah data Simple Queue', 'info');
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