<?php

namespace App\Http\Controllers;

use App\Models\EmpDevice;
use App\Models\FtmAttLog;
use App\Models\FtmEmp;
use App\Models\FtmHoliday;
use App\Models\PackageException;
use App\Models\Token;
use Illuminate\Http\Request;

class AbonController extends Controller
{
    public function version_name()
    {
        $package = PackageException::query()->get();
        $data = [
            'version_name' => '1.4',
            'package_exception' => $package->toArray(),
            'version_code' => '4',
        ];
        echo json_encode($data);
    }

    public function isUseDeviceId($deviceid)
    {
        $device = EmpDevice::select(['device_id','nik'])->where([['device_id', $deviceid], ['end', null]])->get();
        if($device->count() > 0){
            echo json_encode($device);
        }else{
            echo 'false';
        }
    }

    public function getPinAndEmpIdPegawai($nik)
    {
        $query = EmpDevice::select(['device_id'])->where([['nik', $nik], ['end', null]])->get();
        if ($query->count() == 1) {
            $arrayQuery = $query->toArray();
            $array[0]['device_id'] = $arrayQuery[0]['device_id'];
            return $array;
        } else {
            $array[0]['device_id'] = 'null';
            return $array;
        }
    }

    public function getDetailPegawaiByEmpId($nik)
    {
        $query = FtmEmp::select(['emp_id_auto', 'pin'])->where('nik', $nik)->get();
        if ($query->count() == 1) {
            $arrayQuery = $query->toArray();
            $array = $this->getPinAndEmpIdPegawai($nik);
            $array[0]['pin'] = $arrayQuery[0]['pin'];
            $array[0]['emp_id_auto'] = $arrayQuery[0]['emp_id_auto'];

            return $array;
        } else {
            return false;
        }
    }

    public function Token($nik, $deviceid, $date)
    {
        $token = md5($nik . $deviceid . $date);
        $data = [
            'id_token' => 0,
            'nik' => $nik,
            'token' => $token,
            'date' => $date,
        ];
        $result = Token::create($data);
        if ($result) {
            return $token;
        } else {
            return 'false';
        }
    }

    public function login(Request $request)
    {
        $nik = $request->nik;
        $deviceid = $request->deviceid;
        $login = json_encode($this->getDetailPegawaiByEmpId($nik));
        if ($login != 'false') {
            $token = $this->Token($nik,$deviceid,date('Y-m-d H:i:s'));
            $login .='##'.$token;
        }
        echo $login;
    }

    function DeviceID($nip, $deviceid)
    {
        $data = [
            'id' => 0,
            'nik' => $nip,
            'device_id' => $deviceid,
            'start' => date('Y-m-d'),
        ];
        return EmpDevice::create($data);
    }

    public function insertDeviceId(Request $request){
        $deviceid = $request->device_id;
        $nik = $request->nik;
        $hasil = $this->DeviceID($nik,$deviceid);
        echo $hasil;
    }

    public function checkConnection(){
		echo "terkoneksi";
	}

    public function cekToken($nik, $token)
    {
        $query = Token::select('token')->where('nik', $nik)->orderBy('date', 'desc')->limit(1, 0)->first();
        if ($query->count() == 1) {
            return $query->token;
        } else {
            return false;
        }
    }

    public function checkHoliday($date){
        $query = FtmHoliday::where('holiday_date', $date)->get();
        if ($query->count() > 0) {
            $result_array = $query->toArray();
            return [
                'status' => 'false',
                'sebab' => 'Hari ini adalah :'.$result_array[0]['holiday_note'],
            ];
        }
        else{
            return ['status' => 'true'];
        }	
    }

    public function checkAbsenExist($pin,$io_mode,$date)
	    {	
	    	if (date('w') == '0' || date('w') == '6') {
	    		return [
                    'status' => 'false',
                    'sebab' => 'Hari ini hari libur.'
                ];
	    	}else{
	    		$arrayLibur = $this->checkHoliday($date);
	    		if ($arrayLibur['status'] == 'true') {
                    $query = FtmAttLog::where([['pin', $pin], ['io_mode', $io_mode]])->whereDate('scan_date', $date)->get();
			    	if ($query->count() > 0) {
			    		$array = $query->toArray();
			    		return [
                            'status' => 'false',
                            'sebab' => 'Anda telah Absen pada '.$array[0]['scan_date'].'.'
                        ];
			    	}else{
			    		return ['status' => 'true'];
			    	}	
		    	}else{
		    		return $arrayLibur;
		    	}
	    	}
	    }

    private function isEmpty($value1){
	    if (is_null($value1)||$value1==null||$value1=="")
			return true;
    	else	    		
            return false;
	}

    public function insertAbsenNew(Request $request){
        $nik = $request->nik;
        $deviceid = $request->device_id;
        $pin = $request->pin;
        $verify_mode = $request->verify_mode;
        $io_mode = $request->io;
        $datetime = date('Y-m-d H:i:s');
        $work_code = $request->work_code;
        $ex_id = $request->ex;
        $flag = $request->flag;
        $rowguid = $request->rowguid;
        $io_update = $request->io_update;
        $token = $request->token;
        $hasil = $this->cekToken($nik,$token);
        if ($this->isEmpty($token) || $this->isEmpty($nik)) {
            echo "invalid";
        }else{
            $arrayHasil = $this->checkAbsenExist($pin,$io_mode,date('Y-m-d'));
            if ($arrayHasil['status'] != 'false') {
                if ($token == $hasil) {
                    $data = [
                        'sn' => 'AND'.$deviceid,
                        'scan_date' => $datetime,
                        'pin' => $pin,
                        'verify_mode' => $verify_mode,
                        'io_mode' => $io_mode,
                        'work_code' => $work_code,
                        'ex_id' => $ex_id,
                        'flag' => $flag,
                        'rowguid' => $rowguid,
                        'io_mode_update' => $io_update
                    ];
                    $result = FtmAttLog::create($data);
                    if ($result > 0) {
                        echo "[".json_encode(['status' => 'true'])."]";
                    }else{
                        echo "[".json_encode(['status' => 'false','sebab' => 'gagal melakukan absen.'])."]";
                    }
                }else{
                    echo "[".json_encode(['status' => 'false','sebab' => 'Invalid Token.'])."]";
                }
            }else{
                echo "[".json_encode($arrayHasil)."]";
            }
        }
    }

}
