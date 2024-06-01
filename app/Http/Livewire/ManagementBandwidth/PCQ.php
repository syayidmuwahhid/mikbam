<?php

namespace App\Http\Livewire\ManagementBandwidth;

use App\Helpers\AnyHelpers;
use Livewire\Component;
use \RouterOS\Query;

class PCQ extends Component
{
    public $PCQs, $interfaces, $parents;

    protected $listeners = ['removeItem' => 'remove', 'store'];

    public function mount()
    {
        $this->getInterface();
        $this->getParent();
    }

    public function render()
    {
        session()->put('menu_active', 'management-bandwidth');
        session()->put('sub_menu_active', 'pcq');
        return view('livewire.management-bandwidth.pcq');
    }

    public function getData()
    {
        try {
            $query = new Query('/queue/simple/print');
            $query->where('comment', 'pcq');
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
                    $queue[$i]["comment"] = isset($queue[$i]["comment"]) ? $queue[$i]["comment"] : "";

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
                        if (isset($queue[$i]['comment'])) {
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
                $this->PCQs = $queue;
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('swal:toast', [
                'type' => 'error',
                'title' => 'Kesalahan',
                'text' => $th->getMessage(),
            ]);
            session()->forget('login');
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

            if ($data['rate'] != null) {
                $rate = explode('/', strtoupper($data['rate']));
                $rate_u = $rate[0];
                $rate_d = $rate[1];
            } else {
                $rate_u = "0";
                $rate_d = "0";
            }

            //buat queue type
            $query = new Query('/queue/type/add');
            $query->equal('name', 'PCQU-' . $data['name']);
            $query->equal('kind', 'pcq');
            $query->equal('pcq-rate', $rate_u);
            $query->equal('pcq-classifier', 'src-address');
            $queue_typeU = AnyHelpers::loginSesi()->query($query)->read();

            $query = new Query('/queue/type/add');
            $query->equal('name', 'PCQD-' . $data['name']);
            $query->equal('kind', 'pcq');
            $query->equal('pcq-rate', $rate_d);
            $query->equal('pcq-classifier', 'dst-address');
            $queue_typeD = AnyHelpers::loginSesi()->query($query)->read();

            if (isset($queue_typeU["after"]) && isset($queue_typeD["after"])) {
                $query = new Query('/queue/simple/add');
                $query->equal('name', $data['name']);
                $query->equal('comment', "pcq");
                $query->equal('target', $data['target']);
                $query->equal('parent', $data['parent']);
                if ($data['dst'] != "") {
                    $query->equal('dst', $data['dst']);
                }
                $query->equal('max-limit', $max_limit);
                $query->equal('queue', "PCQU-" . $data['name'] . "/PCQD-" . $data['name']);
                $queue = AnyHelpers::loginSesi()->query($query)->read();

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
                    AnyHelpers::saveLog('Berhasil Menambah data PCQ', 'info');
                    $this->dispatchBrowserEvent('swal:toast', [
                        'type' => 'success',
                        'title' => 'Berhasil',
                        'text' => '',
                    ]);
                }
            } else {
                AnyHelpers::saveLog('Gagal Menambah data PCQ', 'error');
                $this->dispatchBrowserEvent('swal:toast', [
                    'type' => 'error',
                    'title' => 'Kesalahan',
                    'text' => 'Tidak Dapat Membuat PCQ Type',
                ]);
                $this->dispatchBrowserEvent('unBlockUI');
                return false;
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
                AnyHelpers::saveLog('Berhasil ' . $stat . ' PCQ', 'info');
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
            //get pcq type name
            $query = new Query('/queue/simple/print');
            $query->where('.id', $id);
            $q_type = AnyHelpers::loginSesi()->query($query)->read();

            $queue_type = explode('/', $q_type[0]["queue"]);
            $queue_type_u = $queue_type[0];
            $queue_type_d = $queue_type[1];

            //hapus queue simple
            $query = new Query("/queue/simple/remove");
            $query->equal('.id', $id);
            $queue = AnyHelpers::loginSesi()->query($query)->read();

            if (isset($queue["after"]["message"])) {
                AnyHelpers::saveLog($queue["after"]["message"], 'error');
                $this->dispatchBrowserEvent('swal:toast', [
                    'type' => 'error',
                    'title' => 'Kesalahan',
                    'text' => $queue["after"]["message"],
                ]);
                $this->dispatchBrowserEvent('unBlockUI');
                return false;
            } else {
                //hapus queue type
                $query = new Query('/queue/type/print');
                $query->where('name', $queue_type_u);
                $getidU = AnyHelpers::loginSesi()->query($query)->read();

                $query = new Query('/queue/type/remove');
                $query->equal('.id', $getidU[0][".id"]);
                $hapus_u = AnyHelpers::loginSesi()->query($query)->read();

                $query = new Query('/queue/type/print');
                $query->where('name', $queue_type_d);
                $getidD = AnyHelpers::loginSesi()->query($query)->read();

                $query = new Query('/queue/type/remove');
                $query->equal('.id', $getidD[0][".id"]);
                $hapus_d = AnyHelpers::loginSesi()->query($query)->read();

            }
            AnyHelpers::saveLog('Berhasil Menambah data PCQ', 'info');
            $this->dispatchBrowserEvent('swal:toast', [
                'type' => 'success',
                'title' => 'Berhasil',
                'text' => '',
            ]);
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