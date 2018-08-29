<?php

namespace App\Http\Controllers\Admin;
use App\Models\Admin\Role;
use App\Models\Admin\AdminUser as User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\BaseController;
use App\Models\Admin\GoodsModel;
use App\Models\Admin\GoodsProductsModel;
use App\Models\Admin\GoodsSpecModel;
use App\Models\Admin\GoodsSpecValuesModel;
use App\Models\Admin\GoodsSpecIndexModel;
//商品管理
class GoodsController extends BaseController
{


    public function __construct()
    {
        parent::__construct();
        $this->goodsModel = new GoodsModel();
        $this->productModel = new GoodsProductsModel();
        $this->specModel = new GoodsSpecModel();
        $this->specValuesModel = new GoodsSpecValuesModel();
        $this->specIndexModel = new GoodsSpecIndexModel();
    }

    /**
     * 列表
     */
    public function index(Request $request)
    {
        $sj = $request->sj;
        $status = $request->status;
        $keywords = $request->keywords;
        $page = $request->page ? $request->page : 1;
        $where = " 1=1 ";
        $pages['page'] = isset($page) ? $page : 1;
        if(isset($sj) && $sj>=0){//上下架
            $pages['sj'] = $sj;
            $where .= " and sj = {$sj}";
        }
        if(isset($status) && $status>=0){//状态
            $pages['status'] = $status;
            $where .= " and status = {$status}";
        }


        if(!empty($keywords)){//用户名，真实姓名，手机号，邮箱，qq号
            $pages['keywords'] = $keywords;
            $where .= " and ( concat(title,goods_sn) like '%{$keywords}%'   ) ";
        }
        $links = $this->goodsModel
            ->whereRaw($where)
            ->orderBy('goods_id','desc')
            ->paginate($this->page_size)
            ->appends($pages);

        $data['data'] = $links;
        $data['page'] = $pages;
        return view('admin.goods.index',$data);
    }

    //商品添加
    public function add(Request $request)
    {
        if($request->isMethod('post')) {
           //  dd($request->all());
            $data = $request->only('title','alias','unit','price','score','store','goods_type','desc','intro','attr','sku_items','pictures');

            $time = time();
            if(empty($data['title'])){
                return redirect()->back()->withInput()->withErrors('商品标题不能为空！');
            }

            if(empty($data['pictures'])){
                return redirect()->back()->withInput()->withErrors('商品图片不能为空！');
            }

            try {
                \DB::beginTransaction();
                $data['goods_sn'] = generateGoodsSn();
                $data['create_time'] = $time;
                $data['indexpic'] = $data['pictures'][0];//主图取第一张
                $data['pictures'] = serialize($data['pictures']);//图集序列化存储
                foreach($data['attr']['key'] as $key=>$val){
                    $attr[] = ['key'=>$val,'val'=>$data['attr']['val'][$key]];
                }

                $data['attr'] = serialize($attr);
                $sku_items = !empty($data['sku_items']) ? $data['sku_items'] : [];
                unset($data['sku_items']);
                $goods_id = $this->goodsModel->insertGetId($data); //商品添加

                if(!empty($sku_items['spec_id'])){//有规格
                    $spec_ids = $spec_titles = $spec_value_ids = $spec_values = $product = [];
                    foreach($sku_items['spec_id'] as $k=>$v){
                        $spec_ids = explode('@',$v);
                        $spec_titles = explode('@',$sku_items['spec_title'][$k]);

                        $spec_value_ids = explode('@',$sku_items['spec_value_id'][$k]);
                        $spec_values = explode('@',$sku_items['spec_value'][$k]);
                        foreach($spec_ids as $k1=>$v1){

                            if($k1 < count($spec_ids) -1){
                                $spec_data[$k1]['spec_id'] = $v1;
                                $spec_data[$k1]['spec_value_id'] = $spec_value_ids[$k1];
                                $spec_index[$k1] = ['spec_id'=>$v1,'spec_title'=>$spec_titles[$k1],'spec_value_id'=>$spec_value_ids[$k1],'spec_value'=>$spec_values[$k1]];
                            }
                        }

                        $product['spec_index'] = serialize($spec_index);  //sku对应的规格序列化存储
                        $product['product_sn'] = $data['goods_sn'].'-'.sprintf("%04d", $k+1);
                        $product['goods_id'] = $goods_id;
                        $product['product_score'] = $sku_items['score'][$k];
                        $product['product_store'] = $sku_items['store'][$k];
                        $product['product_sellout'] = 0;
                        $product['status'] = 1;
                        $product['create_time'] = $time;
                        $product_id = $this->productModel->insertGetId($product);

                        foreach($spec_data as $spec_key =>$spec_val){
                            $spec_data[$spec_key]['goods_id'] = $goods_id;
                            $spec_data[$spec_key]['product_id'] = $product_id;

                        }
                        $this->specIndexModel->insert($spec_data);
                    }
                }


              \DB::commit();
                return redirect('admin/goods/index')->withSuccess('操作成功！');
            } catch (\Exception $e) {//$e->getCode(), $e->getMessage()
                dd($e->getMessage());
               \DB::rollback();
                return redirect()->back()->withInput()->withErrors('操作失败！');
            }

        }

        //商品规格项
        $spec = $this->specModel->select('spec_id','spec_title')->where(['status'=>1])->get()->toArray();
        return view('admin.goods.add',['spec'=>$spec]);


    }

