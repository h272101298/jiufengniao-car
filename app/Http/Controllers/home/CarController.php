<?php

namespace App\Http\Controllers\home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CarController extends Controller
{
    /**
     * 一般搜索
     * @param Request $request   text
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $search = $request->get('text');
        $data = DB::table('type')
            ->leftJoin('brand', 'brand.id', '=', 'type.bid')
            ->where('brand.brand', 'like', '%' . $search . '%')
            ->orWhere('type.type', 'like', '%' . $search . '%')
            ->select( 'brand.brand', 'type.type','type.id','type.bid')
            ->get();

        return response()->json([
            'data' => $data
        ]);
    }

    public function brandSearch(Request $request){
        $search=$request->get('text');
        $data=DB::table('brand')
            ->where('brand','like','%'.$search.'%')
            ->select('id','brand','logo')
            ->get();
        return response()->json([
           'data'=>$data
        ]);
    }

    /**
     * 高级搜索
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function car_search(Request $request)
    {
        $limit=$request->get('limit');
        $page=$request->get('page');
        $ord = $request->get('order');
        switch ($ord) {
            case '':
                $order = 'car.add_time desc';
                break;
            case 'date1':
                $order = 'car.add_time desc';
                break;
            case 'date2':
                $order = 'car.add_time asc';
                break;
            case 'age1':
                $order = 'car.age desc';
                break;
            case 'age2':
                $order = 'car.age asc';
                break;
            case 'mile1':
                $order = 'car.mileage desc';
                break;
            case 'mile2':
                $order = 'car.mileage asc';
                break;
        }
        $model = $request->get('model');
        $price_min = $request->get('price_min');
        $price_max = $request->get('price_max');
        $new = $request->get('new');
        $age_min = $request->get('age_min');
        $age_max = $request->get('age_max');
        $mile_min = $request->get('mile_min');
        $mile_max = $request->get('mile_max');
        $color = $request->get('color');
        $speed = $request->get('speed');
        $fuel = $request->get('fuel');
        $vol_min = $request->get('vol_min');
        $vol_max = $request->get('vol_max');
        $recommend = $request->get('recommend');
        $tid=$request->get('tid');
        $bid=$request->get('bid');
        $pro=$request->get('pro');

        $data = DB::table('car')
            ->leftJoin('brand', 'brand.id', '=', 'car.bid')
            ->leftJoin('type', 'type.id', '=', 'car.tid')
            ->leftJoin('pay_to_car', 'pay_to_car.cid', '=', 'car.id')
            ->where('car.shelves', '=', 1)
            ->where(function ($query) use ($tid) {
                if (!empty($tid)) {
                    $query->where('car.tid', '=', $tid);
                }
            })
            ->where(function ($query) use ($bid) {
                if (!empty($bid)) {
                    $query->where('car.bid', '=', $bid);
                }
            })
            ->where(function ($query) use ($pro) {
                if (!empty($pro)) {
                    $query->where('car.pro', '=', $pro);
                }
            })
            ->where(function ($query) use ($new) {
                if (!empty($new)) {
                    $query->where('car.new', '=', $new);
                }
            })
            ->where(function ($query) use ($recommend) {
                if (!empty($recommend)) {
                    $query->where('car.recommend', '=', $recommend);
                }
            })
            ->where(function ($query) use ($price_max, $price_min) {
                if (!empty($price_max) && !empty($price_min)) {
                    $query->where('pay_to_car.price', '>=', $price_min)->where('pay_to_car.price', '<=', $price_max);
                }elseif(!empty($price_max) && empty($price_min)){
                    $query->where('pay_to_car.price', '<=', $price_max);
                }elseif(empty($price_max) && !empty($price_min)){
                    $query->where('pay_to_car.price', '>=', $price_min);
                }
            })
            ->where(function ($query) use ($model) {
                if (!empty($model)) {
                    $model_size = sizeof($model);
                    for ($i = 0; $i < $model_size; $i++) {
                        $query->orWhere('car.model', '=', $model[$i]);
                    }
                }
            })
            ->where(function ($query) use ($age_max, $age_min) {
                if (!empty($age_max) && !empty($age_min)) {
                    $query->where('car.age', '<=', $age_max)->where('car.age', '>=', $age_min);
                }
            })
            ->where(function ($query) use ($mile_max, $mile_min) {
                if (!empty($mile_max) && !empty($mile_min)) {
                    $query->where('car.mileage', '>=', $mile_min)->where('car.mileage', '<=', $mile_max);
                }
            })
            ->where(function ($query) use ($color) {
                if (!empty($color)) {
                    $style_size = sizeof($color);
                    for ($i = 0; $i < $style_size; $i++) {
                        $query->orWhere('car.color', 'like','%'. $color[$i].'%');
                    }
                }
            })
            ->where(function ($query) use ($fuel) {
                if (!empty($fuel)) {
                    $fuel_size = sizeof($fuel);
                    for ($i = 0; $i < $fuel_size; $i++) {
                        $query->orWhere('car.fuel', '=', $fuel[$i]);
                    }
                }
            })
            ->where(function ($query) use ($speed) {
                if (!empty($speed)) {
                    $speed_size = sizeof($speed);
                    for ($i = 0; $i < $speed_size; $i++) {
                        $query->orWhere('car.speed', '=', $speed[$i]);
                    }
                }
            })
            ->where(function ($query) use ($vol_max, $vol_min) {
                if (!empty($vol_max) && !empty($vol_min)) {
                    $query->where('car.vol', '>=', $vol_min)->where('car.vol', '<=', $vol_max);
                }
            })
            ->limit($limit)
            ->offset(($page - 1) * $limit)
            ->select('car.id', 'car.pic', 'brand.brand', 'type.type','car.vol', 'car.style', 'car.model',
                'car.new', 'pay_to_car.payment', 'pay_to_car.price', 'pay_to_car.monthly_pay','car.year',
                'car.mileage','car.speed','car.pro')
            ->orderbyRaw($order)
            ->get();

        foreach ($data as $value) {
            switch ($value->pro){
                case 1:
                    $value->pro='超值特卖';
                    break;
                case 2:
                    $value->pro='降价急售';
                    break;
                case 3:
                    $value->pro='准新车';
                    break;
                case 4:
                    $value->pro='爆款SUV';
                    break;
                case 5:
                    $value->pro='可迁全国';
                    break;
                case 0:
                    $value->pro='不限';
            }
            if ($value->new == 1) {
                $value->new = '新车';
            } else {
                $value->new = '二手车';
            }
        }
        return response()->json([
            'data' => $data
        ]);
    }

    /**
     * 前台各汽车页面
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
//        $type=$request->get('type');
        $page = $request->get('page');
        $limit = $request->get('limit');
        $pro = $request->get('pro');
        $ord = $request->get('order');
        $new = $request->get('new');
        $money_min=$request->get('money_min');
        $money_max=$request->get('money_max');
        $recommend = $request->get('recommend');
        $tid=$request->get('tid');

        switch ($ord) {
            case '':
                $order = 'car.add_time desc';
                break;
            case 'date1':
                $order = 'car.add_time desc';
                break;
            case 'date2':
                $order = 'car.add_time asc';
                break;
            case 'age1':
                $order = 'car.age desc';
                break;
            case 'age2':
                $order = 'car.age asc';
                break;
            case 'mile1':
                $order = 'car.mileage desc';
                break;
            case 'mile2':
                $order = 'car.mileage asc';
                break;
        }
        $data = DB::table('car')
            ->leftJoin('brand', 'brand.id', '=', 'car.bid')
            ->leftJoin('type', 'type.id', '=', 'car.tid')
            ->leftJoin('pay_to_car', 'pay_to_car.cid', '=', 'car.id')
            ->where(function ($query) use ($pro) {
                if (!empty($pro)) {
                    $query->where('car.pro', '=', $pro);
                }
            })
            ->where(function ($query) use ($tid) {
                if (!empty($tid)) {
                    $query->where('type.id', '=', $tid);
                }
            })
            ->where(function ($query) use ($new) {
                if (!empty($new)) {
                    $query->where('car.new', '=', $new);
                }
            })
            ->where(function ($query) use ($recommend) {
                if (!empty($recommend)) {
                    $query->where('car.recommend', '=', $recommend);
                }
            })
            ->where(function ($query) use ($money_max,$money_min) {
                if (!empty($money_max)&& empty($money_min)) {
                    $query->where('pay_to_car.price','<=',$money_max);
                }elseif(empty($money_max)&& !empty($money_min)){
                    $query->where('pay_to_car.price', '>=', $money_min);
                }elseif(!empty($money_max) && !empty($money_min)){
                    $query->where('pay_to_car.price', '>=', $money_min)->where('pay_to_car.price','<=',$money_max);
                }
            })
            ->where('car.shelves', '=', 1)
            ->limit($limit)
            ->offset(($page - 1) * $limit)
            ->select('car.id', 'car.pic', 'brand.brand', 'type.type', 'car.style', 'car.model', 'car.pro'
                , 'car.vol', 'car.new', 'pay_to_car.payment', 'pay_to_car.price', 'pay_to_car.monthly_pay','car.year',
                'car.mileage','car.speed')
            ->orderbyRaw($order)
            ->get();


        $total = sizeof($data);
        $data = CntoEn($data);
        return response()->json([
            'data' => $data,
            'total' => $total
        ]);
    }

    /**
     * 品牌列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function brand()
    {
        $brand = DB::table('brand')->select('brand','id','logo')->get();

        foreach ($brand as $value) {
            $value->word = getFirstCharter($value->brand);
        }
        dump($brand);
        exit();
        $len = sizeof($brand);
        for ($i = 1; $i < $len; $i++) {
            for ($k = 0; $k < $len - $i; $k++) {
                if ($brand[$k]->word > $brand[$k + 1]->word) {
                    $tmp = $brand[$k + 1];
                    $brand[$k + 1] = $brand[$k];
                    $brand[$k] = $tmp;
                }
            }
        }
        $res[][]='';
        for($i=0,$j=0,$l=0;$i<$len;$i++){
            if($i==0){
                $res[$j]['title']=$brand[$i]->word;
                $res[$j]['brand'][$l]['id']=$brand[$i]->id;
                $res[$j]['brand'][$l]['brandName']=$brand[$i]->brand;
                $res[$j]['brand'][$l]['logo']=$brand[$i]->logo;
                $l++;
            }else{
                if($res[$j]['title']==$brand[$i]->word){
                    $res[$j]['brand'][$l]['id']=$brand[$i]->id;
                    $res[$j]['brand'][$l]['brandName']=$brand[$i]->brand;
                    $res[$j]['brand'][$l]['logo']=$brand[$i]->logo;
                    $l++;
                }else{
                    $j++;
                    $l=0;
                    $res[$j]['title']=$brand[$i]->word;
                    $res[$j]['brand'][$l]['id']=$brand[$i]->id;
                    $res[$j]['brand'][$l]['brandName']=$brand[$i]->brand;
                    $res[$j]['brand'][$l]['logo']=$brand[$i]->logo;
                    $l++;
                }
            }
        }
//        dump($res);
//        dump($brand);
        return response()->json([
            'data'=>$res
        ]);
    }

    /**
     * 型号
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function type(Request $request){
        $bid=$request->get('bid');
        $data=DB::table('type')->where('bid',$bid)->select('id','type','logo')->get();
        foreach ($data as $value){
            $value->total=DB::table('car')->where('tid',$value->id)->select('id')->count();
        }
        return response()->json([
            'data'=>$data
        ]);
    }

    /**
     * 汽车详细页
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function car_info(Request $request){
        $cid=$request->get('id');
        $new=$request->get('new');
        $token=$request->get('token');
        if($token) {
            $openid = DB::table('mid')->where('token', $token)->value('openid');
            if (!$openid) {
                return response()->json([
                    'msg' => 'token错误'
                ]);
            }
            $uid = DB::table('info')->where('openid', $openid)->value('id');
        }
        if($new==1) {
            $data = DB::table('car')
                ->leftJoin('brand', 'brand.id', '=', 'car.bid')
                ->leftJoin('type', 'type.id', '=', 'car.tid')
                ->leftJoin('pay_to_car', 'pay_to_car.cid', '=', 'car.id')
                ->where('car.id', '=', $cid)
                ->select( 'brand.brand', 'type.type', 'car.style', 'car.vol', 'car.model',
                    'car.new', 'pay_to_car.payment', 'pay_to_car.monthly_pay', 'pay_to_car.time', 'pay_to_car.price','car.year',
                    'car.struct', 'car.size', 'car.engine', 'car.speed', 'car.drive', 'car.fuel', 'car.cons','car.color','car.article',
                    'car.add_time','car.pic')
                ->get();
            $data=$data[0];
            $photo=DB::table('pic_to_car')->where('cid',$cid)->pluck('photo');
            $data->photo=$photo;
        }else{
            $data = DB::table('car')
                ->leftJoin('brand', 'brand.id', '=', 'car.bid')
                ->leftJoin('type', 'type.id', '=', 'car.tid')
                ->leftJoin('pic_to_car', 'pic_to_car.cid', '=', 'car.id')
                ->leftJoin('pay_to_car', 'pay_to_car.cid', '=', 'car.id')
                ->leftJoin('report_to_car','report_to_car.cid','=','car.id')
                ->where('car.id', '=', $cid)
                ->select('pic_to_car.photo', 'brand.brand', 'type.type', 'car.style', 'car.vol', 'car.model','car.color',
                    'car.new', 'pay_to_car.payment', 'pay_to_car.monthly_pay', 'pay_to_car.time', 'pay_to_car.price',
                    'car.struct', 'car.size', 'car.engine', 'car.speed', 'car.drive', 'car.fuel', 'car.cons','car.year',
                    'car.mileage','car.area','car.reg','report_to_car.report','report_to_car.author','report_to_car.author_pic','car.article',
                    'car.add_time')
                ->get();
            $data=$data[0];
            $photo=DB::table('pic_to_car')->where('cid',$cid)->pluck('photo');
            $data->photo=$photo;
        }
        $data->add_time=date('Y-m-d H:i:s',$data->add_time);
        if(isset($uid)) {
            $isCollect = DB::table('collection')->where('uid', $uid)->where('cid', $cid)->value('id');
            if ($isCollect) {
                $data->uid = 1;
            } else {
                $data->uid = 0;
            }
        }
        if($data->new==1){
            $data->new='新车';
        }else{
            $data->new='二手车';
        }
        return response()->json([
            'data'=>$data
        ]);
    }

    /**
     *汽车全部配置
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function car_info_all(Request $request){
        $cid=$request->get('id');
        $data=DB::table('config_s')->where('cid',$cid)->value('val');
        return response()->json([
            'data'=>$data
        ]);
    }

    public function filtrate(){
        $data=DB::table('filtrate_c')->select('id','name')->get();
        foreach($data as $value){
                $conf=DB::table('filtrate')->where('pid',$value->id)->pluck('value');
                $len=sizeof($conf);
                for($i=0;$i<$len;$i++){
                    $value->value[$i]['value']=$conf[$i];
                    $value->value[$i]['checked']=false;
                }
        }
        for($i=1;$i<4;$i++){
            $data[$i]->max='';
            $data[$i]->min='';
        }
        return response()->json([
            'data'=>$data
        ]);
    }


}
