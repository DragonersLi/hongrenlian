<?php

namespace App\Http\Controllers\Admin;
use App\Models\Admin\Role;
use App\Models\Admin\AdminUser as User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\BaseController;
use App\Models\Admin\GoodsCatModel;
//商品分类管理
class GoodsCatController extends BaseController
{


    public function __construct()
    {
        $this->model = new GoodsCatModel();
    }

    /**
     * 列表
     */
    public function index(Request $request)
    {

        $sex = $request->sex;
        $is_hot = $request->is_hot;
        $keywords = $request->keywords;
        $page = $request->page;
        $where = " 1=1 ";
        $pages['page'] = isset($page) ? $page : 1;
        if(isset($sex) && $sex>=0){//性别
            $pages['sex'] = $sex;
            $where .= " and sex = {$sex}";
        }
        if(isset($is_hot) && $is_hot>=0){//热门
            $pages['is_hot'] = $is_hot;
            $where .= " and is_hot = {$is_hot}";
        }


        if(!empty($keywords)){//用户名，真实姓名，手机号，邮箱，qq号
            $pages['keywords'] = $keywords;
            $where .= " and ( title like '%{$keywords}%'   ) ";
        }

        $links = $this->model->whereRaw($where)->orderBy('id','desc')->paginate($this->page_size)->appends($pages);



        $data['data'] = $links;
        $data['page'] = $pages;
        return view('admin.goods_cat.index',$data);
    }


    //添加
    public function add(Request $request)
    {
        if($request->isMethod('post')){
            try{
                $data = $request->except('_token','file','id');
                $data['create_time'] = time();
                if($data['cat1'] && $data['cat2']){
                    $data['pid'] = $data['cat2'];
                    $data['cat_path'] = ','.$data['cat1'].','.$data['cat2'];
                }elseif($data['cat1'] && !$data['cat2']){
                    $data['pid'] = $data['cat1'];
                    $data['cat_path'] = ','. $data['pid'];
                }
                unset($data['cat1'],$data['cat2']);
               // dd($data);
                \DB::table('goods_cat')->insert($data);
                return redirect('admin/goods_cat/index')->withSuccess('操作成功!');
            }catch (\Exception $e){
                return redirect()->back()->withInput()->withErrors($e->getMessage());
            }
        }

        $data['cats'] = \DB::table('goods_cat')->whereRaw("disabled = 0 and pid = 0")->get();
        return view('admin.goods_cat.add',$data);
    }

    //编辑
    public function edit(Request $request)
    {
        $id = $request->id;
        if($request->isMethod('post')){
            try{
                $data = $request->except('_token','file');
                $data['update_time'] = time();
                if($data['cat1'] && $data['cat2']){
                    $data['pid'] = $data['cat2'];
                    $data['cat_path'] = ','.$data['cat1'].','.$data['cat2'];
                }elseif($data['cat1'] && !$data['cat2']){
                    $data['pid'] = $data['cat1'];
                    $data['cat_path'] = ','. $data['pid'];
                }
                unset($data['cat1'],$data['cat2']);//dd($data);
                \DB::table('goods_cat')->where(['id'=>$data['id']])->update($data);
                return redirect('admin/goods_cat/index')->withSuccess('操作成功!');
            }catch (\Exception $e){
                return redirect()->back()->withInput()->withErrors($e->getMessage());
            }
        }

        $data = $this->model->where(['id'=>$id])->first();

        $cats = \DB::table('goods_cat')->whereRaw("disabled = 0 and pid = 0")->get();
        $data['cats']  = $cats;
        $cat_path = $data->cat_path;
        $arr = explode(',',$cat_path);
        if(count($arr) == 3){
            $data['cat1'] = $arr[1];
            $data['cat2'] = $arr[2];

        }elseif(count($arr) == 2){
            $data['cat1'] = $arr[1];
        }
        return view('admin.goods_cat.edit',$data);
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
        return $message = $this->uploadFile('goods_cat');
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
}