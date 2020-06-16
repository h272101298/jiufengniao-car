<?php
/**
 * Created by PhpStorm.
 * User: zeng
 * Date: 2018/5/29
 * Time: 下午2:52
 */

/**
 * 返回json响应
 */
if (!function_exists('jsonResponse')){
    function jsonResponse($param,$code=200){
        return response()->json($param,$code);
    }
}
/**
 * 返回视图响应
 */
if (!function_exists('viewResponse')){
    function viewResponse($view,$param){
        return view($view,$param);
    }
}
/**
 * 返回随机字符串
 */
if (!function_exists('createNonceStr')){
    function CreateNonceStr($length = 10){
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
}

/**
 * 对象转数组
 */
if(!function_exists('ObjToArray')){
    function ObjToArray($obj){
        $res=array();
        foreach ($obj as $key=>$value){
            $res[$key]=$value->id;
        }
        return $res;
    }
}

/**
 * 翻译
 */
if(!function_exists('CnToEn')){
    function CntoEn($data){
        foreach ($data as $value) {
            if(isset($value->pro)) {
                switch ($value->pro) {
                    case 1:
                        $value->pro = '超值特卖';
                        break;
                    case 2:
                        $value->pro = '降价急售';
                        break;
                    case 3:
                        $value->pro = '准新车';
                        break;
                    case 4:
                        $value->pro = '爆款SUV';
                        break;
                    case 5:
                        $value->pro = '可迁全国';
                        break;
                    case 0:
                        $value->pro='不限';
                        break;
                }
            }
            if(isset($value->new)) {
                switch ($value->new) {
                    case 1:
                        $value->new = '新车';
                        break;
                    case 2:
                        $value->new = '二手车';
                        break;
                }
            }

        }
        return $data;
    }
}

/**
 * 首字母
 */
if(!function_exists('getFirstCharter')){
    function getFirstCharter($str){
        if(empty($str)){return '';}
        $fchar=ord($str{0});
        if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
        $s1=iconv('UTF-8','GBK',$str);
        $s2=iconv('GBK','UTF-8',$s1);
        $s=$s2==$str?$s1:$str;
        $asc=ord($s{0})*256+ord($s{1})-65536;
        dump($asc);
        if($asc>=-20319&&$asc<=-20284) return 'A';
        if($asc>=-20283&&$asc<=-19776) return 'B';
        if($asc>=-19775&&$asc<=-19219) return 'C';
        if($asc>=-19218&&$asc<=-18711) return 'D';
        if($asc>=-18710&&$asc<=-18527) return 'E';
        if($asc>=-18526&&$asc<=-18240) return 'F';
        if($asc>=-18239&&$asc<=-17923) return 'G';
        if($asc>=-17922&&$asc<=-17418) return 'H';
        if($asc>=-17417&&$asc<=-16475) return 'J';
        if($asc>=-16474&&$asc<=-16213) return 'K';
        if($asc>=-16212&&$asc<=-15641) return 'L';
        if($asc>=-15640&&$asc<=-15166) return 'M';
        if($asc>=-15165&&$asc<=-14923) return 'N';
        if($asc>=-14922&&$asc<=-14915) return 'O';
        if($asc>=-14914&&$asc<=-14631) return 'P';
        if($asc>=-14630&&$asc<=-14150) return 'Q';
        if($asc>=-14149&&$asc<=-14091) return 'R';
        if($asc>=-14090&&$asc<=-13319) return 'S';
        if($asc>=-13318&&$asc<=-12839) return 'T';
        if($asc>=-12838&&$asc<=-12557) return 'W';
        if($asc>=-12556&&$asc<=-11848) return 'X';
        if($asc>=-11847&&$asc<=-11056) return 'Y';
        if($asc>=-11055&&$asc<=-10247) return 'Z';
//        if($asc==-9559) return 'O';
        return null;
    }
}

/**
 * 图片压缩
 */
if(!function_exists('ImageCompression')){
    function ImageCompression($photo,$ext,$radio){

        $name=uniqid().'.'.$ext;
        switch ($ext){
            case 'jpg':
            case 'jpeg':
            $dst_im = imagecreatefromjpeg($photo);
               imagejpeg($dst_im,'../public/uploads/pic/'.$name,$radio);
                break;
            case 'png':
                $dst_im=imagecreatefrompng($photo);
                imagepng($dst_im,'../public/uploads/pic/'.$name,$radio);
                break;
        }
        imagedestroy($dst_im);
        return 'uploads/pic/'.$name;
    }
}


