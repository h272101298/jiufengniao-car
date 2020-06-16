<?php

namespace App\Http\Controllers\home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PHPUnit\Runner\Exception;

class WxController extends Controller
{

    private $appId='wx5c8fd8d831e5b814';
    private $appSecret='9489708d0bb563f67a3a92829b11db7e';

    /**
     * 微信登录
     * @param Request $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function wxlogin(Request $post){
        $code=$post->code;
        $encryptedData=$post->encryptedData;
        $iv=$post->iv;
        $appId=$this->appId;
        $appSecret=$this->appSecret;
        $Getsessionkey='https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code';
        $url = sprintf($Getsessionkey, $appId, $appSecret, $code);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($output, JSON_UNESCAPED_UNICODE);
        if(isset($data->errcode)){
            return response()->json([
                'msg'=>'登录失败！'
            ]);
        }
        $openid=$data['openid'];
        $session_key=$data['session_key'];
        $secret_data=$this->decryptData($encryptedData,$iv,$appId,$session_key);
        if($secret_data==false){
            return response()->json([
                '解密失败！'
            ]);
        }

        $now=time();
        $secret_data=json_decode($secret_data);
        $nickname=$secret_data->nickName;
        $sex=$secret_data->gender;
        $province=$secret_data->province;
        $city=$secret_data->city;
        $pic=$secret_data->avatarUrl;
        $check=DB::table('mid')->where('openid',$openid)->value('id');
        if(!$check) {
            $token = sha1($secret_data->watermark->timestamp . $appId);
            try {
                DB::table('info')->insert([
                    'openid' => $openid, 'province' => $province, 'city' => $city,
                    'add_time' => $now, 'pic' => $pic,'nickname'=>$nickname,'sex'=>$sex
                ]);
                DB::table('mid')->insert([
                    'openid' => $openid,
                    'token' => $token
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'msg' => 'fail',
                    'Exception' => $e
                ]);
            }
        }else{
            $token=DB::table('mid')->where('openid',$openid)->value('token');
        }
        return response()->json([
            'token'=>$token,
            'nickname'=>$nickname,
            'sex'=>$sex,
            'province'=>$province,
            'city'=>$city,
            'pic'=>$pic
        ]);
    }


    /**
     * 数据解密
     * @param $encryptedData
     * @param $iv
     * @param $appid
     * @param $sessionKey
     * @return bool|string
     */
    public function decryptData($encryptedData, $iv,$appid, $sessionKey){
        if (strlen($sessionKey) != 24) {
            return false;
        }
        $aesKey=base64_decode($sessionKey);


        if (strlen($iv) != 24) {
            return false;
        }
        $aesIV=base64_decode($iv);

        $aesCipher=base64_decode($encryptedData);

        $result=openssl_decrypt($aesCipher,"AES-128-CBC",$aesKey,1,$aesIV);

        $dataObj=json_decode($result);
        if( $dataObj  == NULL )
        {
            return false;
        }
        if( $dataObj->watermark->appid != $appid )
        {
            return false;
        }
        $data = $result;
        return $data;
    }

}
