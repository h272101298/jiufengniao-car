<?php

namespace App\Http\Controllers\home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FigureController extends Controller
{
   public function index(Request $request){
       $attr=$request->get('attr');
       $data=DB::table('figure')->where('attr',$attr)->pluck('pic');
       return response()->json([
           'data'=>$data
       ]);
   }

    /**
     * 入口图
     * @return \Illuminate\Http\JsonResponse
     */
    public function entrance(){
        $data=DB::table('figure')
            ->where('attr',4)
            ->orWhere('attr',6)
            ->orWhere('attr',8)
            ->orWhere('attr',10)
            ->orWhere('attr',12)
            ->orWhere('attr',14)
            ->select('attr','pic')
            ->get();
        $pic=DB::table('figure')->where('attr',1)->pluck('pic');
        return response()->json([
            'data'=>$data,
            'fig'=>$pic
        ]);
    }
}


