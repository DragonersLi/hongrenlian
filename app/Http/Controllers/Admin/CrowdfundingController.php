<?php
namespace App\Http\Controllers\Admin;
use App\Models\Admin\Role;
use App\Models\Admin\AdminUser as User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\BaseController;
use App\Models\Admin\CrowdfundingModel;
use App\Models\Admin\CrowdfundingDetailModel;
class CrowdfundingController extends BaseController
{

    public $status_type = [

      1=>'未开始',
      2=>'进行中',
      3=>'已成功',
      4=>'未成功',
     // 5=>'已关闭'

    ];
    public function __construct()
    {
        $this->model = new CrowdfundingModel();
        $this->detailModel = new CrowdfundingDetailModel();


    }
    /**
     * 众筹列表
     */
    public function index(Request $request)
    {


        $status_type = $request->status_type;
        $keywords = $request->keywords;
        $page = $request->page;

        $where = " 1=1 ";
        $pages['page'] = isset($page) ? $page : 1;
        $datetime = time();
        switch ($status_type) {
            case 1: // 未开始
                $pages['status_type'] = 1;
                $where .= " and '{$datetime}' < start_time";
                break;
            case 2: // 进行中
                $pages['status_type'] = 2;
                $where.= " and '{$datetime}' between start_time and end_time";
                break;
            case 3: // 已成功
                $pages['status_type'] = 3;
                $where.= " and status = 1 ";
                break;
            case 4: // 未成功
                $pages['status_type'] = 4;
                $where.=" and status = 0 and '{$datetime}' > end_time";
                break;
        }



        if(!empty($keywords)){//活动标题，投票和被投票中奖标题
            $pages['keywords'] = $keywords;
            $where .= " and ( title like '%{$keywords}%' ) ";
        }

        $links = $this->model->whereRaw($where)->orderBy('id','desc')->paginate($this->page_size)->appends($pages);
        $data['data'] = $links;
        $data['page'] = $pages;
        $data['status_type'] = $this->status_type;
        return view('admin.crowdfunding.index',$data);
    }

    //添加
    public function add(Request $request)
    {
        if($request->isMethod('post')){
            try{
                $data = $request->except('_token','file');
                $rangedatetime = explode('~',$data['rangedatetime']);
                $data['start_time'] = strtotime($rangedatetime[0]);
                $data['end_time'] = strtotime($rangedatetime[1]);
                unset($data['rangedatetime']);
                $data['status'] = 1;//  启用
                $data['create_time'] =  time();
                $this->model->insertGetId($data);
                return redirect('admin/crowdfunding/index')->withSuccess('操作成功!');
            }catch (\Exception $e){
                return redirect()->back()->withInput()->withErrors($e->getMessage());
            }
        }

        return view('admin.crowdfunding.add',$data = []);
    }
    //编辑活动
    public function edit(Request $request)
    {

        if($request->isMethod('post')){
            try{
                $data = $request->except('_token','file');
                $range = $data['rangedatetime'];
                if(!empty($range)){
                    $rangedatetime = explode('~',$range);
                    $data['start_time'] = strtotime($rangedatetime[0]);
                    $data['end_time'] = strtotime($rangedatetime[1]);
                }
                unset($data['rangedatetime']);
                $data['update_time'] =  time();
                $this->model->where(['id'=>$data['id']])->update($data);
                return redirect('admin/crowdfunding/index')->withSuccess('操作成功!');
            }catch (\Exception $e){
                return redirect()->back()->withInput()->withErrors($e->getMessage());
            }
        }
        $id = $request->id;
        $data = $this->model->where(['id'=>$id])->first()->toArray();
        $data['rangedatetime'] = $data['start_time'].' ~ '.$data['end_time'];
        return view('admin.crowdfunding.edit',$data);
    }


    //冻结或解冻
    public function changeStatus(Request $request)
    {
        try{
            $id = $request->id;
//          if(in_array($request->status,[0,1])){
                $status =($request->status == 9)? 0 : 9;
                $this->model->where(['id'=>$id])->update(['status'=>$status]);
                return redirect()->back()->withSuccess('操作成功!');
//            }else{
//                return redirect()->back()->withErrors('状态不允许更改!');
//            }

        }catch (\Exception $e){
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }
    }


    //查看
    public function detail(Request $request)
    {
        $id = $request->id;
        $page = $request->page;
        $data['data'] =  \DB::table('crowdfunding_detail')
            ->leftjoin('users','users.id','=','crowdfunding_detail.uid')
            ->select('crowdfunding_detail.*','users.username','users.mobile')
            ->where(['cid'=>$id])->orderBy('id','desc')
            ->paginate($this->page_size);
        return view('admin.crowdfunding.detail',$data);
    }


    //上传图片
    public function upload(Request $request)
    {
        return $message = $this->uploadFile('crowdfunding');
    }

}
