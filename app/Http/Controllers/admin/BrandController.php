<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{
    public function index(Request $request){
        $text=$request->get('text');
        $page=$request->get('page');
        $limit=$request->get('limit');
        if($text){
            $data=DB::table('brand')->where('brand','like','%'.$text.'%')->limit($limit)->offset(($page - 1) * $limit)->get();
            $total = DB::table('brand')->where('brand','like','%'.$text.'%')->select('id')->count();
        }else {
            $total = DB::table('brand')->select('id')->count();
            $data = DB::table('brand')->limit($limit)->offset(($page - 1) * $limit)->get();
        }
        $i = 1;
        foreach ($data as $value) {
            $value->type_count = DB::table('type')->where('bid', $value->id)->select('id')->count();
            $value->car_count = DB::table('car')->where('bid', $value->id)->select('id')->count();
            $value->no = $i + ($page - 1) * $limit;
            $i++;
        }
        return response()->json([
            'data' => $data,
            'total' => $total
        ]);
    }

    public function add(Request $request){
        $data=$request->all();
        if(!isset($data['logo'])||!isset($data['brand'])){
            return response()->json([
                'msg'=>'请输入logo及品牌名称！'
            ]);
        }
        $result=DB::table('brand')->insert($data);
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

    public function editshow(Request $request){
        $id=$request->get('id');
        $data=DB::table('brand')->where('id',$id)->get();
        return response()->json([
            'data'=>$data
        ]);
    }

    public function edit(Request $post){
        $data=$post->all();
        DB::table('brand')->where('id',$data['id'])->update($data);
        return response()->json([
            'msg'=>'success'
        ]);
    }

    public function del(Request $post){
        $id=$post->id;
        $result=DB::table('brand')->where('id',$id)->delete();
        $result1=DB::table('type')->where('bid',$id)->delete();
        if($result&&$result1) {
            return response()->json([
                'msg'=>'success'
            ]);
        }else{
            return response()->json([
                'msg'=>'fail'
            ]);
        }
    }

    public function type(Request $request){
        $page=$request->get('page');
        $limit=$request->get('limit');
        $i=1;
        if($request->get('bid')){
            $bid=$request->get('bid');
            $total=DB::table('type')->where('bid',$bid)->select('id')->count();
            $data=DB::table('type')->where('bid',$bid)->select('id','type','logo')->limit($limit)->offset(($page-1)*$limit)->get();
        }else{
            $total=DB::table('type')->select('id')->count();
            $data=DB::table('type')->limit($limit)->offset(($page-1)*$limit)->select('id','type','logo')->get();
        }
        foreach($data as $value){
            $value->car_count=DB::table('car')->where('tid',$value->id)->select('id')->count();
            $value->main_id=$i+($page-1)*$limit;
            $i++;
        }
        return response()->json([
            'data'=>$data,
            'total'=>$total
        ]);
    }

    public function type_add(Request $request){
        $data=$request->all();
        $result=DB::table('type')->insert($data);
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

    public function type_edit_show(Request $request){
        $id=$request->get('id');
        $data=DB::table('type')->where('id',$id)->select('id','type','logo')->get();
        $data=$data[0];
        return response()->json([
            'data'=>$data
        ]);
    }

    public function type_edit(Request $post){
        $data=$post->all();
        DB::table('type')->where('id',$data['id'])->update([
            'type'=>$data['type'],'logo'=>$data['logo']
        ]);
        return response()->json([
            'msg'=>'success'
        ]);
    }

    public function type_check(Request $request)
    {
        $id = $request->get('id');
        $check = DB::table('car')->where('tid', $id)->select('id')->get();
        $len = sizeof($check);
        if ($len) {
            return response()->json([
                'msg'=>'该型号下存在车辆'
            ]);
        }else{
            return response()->json([
                'msg'=>'该型号下无车辆'
            ]);
        }
    }

    public function type_del(Request $post){
        $id=$post->id;
        $result=DB::table('type')->where('id',$id)->delete();
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

    public function brand(){
        $data=DB::table('brand')->select('brand','id')->get();
        return response()->json([
            'data'=>$data
        ]);
    }

    public function gettype(Request $request){
        $brand=$request->get('brand');
        $bid=DB::table('brand')->where('brand',$brand)->value('id');
        $type=DB::table('type')->where('bid',$bid)->pluck('type');
        return response()->json([
           'data'=>$type
        ]);
    }

}
