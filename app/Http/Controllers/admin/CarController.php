<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PHPUnit\Runner\Exception;

class CarController extends Controller
{
    public function total(){
        $data=DB::table('car')->select('id')->count();
        return response()->json([
            'data'=>$data
        ]);
    }

    public function index(Request $request){
        $limit=$request->get('limit');
        $page=$request->get('page');
        $new=$request->get('new');
        $i=1;
        $total=DB::table('car')
            ->leftJoin('pay_to_car', 'car.id', '=', 'pay_to_car.cid')
            ->leftJoin('brand','car.bid','=','brand.id')
            ->leftJoin('type','car.tid','=','type.id')
            ->where('car.new', $new)
            ->where('new',$new)
            ->select('car.id')
            ->count();
        $data = DB::table('car')
            ->leftJoin('pay_to_car', 'car.id', '=', 'pay_to_car.cid')
            ->leftJoin('brand','car.bid','=','brand.id')
            ->leftJoin('type','car.tid','=','type.id')
            ->where('car.new', $new)
            ->limit($limit)
            ->offset(($page - 1) * $limit)
            ->select('brand.brand','type.type', 'car.id', 'car.year', 'car.style', 'car.shelves', 'car.recommend', 'pay_to_car.price','car.pic')
            ->get();
//
        foreach($data as $value){
            $value->main_id=$i+($page-1)*$limit;
            $i++;
        }
        return response()->json([
            'data'=>$data,
            'total'=>$total
        ]);
    }

    public function click(Request $request){
        $type=$request->get('type');
        $id=$request->get('id');
        if($type==1){
            $data=DB::table('car')->where('id',$id)->value('shelves');
            if($data==1){
                DB::table('car')->where('id',$id)->update(['shelves'=>2]);
            }else{
                DB::table('car')->where('id',$id)->update(['shelves'=>1]);
            }
        }else{
            $data=DB::table('car')->where('id',$id)->value('recommend');
            if($data==1){
                DB::table('car')->where('id',$id)->update(['recommend'=>2]);
            }else{
                DB::table('car')->where('id',$id)->update(['recommend'=>1]);
            }
        }
        return response()->json([
            'msg'=>'success'
        ]);
    }

    public function add(Request $request){
        $new=$request->get('new');
        $data=$request->all();
        $data['bid']=DB::table('brand')->where('brand',$data['bid'])->value('id');
        $data['tid']=DB::table('type')->where('type',$data['tid'])->value('id');
        $now=time();
        $photo=$request->get('photo');
        if($photo){
            $len=sizeof($photo);
        }

        if($new==1){
            $result=DB::table('car')->insert([
                'pic'=>$data['pic'],'bid'=>$data['bid'],'tid'=>$data['tid'],'year'=>$data['year'],
                'style'=>$data['style'],'pro'=>$data['pro'],'article'=>$data['article'],'new'=>$new,
                'add_time'=>$now,'model'=>$data['model'],'speed'=>$data['speed'],'fuel'=>$data['fuel'],'vol'=>$data['vol'],
                'struct'=>$data['struct'],'size'=>$data['size'],'engine'=>$data['engine'],'drive'=>$data['drive'],
                'cons'=>$data['cons'],'color'=>$data['color'],'shelves'=>1,'recommend'=>2
            ]);
            $cid=DB::table('car')->where('pic',$data['pic'])->value('id');
            $result1=DB::table('pay_to_car')->insert([
                'cid'=>$cid,'payment'=>$data['payment'],'time'=>$data['time'],
                'monthly_pay'=>$data['monthly_pay'],'price'=>$data['price']
            ]);
            if($photo) {
                for ($i = 0; $i < $len; $i++) {
                    $result2 = DB::table('pic_to_car')->insert([
                        'cid' => $cid, 'photo' => $photo[$i]
                    ]);
                }
            }
            DB::table('report_to_car')->insert([
                'cid'=>$cid,'report'=>$data['report'],'author'=>$data['author'],'author_pic'=>$data['author_pic']
            ]);
        }else{
            $result=DB::table('car')->insert([
                'pic'=>$data['pic'],'bid'=>$data['bid'],'tid'=>$data['tid'],'age'=>$data['age'],'year'=>$data['year'],
                'style'=>$data['style'],'pro'=>$data['pro'],'article'=>$data['article'],
                'mileage'=>$data['mileage'],'new'=>$new, 'add_time'=>$now,'model'=>$data['model'],
                'speed'=>$data['speed'],'fuel'=>$data['fuel'],'vol'=>$data['vol'],
                'struct'=>$data['struct'],'size'=>$data['size'],'engine'=>$data['engine'],'drive'=>$data['drive'],
                'cons'=>$data['cons'],'color'=>$data['color'],'area'=>$data['area'],'reg'=>$data['reg'],'shelves'=>1,'recommend'=>2
            ]);
            $cid=DB::table('car')->where('pic',$data['pic'])->value('id');
            $result1=DB::table('pay_to_car')->insert([
                'cid'=>$cid,'payment'=>$data['payment'],'time'=>$data['time'],
                'monthly_pay'=>$data['monthly_pay'],'price'=>$data['price']
            ]);
            for($i=0;$i<$len;$i++) {
                $result2 = DB::table('pic_to_car')->insert([
                    'cid' => $cid, 'photo' => $photo[$i]
                ]);
            }
//            $data['annual']=strtotime($data['annual']);
            
            DB::table('report_to_car')->insert([
                'cid'=>$cid,'report'=>$data['report'],'author'=>$data['author'],'author_pic'=>$data['author_pic']
            ]);
        }
        $id=DB::table('car')->where('pic',$data['pic'])->value('id');
        if($result&&$result1&&$result2){
            return response()->json([
                'msg'=>'success',
                'data'=>$id
            ]);
        }else{
            return response()->json([
                'msg'=>'fail'
            ]);
        }
    }

