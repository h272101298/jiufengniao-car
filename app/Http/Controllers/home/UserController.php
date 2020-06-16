<?php

namespace App\Http\Controllers\home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    /**
     * 预约
     * @param Request $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function appoint(Request $post){
        $token=$post->token;
        $openid=DB::table('mid')->where('token',$token)->value('openid');
        if(!$openid){
            return response()->json([
                'msg'=>'token验证错误'
            ]);
        }
        $uid=DB::table('info')->where('openid',$openid)->value('id');
        $cid=$post->cid;
        $name=$post->name;
        $phone=$post->phone;
        $usertype=$post->usertype;
        $car_type=$post->car_type;
        $reg=$post->reg;
        $now=time();
        if($uid && $cid){
            $check=DB::table('appoint')->where('uid',$uid)->where('cid',$cid)->value('id');
            if($check){
                return response()->json([
                   'msg'=>'已预约'
                ]);
            }
        }
        if($usertype==1) {
            $res = DB::table('appoint')->insert([
                'uid' => $uid, 'usertype' => $usertype, 'cid' => $cid, 'type' => 2, 'date' => $now,
                'name' => $name, 'phone' => $phone
            ]);
        }else{
            $res=DB::table('appoint')->insert([
                'uid'=> $uid,'usertype'=>$usertype,'type'=>2,'date'=>$now,
                'name'=>$name,'phone'=>$phone,'reg'=>$reg,'car_type'=>$car_type
            ]);
        }
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

    public function custom(Request $post){
        $data=$post->all();
        $time=time();
        $res=DB::table('custom')->insert([
           'brand'=>$data['brand'],'type'=>$data['type'],'style'=>$data['style'],
            'speed'=>$data['speed'],'color'=>$data['color'],'name'=>$data['name'],
            'phone'=>$data['phone'],'date'=>$time
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

    /**
     * 收藏
     * @param Request $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function collection(Request $post){
        $token=$post->token;
        $cid=$post->cid;
        $openid=DB::table('mid')->where('token',$token)->value('openid');
        if(!$openid){
            return response()->json([
                'msg'=>'token验证错误'
            ]);
        }
        $uid=DB::table('info')->where('openid',$openid)->value('id');
        $check=DB::table('collection')->where('uid',$uid)->where('cid',$cid)->value('id');
        if($check){
            return response()->json([
               'msg'=>'已收藏！'
            ]);
        }
        $res=DB::table('collection')->insert(['uid'=>$uid,'cid'=>$cid]);
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

    /**
     * 收藏取消
     * @param Request $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function collection_cancel(Request $post){
        $token=$post->token;
        $cid=$post->cid;
        $openid=DB::table('mid')->where('token',$token)->value('openid');
        if(!$openid){
            return response()->json([
                'msg'=>'token验证错误'
            ]);
        }
        $uid=DB::table('info')->where('openid',$openid)->value('id');
        $res=DB::table('collection')->where('cid',$cid)->where('uid',$uid)->delete();
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

    /**
     * 预约列表
     * @param Request $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function AppointList(Request $post){
        $token=$post->token;
        $limit=$post->limit;
        $page=$post->post;
        $openid=DB::table('mid')->where('token',$token)->value('openid');
        if(!$openid){
            return response()->json([
                'msg'=>'token验证错误'
            ]);
        }
        $uid=DB::table('info')->where('openid',$openid)->value('id');
        $cid=DB::table('appoint')->where('uid',$uid)->pluck('cid');
        if($cid) {
            $data = DB::table('car')
                ->leftJoin('brand', 'brand.id', '=', 'car.bid')
                ->leftJoin('type', 'type.id', '=', 'car.tid')
                ->leftJoin('pay_to_car', 'pay_to_car.cid', '=', 'car.id')
                ->where('shelves', '=', 1)
                ->whereIn('car.id',$cid)
                ->limit($limit)
                ->offset(($page - 1) * $limit)
                ->select('car.pic', 'brand.brand', 'type.type', 'car.style', 'car.vol', 'car.model',
                    'car.new', 'pay_to_car.payment', 'pay_to_car.price', 'pay_to_car.monthly_pay', 'car.year',
                    'car.mileage','car.speed','car.id')
                ->get();
        }else{
            $data='';
        }
       return response()->json([
          'data'=>$data
       ]);
    }

    /**
     * 收藏列表
     * @param Request $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function CollectionList(Request $post){
        $token=$post->token;
        $page=$post->page;
        $limit=$post->limit;
        $openid=DB::table('mid')->where('token',$token)->value('openid');
        if(!$openid){
            return response()->json([
                'msg'=>'token验证错误'
            ]);
        }
        $uid=DB::table('info')->where('openid',$openid)->value('id');
        $cid=DB::table('collection')->where('uid',$uid)->pluck('cid');
        if($cid) {
            $data = DB::table('car')
                ->leftJoin('brand', 'brand.id', '=', 'car.bid')
                ->leftJoin('type', 'type.id', '=', 'car.tid')
                ->leftJoin('pay_to_car', 'pay_to_car.cid', '=', 'car.id')
                ->whereIn('car.id', $cid)
                ->limit($limit)
                ->offset(($page - 1) * $limit)
                ->select('car.pic', 'brand.brand', 'type.type', 'car.style', 'car.vol', 'car.model',
                    'car.new', 'pay_to_car.payment', 'pay_to_car.price', 'pay_to_car.monthly_pay', 'car.year',
                    'car.mileage','car.speed','car.id')
                ->get();
        }else{
            $data='';
        }
        foreach($data as $value){
            if($value->new==1){
                $value->new='新车';
            }elseif($value->new==2){
                $value->new='二手车';
            }
        }
        return response()->json([
            'data'=>$data
        ]);
    }
}