    //编辑
    public function edit(Request $request)
    {

        if($request->isMethod('post')){
            $time = time();
            try{
                $data = $request->except('_token');  //dd($data);
                $goods_data['update_time'] = $time;
                $goods_data['indexpic'] = $data['pictures'][0];
                $goods_data['store'] = $data['store'];
                $goods_data['score'] = $data['score'];
                $goods_data['price'] = $data['price'];
                $goods_data['title'] = $data['title'];
                $goods_data['alias'] = $data['alias'];
                $goods_data['unit'] = $data['unit'];
                $goods_data['goods_type'] = $data['goods_type'];
                $goods_data['pictures'] = serialize($data['pictures']);
                $goods_data['desc'] = $data['desc'];
                $goods_data['intro'] = $data['intro'];
                foreach($data['attr']['key'] as $key=>$val){
                    $attr[] = ['key'=>$val,'val'=>$data['attr']['val'][$key]];
                }

                $goods_data['attr'] = serialize($attr);
                if(empty($data['goods_id'])){
                    return redirect()->back()->withInput()->withErrors('商品不存在！');
                }
                if(empty($data['title'])){
                    return redirect()->back()->withInput()->withErrors('商品标题不能为空！');
                }

                $goods = $this->goodsModel->where(['goods_id'=>$data['goods_id']])->update($goods_data);
                    return redirect('admin/goods/index')->withSuccess('操作成功！');

            }catch (\Exception $e){dd($e->getMessage());
                return redirect()->back()->withInput()->withErrors($e->getMessage());
            }
        }
        $goods_id = $request->goods_id ? $request->goods_id : 0;
        $data = $this->goodsModel->where(['goods_id'=>$goods_id])->first()->toArray();
        $data['pictures'] = unserialize($data['pictures']);
        $data['attr'] = unserialize($data['attr']);
        $data['products'] = $this->productModel->where(['goods_id'=>$data['goods_id']])->get()->toArray();
       foreach($data['products'] as $k=>$v){
           $data['products'][$k]['spec_index'] = unserialize($v['spec_index']);
       }
        //商品规格项
        $spec = $this->specModel->select('spec_id','spec_title')->where(['status'=>1])->get()->toArray();
        $data['spec'] = $spec;
        return view('admin.goods.edit',$data);
    }