    public function edit_show(Request $request){
        $new=$request->get('new');
        $cid=$request->get('cid');
        if($new==1){
            $data=DB::table('car')
                ->leftJoin('pay_to_car','pay_to_car.cid','=','car.id')
                ->leftJoin('brand','car.bid','=','brand.id')
                ->leftJoin('type','car.tid','=','type.id')
                ->leftJoin('report_to_car','report_to_car.cid','=','car.id')
                ->where('car.id',$cid)
                ->select('pay_to_car.payment','pay_to_car.monthly_pay','pay_to_car.price','pay_to_car.time'
                    ,'brand.brand','car.bid','type.type','car.tid','car.pic','car.year','car.style','car.model',
                    'car.pro', 'car.article','car.new','report_to_car.report','report_to_car.author','report_to_car.author_pic',
                    'car.speed','car.fuel','car.vol','car.struct','car.size','car.engine','car.drive','car.cons','car.color')
                ->get();
            $data=$data[0];
            $data->photo=DB::table('pic_to_car')->where('cid',$cid)->pluck('photo');

        }else{
            $data=DB::table('car')
                ->leftJoin('pay_to_car','pay_to_car.cid','=','car.id')
                ->leftJoin('brand','car.bid','=','brand.id')
                ->leftJoin('type','car.tid','=','type.id')

                ->leftJoin('report_to_car','report_to_car.cid','=','car.id')
                ->where('car.id',$cid)
                ->select('pay_to_car.payment','pay_to_car.monthly_pay','pay_to_car.price','pay_to_car.time'
                    ,'brand.brand','type.type','car.pic','car.age','car.year','car.style','car.model','car.pro','car.new',
                    'car.article','car.mileage','report_to_car.report','report_to_car.author','report_to_car.author_pic'
                    , 'car.speed','car.fuel','car.vol','car.struct','car.size','car.engine','car.drive','car.cons','car.color','car.area','car.reg')
                ->get();
            $data=$data[0];
            $data->photo=DB::table('pic_to_car')->where('cid',$cid)->pluck('photo');
        }
        return response()->json([
            'data'=>$data
        ]);
    }

    public function edit(Request $post){
        $new=$post->new;
        $data=$post->all();
        $photo=$post->photo;
        $len=sizeof($photo);
        if($new==1){
            DB::table('car')->where('id',$data['id'])->update([
                'pic'=>$data['pic'], 'year'=>$data['year'],'style'=>$data['style']
                ,'pro'=>$data['pro'], 'article'=>$data['article'],'model'=>$data['model']
                ,'speed'=>$data['speed'],'fuel'=>$data['fuel'],'vol'=>$data['vol'],
                'struct'=>$data['struct'],'size'=>$data['size'],'engine'=>$data['engine'],'drive'=>$data['drive'],
                'cons'=>$data['cons'],'color'=>$data['color']
            ]);
            DB::table('pay_to_car')->where('cid',$data['id'])->update([
                'payment'=>$data['payment'],'monthly_pay'=>$data['monthly_pay'],
                'price'=>$data['price']
            ]);
            DB::table('pic_to_car')->where('cid',$data['id'])->delete();
            for($i=0;$i<$len;$i++) {
                DB::table('pic_to_car')->insert(['cid'=>$data['id'],'photo'=>$photo[$i]]);
            }
            DB::table('report_to_car')->where('cid',$data['id'])->update([
                'report'=>$data['report'],'author'=>$data['author'],'author_pic'=>$data['author_pic']
            ]);
        }else{
            DB::table('car')->where('id',$data['id'])->update([
               'pic'=>$data['pic'],'age'=>$data['age'],'year'=>$data['year'],'style'=>$data['style'],'pro'=>$data['pro'],
                'article'=>$data['article'],'mileage'=>$data['mileage'],'model'=>$data['model']
                ,'speed'=>$data['speed'],'fuel'=>$data['fuel'],'vol'=>$data['vol'],
                'struct'=>$data['struct'],'size'=>$data['size'],'engine'=>$data['engine'],'drive'=>$data['drive'],
                'cons'=>$data['cons'],'color'=>$data['color'],'area'=>$data['area'],'reg'=>$data['reg']
            ]);
            DB::table('pay_to_car')->where('cid',$data['id'])->update([
                'payment'=>$data['payment'],'monthly_pay'=>$data['monthly_pay'],
                'price'=>$data['price']
            ]);
            DB::table('pic_to_car')->where('cid',$data['id'])->delete();
            for($i=0;$i<$len;$i++) {
                DB::table('pic_to_car')->insert(['cid'=>$data['id'],'photo'=>$photo[$i]]);
            }
//            $data['annual']=strtotime($data['annual']);
            DB::table('report_to_car')->where('cid',$data['id'])->update([
                'report'=>$data['report'],'author'=>$data['author'],'author_pic'=>$data['author_pic']
            ]);
        }
        return response()->json([
            'msg'=>'success'
        ]);
    }

