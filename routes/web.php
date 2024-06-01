<?php

use App\Helpers\AnyHelpers;
use App\Http\Controllers\LainnyaController;
use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Auth\Login;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\KonfigurasiDasar\DHCPServer;
use App\Http\Livewire\KonfigurasiDasar\InternetGateway;
use App\Http\Livewire\KonfigurasiDasar\IpAddress;
use App\Http\Livewire\Lainnya\Service;
use App\Http\Livewire\Lainnya\DaftarRouter;
use App\Http\Livewire\Lainnya\TimeZone;
use App\Http\Livewire\Lainnya\Akun;
use App\Http\Livewire\ManagementBandwidth\PCQ;
use App\Http\Livewire\ManagementBandwidth\QueueTree;
use App\Http\Livewire\ManagementBandwidth\SimpleQueue;
use App\Http\Livewire\MonitoringTraffic\DropTraffic;
use App\Http\Livewire\MonitoringTraffic\Monitoring;
use App\Http\Livewire\MonitoringTraffic\Notifikasi;
use App\Http\Livewire\MonitoringTraffic\PrioritasTraffic;
use \RouterOS\Query;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/login', Login::class)->name('login');
Route::get('/logout', [Login::class, 'logout'])->name('logout');

Route::middleware('login')->group(function () {
    // Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/', function(){
        session()->put('menu_active', 'dashboard');
        session()->put('sub_menu_active', '');
        return view('livewire.dashboard-base', ['slot' => '']);
    })->name('dashboard');
    Route::post('/get-traffic-eth1', function(){
        $response2 = AnyHelpers::loginSesi()->query('/system/clock/print')->read();
        $q = new Query('/interface/print');
        $q->where('default-name', 'ether1');
        $response4 = AnyHelpers::loginSesi()->query($q)->read();

        $chart = array(
            'time' => $response2[0]["time"],
            'rx_byte' => number_format($response4[0]["rx-byte"] / 1000000, 2),
            'tx_byte' => number_format($response4[0]["tx-byte"] / 1000000, 2)
        );
        return response()->json($chart);
    })->name('get-trafic-eth1');

    //konfigurasi dasar
    Route::get('/konfigurasi-dasar/ip-address', IpAddress::class)->name('ip-address');
    Route::get('/konfigurasi-dasar/internet-gateway', InternetGateway::class)->name('internet-gateway');
    Route::get('/konfigurasi-dasar/dhcp-server', DHCPServer::class)->name('dhcp-server');
    
    //management bandwidth
    Route::get('/management-bandwidth/simple-queue', SimpleQueue::class)->name('simple-queue');
    Route::get('/management-bandwidth/queue-tree', QueueTree::class)->name('queue-tree');
    Route::get('/management-bandwidth/pcq', PCQ::class)->name('pcq');

    //monitoring traffic
    Route::get('/monitoring-traffic/drop-traffic', DropTraffic::class)->name('drop-traffic');
    // Route::get('/monitoring-traffic/monitoring', Monitoring::class)->name('monitoring');
    Route::get('/monitoring-traffic/monitoring', function(){
        session()->put('menu_active', 'monitoring-traffic');
        session()->put('sub_menu_active', 'monitoring-traffic');
        return view('livewire.monitoring-traffic.monitoring-base', ['slot' => '']);
    })->name('monitoring');
    Route::post('/monitoring-traffic/get-traffic-queue', [Monitoring::class, 'getChart'])->name('get-traffic-queue');
    Route::get('/monitoring-traffic/notifikasi', Notifikasi::class)->name('notifikasi');
    Route::get('/monitoring-traffic/prioritas-traffic', PrioritasTraffic::class)->name('prioritas-traffic');

    //lainnya
    Route::get('/lainnya/service', Service::class)->name('service');
    Route::get('/lainnya/daftar-router', DaftarRouter::class)->name('daftar-router');
    Route::get('/lainnya/time-zone', TimeZone::class)->name('time-zone');
    Route::get('/lainnya/akun', Akun::class)->name('akun');
    Route::post('/lainnya/get-identity', [LainnyaController::class, 'getIdentity'])->name('get-identity');
    Route::post('/lainnya/set-identity', [LainnyaController::class, 'setIdentity'])->name('set-identity');
    Route::post('/lainnya/get-clock', [LainnyaController::class, 'getClock'])->name('get-clock');
    Route::post('/lainnya/set-clock', [LainnyaController::class, 'setClock'])->name('set-clock');
    Route::get('/lainnya/backup', [LainnyaController::class, 'backup'])->name('backup');
    Route::post('/lainnya/restore', [LainnyaController::class, 'restore'])->name('restore');


});

Route::get('/tes', function(){
    // $tb_service = AnyHelpers::getJson('service_list', ['key' => 'type', 'value' => 'layer7']);
    // return response()->json($tb_service);

    $a = 'os "ss"';
    echo str_replace('"', "'", $a);
});