<?php

namespace App\Http\Controllers\Admin;
use App\Models\Admin\Role;
use App\Models\Admin\AdminUser as User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\BaseController;
//商品规格管理
class GoodsSpecController extends BaseController
{


    public function __construct()
    {
    }

    /**
     * 列表
     */
    public function index(Request $request)
    {
        $pages['page'] = isset($page) ? $page : 1;
        $links = \DB::table('goods_spec')->orderBy('spec_id','desc')->paginate($this->page_size)->appends($pages);
            //select(\DB::raw('GROUP_CONCAT(DISTINCT spec_value) as spec_values') )

        $data['data'] = $links;
        $data['page'] = $pages;
        return view('admin.goods_spec.index',$data);
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
        return $message = $this->uploadFile('goods_spec');
    }


}