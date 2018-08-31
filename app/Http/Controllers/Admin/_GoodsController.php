<?php

namespace App\Http\Controllers\Admin;
use App\Models\Admin\Role;
use App\Models\Admin\AdminUser as User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\BaseController;
use App\Models\Admin\GoodsModel;
use DB;
//商品管理
class GoodsController extends BaseController
{


    public function __construct()
    {
        $this->model = new GoodsModel();
    }

    /**
     * 列表
     */
    public function index(Request $request)
    {
        $pages['page'] = isset($page) ? $page : 1;
        $sj = $request->sj;//上架状态
        $status = $request->status;//商品状态
        $keywords = $request->keywords;//商品关键字搜索
        $where = " 1=1 ";
        $page = $request->page;//页数
        $pages['page'] = isset($page) ? $page : 1;
        $sj = isset($sj) ? $sj  : 1;
        $status = isset($status) ? $status : 1;
        $where .="and sj = {$sj} and status = {$status}";
        if(!empty($keywords)){//商品名称 商品货号
            $pages['keywords'] = $keywords;
            $where .= " and ( concat(goods.title,goods.goods_sn) like '%{$keywords}%'   ) ";
        }
        $links = $this->model->select()->whereRaw($where)->orderBy('goods_id','desc')->paginate(5)->appends($pages);
        $data['data'] = $links;
        $data['page'] = $pages;
        return view('admin.goods.index',$data);
    }


    //添加 商品数据页面
    public function addgoodsitem()
    {
        //$spec=[];
        //商品规格项  
        $spec['item']=DB::table('goods_spec') 
            ->select('spec_id','spec_title')
            ->where(['goods_spec.status'=>1])
            ->get();
        //规格项对应的值
        $spec['value']=DB::table('goods_spec') 
            ->join('goods_spec_values','goods_spec_values.spec_id','=','goods_spec.spec_id')
            ->select('goods_spec.spec_id','spec_title','spec_value_id','spec_value')
            ->where(['goods_spec.status'=>1])
            ->get();
            //print_r($spec);die;
        return view('admin.goods.add',['spec'=>$spec]);
    }
    //商品添加
    public function add(Request $request)
    {
        //当前时间
        $time = time();
        $data['goods_type']=$request->goods_type;
        $data['title']=$request->title;
        $data['alias']=$request->alias;
        $data['indexpic']=$request->indexpic;
        $data['goods_sn']=generateGoodsSn();
        $data['pictures']=serialize($request->check_val);
        $data['price']=$request->price;
        $data['score']=$request->jg;
        $data['store']=$request->kc;
        $data['sellout']=0;
        $attr_val=explode(',',$request->attr_val);
        $vals_val=explode(',',$request->vals_val);
        $attr=[];
        foreach($attr_val as $k=>$v){
            $attr[$k]['attr']=$v;
            $attr[$k]['val']=$vals_val[$k];
        }
        $data['attr']=serialize($attr);
        $data['unit']=$request->unit;
        $data['intro']=0;//暂时为0
        $data['desc']=$request->intro_spec;
        $data['create_time']=$time;
        //虚拟商品
        if($data['goods_type']==0){
            //虚拟商品添加
            $res=DB::table('goods')->insert($data);
            if($res){
                $success=1;
                return $success;
            }else{
                $error=0;
                return $error;
            }
        }else{//实物
            //规格参数
            $ys_val=explode(',',$request->ys_val);
            $cm_val=explode(',',$request->cm_val);
            foreach ($ys_val as $k => $v) {
                $ys_val[$k]=explode('|',$v);
                $cm_val[$k]=explode('|',$cm_val[$k]);
            }
            //商品添加
            $res=DB::table('goods')->insertGetId($data);
            if($res){
                //goods_product
                $goods_id=$res;
                $product_sn=$data['goods_sn'];
                $p=explode(',',$request->kucun_val);
                $product_store=explode(',',$request->kucun_val);
                $product_score=explode(',',$request->mprice_val);
                $create_time=time();
                foreach ($p as $k => $v) {
                    $product_id=DB::table('goods_products')->insertGetId(['goods_id'=>$goods_id,'product_sn'=>$product_sn,'product_store'=>$product_store[$k],'product_score'=>$product_score[$k],'create_time'=>$create_time]);
                    $arr=DB::table('goods_spec_index')->insert(['goods_id'=>$goods_id,'product_id'=>$product_id,'spec_id'=>$ys_val[$k][0],'spec_value_id'=>$ys_val[$k][1]]);
                    $brr=DB::table('goods_spec_index')->insert(['goods_id'=>$goods_id,'product_id'=>$product_id,'spec_id'=>$cm_val[$k][0],'spec_value_id'=>$cm_val[$k][1]]);
                }
            }else{
                $error=0;
                return $error;
            }
        }
        
    }

