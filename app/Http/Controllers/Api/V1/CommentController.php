<?php

namespace App\Http\Controllers\Api\V1;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Controllers\Api\Wechat\ErrMsg as Msg;
use App\Models\Admin\CommentModel as commentModel;
class CommentController extends ApiBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->commentModel = new commentModel;
    }

    //列表
    public function index(Request $request)
    {
        $page = $request->page ? $request->page : 1;//分页
        $size = $request->size ?  $request->size : $this->page_size;//分页取记录数
        $keywords = $request->keywords ? $request->keywords : '';//关键词
        $type = $request->type ? $request->type : 0;//类型（0:红人；1：众筹；2：投票）
        $resource_id = $request->resource_id ? $request->resource_id :0;


        $data = $this->commentModel->join('users','users.id','=','users_comment.user_id')->select('users_comment.*','users.username','users.avatar','users.mobile')->where(['type'=>$type,'resource_id'=>$resource_id])->orderBy('id','desc')->paginate($size)->toArray();

        if(!empty($data['data'])){
            foreach($data['data'] as $k=>$v){
                    $res[$k]['id'] = $v['id'];
                    $res[$k]['content'] = $v['content'];
                    $res[$k]['create_time'] = $v['create_time'];
                    $res[$k]['update_time'] = $v['update_time'];
                    $res[$k]['username'] = $v['username'];
                    $res[$k]['mobile'] = $v['mobile'];
                if($v['avatar']){
                    if(substr($v['avatar'],0,4) == 'http'){
                        $res[$k]['avatar'] = $v['avatar'];//头像
                    }else{
                        $res[$k]['avatar'] = $this->base_url.$v['avatar'];//头像
                    }
                }else{
                    $res[$k]['avatar'] = '';//无头像
                }


            }
        }else{
            $res = [];
        }
        $result =[
            'total'=>$data['total'],
            'count'=>count($data['data']),
            'page'=>$data['current_page'],
            'size'=>$data['per_page'],
            'last'=>$data['last_page'],
            'data'=>$res,
        ];
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$result]);
    }

        //添加
        public function add(Request $request){

                $data = $request->only('user_id','type','resource_id','content');
                if(!$data['user_id'] || !isset($data['type']) || !$data['resource_id']|| !$data['content']){//参数丢失
                    return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
                }
                $data['create_time'] = time();
                $comment = $this->commentModel->insert($data);
                if($comment){//评论成功
                    return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none]);
                }else{//评论失败
                    return response()->json(['msg'=>Msg::getMsg(Msg::$err_failedComment),'code'=>Msg::$err_failedComment]);
                }
        }

}
