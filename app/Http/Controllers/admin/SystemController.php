<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use OSS\OssClient;
use OSS\Core\OssException;
use PHPUnit\Runner\Exception;

class SystemController extends Controller
{
    private $radio=50;
    private $AppId='wx5c8fd8d831e5b814';
    private $AppSecret='9489708d0bb563f67a3a92829b11db7e';

    private $accessKeyId='LTAI367tqCL6Dh89';
    private $accessKeySecret='hO9BewodDawazftIhL4ONa45KCA0gH';
    private $endpoint='http://oss-cn-beijing.aliyuncs.com';
    private $bucket='libida';

    public function upload(Request $post){
        if (!$post->hasFile('pic')){
            return response()->json([
                'msg'=>'空文件'
            ]);
        }
        $pic=$post->file('pic');

        if($pic->isValid()){
            $name=uniqid();
            $ext=$pic->getClientOriginalExtension();
            $allow =  [
                'jpg',
                'png',
                'jpeg',
            ];
            if (!in_array(strtolower($ext),$allow)){
                return response()->json([
                    'msg'=>'不支持的文件格式'
                ]);
            }
            $ext=strtolower($ext);
            $name=$name.'.'.$ext;
            try {
                $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
                $ossClient->uploadFile($this->bucket,$name,$pic->path());
            }catch(OssException $e){
                return response()->json([
                    'msg'=>'fail',
                    'error'=>$e->getMessage()
                ]);
            }
            $path='https://libida.oss-cn-beijing.aliyuncs.com/'.$name;
            return response()->json([
                'msg'=>'success',
                'path'=>$path
            ]);
        }else{
            return response()->json([
                'msg'=>'上传失败！'
            ]);
        }
    }

    public function company(){
        $data=DB::table('system')->get();
        $data=$data[0];
        return response()->json([
            'data'=>$data
        ]);
    }

    public function edit_company(Request $request){
        $data=$request->all();
        $result=DB::table('system')->update($data);
        if($result){
            return response()->json([
                'msg'=>'success'
            ]);
        }else{
            return response()->json([
                'msg'=>'fail'
            ]);
        }
    }

    public function wx(){
        $data=DB::table('wx')->get();
        return response()->json([
            'data'=>$data
        ]);
    }

    public function edit_wx(Request $request){
        $data=$request->all();
        $result=DB::table('wx')->update($data);
        if($result){
            return response()->json([
                'msg'=>'success'
            ]);
        }else{
            return response()->json([
                'msg'=>'fail'
            ]);
        }
    }

    public function formId_Save(Request $post){
        $formId=$post->formId;
        $token=$post->token;
        $openid=DB::table('mid')->where('token',$token)->value('openid');
        if($formId==false||$openid==false){
            return response()->json([
                'msg'=>'1'
            ]);
        }
        $now=time();
        $res = DB::table('form')->insert([
            'formId' => $formId,
            'openid' => $openid,
            'date' => $now
        ]);
        if($res){
            return response()->json([
                'msg'=>'success'
            ]);
        }else{
            return response()->json([
                'msg'=>'fail'
            ]);
        }
    }

    //消息
    public  function sendmes(Request $post)
    {
        $id=array();
        $today=time();
        $last=$today-604800;
        DB::table('form')->where('date','<',$last)->delete();
        $mes = DB::table('form')->groupby('openid')->get();
        $len=sizeof($mes);

        $url=$post->url;
        $name = $post->name;
        $price = $post->price;
        $article = $post->article;
        // $openid=DB::table('info')->pluck('openid');
        $now = date('Y年m月d日',$today);

        $accessToken = $this->getToken();

//        dump($accessToken);
        for($i=0;$i<$len;$i++) {
            $id[$i]=$mes[$i]->id;
            $data = [
                'touser' => $mes[$i]->openid,
                'template_id' => 'mpShfnDIpGyfzLyKRw2EpAqtVHFOA7PCP1UiBEmHPR4',
                'page' => $url,
                'form_id' => $mes[$i]->formId,
                'data' =>
                    [
                        'keyword1' =>
                            [
                                'value' => $name
                            ],
                        'keyword2' =>
                            [
                                'value' => $now

                            ],

                        'keyword3' =>
                            [
                                'value' => $price,'万元'

                            ],
                        'keyword4' =>
                            [
                                'value' => $article

                            ]
                    ],
                'emphasis_keyword' => ''
            ];
            $json = json_encode($data, JSON_UNESCAPED_UNICODE);
            $sendUrl = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=' . $accessToken['access_token'];
            $surl = $sendUrl;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $surl);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
            if (!empty($json)) {
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
            }
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($curl);
//            if ($output === FALSE) {
//                return false;
//            }
            curl_close($curl);
//            $message=json_decode($output, JSON_UNESCAPED_UNICODE);
//            dump($message);
            //  }
        }
        $idLen=sizeof($id);
        if($idLen==1){
            DB::table('form')->where('id',$id[0])->delete();
        }
        DB::table('form')->whereIn('id',$id)->delete();
        return 1;
    }

    private function getToken(){
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
        $appId = $this->AppId;
        $appSecret = $this->AppSecret;
        $url = sprintf($url, $appId, $appSecret);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        if ($output === FALSE) {
            return false;
        }
        curl_close($curl);
        $accessToken = json_decode($output, JSON_UNESCAPED_UNICODE);
        if(isset($accessToken['errcode'])){
            if($accessToken['errcode'] == -1){
                $this->getToken();
            }
        }
        return $accessToken;
    }
}
