<?php

namespace App\Http\Controllers\Api\V1;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Controllers\Api\Wechat\ErrMsg as Msg;
use App\Models\Admin\UsersModel;
use App\Models\Admin\RankingModel;
use App\Models\Admin\LikesModel;
use App\Models\Admin\FreezeModel;
use App\Models\Admin\FinanceModel;
use App\Models\Admin\FollowModel;
class StarController extends ApiBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->usersModel = new UsersModel;
        $this->rankingModel = new RankingModel;
        $this->likesModel = new LikesModel;
        $this->freezeModel = new FreezeModel;
        $this->financeModel = new FinanceModel;
        $this->followModel = new FollowModel;
    }

    //列表
    public function index(Request $request)
    {
        $page = $request->page ? $request->page : 1;//分页
        $size = $request->size ?  $request->size : $this->page_size;//分页取记录数
        $keywords = $request->keywords ? $request->keywords : '';//关键词

        $where = "  level >= 0  and hot_images !=''  ";
        if($keywords){
            $where.=" and concat(username,mobile) like '%{$keywords}%' ";
        }
        $data = $this->usersModel->whereRaw($where)->select('id','level','username','mobile','score','freeze_score','hot_intro','hot_video','hot_images')->orderBy('level','desc')->paginate($size)->toArray();
        $result = ['data'=>[]];
        if(!empty($data['data'])){
            foreach($data['data'] as $k=>$v){
                $res[$k]['id'] = $v['id'];
                $res[$k]['level'] = $v['level'];
                $res[$k]['username'] = $v['username'];
                $res[$k]['mobile'] = $v['mobile'];
                $res[$k]['score'] = $v['score'];
                $res[$k]['freeze_score'] = $v['freeze_score'];
                $res[$k]['hot_intro'] = $v['hot_intro'];
                $res[$k]['hot_video'] = $v['hot_video'];
                $images = empty($v['hot_images']) ? []: unserialize($v['hot_images']);

                if(!empty($images)){
                    foreach($images as $key=>$val){
                        $res[$k]['hot_images'][$key] = $this->base_url.$val;
                    }
                }else{
                    $res[$k]['hot_images'] = [];
                }

                $res[$k]['hot_video'] = empty($v['hot_video'])?'':$this->base_url.$v['hot_video'];

            }
            $result =[
                'total'=>$data['total'],
                'count'=>count($data['data']),
                'page'=>$data['current_page'],
                'size'=>$data['per_page'],
                'last'=>$data['last_page'],
                'data'=>$res,
            ];
        }
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$result]);
    }

    //红人详情
    public function detail(Request $request){

        $id = $request->id ? $request->id : 0;
        $user_id = $request->user_id ? $request->user_id :0;
        if(!$id){//参数丢失
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        $user = $this->usersModel->where(['id'=>$id])->first();

        if(empty($user)){//红人不存在
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        $user = $user->toArray();
        $data['user_id'] = $user['id'];//红人ID
        $data['ranking'] = $this->usersModel->whereRaw("likes > {$user['likes']} ")->count('id')+1;//当前的排名
        $data['total_star'] = $this->usersModel->whereRaw("level > 0")->count('id');//等级大于1红人总数
        $data['beat_star'] = ($data['total_star'] - $data['ranking']) / $data['total_star'] * 100;//打败红人百分比

        //处理排名
        $toDay = date('Y-m-d');
        $yesterDay = date('Y-m-d',time() - 86400);
        $todayPosition = $this->rankingModel->where(['user_id'=>$id,'date'=>$toDay])->select('position')->first();//今天排名
        $yesterPosition = $this->rankingModel->where(['user_id'=>$id,'date'=>$yesterDay])->select('position')->first();//昨天排名

        if(!empty($yesterPosition->position)) {
            if($yesterPosition->position > $data['ranking']) {//昨天排名大于当前排名
                $data['trend'] = 'up';
                $data['position'] = $yesterPosition->position - $data['ranking'];
            }else{//昨天排名小于当前排名
                $data['trend'] = 'down';
                $data['position'] = $data['ranking'] - $yesterPosition->position;
            }
        }else{
            $data['trend'] = 'up';
            $data['position'] = 0;
        }
        if(empty($todayPosition->position)) {//数据库无今日排名，则插入
            $res = $this->rankingModel->insert([ 'date' => $toDay,'user_id' => $id,'position' => $data['ranking']]);

        }else{
            if($data['ranking'] != $todayPosition->position){//当前排名和今日排名不相同
                $res = $this->rankingModel->where(['date' => $toDay, 'user_id' => $id])->update(['position'=>$data['ranking']]);
            }
        }
        $data['username'] = $user['username'];
        $data['mobile'] = $user['mobile'];
        if(!empty($user['avatar'])){
            if(substr($user['avatar'],0,4) == 'http'){
                $data['avatar'] = $user['avatar'];//头像
            }else{
                $data['avatar'] = $this->base_url.$user['avatar'];//头像
            }
        }else{
            $data['avatar'] = '';//无头像
        }
        $data['sex'] = $user['sex'];
        $data['constellation'] = $user['constellation'];
        $data['score'] = $user['score'];
        $data['likes'] = $user['likes'];
        $data['next_level_likes'] = ($user['level']>=5)?0:$this->level[$user['level']+1]['likes'];
        $data['upgrade_need_likes'] = $data['next_level_likes']-$data['likes'];
        $data['follow'] = $user['follow'];
        $data['fans'] = $user['fans'];
        $data['level'] = $user['level'];
        $follow = $this->followModel->where(['user_id'=>$user_id,'star_id'=>$user['id']])->first();
        $data['is_collect'] = empty($follow) ? 0 : 1;//0：未关注；1：已关注
        $data['hot_intro'] = $user['hot_intro'];
        $data['hot_video'] = empty($user['hot_video'])?'':$this->base_url.$user['hot_video'];
        $user['hot_images'] = unserialize($user['hot_images']);
        if(!empty($user['hot_images'])){
            foreach($user['hot_images'] as $key=>$val){
                $data['hot_images'][$key] = $this->base_url.$val;
            }
        }else{
            $data['hot_images'] = [];
        }
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$data]);

    }


    //红人点赞
    public function like(Request $request)
    {
        $star_id = $request->star_id ? $request->star_id : 0;
        $user_id = $request->user_id ? $request->user_id : 0;
        if(!$star_id || !$user_id) {
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        if($star_id == $user_id) {
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_cannotLikeSelf),'code'=>Msg::$err_cannotLikeSelf,'result'=>'不能给自己点赞']);
        }

        $user = $this->usersModel->where(['id'=>$user_id])->first();

        if(empty($user)){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noUser),'code'=>Msg::$err_noUser,'result'=>'用户不存在']);
        }
        if($user->score < 1){
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noScore),'code'=>Msg::$err_noScore,'result'=>'红人圈余额不足']);
        }
        \DB::beginTransaction();
        try {
            // 用户支出记录，点赞支出
            $this->financeModel->insert([
                'user_id' => $user_id,
                'type' => 2,
                'number' => -1,
                'action' => 1,
                'note' => '点赞支出',
                'create_time'=> time()
            ]);
            $this->usersModel->where(['id'=>$user_id])->decrement('score',1);//投票支出
            $this->usersModel->where(['id'=>$star_id])->increment('likes',1,['freeze_score'=>\DB::raw('freeze_score+1')]);//增加冻结金额
            $this->likesModel->insert(['star_id'=>$star_id,'user_id'=>$user_id,'create_time'=>time()]);
            $time = time();
            $thaw_time = $time + (86400 * 7);//冻结7天

            $this->freezeModel->insert(['type'=>1,'user_id'=>$star_id,'number'=>1,'status'=>0,'thaw_time'=>$thaw_time,'create_time'=>$time]);//冻结类型 1点赞 2推荐 3投票

            \DB::commit();
            // $UserService->upgradeLevel($star_id);//升级
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>'点赞成功']);
        } catch (\Exception $e) {
            \DB::rollback();
        }
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_failedLike),'code'=>Msg::$err_failedLike,'result'=>'点赞失败']);
    }

    //关注
    public function follow(Request $request)
    {
        $star_id = $request->star_id ? $request->star_id : 0;
        $user_id = $request->user_id ? $request->user_id : 0;
        if(!$star_id || !$star_id) {
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        if($star_id == $user_id) {
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_cannotFollowSelf),'code'=>Msg::$err_cannotFollowSelf,'result'=>'不能自己关注自己']);
        }
        $count = $this->followModel->where(['user_id'=>$user_id,'star_id'=>$star_id])->count('id');

        if($count){//取消关注
            $result = $this->followModel->where(['user_id'=>$user_id,'star_id'=>$star_id])->delete();

            if($result) {
                $this->usersModel->where(['id'=>$star_id])->decrement('fans',1);//红人粉丝-1
                $this->usersModel->where(['id'=>$user_id])->decrement('follow',1);//用户关注-1

                return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>'取消关注成功']);


            }else{
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_failedCancelFollow),'code'=>Msg::$err_failedCancelFollow,'result'=>'取消关注失败']);

            }
        }else{//添加关注
            $result = $this->followModel->insert(['star_id'=>$star_id,'user_id'=>$user_id,'create_time'=>time()]);

            if($result){
                $this->usersModel->where(['id'=>$star_id])->increment('fans',1);//红人粉丝+1
                $this->usersModel->where(['id'=>$user_id])->increment('follow',1);//用户关注+1
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>'关注成功']);

            }else{
                return response()->json(['msg'=>Msg::getMsg(Msg::$err_failedFollow),'code'=>Msg::$err_failedFollow,'result'=>'关注失败']);

            }
        }
    }


    //搜索红人
    public function search(Request $request)
    {
        $page = $request->page ? $request->page : 1;//分页
        $size = $request->size ?  $request->size : $this->page_size;//分页取记录数
        $keywords = $request->keywords ? $request->keywords : '';//关键词
        $user_id = $request->user_id ? $request->user_id : 0;
        if(empty($keywords)) {
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        $data = $this->usersModel
            ->whereRaw("concat(username,mobile) like '%{$keywords}%'")
            ->select('id','username','mobile','avatar','level')
            ->paginate($size)
            ->toArray();

        $result = ['data'=>[]];
        if(!empty($data['data'])){
            foreach($data['data'] as $k=>$v){

                if(!empty($v['avatar'])){
                    if(substr($v['avatar'],0,4) == 'http'){
                        $data['data'][$k]['avatar'] =  $v['avatar'];//头像
                    }else{
                        $data['data'][$k]['avatar'] =  $this->base_url.$v['avatar'];//头像
                    }
                }else{
                    $data['data'][$k]['avatar'] =  '';//无头像
                }
            }
            $result =[
                'total'=>$data['total'],
                'count'=>count($data['data']),
                'page'=>$data['current_page'],
                'size'=>$data['per_page'],
                'last'=>$data['last_page'],
                'data'=>$data['data'],
            ];
        }

        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$result]);


    }


    //排行榜
    public function ranking(Request $request)
    {
        $type = $request->param('type','income');
        $time = $request->param('time','day');
        $start = [];
        $start['day'] = Time::today()[0];
        $start['week'] = Time::week()[0];
        $start['month'] = Time::month()[0];
        if($type == "income") {
            $joinId = 'likes.star_id';
        }else{
            $joinId = 'likes.user_id';
        }
        $data = LikesModel::where('likes.create_time', '>=', $start[$time])
            ->field('user.id, user.nickname, user.avatar, count(likes.id) as nums')
            ->join('user', 'user.id = '.$joinId, 'left')
            ->group($joinId)
            ->order('nums desc')
            ->limit(20)
            ->select();
        return $this->json(0, 'SUCCESS', $data);
    }

    //邀请
    public function invite(Request $request)
    {
        $user_id = $request->user_id ? $request->user_id : 0;
        $type = $request->type ? $request->type : 0;//类型（0：红人；1：众筹；2：投票）
        $resource_id = $request->resource_id ? $request->resource_id : 0;//资源id
        if(!$user_id || !isset($type) || !$resource_id) {
            return response()->json(['msg'=>Msg::getMsg(Msg::$err_noParameter),'code'=>Msg::$err_noParameter]);
        }
        $data = ['title'=>$this->invite_title,'desc'=>$this->invite_desc,'logo'=>$this->invite_logo,'url'=>$this->invite_url];
        return response()->json(['msg'=>Msg::getMsg(Msg::$err_none),'code'=>Msg::$err_none,'result'=>$data]);
    }
}