    //正常或禁用
    public function changeStatus(Request $request)
    {
        try{
            $goods_id = $request->goods_id;
            $status = $request->status ? 0 : 1;
            $this->goodsModel->where(['goods_id'=>$goods_id])->update(['status'=>$status,'update_time'=>time()]);
            return redirect()->back()->withSuccess('操作成功!');
        }catch (\Exception $e){
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }


    }
    //上下架
    public function sjStatus(Request $request)
    {
        try{
            $time = time();
            $goods_id = $request->goods_id ? $request->goods_id : 0;
            $sj = $request->sj ? 0 : 1;
            if(empty($goods_id)){
                return redirect()->back()->withInput()->withErrors('商品ID不能为空！');
            }

            $sj ? ($data['up_time'] = $time) : ($data['down_time'] = $time) ;
            $data['sj'] = $sj;
            $data['update_time'] = $time;
            $this->goodsModel->where(['goods_id'=>$goods_id])->update($data);
            return redirect()->back()->withSuccess('操作成功!');
        }catch (\Exception $e){
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function skuStatus($product_id = 0){
        if(empty($product_id)){
            return redirect()->back()->withInput()->withErrors('商品SKU不能为空！');
        }
        $product = $this->productModel->where(['product_id'=>$product_id])->select('status')->first();
         $status = $product->status ? 0 :  1;
         $res = $this->productModel->where(['product_id'=>$product_id])->update(['status'=>$status,'update_time'=>time()]);
        if(empty($res)){
            return redirect()->back()->withInput()->withErrors('商品SKU不能为空！');
        }
        return redirect()->back()->withSuccess('操作成功!');
    }

    public function upload(Request $request)
    {

        $file = $request->file('pictures');

        // 获取文件路径
        $transverse_pic = $file->getRealPath();
        $upload_path = '/upload/image/goods/';
        // public路径
        $path = public_path($upload_path);
        // 获取后缀名
        $postfix = $file->getClientOriginalExtension();
        // 拼装文件名
        $fileName = date('YmdHis').time().rand(0,10000).'.'.$postfix;
        // 移动
        if(!$file->move($path,$fileName)){
            return response()->json(['ServerNo' => '400','ResultData' => '文件保存失败']);
        }
        // 这里处理 数据库逻辑
        //file_put_contents('./logs.php',$upload_path.$fileName.PHP_EOL,FILE_APPEND);
        /**
         *Store::uploadFile(['fileName'=>$fileName]);
         **/
        return response()->json(['code'=>0,'ServerNo' => '200','ResultData' => $fileName]);
    }

    //根据规格名id获取规格值列表
    public function getSpecValues(Request $request){
        $spec_id = $request->spec_id ? $request->spec_id : 0;
        $spec_title = $request->spec_title ? $request->spec_title : '';
         $spec_values = $this->specValuesModel->where(['spec_id'=>$spec_id])->select('spec_value_id','spec_value')->get()->toArray();

         $options = '<p class="spec_value_status_'.$spec_id.'">';
         foreach($spec_values as $k=>$v){
             $options .= "<input type='checkbox' class='spec_value_index' spec_data='spec_id:{$spec_id},spec_title:{$spec_title},spec_value_id:{$v['spec_value_id']},spec_value:{$v['spec_value']}'  spec_id='{$spec_id}' spec_title='{$spec_title}'  spec_value='{$v['spec_value']}' name='spec_values_{$v['spec_value_id']}'  value='{$v['spec_value_id']}' style='margin:5px;'>{$v['spec_value']}&nbsp;&nbsp;&nbsp;&nbsp;";
         }
         $options .= '</p>';
       return $options;
    }
    //生成sku规格
    public function generateSKU(Request $request){
        $data = $request->sku;
        $res = [];
        if(!empty($data)){
            $data = json_decode($data);
            foreach($data as $k=>$v){ //"1:颜色|1:红色"
                $res[$v->spec_id][] = ['spec_id'=>$v->spec_id,'spec_title'=>$v->spec_title,'spec_value_id'=>$v->spec_value_id,'spec_value'=>$v->spec_value,'spec_data'=>$v->spec_data];
            }
        }
        return array_values($res);
    }


}