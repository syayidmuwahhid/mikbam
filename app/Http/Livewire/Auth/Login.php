<?php

namespace App\Http\Livewire\Auth;

use Livewire\Component;
use RealRashid\SweetAlert\Facades\Alert;
use App\Helpers\AnyHelpers;
use Illuminate\Support\Facades\Crypt;

class Login extends Component
{
    public $ip, $username, $port, $password;
    public $scanlists = [];

    protected $listener = [
        'Scan',
        'save',
        'putIP',
        'next',
        'cek_koneksi'
    ];

    protected $rules = [
        // 'ip' => 'required|min:6',
        'username' => 'required',
        'password' => 'required',
        'port' => 'required',
    ];

    public function mount()
    {
        $this->port = 8728;
    }

    public function render()
    {
        $this->cekSesi();
        return view('livewire.auth.login')->layout('layouts.app-login');
    }

    public function cekSesi()
    {
        if (!empty(session()->get('login'))) {
            return redirect()->route('dashboard');
        }
    }

    public function Scan()
    {
        $this->dispatchBrowserEvent('scanKlik');

        // ini_set('max_execution_time', 0);
        // ini_set('memory_limit', -1);
        $ipadd = $_SERVER['REMOTE_ADDR'];
        $ip = explode('.', $ipadd);
        $network_id = $ip[0] . '.' . $ip[1] . '.' . $ip[2] . '.';
        $iplist = [];

        for ($i = 1; $i < 255; $i++) {
            $connection = @fsockopen($network_id . $i, 8728, $errno, $errstr, 0.05);

            if (is_resource($connection)) {
                $iplist[] = $network_id . $i;
                $this->ip = $network_id . $i;
                fclose($connection);
            }
        }
        $this->scanlists = $iplist;
    }

    public function save()
    {
        try {
            $resp = AnyHelpers::loginRouter($this->ip, $this->username, Crypt::encryptString($this->password), $this->port);
            if ($resp["code"] == 200) {
                $data = array(
                    'identity' => $resp["data"],
                    'ip' => $this->ip,
                    'username' => $this->username,
                    'password' => Crypt::encryptString($this->password),
                    'port' => $this->port,
                    'last_active' => null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => null
                );
            } else {
                $data = array(
                    'identity' => null,
                    'ip' => $this->ip,
                    'username' => $this->username,
                    'password' => Crypt::encryptString($this->password),
                    'port' => $this->port,
                    'last_active' => null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => null
                );
            }
            //cek data
            $cek = AnyHelpers::getJson('router_list', ["key" => "ip", "value" => $this->ip]);
            if (count($cek) > 0) {
                AnyHelpers::updateJson('router_list', $data, ["key" => "ip", "value" => $this->ip]);
            } else {
                AnyHelpers::insertJson('router_list', $data);
            }
            
            // AnyHelpers::saveLog('Menambah data Router', 'info');

            $this->dispatchBrowserEvent('swal:toast', [
                'type' => 'success',
                'title' => 'Berhasil',
                'text' => '',
            ]);
        } catch (Exception $e) {
            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'error',
                'title' => 'Kesalahan',
                'text' => $e,
            ]);
        } catch (\Throwable $e) {
            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'error',
                'title' => 'Kesalahan',
                'text' => $e->getMessage(),
            ]);
        }
    }

    public function next()
    {
        $this->dispatchBrowserEvent('blockUI', ['msg' => 'Loading . . . ']);
        if ($this->pingIP($this->ip) == 0) {
            $this->dispatchBrowserEvent('doNext', ['ip' => $this->ip]);
            $this->dispatchBrowserEvent('unBlockUI');
        } else {
            $this->dispatchBrowserEvent('unBlockUI');
            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'error',
                'title' => 'Tidak Dapat Terhubung',
                'text' => 'Pastikan Router Terhubung dengan Jaringan yang sama dengan Server',
            ]);
        }
    }

    public function pingIP($ip)
    {
        exec("ping -n 2 $ip", $output, $status);
        return $status;
    }

    public function submit()
    {
        try {
            $resp = AnyHelpers::loginRouter($this->ip, $this->username, Crypt::encryptString($this->password), $this->port);
            if ($resp["code"] == 200) {
                $data = array(
                    'identity' => $resp["data"],
                    'ip' => $this->ip,
                    'username' => $this->username,
                    'password' => Crypt::encryptString($this->password),
                    'port' => $this->port,
                    'last_active' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => null
                );

                //cek data
                $cek = AnyHelpers::getJson('router_list', ["key" => "ip", "value" => $this->ip]);
                if (count($cek) > 0) {
                    AnyHelpers::updateJson('router_list', $data, ["key" => "ip", "value" => $this->ip]);
                } else {
                    AnyHelpers::insertJson('router_list', $data);
                }
                
                session()->put('login', $data);

                AnyHelpers::saveLog('Berhasil Login', 'info');

                $this->dispatchBrowserEvent('swal:toast', [
                    'type' => 'success',
                    'title' => 'Berhasil Login',
                    'text' => '',
                ]);
                redirect()->route('dashboard');

            } else {
                $this->dispatchBrowserEvent('swal:modal', [
                    'type' => 'error',
                    'title' => 'Kesalahan',
                    'text' => $resp["msg"],
                ]);
            }
        } catch (Exception $e) {
            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'error',
                'title' => 'Kesalahan',
                'text' => $e,
            ]);
        } catch (\Throwable $e) {
            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'error',
                'title' => 'Kesalahan',
                'text' => $e->getMessage(),
            ]);
        }
    }

    function logout()
    {
        AnyHelpers::saveLog('Logout', 'info');
        session()->forget('login');
        return redirect()->route('dashboard');
    }
}
