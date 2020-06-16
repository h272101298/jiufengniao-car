<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;

class AppointController extends Controller
{
    public function total(){
        $data=DB::table('appoint')->select('id')->count();
        $data1=DB::table('custom')->count();
        return response()->json([
            'data'=>$data+$data1
        ]);
    }

    public function info(Request $request){

        $page=$request->get('page');
        $limit=$request->get('limit');
        $i=1;
        $sid=$request->get('sid');
        if($sid){
            $sid=$request->get('sid');
            $total=DB::table('appoint')->whereIn('id',$sid)->select('id')->count();
            $data=DB::table('appoint')
                ->whereIn('id',$sid)
                ->limit($limit)
                ->offset(($page - 1) * $limit)
                ->orderby('date','DESC')
                ->get();
        }else{
            $total=DB::table('appoint')->select('id')->count();
            $data=DB::table('appoint')
                ->limit($limit)
                ->offset(($page - 1) * $limit)
                ->orderby('date','DESC')
                ->get();
        }
        foreach($data as $value){
            $value->main_id=$i+($page-1)*$limit;
            $i++;
            $value->date=date('Y-m-d H:i:s',$value->date);
            if($value->usertype==1){
                $value->usertype='买车';
            }else{
                $value->usertype='卖车';
            }
            if($value->type==1){
                $value->type='已处理';
            }else{
                $value->type='未处理';
            }
        }
        return response()->json([
            'data'=>$data,
            'total'=>$total
        ]);
    }

    public function detail(Request $request){
        $id=$request->get('aid');
        $type=DB::table('appoint')->where('id',$id)->value('usertype');
        if($type==2){
            $data=DB::table('appoint')
                ->where('id',$id)
                ->select('name','phone','date','usertype','reg','car_type')
                ->get();
            $data=$data[0];
        }else {
            $data = DB::table('appoint')
                ->leftJoin('car', 'car.id', '=', 'appoint.cid')
                ->leftJoin('pay_to_car', 'pay_to_car.cid', '=', 'appoint.cid')
                ->where('appoint.id', $id)
                ->select('appoint.name', 'appoint.phone', 'appoint.date', 'appoint.usertype',
                    'car.new', 'car.id', 'car.pic', 'car.style', 'pay_to_car.payment','pay_to_car.price',
                    'pay_to_car.monthly_pay','pay_to_car.time')
                ->get();
            $data = $data[0];
            if(isset($data->id)) {
                $check = DB::table('car')->where('id', $data->id)->select('bid', 'tid')->get();
                $brand = DB::table('brand')->where('id', $check[0]->bid)->value('brand');
                $type = DB::table('type')->where('id', $check[0]->tid)->value('type');
                $data->brand = $brand;
                $data->type = $type;
            }

        }
        $data->date = date('Y-m-d H:i:s', $data->date);
        if ($data->usertype == 1) {
            $data->usertype = '买车';
        } else {
            $data->usertype = '卖车';
        }
        return response()->json([
            'data'=>$data
        ]);
    }

    public function search(Request $request){
        $search=$request->get('search');
        $date_start=$request->get('start');
        $date_end=$request->get('end');
        $usertype=$request->get('usertype');
        $type=$request->get('type');
        $sid=DB::table('appoint')
            ->where(function($query) use($search){
                if(! empty($search)){
                    $query->where('name','like','%'.$search.'%')->orwhere('phone','like','%'.$search.'%');
                }
            })
            ->where(function($query) use ($date_start,$date_end){
                if(!empty($date_start) && !empty($date_end)){
                    $start = strtotime($date_start);
                    $end =  strtotime($date_end.'+ 1 days');
                    $query->where('date','>=',$start)->where('date','<',$end);
                }
            })
            ->where(function($query) use($usertype){
                if(! empty($usertype)){
                    $query->where('usertype','=',$usertype);
                }
            })
            ->where(function($query) use($type){
                if(! empty($type)){
                    $query->where('type','=',$type);
                }
            })
            ->pluck('id');;
        $page=$request->get('page');
        $limit=$request->get('limit');
        $i=1;
        $total=DB::table('appoint')->whereIn('id',$sid)->select('id')->count();

        $data=DB::table('appoint')
            ->whereIn('id',$sid)
            ->limit($limit)
            ->offset(($page - 1) * $limit)
            ->get();

        foreach($data as $value){
            $value->main_id=$i+($page-1)*$limit;
            $i++;
            $value->date=date('Y-m-d H:i:s',$value->date);
            if($value->usertype==1){
                $value->usertype='买车';
            }else{
                $value->usertype='卖车';
            }
            if($value->type==1){
                $value->type='已处理';
            }else{
                $value->type='未处理';
            }
        }
        return response()->json([
            'data'=>$data,
            'total'=>$total
        ]);

    }

    public function click(Request $request){
        $id=$request->get('aid');
        $result=DB::table('appoint')->where('id',$id)->update(['type'=>1]);
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

    public function del(Request $request){
        $id=$request->get('aid');
        $res=DB::table('appoint')->where('id',$id)->delete();
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

    public function custom(Request $request){
        $page=$request->get('page');
        $limit=$request->get('limit');
        $total=DB::table('custom')
            ->orderBy('date','DESC')
            ->count();
        $data=DB::table('custom')
            ->orderBy('date','DESC')
            ->limit($limit)
            ->offset(($page - 1) * $limit)
            ->get();
        foreach($data as $value){
            $value->deal=$value->deal==1?'已处理':'未处理';
            $value->date=date('Y-m-d H:i:s',$value->date);
        }
        return response()->json([
            'data'=>$data,
            'total'=>$total
        ]);
    }

    public function custom_click(Request $request){
        $id=$request->get('id');
        $deal=DB::table('custom')->where('id',$id)->value('deal');
        switch($deal){
            case 1:
                DB::table('custom')->where('id',$id)->update(['deal'=>0]);
                break;
            case 0:
                DB::table('custom')->where('id',$id)->update(['deal'=>1]);
                break;
        }
        return response()->json([
            'msg'=>'success'
        ]);
    }

    public function custom_delete(Request $request){
        $id=$request->get('id');
        $res=DB::table('custom')->where('id',$id)->delete();
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
}