    //编辑
    public function edit(Request $request)
    {
        $id = $request->id;
        if($request->isMethod('post')){
            $time = time();
            try{
                $data = $request->except('_token');  //dd($data);
                $goods_data['update_time'] = $time;
                $goods_data['indexpic'] = $data['pictures'][0];
                $goods_data['kucun'] = $data['kucun'];
                $goods_data['price'] = $data['price'];
                $goods_data['memprice'] = $data['memprice'];
                $goods_data['pictures'] = serialize($data['pictures']);
                $goods_data['goods_sn'] = $data['goods_sn'];
                $goods_data['title'] = $data['title'];
                $goods_data['alias'] = $data['alias'];
                $goods_data['intro'] = $data['intro'];

                if($data['cat3']){
                    $goods_data['cat_id'] = $data['cat3'];
                }elseif($data['cat2']){
                    $goods_data['cat_id'] = $data['cat2'];
                }elseif($data['cat1']){
                    $goods_data['cat_id'] = $data['cat1'];
                }
                foreach($data['attr'] as $k=>$v){
                    if(!empty($v) && !empty($data['vals'][$k])){
                        $attr_arr[] = ['attr'=>$v,'val'=>$data['vals'][$k]];
                    }
                }
                $goods_data['attr'] = serialize($attr_arr);
                \DB::beginTransaction();
                $goods = \DB::table('goods')->where(['goods_id'=>$data['goods_id']])->update($goods_data);

                foreach($data['kc'] as $k=>$v){
                    $product_id = $data['product_id'][$k];
                    if($product_id){//更新

                        $product_datas['kc'] = $v;
                        $product_datas['jg'] = $data['jg'][$k];
                        $product_datas['ys'] = $data['ys'][$k];
                        $product_datas['cm'] = $data['cm'][$k];
                        $product_datas['update_time'] = $time;

                        $product = \DB::table('goods_products')->where(['product_id'=>$product_id])->update($product_datas);
                    }else{//新增

                        $product_datas['kc'] = $v;
                        $product_datas['jg'] = $data['jg'][$k];
                        $product_datas['ys'] = $data['ys'][$k];
                        $product_datas['cm'] = $data['cm'][$k];
                        $product_datas['product_sn'] = $goods_data['goods_sn'];
                        $product_datas['create_time'] = $time;
                        $product = \DB::table('goods_products')->insert($product_datas);
                    }
                }



                if($goods && $product){
                    \DB::commit();
                    return redirect('admin/goods/index')->withSuccess('操作成功！');
                }else{
                    \DB::rollback();
                    return redirect()->back()->withInput()->withErrors('操作失败！');
                }

            }catch (\Exception $e){
                return redirect()->back()->withInput()->withErrors($e->getMessage());
            }
        }

        $data = $this->model->where(['goods_id'=>$id])->first();
        $data['products'] = \DB::table('goods_products')->where(['product_sn'=>$data->goods_sn])->get();
        $data['cats'] =  \DB::table('goods_cat')->whereRaw("disabled = 0 and pid = 0")->get();
        $data['pictures'] = unserialize($data->pictures);
        $data['attr'] = unserialize($data->attr);
        return view('admin.goods.edit',$data);
    }

    //正常或禁用
    public function changeStatus(Request $request)
    {
        try{
            $goods_id = $request->goods_id;
            $status = $request->status ? 0 : 1;
            $this->model->where(['goods_id'=>$goods_id])->update(['status'=>$status,'update_time'=>time()]);
            return redirect()->back()->withSuccess('操作成功!');
        }catch (\Exception $e){
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }


    }
    //上下架
    public function sjStatus(Request $request)
    {
        try{
            $goods_id = $request->goods_id;
            $sj = $request->sj ? 0 : 1;
            $this->model->where(['goods_id'=>$goods_id])->update(['sj'=>$sj,'update_time'=>time()]);
            return redirect()->back()->withSuccess('操作成功!');
        }catch (\Exception $e){
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }


    }

    public function skuStatus(Request $request){
         $product_id = $request->product_id;
         $status = $request->status?0:1;
         $res = \DB::table('goods_proudcts')->where(['id'=>$product_id])->update(['status'=>$status,'update_time'=>time()]);
       // echo  $res ? 1 : 0;
         //dd($res);
        return response()->json(array(
            'status' => $res?1:0,
            'msg' => 'ok',
        ));
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





}