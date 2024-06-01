<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \RouterOS\Query;
use App\Helpers\AnyHelpers;
use Illuminate\Support\Facades\Crypt;

class LainnyaController extends Controller
{
    function getIdentity() {
        $data = array(
            'code' => 500,
            'status' => 'error',
            'data' => ''
        );
        try {
            //query
            $query = new Query('/system/identity/print');
            $response = AnyHelpers::loginSesi()->query($query)->read();
            if (isset($response["after"]["message"])) {
                $data['msg'] = $response["after"]["message"];
            } else {
                $data = array(
                    'code' => 200,
                    'status' => 'success',
                    'msg' => 'Data Berhasil Diambil',
                    'data' => $response[0]['name']
                );
            }
        } catch (\Throwable $th) {
            $data['msg'] = $th->getMessage();
        }
        return response()->json($data);
    }
    
    function setIdentity(Request $request) {
        $data = array(
            'code' => 500,
            'status' => 'error',
            'data' => ''
        );
        try {
            //query
            $query = new Query('/system/identity/set');
            $query->equal('name', $request->identity);
            $response = AnyHelpers::loginSesi()->query($query)->read();
            if (isset($response["after"]["message"])) {
                $data['msg'] = $response["after"]["message"];
            } else {
                $data = array(
                    'code' => 200,
                    'status' => 'success',
                    'msg' => 'Data Berhasil Diambil',
                    'data' => ''
                );
            }
        } catch (\Throwable $th) {
            $data['msg'] = $th->getMessage();
        }
        return response()->json($data);
    }

    function getClock()
    {
        $data = array(
            'code' => 500,
            'status' => 'error',
            'data' => ''
        );
        try {
            //query
            $query = new Query('/system/clock/print');
            $response = AnyHelpers::loginSesi()->query($query)->read();
            if (isset($response["after"]["message"])) {
                $data['msg'] = $response["after"]["message"];
            } else {
                $response[0]['time_zone_name'] = $response[0]['time-zone-name'];
                $data = array(
                    'code' => 200,
                    'status' => 'success',
                    'msg' => 'Data Berhasil Diambil',
                    'data' => $response
                );
            }
        } catch (\Throwable $th) {
            $data['msg'] = $th->getMessage();
        }
        return response()->json($data);
    }

    function setClock(Request $request)
    {
        $data = array(
            'code' => 500,
            'status' => 'error',
            'data' => ''
        );

        try {
            //get data negara
            $negara = AnyHelpers::getJson('time_zone', ["key" => "negara", "value" => $request->negara]);

            //query ntp
            $query = new Query('/system/ntp/client/set');
            $query->equal('enabled', 'yes');
            // $query->equal('mode', 'unicast');
            $query->equal('primary-ntp', gethostbyname($negara[0]['domain_negara']));
            $query->equal('secondary-ntp', gethostbyname($negara[0]['domain_benua']));
            $response = AnyHelpers::loginSesi()->query($query)->read();
            if (isset($response["after"]["message"])) {
                $data['msg'] = $response["after"]["message"];
                return response()->json($data);
            } else {
                $data = array(
                    'code' => 200,
                    'status' => 'success',
                    'msg' => 'Data Berhasil Dirubah',
                    'data' => ''
                );
            }

            //query clock
            $query2 = new Query('/system/clock/set');
            $query2->equal('time-zone-name', $negara[0]['time_zone']);
            $query2->equal('time-zone-autodetect', 'no');
            $response = AnyHelpers::loginSesi()->query($query2)->read();

            if (isset($response["after"]["message"])) {
                $data['msg'] = $response["after"]["message"];
            } else {
                $data = array(
                    'code' => 200,
                    'status' => 'success',
                    'msg' => 'Data Berhasil Dirubah',
                    'data' => ''
                );
            }
        } catch (\Throwable $th) {
            $data['msg'] = $th->getMessage();
        }
        return response()->json($data);
    }
    
