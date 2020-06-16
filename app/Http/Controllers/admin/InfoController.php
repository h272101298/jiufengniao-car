<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class InfoController extends Controller
{


   public function index(Request $request){
       $page=$request->get('page');
       $limit=$request->get('limit');
       $i=1;
       $data=DB::table('info')
           ->limit($limit)
           ->offset(($page - 1) * $limit)
           ->select('id','nickname','pic','openid','add_time')
           ->get();
       foreach($data as $value){
           $value->add_time=date('Y-m-d H:i:s',$value->add_time);
           $value->main_id=$i+($page-1)*$limit;
           $i++;
       }
       return response()->json([
          'data'=>$data
       ]);
   }

    public function test(Request $request){
        $arr=$request->get('arr');
        $res=$this->quick_soft($arr);
        return response()->json([
           'data'=>$res
        ]);

    }

    public function quick_soft($arr){
        if(!is_array($arr)) return false;
        $length=count($arr);
        if($length<=1) return $arr;
        $left=$right=array();
        for($i=1;$i<$length;$i++)
        {
            if($arr[$i]<$arr[0]){
                $left[]=$arr[$i];
            }else{
                $right[]=$arr[$i];
            }
        }
        $left=$this->quick_soft($left);
        dump($left);
        $right=$this->quick_soft($right);
        return array_merge($left,array($arr[0]),$right);
    }
}
