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
use App\Models\Admin\OrderModel as OrderModel;
use App\Models\Admin\OrderDetailModel as OrderDetailModel;
use App\Models\Admin\UsersModel;
use App\Models\Admin\AddressModel;
use App\Models\Admin\RegionModel;
use App\Models\Admin\MessageModel as MessageModel;
use App\Models\Admin\FinanceModel;

class OrderController extends ApiBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->goodsModel = new GoodsModel;
        $this->productModel = new GoodsProductsModel;
        $this->specIndexModel = new GoodsSpecIndexModel;
        $this->specModel = new GoodsSpecModel;
        $this->specValuesModel = new GoodsSpecValuesModel;
        $this->orderModel = new OrderModel;
        $this->orderDetailModel = new OrderDetailModel;
        $this->usersModel = new UsersModel;
        $this->addressModel = new AddressModel;
        $this->regionModel = new RegionModel;
        $this->financeModel = new FinanceModel;
    }

    //订单列表
    public function index(Request $request)
    {
        $page = $request->page ? $request->page : 1;
        $size = $request->size ?  $request->size : $this->page_size;
        $order_status = $request->order_status;
        $user_id = $request->user_id ? $request->user_id : 0;
        $where = " user_id ={$user_id} and order_status in(0,1,2,3,4,6)";
        if(isset($order_status) && in_array($order_status,[0,1,2,3,4,6])){
            $where.= " and order_status = {$order_status}  ";
        }

        $data = $this->orderModel
            ->select('orders.*','detail.goods_id','detail.product_id','detail.title','detail.score','detail.number','detail.indexpic')
            ->leftjoin('orders_detail as detail','detail.order_id','=','orders.order_id')
            ->whereRaw($where)
            ->orderBy('orders.order_id','desc')
            ->paginate($size)->toArray();
        $result = ['data'=>[]];
        if(!empty($data['data'])){
            foreach($data['data'] as $k=>$v){
                $order[$k]['order_id'] = (string)$v['order_id'];
                $order[$k]['pay_status'] = $v['pay_status'];
                $order[$k]['order_status'] = $v['order_status'];
                $order[$k]['total_score'] = $v['total_score'];
                $order[$k]['score'] = $v['score'];
                $order[$k]['number'] = $v['number'];
                $order[$k]['title'] = $v['title'];
                $order[$k]['goods_id'] = $v['goods_id'];
                $order[$k]['indexpic'] = $this->base_url.$v['indexpic'];

                $order[$k]['create_time'] = $v['create_time'];
                if(!$v['pay_status']){//未支付 关闭时间
                    $order[$k]['close_time'] = $this->order_close_time;
                }
                if($v['order_status'] ==2){//待收货，自动确认收货
                    $order[$k]['close_time'] = $this->order_sure_time;
                }
                $order[$k]['spec_index'] =[];
                if($v['product_id']){//商品有规格
                    $product = $this->productModel->where(['product_id'=>$v['product_id']])->first();
                    if(!empty($product->spec_index)){
                        $spec_index = unserialize($product->spec_index);
                        is_array($spec_index) && $order[$k]['spec_index'] = $spec_index;
                    }

                }

            }
            $result =[
                'total'=>$data['total'],
                'count'=>count($data['data']),
                'page'=>$data['current_page'],
                'size'=>$data['per_page'],
                'last'=>$data['last_page'],
                'data'=>$order,
            ];
        }
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$result]);
    }

    //订单详情
    public function detail(Request $request){
        $order_id = $request->order_id ? $request->order_id : 0;
        if(empty($order_id)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        $order = $this->orderModel
            ->select('orders.*','detail.goods_id','detail.product_id','detail.title','detail.score','detail.number','detail.indexpic')
            ->join('orders_detail as detail','detail.order_id','=','orders.order_id')
            ->where(['orders.order_id'=>$order_id])
            ->first();
        if(empty($order)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noOrder),'code'=>Msg::$err_noOrder]);
        }

        $order = $order->toArray();
        $goods = $this->goodsModel->where(['goods_id'=>$order['goods_id']])->first();
        $data['goods_id'] = $goods['goods_id'];
        $data['goods_type'] = $goods['goods_type'];

        $data['receive_name'] = $order['receive_name'];
        $data['receive_phone'] = $order['receive_phone'];
        $data['receive_address'] = $order['receive_address'];
        if($order['product_id']){//商品有规格
            $product = $this->productModel->where(['product_id'=>$order['product_id']])->first();
            if(!empty($product->spec_index)){
                $spec_index = unserialize($product->spec_index);
                is_array($spec_index) && $data['spec_index'] = $spec_index;
            }

        }
        if(!$order['order_status']){//未支付 关闭时间
            $data['close_time'] = $this->order_close_time;
        }
        if($order['order_status'] ==2){//待收货，自动确认收货
            $data['close_time'] = $this->order_sure_time;
        }
        $data['order_id'] = (string)$order['order_id'];
        $data['title'] = $order['title'];
        $data['indexpic'] = $this->base_url.$order['indexpic'];
        $data['number'] = $order['number'];
        $data['score'] = $order['score'];
        $data['total_score'] = $order['total_score'];
        $data['order_status'] = $order['order_status'];
        $data['create_time'] = $order['create_time'];
        $data['pay_time'] = $order['pay_time'];
        $data['wuliu_name'] = $order['wuliu_name'];
        $data['wuliu_code'] = $order['wuliu_code'];
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$data]);

    }



    //生成订单
    public function create(Request $request)
    {
        try {

            $data = $request->only('user_id','address_id','goods_id','number','spec_index');
            $data['spec_index'] = is_array($data['spec_index']) ? $data['spec_index'] : json_decode($data['spec_index'],true);//兼容ios传json字符串
            if(empty($data['user_id']) || empty($data['goods_id']) || empty($data['number'])){
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
            }
            $user = $this->usersModel->where(['id'=>$data['user_id']])->first();
            if(empty($user)){//当前用户不存在
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_noUser),'code'=>Msg::$err_noUser]);
            }

            $goods = $this->goodsModel->where(['goods_id'=>$data['goods_id']])->first();
            if(empty($goods)){//商品不存在
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_noGoods),'code'=>Msg::$err_noGoods]);
            }

            if($goods->goods_type){//实物有收货地址
                if(isset($data['address_id'])){
                    $address = $this->addressModel->where(['id'=>$data['address_id'],'user_id'=>$data['user_id']])->first();
                    if(empty($address)){//收货地址不存在
                        return response()->json(['msg'=>Msg::getMsg(Msg::$err_noAddress),'code'=>Msg::$err_noAddress]);
                    }
                    $addr = $this->regionModel->whereRaw( "id = {$address->province} or id = {$address->city} or id = {$address->area} ")->select(\DB::raw('GROUP_CONCAT(name separator " ") as receive_address'))->first();

                    $order['receive_name'] = $address->receive_name;
                    $order['receive_phone'] = $address->receive_phone;
                    $order['receive_address'] = $addr->receive_address.' '.$address->address;
                    $order['address_info'] = serialize(['province'=>$address->province,'city'=>$address->city,'area'=>$address->address]);
                    $order['order_type'] = 0;

                }
            }else{
                $order['order_type'] = 1;//虚拟物不需快递
            }
            if(!empty($data['spec_index'])){//商品有规格

                foreach($data['spec_index'] as $k=>$v){
                    $spec_data = $this->specIndexModel->select('product_id','spec_id','spec_value_id')->where(['goods_id'=>$data['goods_id'],'spec_id'=>$v['spec_id'],'spec_value_id'=>$v['spec_value_id']])->get()->toArray();

                    foreach($spec_data as $k1=>$v1){
                        $spec[] = $v1['product_id'];
                    }
                }
                $array_count_values = array_count_values($spec);//每个值出现多少次

                $product_id = array_search(max($array_count_values),$array_count_values);//查询出现最多值的key即为skuID


                if(empty($product_id)){//根据规格找不到商品sku
                    return response()->json(['msg'=>Msg::getMsg(Msg::$err_noProduct),'code'=>Msg::$err_noProduct]);
                }
                $product = $this->productModel->where(['product_id'=>$product_id])->first();//当前要购买的sku
                if(empty($product)){//根据规格找不到商品sku
                    return response()->json(['msg'=>Msg::getMsg(Msg::$err_noProduct),'code'=>Msg::$err_noProduct]);
                }

                $order_detail['product_id'] =  $product_id;
                $order_detail['score'] =  $product->product_score;
                $now_store = $product->product_store - $product->product_sellout;//实时库存

            }else{//商品无规格
                $order_detail['product_id'] = 0;
                $order_detail['score'] = $goods->score;
                $now_store = $goods->score - $goods->sellout;//实时库存
            }
            if($now_store < $data['number']){//库存不足
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_noStore),'code'=>Msg::$err_noStore]);

            }
            $order['order_id'] = OrderModel::getOrderId();
            $order['user_id'] = $data['user_id'];
            $order['total_score'] = $data['number'] * $order_detail['score'];
            $order['total_number'] = $data['number'];
            $order['pay_status'] = 0;//未支付
            $order['order_status'] = 0;//未兑换
            $order['create_time'] = time();

            $order_detail['order_id'] = $order['order_id'];
            $order_detail['goods_id'] = $goods->goods_id;
            $order_detail['title'] = $goods->title;
            $order_detail['number'] = $data['number'];
            $order_detail['indexpic'] = $goods->indexpic;

            if($user->score <  $order['total_score']){//红人圈不足
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_noScore),'code'=>Msg::$err_noScore]);
            }

            \DB::beginTransaction();
            $this->orderModel->insert($order);
            $this->orderDetailModel->insert($order_detail);

            \DB::commit();
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>['order_id'=>$order['order_id']]]);
        } catch (\Exception $e) {  //dd($e->getMessage());
            \DB::rollback();
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_failedOrder),'code'=>Msg::$err_failedOrder]);
        }
    }

    //支付订单
    public function pay(Request $request)
    {
        try {
            $data = $request->only('user_id','order_id');

            if(empty($data['user_id']) || empty($data['order_id'])){
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
            }
            $user = $this->usersModel->where(['id'=>$data['user_id']])->first();
            if(empty($user)){//当前用户不存在
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_noUser),'code'=>Msg::$err_noUser]);
            }

            $order = $this->orderModel->where(['order_id'=>$data['order_id']])->first();

            if($data['user_id'] != $order->user_id){//订单不属于该用户
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_notYourOder),'code'=>Msg::$err_notYourOder]);

            }
            if($order->pay_status){//订单已支付
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_alreadyPay),'code'=>Msg::$err_alreadyPay]);

            }

            \DB::beginTransaction();
            $goods = $this->orderDetailModel->join('goods','goods.goods_id','orders_detail.goods_id')->where(['order_id'=>$order->order_id])->first();//订单详情表找到购买的商品id  //找到购买的商品信息
            if(empty($goods)){
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_noGoods),'code'=>Msg::$err_noGoods]);
            }

            if($goods->goods_type){//实物代发货，虚拟待收货
                $order_status =  1;
                MessageModel::addMessage($user->id, "兑换商品红人圈支出", "您的订单号{$order->order_id}成功支付红人圈{$order->total_score}，我们会尽快为您发货！");

            }else{
                $order_status =  2;
                MessageModel::addMessage($user->id, "兑换商品红人圈支出", "您的订单号{$order->order_id}成功支付红人圈{$order->total_score}，我们已为您发货，请及时确认收货！");

            }
            $this->orderModel->where(['order_id'=>$order->order_id])->update(['pay_status'=>1,'order_status'=>$order_status,'pay_time'=>time()]);

            //支出记录			
            $this->financeModel->insert([
                'user_id' => $data['user_id'],
                'type' => 5,
                'number' => - $order['total_score'],
                'action' => 1,
                'note' => '兑换商品支出红人圈',
                'create_time'=>time()
            ]);
            $this->usersModel->where(['id'=>$user->id])->decrement('score',$order->total_score);//用户减红人圈
            if($goods->product_id){//更新sku表库存
                //$this->productModel->where(['product_id'=>$goods->product_id])->decrement('product_store',$order->total_number);
                $this->productModel->where(['product_id'=>$goods->product_id])->increment('product_sellout',$order->total_number);
            }else{//更新商品表库存
                //$this->goodsModel->where(['goods_id'=>$goods->goods_id])->decrement('store',$order->total_number);
                $this->goodsModel->where(['goods_id'=>$goods->goods_id])->decrement('sellout',$order->total_number);
            }


            \DB::commit();
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none]);
        } catch (\Exception $e) { //dd($e->getMessage());
            \DB::rollback();
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_failedPay),'code'=>Msg::$err_failedPay]);
        }


    }

    //更改订单状态[取消订单，删除订单,确认收货]
    public function changeStatus(Request $request){
        $data = $request->only('user_id','order_id','order_status');

        if(empty($data['user_id']) || empty($data['order_id']) || empty($data['order_status'])){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        $user = $this->usersModel->where(['id'=>$data['user_id']])->first();
        if(empty($user)){//当前用户不存在
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noUser),'code'=>Msg::$err_noUser]);
        }
        $order = $this->orderModel->where(['order_id'=>$data['order_id']])->first();
        if(empty($order)){//订单不存在
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noOrder),'code'=>Msg::$err_noOrder]);
        }
        if($data['user_id'] != $order->user_id){//订单不属于该用户
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_notYourOder),'code'=>Msg::$err_notYourOder]);

        }
        if(in_array($data['order_status'],[3,4,5])){//3：已完成；4：已取消；5：已删除
            $time = time();
            $update_order = [];
            if($data['order_status'] == 3){//确认收货
                //if($order->pay_status && $order->order_status == 2){//已支付，待收货才能确认收货
                $updata_order['order_status'] = 3;
                $updata_order['sure_time'] = $time;
                //}else{//不能确认收货
                //     return response()->json(['msg'=>Msg::getMsg(Msg::$err_cannotSureOrder),'code'=>Msg::$err_cannotSureOrder]);
                // }
            }
            if($data['order_status'] == 4){//取消订单
                //if(!$order->pay_status && !$order->order_status){//只能取消未支付，待兑换订单
                $updata_order['order_status'] = 4;
                $updata_order['update_time'] = $time;
                //}else{//已支付订单不能取消
                //     return response()->json(['msg'=>Msg::getMsg(Msg::$err_cannotCancelOrder),'code'=>Msg::$err_cannotCancelOrder]);
                // }
            }
            if($data['order_status'] == 5){//删除订单
                //if(!$order->pay_status){//只能删除未支付
                $updata_order['order_status'] = 5;
                $updata_order['delete_time'] = $time;
                //}else{//已支付订单不能删除
                //    return response()->json(['msg'=>Msg::getMsg(Msg::$err_cannotDeleteOrder),'code'=>Msg::$err_cannotDeleteOrder]);
                //}
            }
            $res = $this->orderModel->where(['order_id'=>$order->order_id])->update($updata_order);
            if($res){//操作成功
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none]);
            }else{//操作失败
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_failed),'code'=>Msg::$err_failed]);
            }
        }

        return response()->json(['msg'=>Msg::getMsg(Msg::$err_illegalRequest),'code'=>Msg::$err_illegalRequest]);
    }

    //获取订单超时时间
    public function getCloseOrderTime(){
        return $this->order_close_time;
    }
    //获取订单自动确认时间
    public function getSureOrderTime(){
        return $this->order_sure_time;
    }

}
