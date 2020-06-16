<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FigureController extends Controller
{
    public function index(Request $request){
        $attr=$request->get('attr');
        $data=DB::table('figure')
            ->where(function($query) use($attr){
                if(! empty($attr)){
                    $query->where('attr','=',$attr);
                }
            })
            ->get();
        return response()->json([
            'data'=>$data,
        ]);
    }

    public function add(Request $request){
        $data=$request->all();
        $result=DB::table('figure')->insert($data);
        if($result) {
            return response()->json([
                'msg' => 'success'
            ]);
        }else{
            return response()->json([
                'msg'=>'fail'
            ]);
        }
    }

    public function editshow(Request $request){
        $id=$request->get('id');
        $data=DB::table('figure')->where('id',$id)->get();
        return response()->json([
           'data'=>$data
        ]);
    }

    public function edit(Request $request){
        $data=$request->all();
        DB::table('figure')->where('id',$data['id'])->update(['pic'=>$data['pic'],
            'attr'=>$data['attr']]);
        return response()->json([
           'msg'=>'success'
        ]);
    }

    public function del(Request $request){
        $id=$request->get('id');
        $result=DB::table('figure')->where('id',$id)->delete();
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
}
