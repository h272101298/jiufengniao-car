<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    public function index(){
        $data=DB::table('users')
            ->leftJoin('role','users.rid','=','role.id')
            ->select('users.id','users.username','role.name')
            ->get();
        return response()->json([
            'data'=>$data
        ]);
    }

    public function add(Request $post){
        $data=$post->all();
        $rid=DB::table('role')->where('name',$data['name'])->value('id');
        $check=DB::table('users')->where('username',$data['username'])->value('id');
        if($check){
            return response()->json([
                'msg'=>'已存在用户名'
            ]);
        }
        if($data['password']!=$data['repassword']){
            return response()->json([
              'msg '=>'再次输入密码不一致'
            ]);
        }
        $len=strlen($data['password']);

        if($len<6){
            return response()->json([
                'msg'=>'密码不能小于6位'
            ]);
        }
        $data['password']=bcrypt($data['password']);
        $result=DB::table('users')->insert(['username'=>$data['username'],'password'=>$data['password'],'rid'=>$rid]);
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
        $data=DB::table('users')
            ->leftJoin('role','users.rid','=','role.id')
            ->select('users.id','users.username','role.name')
            ->get();
        foreach($data as $value){
            if($value->id==$id) {
                $res=$value;
            }
        }
        return response()->json([
            'data'=>$res
        ]);
    }

    public function edit(Request $post){
        $data=$post->all();
        $rid=DB::table('role')->where('name',$data['name'])->value('id');

        if(!isset($data['password'])){
            DB::table('users')->where('id',$data['id'])->update(['rid'=>$rid]);
            return response()->json([
                'msg'=>'success'
            ]);
        }
        $len=strlen($data['password']);
        if($len<6){
            return response()->json([
                'msg'=>'密码不能小于6位'
            ]);
        }
        $data['password']=bcrypt($data['password']);
        DB::table('users')->where('id',$data['id'])->update(['password'=>$data['password'],'rid'=>$rid]);
        return response()->json([
            'msg'=>'success'
        ]);
    }

    public function del(Request $post){
        $id=$post->id;
        $result=DB::table('users')->where('id',$id)->delete();
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

    public function login(Request $post){
        $username=$post->username;
        $password=$post->password;
        if(Auth::attempt(['username'=>$username,'password'=>$password],false)){
            $rid=DB::table('users')->where('id',Auth::id())->value('rid');
            $role=DB::table('role')->where('id',$rid)->get();
            $role=$role[0];
            return response()->json([
                'username'=>$username,
                'role'=>$role
            ]);
        }else{
            return response()->json([
                'msg'=>'帐号或密码错误'
            ]);
        }

    }

    public function logout(){
        Auth::logout();
        return response()->json([
            'msg'=>'success'
        ]);
    }

    public function role(){
        $data=DB::table('role')->get();
        foreach ($data as $value){
            $value->car=strval($value->car);
            $value->car_config=strval($value->car_config);
            $value->info=strval($value->info);
            $value->appoint=strval($value->appoint);
            $value->system=strval($value->system);
            $value->perm=strval($value->perm);
            $value->adv=strval($value->adv);
            $value->brand=strval($value->brand);
        }
        return response()->json([
            'data'=>$data
        ]);

    }

    public function role_add(Request $request){
        $data=$request->all();
        $result=DB::table('role')->insert($data);
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

    public function role_edit_show(Request $request){
        $id=$request->get('id');
        $data=DB::table('role')->where('id',$id)->get();
        $data=$data[0];
        return response()->json([
            'data'=>$data
        ]);
    }

    public function role_edit(Request $post){
        $data=$post->all();
        $result=DB::table('role')->where('id',$data['id'])->update([
            'name'=>$data['name'],
            'car'=>$data['car'],
            'car_config'=>$data['car_config'],
            'info'=>$data['info'],
            'appoint'=>$data['appoint'],
            'system'=>$data['system'],
            'perm'=>$data['perm'],
            'adv'=>$data['adv'],
            'brand'=>$data['brand']
        ]);
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

    public function role_del(Request $post){
        $id=$post->id;
        $result=DB::table('role')->where('id',$id)->delete();
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