    function backup(Request $request)
    {
        try {
            //get identity
            $query = new Query('/system/identity/print');
            $identity = AnyHelpers::loginSesi()->query($query)->read();

            $filename = date('d-m-Y') . "_" . $identity[0]['name'] . ".backup";
            $folder = public_path('/storage/backup/');

            //query backup
            $query = new Query('/system/backup/save');
            $query->equal('name', $filename);

            if ($request->auth == 0 || $request->pass == null) { //no encrypt
                $query->equal('dont-encrypt', 'yes');
            } else { // encrypt
                $query->equal('encryption', 'aes-sha256');
                $query->equal('password', $request->pass);
            }

            $backup = AnyHelpers::loginSesi()->query($query)->read();

            //query get ftp info
            $query = new Query('/ip/service/print');
            $query->where('name', 'ftp');
            $ftp = AnyHelpers::loginSesi()->query($query)->read();

            if ($ftp[0]["disabled"] == "false") {
                if ($backup == null) {
                    //FTP
                    $conn_id = ftp_connect(session()->get('login')['ip'], (int)$ftp[0]["port"]);
                    // login with username and password
                    $login_result = ftp_login($conn_id, session()->get('login')['username'], Crypt::decryptString(session()->get('login')['password']));

                    // try to download $server_file and save to $local_file
                    if (ftp_get($conn_id, $folder . $filename, $filename, FTP_BINARY)) {
                        //get info file
                        $query = new Query('/file/print');
                        $query->where('name', $filename);
                        $resultfile = AnyHelpers::loginSesi()->query($query)->read();

                        if (count($resultfile) > 0) {
                            // hapus backup di mikrotik
                            $query = new Query('/file/remove');
                            $query->equal('.id', $resultfile[0][".id"]);
                            $delete = AnyHelpers::loginSesi()->query($query)->read();
                        }

                        // download file
                        return response()->download($folder . $filename);
                    } else {
                        echo "Error";
                    }
                    ftp_close($conn_id);
                } else {
                    echo $backup["after"]["message"];
                }
            } else {
                echo "<script>alert('Service FTP Disable !!, agar dapat melakukan backup silakan aktifkan service FTP pada mikrotik');</script>";
            }
        } catch (\Throwable $th) {
            $data['msg'] = $th->getMessage();
        }
        return response()->json($data);
    }

    function restore(Request $request)
    {
        $data = array(
            'code' => 500,
            'status' => 'error',
            'data' => ''
        );

        try {
            //get identity
            $query = new Query('/system/identity/print');
            $identity = AnyHelpers::loginSesi()->query($query)->read();

            $filename = $identity[0]['name'] . ".backup";

            // store to server
            $request->file->move(public_path('/storage/restore/'), $filename);

            //get file
            $file = public_path('/storage/restore/' . $filename);

            //query get ftp info
            $query = new Query('/ip/service/print');
            $query->where('name', 'ftp');
            $ftp = AnyHelpers::loginSesi()->query($query)->read();

            if ($ftp[0]["disabled"] == "false") {
                //FTP
                $conn_id = ftp_connect(session()->get('login')['ip'], (int)$ftp[0]["port"]);
                // login with username and password
                $login_result = ftp_login($conn_id, session()->get('login')['username'], Crypt::decryptString(session()->get('login')['password']));

                // try to upload $local_file and save to $server_file
                if (ftp_put($conn_id, $filename, $file, FTP_BINARY)) {

                    //query restore
                    $query = new Query('/system/backup/load');
                    $query->equal('name', $filename);
                    $query->equal('password', $request->pass);
                    $restore = AnyHelpers::loginSesi()->query($query)->read();

                    ftp_close($conn_id);
                    if (isset($response["after"]["message"])) {
                        $data['msg'] = $response["after"]["message"];
                    } else {
                        $data = array(
                            'code' => 200,
                            'status' => 'success',
                            'msg' => 'Router akan Booting... Silakan Login kembali',
                            'data' => ''
                        );
                    }
                } else {
                    $data['msg'] = 'Gagal Upload File';
                }
            } else {
                $data['msg'] = 'Service FTP Disable !!, agar dapat melakukan restore silakan aktifkan service FTP pada mikrotik';
            }
            
        } catch (\Throwable $th) {
            $data['msg'] = $th->getMessage();
        }
        return response()->json($data);
    }
}