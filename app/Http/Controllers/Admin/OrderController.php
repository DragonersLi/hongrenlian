<?php

namespace App\Http\Controllers\Admin;
use App\Models\Admin\Role;
use App\Models\Admin\AdminUser as User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\BaseController;
use App\Models\Admin\OrderModel;
use App\Models\Admin\MessageModel as MessageModel;
use Illuminate\Support\Facades\DB;

//订单管理
class OrderController extends BaseController
{

    public $order_status = [

        0 => '待兑换',
        1 => '待发货',
        2 => '待收货',
        3 => '已完成',
        4 => '已取消',
        5 => '已删除',
        6 => '超时关闭',
    ];

    public function __construct()
    {
        $this->model = new OrderModel;
    }

    /**
     * 列表
     */
    public function index(Request $request)
    {
        $order_status = $request->order_status;
        $keywords = $request->keywords;
        $where = " 1=1 ";
        if(isset($order_status) && $order_status>=0){//订单状态
            $pages['order_status'] = $order_status;
            $where .= " and order_status = {$order_status}";
        }


        if(!empty($keywords)){//订单编号，用户信息
            $pages['keywords'] = $keywords;
            $where .= " and ( concat(orders.order_id,users.username) like '%{$keywords}%'   ) ";
        }
        $pages['page'] = isset($page) ? $page : 1;
        $links = $this->model->join("users","users.id","=","orders.user_id")->whereRaw($where)->orderBy('order_id','desc')->paginate($this->page_size)->appends($pages); 

        $data['data'] = $links;
        $data['page'] = $pages;
        $data['orderStatus'] = $this->order_status;
        return view('admin.order.index',$data);
    }


    //添加
    public function add(Request $request)
    {
        if($request->isMethod('post')){
            try{
                $data = $request->except('_token');
                $time = time();// dd($data);
                $spec_data['spec_title'] = $data['spec_title'];
                $spec_data['spec_alias'] = $data['spec_alias'];
                $spec_data['status'] = 1;
                $spec_data['create_time'] =  $time;

                \DB::beginTransaction();
                $spec_id = \DB::table('goods_spec')->insertGetId($spec_data);
                foreach($data['spec_value'] as $k=>$v){
                    if(!empty($v)){
                        $spec_values[$k]['spec_id'] = $spec_id;
                        $spec_values[$k]['spec_value'] = $v;
                        $spec_values[$k]['spec_value_sort'] = $data['spec_value_sort'][$k]?$data['spec_value_sort'][$k]:$k+1;
                        $spec_values[$k]['create_time'] =  $time;
                    }
                }

                $spec_values_ids = \DB::table('goods_spec_values')->insert($spec_values);
                if($spec_id && $spec_values_ids){
                    \DB::commit();
                    return redirect('admin/goods_spec/index')->withSuccess('操作成功!');
                }else{
                    \DB::rollback();
                }

            }catch (\Exception $e){
                return redirect()->back()->withInput()->withErrors($e->getMessage());
            }
        }

        return view('admin.goods_spec.add',$data = []);
    }

    //编辑
    public function edit(Request $request)
    {
        $spec_id = $request->spec_id;
        if($request->isMethod('post')){
            try{
                $data = $request->except('_token');
                $time = time();// dd($data);
                $spec_data['spec_title'] = $data['spec_title'];
                $spec_data['spec_alias'] = $data['spec_alias'];
                $spec_data['update_time'] =  $time;

                \DB::beginTransaction();
                $spec_res = \DB::table('goods_spec')->where(['spec_id'=>$data['spec_id']])->update($spec_data);
                foreach($data['spec_value_id'] as $k=>$v){
                    if(!empty($v)){//更新
                        $spec_values['spec_id'] = $spec_id;
                        $spec_values['spec_value'] = $data['spec_value'][$k];
                        $spec_values['spec_value_sort'] = $data['spec_value_sort'][$k]?$data['spec_value_sort'][$k]:$k+1;
                        $spec_values['update_time'] =  $time;
                        $spec_values_ids = \DB::table('goods_spec_values')->where(['spec_value_id'=>$v])->update($spec_values);

                    }else{//新增
                        $spec_values['spec_id'] = $spec_id;
                        $spec_values['spec_value'] = $data['spec_value'][$k];
                        $spec_values['spec_value_sort'] = $data['spec_value_sort'][$k]?$data['spec_value_sort'][$k]:$k+1;
                        $spec_values['create_time'] =  $time;
                        $spec_values_ids = \DB::table('goods_spec_values')->insert($spec_values);
                    }
                }


                if($spec_res && $spec_values_ids){
                    \DB::commit();
                    return redirect('admin/goods_spec/index')->withSuccess('操作成功!');
                }else{
                    \DB::rollback();
                }
            }catch (\Exception $e){
                return redirect()->back()->withInput()->withErrors($e->getMessage());
            }
        }
        $res = \DB::table('goods_spec')->where(['spec_id'=>$spec_id])->first();
        $data['spec_title'] = $res->spec_title;
        $data['spec_alias'] = $res->spec_alias;
        $data['spec_id'] = $spec_id;
        $data['spec_values'] = \DB::table('goods_spec_values')->where(['spec_id'=>$spec_id])->get();//dd($data);
        return view('admin.goods_spec.edit',$data);
    }