    public function del(Request $post){
        $id=$post->id;
        try {
            DB::transaction(function () use ($id) {
                DB::table('car')->where('id', $id)->delete();
                DB::table('basic')->where('cid', $id)->delete();
                DB::table('config_s')->where('cid', $id)->delete();
            });
        }catch(Exception $e){
            return response()->json([
                'msg'=>'fail'
            ]);
        }

        return response()->json([
            'msg'=>'success'
        ]);

    }

//    public function info(Request $request){
//        $id=$request->get('id');
//        $data=DB::table('car')
//            ->leftJoin('pay_to_car', 'car.id', '=', 'pay_to_car.cid')
//            ->leftJoin('brand','car.bid','=','brand.id')
//            ->leftJoin('type','car.tid','=','type.id')
//            ->where('car.id',$id)
//            ->select('brand.brand','type.type','car.pic','car.age','car.style','car.article',
//                'car.pro','pay_to_car.price','pay_to_car.payment','pay_to_car.monthly_pay')
//            ->get();
//        return response()->json([
//            'data'=>$data
//        ]);
//    }

    public function basic(Request $request){
        $id=$request->get('id');
        $data=DB::table('basic')->where('cid',$id)->get();
        return response()->json([
            'data'=>$data
        ]);
    }

    public function basic_add(Request $request){
        $data=$request->all();
        $result=DB::table('basic')->insert($data);
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

    public function basic_edit_show(Request $request){
        $id=$request->get('id');
        $data=DB::table('basic')->where('cid',$id)->get();
        return response()->json([
            'data'=>$data
        ]);
    }

    public function basic_edit(Request $post){
        $data=$post->all();
        $result=DB::table('basic')->where('id',$data['id'])->update(['name'=>$data['name'],'value'=>$data['value']]);
        return response()->json([
            'msg'=>'success'
        ]);
    }

    public function basic_del(Request $post){
        $id=$post->id;
        $result=DB::table('basic')->where('id',$id)->delete();
        if($result) {
            return response()->json([
                'msg'=>'success'
            ]);
        }else{
            return response()->json([
                'msg'=>'fail'
            ]);
        }
    }

    public function parent(){
        $data=DB::table('config_p')->get();
        return response()->json([
            'data'=>$data
        ]);
    }

    public function parent_add(Request $request){
        $data=$request->all();
        $check=DB::table('config_p')->where('name',$data['name'])->value('id');
        if($check){
            return response()->json([
                'msg'=>'已存在父级配置名称'
            ]);
        }
        $result=DB::table('config_p')->insert($data);
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

    public function parent_del(Request $post)
    {
        $id = $post->id;
        $result = DB::table('config_p')->where('id', $id)->delete();
        DB::table('config_n')->where('pid', $id)->delete();
        if ($result ) {
            return response()->json([
                'msg'=>'success'
            ]);
        }else{
            return response()->json([
                'msg'=>'fail'
            ]);
        }
    }

    public function detail(Request $request){
        $pid=$request->get('pid');
        $data=DB::table('config_n')->where('pid',$pid)->select('id','name')->get();
        return response()->json([
            'data'=>$data
        ]);
    }

    public function detail_add(Request $request){
        $data=$request->all();
        $result=DB::table('config_n')->insert($data);
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

    public function detail_edit(Request $post){
        $data=$post->all();
        $result=DB::table('config_n')->where('id',$data['id'])->update(['name'=>$data['name']]);
        return response()->json([
            'msg'=>'success'
        ]);


    }

    public function detail_del(Request $post){
        $id=$post->id;
        $result=DB::table('config_n')->where('id',$id)->delete();
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

    public function detail_val(Request $request){
        $cid=$request->get('cid');
        $data=DB::table('config_s')->where('cid',$cid)->select('id','val')->get();
        return response()->json([
            'data'=>$data
        ]);
    }

    public function detail_val_add(Request $request){
        $cid=$request->get('cid');
        $data=$request->get('data');
        $res=DB::table('config_s')->insert(['cid'=>$cid,'val'=>$data]);
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

    public function detail_val_edit(Request $post){
        $cid=$post->cid;
        $data=$post->data;
        $res=DB::table('config_s')->where('cid',$cid)->update(['val'=>$data]);
            return response()->json([
                'msg'=>'success'
            ]);

    }

    public function car_search(Request $request){
        $page=$request->get('page');
        $limit=$request->get('limit');
        $brand=$request->get('brand');
        $year=$request->get('year');
        $text=$request->get('text');
        $new=$request->get('new');
        $i=1;
        $total= $data=DB::table('car')
            ->leftJoin('brand','brand.id','=','car.bid')
            ->leftJoin('type','type.id','=','car.tid')
            ->leftJoin('pay_to_car', 'car.id', '=', 'pay_to_car.cid')
            ->where(function($query) use($brand){
                if(! empty($brand)){
                    $query->where('brand.brand','=',$brand);
                }
            })
            ->where(function($query) use($year){
                if(! empty($year)){
                    $query->where('car.year','=',$year);
                }
            })
            ->where(function($query) use($text){
                if(! empty($text)){
                    $query->where('brand.brand','like','%'.$text.'%')->orWhere('type.type','like','%'.$text.'%');
                }
            })
            ->where('car.new',$new)
            ->select('car.id')
            ->count();

        $data=DB::table('car')
            ->leftJoin('brand','brand.id','=','car.bid')
            ->leftJoin('type','type.id','=','car.tid')
            ->leftJoin('pay_to_car', 'car.id', '=', 'pay_to_car.cid')
            ->where(function($query) use($brand){
                if(! empty($brand)){
                    $query->where('brand.brand','=',$brand);
                }
            })
            ->where(function($query) use($year){
                if(! empty($year)){
                    $query->where('car.year','=',$year);
                }
            })
            ->where(function($query) use($text){
                if(! empty($text)){
                    $query->where('brand.brand','like','%'.$text.'%')->orWhere('type.type','like','%'.$text.'%');
                }
            })
            ->where('car.new',$new)
            ->limit($limit)
            ->offset(($page - 1) * $limit)
            ->select('car.id', 'car.year', 'type.type', 'car.style', 'car.shelves', 'car.recommend', 'brand.brand','pay_to_car.price','car.pic')
            ->get();
        foreach($data as $value){
            $value->main_id=$i+($page-1)*$limit;
            $i++;
        }

        return response()->json([
            'data'=>$data,
            'total'=>$total
        ]);
    }

    public function search_menu(){
        $id=array(1,5,6,7);
        $data=DB::table('filtrate_c')->whereIn('id',$id)->get();
        return response()->json([
            'data'=>$data
        ]);
    }

    public function search(Request $request){
        $id=$request->get('pid');
        if($id!=2&&$id!=3&&$id!=4) {
            $data = DB::table('filtrate')
                ->where('pid', $id)
                ->select('id','value','pid')
                ->get();
        }else{
            $data=DB::table('filtrate')
                ->where('pid',$id)
                ->select('id','max','min','pid')
                ->get();
        }
        return response()->json([
            'data'=>$data
        ]);
    }

    public function search_add(Request $request){
        $pid=$request->get('pid');
        if($pid!=2&&$pid!=3&&$pid!=4) {
            $value=$request->get('value');
            $result=DB::table('filtrate')->insert([
                'pid'=>$pid,'value'=>$value
            ]);
        }else{
            $max=$request->get('max');
            $min=$request->get('min');
            $result=DB::table('filtrate')->insert([
                'pid'=>$pid,'max'=>$max,'min'=>$min
            ]);
        }
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

    public function search_edit(Request $post){
        $id=$post->id;
        if($post->value){
            $value=$post->value;
            DB::table('filtrate')->where('id',$id)->update(['value'=>$value]);
        }else{
            $max=$post->max;
            $min=$post->min;
            DB::table('filtrate')->where('id',$id)->update(['max'=>$max,'min'=>$min]);
        }
        return response()->json([
            'msg'=>'success'
        ]);

    }

    public function search_del(Request $post){
        $id=$post->id;
        $result=DB::table('filtrate')->where('id',$id)->delete();
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

    public function unlink($pic){
       // $str='http://192.168.1.111/car/public/uploads/pic/5bd1670b9fbce.jpg';
        $path=str_replace('https://li.qdbnm.com','',$pic);
        $path='../public'.$path;
        unlink($path);

    }

}
