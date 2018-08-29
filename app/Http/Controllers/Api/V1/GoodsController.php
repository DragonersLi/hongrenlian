<?php

namespace App\Http\Controllers\Api\V1;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Controllers\Api\Wechat\ErrMsg as Msg;
use App\Models\Admin\GoodsModel;
use App\Models\Admin\GoodsProductsModel;
use App\Models\Admin\GoodsSpecIndexModel;
use App\Models\Admin\GoodsSpecModel;
use App\Models\Admin\GoodsSpecValuesModel;
use App\Models\Admin\UsersModel;
class GoodsController extends ApiBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->goodsModel = new GoodsModel;
        $this->productModel = new GoodsProductsModel;
        $this->specIndexModel = new GoodsSpecIndexModel;
        $this->specModel = new GoodsSpecModel;
        $this->specValuesModel = new GoodsSpecValuesModel;
        $this->usersModel = new UsersModel;
    }

    //商品列表
    public function index(Request $request)
    {
        $page = $request->page ? $request->page : 1;
        $size = $request->size ?  $request->size : $this->page_size;
        $cid = $request->cid ? $request->cid : 0;
        $where = " status = 1 and sj = 1 ";
        $cid && $where.= " and cat_id = {$cid}  ";
        $data = $this->goodsModel->whereRaw($where)->orderBy('goods_id','desc')->paginate($size)->toArray();
        $result = ['data'=>[]];
        if(!empty($data['data'])){
            foreach($data['data'] as $k=>$v){
                $goods[$k]['goods_id'] = $v['goods_id'];
                $goods[$k]['title'] = $v['title'];
                $goods[$k]['indexpic'] = $this->base_url.$v['indexpic'];
                $goods[$k]['price'] = $v['price'];
                $goods[$k]['score'] = $v['score'];
                $goods[$k]['goods_type'] = $v['goods_type'];//0：虚拟；1：实物


                $product = $this->productModel->where(['goods_id'=>$v['goods_id']])->get()->toArray();
                if(!empty($product['data'])){//有规格
                    $v['store'] = $v['sellout'] = 0;
                    foreach($product['data'] as $key=>$val){
                        $v['store'] += $val['product_store'];
                        $v['sellout'] += $val['product_sellout'];
                    }
                }

                $goods[$k]['goods_status'] = ($v['store'] - $v['sellout'])?1:0;//0：无库存；1：有库存
            }
            $result =[
                'total'=>$data['total'],
                'count'=>count($data['data']),
                'page'=>$data['current_page'],
                'size'=>$data['per_page'],
                'last'=>$data['last_page'],
                'data'=>$goods,
            ];

        }
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$result]);
    }

    //商品详情
    public function detail(Request $request)
    {
        $goods_id = $request->goods_id ? $request->goods_id : 0;
        if(empty($goods_id)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        $goods = $this->goodsModel->where(['goods_id'=>$goods_id])->first();
 
        if(empty($goods)){//商品不存在
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noGoods),'code'=>Msg::$err_noGoods]);
        }

        $goods = $goods->toArray();
        $product = $this->productModel->where(['goods_id'=>$goods_id])->get()->toArray();
		 
        if(!empty($product)){//实物有规格，则库存为所有的sku库存之和
		 
            $goods['store'] = $goods['sellout'] = 0;
            foreach($product as $key=>$val){
                $goods['store'] += $val['product_store'];
                $goods['sellout'] += $val['product_sellout'];
            }
        }  

        $data['goods_id'] = $goods['goods_id'];
        $data['title'] = $goods['title'];
        $data['indexpic'] = $this->base_url.$goods['indexpic'];
        $data['price'] = $goods['price'];
        $data['score'] = $goods['score'];
        $data['store'] = $goods['store'];
        $data['sellout'] = $goods['sellout'];
        $now_store = $goods['store'] - $goods['sellout'];
		$now_store = $now_store>0 ? $now_store : 0;
        $data['now_store'] =  $now_store ? $now_store : 0;
        $data['goods_status'] = $now_store ? 1 : 0; //0:无库存；1：有库存
        $data['goods_type'] = $goods['goods_type'];//0：虚拟；1：实物
        $data['unit'] = $goods['unit'];
        $data['pictures'] = $this->getPictures($goods['pictures']);

        preg_match_all("/[img|IMG].*?src=['|\"](.*?(?:[.gif|.jpg|.png|.jpeg]))['|\"].*?[\/]?>/",$goods['intro'],$match);
        $data['intro'] = $match[1];
        foreach($data['intro'] as $k=>$v){
            $imginfo = getimagesize($v);
            $data['intro_scale'][] =$imginfo[0]/$imginfo[1];
        }
        $data['desc'] = empty($goods['desc']) ? []: explode(PHP_EOL, $goods['desc']); 


        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$data]);

    }

    //商品详情页展示兑换的商品规格信息
    public function exchange(Request $request)
    {
        $goods_id = $request->goods_id ? $request->goods_id : 0;
        if(empty($goods_id)){//参数丢失
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        $goods = $this->goodsModel->where(['goods_id'=>$goods_id])->first();
        if(empty($goods)){//商品不存在
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noGoods),'code'=>Msg::$err_noGoods]);
        }

        $goods = $goods->toArray();
        $data['goods_id'] = $goods['goods_id'];
        $data['title'] = $goods['title'];
        $data['indexpic'] = $this->base_url.$goods['indexpic'];
        $data['score'] = $goods['score'];
        $data['unit'] = $goods['unit'];
        $data['store'] = $goods['store'];
        $data['sellout'] = $goods['sellout'];
        $product = $this->productModel->where(['goods_id'=>$goods_id])->get()->toArray();
        //有规格
        if(!empty($product)){

            $spec = $this->specIndexModel
                ->select('goods_spec_index.spec_id' ,'goods_spec.spec_title')
                ->join('goods_spec','goods_spec.spec_id','=','goods_spec_index.spec_id')
                ->where(['goods_id'=>$goods_id])
                ->groupBy('spec_id')
                ->get()
                ->toArray();
            foreach($spec as $k=>$v){
                $res[$k]['id'] = $v['spec_id'];
                $res[$k]['title'] = $v['spec_title'];
                $spec_val = $this->specIndexModel
                    ->select('goods_spec_index.spec_value_id','goods_spec_values.spec_value')
                    ->join('goods_spec_values','goods_spec_values.spec_value_id','=','goods_spec_index.spec_value_id')
                    ->where(['goods_id'=>$goods_id,'goods_spec_index.spec_id'=>$v['spec_id']])
                    ->groupBy('goods_spec_index.spec_value_id')
                    ->get()
                    ->toArray();
                $res[$k]['contents']  = array_values($spec_val) ;
            }

            $data['goods_spec'] = array_values($res);


            $data['store'] = $data['sellout'] = 0;
            foreach($product as $key=>$val){
                $data['store'] += $val['product_store'];
                $data['sellout'] += $val['product_sellout'];
            }
        }
        $data['now_store'] = $data['store'] - $data['sellout'];
        $data['goods_status'] = $data['now_store']>=1 ? 1 : 0;
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$data]);

    }

    //确认兑换
    public function exchangeSure(Request $request)
    {
        $user_id = $request->user_id ? $request->user_id : 0;
        $goods_id = $request->goods_id ? $request->goods_id : 0;
        $number = $request->number ? $request->number : 0;
        $spec_index = $request->spec_index ? $request->spec_index : [];
        if(empty($goods_id) || empty($number)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        $goods = $this->goodsModel->where(['goods_id'=>$goods_id])->first();
        if(empty($goods)){//商品不存在
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noGoods),'code'=>Msg::$err_noGoods]);
        }
        $goods = $goods->toArray();
        $data['goods_id'] = $goods_id;
        $data['number'] = $number;
        $data['title'] = $goods['title'];
        $data['indexpic'] = $this->base_url.$goods['indexpic'];
        $data['goods_type'] = $goods['goods_type'];
        $data['score'] = $goods['score'];
        $data['store'] = $goods['store'];

        $product = $this->productModel->where(['goods_id'=>$goods_id])->first();

        if(!empty($product)){//有sku，则规格必传

            if(empty($spec_index)){//没传规格
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_noSpecIndex),'code'=>Msg::$err_noSpecIndex]);
            }
            $spec_count = count(unserialize($product->spec_index));
            if($spec_count != count($spec_index)){//有规格，总数不对应
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_errorSpecIndex),'code'=>Msg::$err_errorSpecIndex]);
            }
            foreach($spec_index as $k=>$v){
                $spec_data = $this->specIndexModel->select('product_id','spec_id','spec_value_id')->where(['goods_id'=>$goods_id,'spec_id'=>$v['spec_id'],'spec_value_id'=>$v['spec_value_id']])->get()->toArray();

                foreach($spec_data as $k1=>$v1){
                    $spec[] = $v1['product_id'];
                }
            }
            $array_count_values = array_count_values($spec);//每个值出现多少次
            $product_id = array_search(max($array_count_values),$array_count_values);//查询出现最多值的key即为skuID
            $product_sku = $this->productModel->where(['product_id'=>$product_id])->first();//当前要购买的sku
            $data['spec_index'] = unserialize($product_sku->spec_index);
            $data['score'] = $product_sku->product_score;
            $data['store'] = $product_sku->product_store;
        }
        $data['total_score'] = $number * $data['score'];

        if($data['store'] - $number < 0){//无库存
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noStore),'code'=>Msg::$err_noStore]);
        }
        if(!$user_id){//用户没登陆
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_failureToken),'code'=>Msg::$err_failureToken]);
        }
        $user = $this->usersModel->select('score')->where(['id'=>$user_id])->first();
        if( $user->score - $data['total_score'] < 0){//红人圈不足
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noScore),'code'=>Msg::$err_noScore]);
        }
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$data]);

    }

    //商品状态
    public function getGoodsStatus(Request $request){
        $goods_id = $request->goods_id ? $request->goods_id : 0;
        if(empty($goods_id)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        $goods = $this->goodsModel->where(['goods_id'=>$goods_id])->first();
        if(empty($goods)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noGoods),'code'=>Msg::$err_noGoods]);
        }
        $data['goods_status'] = empty($goods->sj) ? 0 : 1;
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$data]);

    }
    //商品轮播图
    private function getPictures($pictures){
        $arr = unserialize($pictures);
        foreach($arr as $k=>$v){
            $arr[$k] = $this->base_url.$v;
        }
        return empty($arr)?[]:$arr;
    }

    //商品属性
    public function getAttr($attr){
        $arr = unserialize($attr);
        return empty($arr)?[]:$arr;
    }


}