    //正常或禁用
    public function changeStatus(Request $request)
    {
        try{
            $id = $request->id;
            $status = $request->status ? 0 : 1;
            $this->model->where(['id'=>$id])->update(['disabled'=>$status]);
            return redirect()->back()->withSuccess('操作成功!');
        }catch (\Exception $e){
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }


    }

    //上传图片
    public function upload(Request $request)
    {
        $status = 0;
        $data = [];
        if ($request->method()== 'POST') {
            //上传图片具体操作
            $file_name = $_FILES['file']['name'];
            //$file_type = $_FILES["file"]["type"];
            $file_tmp = $_FILES["file"]["tmp_name"];
            $file_error = $_FILES["file"]["error"];
            $file_size = $_FILES["file"]["size"];

            if ($file_error > 0) { // 出错
                $message = $file_error;
            } elseif($file_size > 10485760) { // 文件太大了
                $message = "上传文件不能大于10MB";
            }else{
                $date = date('Ymd');
                $file_name_arr = explode('.', $file_name);
                $new_file_name = date('YmdHis') . '.' . $file_name_arr[1];
                $path = "upload/image/goods_cat/".$date."/";
                $file_path = $path . $new_file_name;
                if (file_exists($file_path)) {
                    $message = "此文件已经存在啦";
                } else {
                    //TODO 判断当前的目录是否存在，若不存在就新建一个!
                    if (!is_dir($path)){mkdir($path,0777);}
                    $upload_result = move_uploaded_file($file_tmp, $file_path);
                    //此函数只支持 HTTP POST 上传的文件
                    if ($upload_result) {
                        $status = 1;
                        $message = '/'.$file_path;
                    } else {
                        $message = "文件上传失败，请稍后再尝试";
                    }
                }
            }
        } else {
            $message = "参数错误";
        }

        exit(json_encode(['status'=>$status,'message'=>$message]));

    }


    public function getChildCat(Request $request){
        $pid = $request->id ? $request->id :0;
        $where = "disabled = 0 and pid = {$pid}";
        $res = \DB::table('goods_cat')->whereRaw($where)->get();

        if(!empty($res)){
            $option = "<option value='0' >请选择</option><br>";
            foreach($res as $k=>$v){
                $option .= "<option value='{$v->id}'>{$v->title}</option><br>";
            }
          echo $option;
        }else{
            echo"";
        }
    }
    private function getCatPath($id = 0){
        $where = " 1=1 ";
        $where.= $id ? " and id={$id}" : " and pid=0";
        $res =  \DB::table('goods_cat')->whereRaw($where)->first();
        return $res->cat_path;
    }

    /**
     * 订单详情
     */
    public function info(Request $request){
        $order_id = $request->order_id ? $request->order_id : 0;
        $data = \DB::table('orders')->join("users","users.id","=","orders.user_id")->join("orders_detail","orders_detail.order_id","=","orders.order_id")->where(['orders.order_id'=>$order_id])->get();
        return view('admin.order.info',['data'=>$data]);
    }

    /**
     * 发货
     */
    public function going(Request $request){
        $order_id = $request->order_id ? $request->order_id : NULL;
        $status = $request->status ? $request->status : NULL;
        $links = $this->model->join("users","users.id","=","orders.user_id")->orderBy('order_id','desc')->first();
        return view('admin.order.going',['order_id'=>$order_id,'status'=>$status, 'user_id'=>$links->user_id, 'total_score'=>$links->total_score]);
    }

    /**
     * 修发货状态
     * @param Request $request->wuliu_name  快递公司
     * @param Request $request->wuliu_code  物流单号
     * @param Request $request->fahuo_time   发货时间
     * @param Request $request->update_time   订单修改时间
     * @param Request $request->order_status   订单状态
     * @return true 操作成功
     */
    public function changego(Request $request){
        $order_id = $request->order_id ? $request->order_id : NULL;
        $data['wuliu_name'] = $request->wuliu_name ? $request->wuliu_name : NULL;//快递公司
        $data['wuliu_code'] = $request->wuliu_code ? $request->wuliu_code : NULL;//物流单号
        $data['fahuo_time'] = time();//发货时间
        $data['update_time'] = time();//更新时间
        $data['order_status'] = 2;
        $res = OrderModel::updateOneOrder($order_id,$data);
         if($res){
             MessageModel::addMessage($request->user_id, "兑换商品已发货", "您的订单号{$request->order_id}成功支付红人圈{$request->total_score}，我们已为您发货，请及时确认收货！");
             return redirect('admin/order/index')->withSuccess('操作成功！');
         }else{
             return redirect('admin/order/index')->withSuccess('操作失败！');
         }
    }

}