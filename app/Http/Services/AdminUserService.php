<?php

namespace App\Http\Services;
class AdminUserService extends BaseService
{

    // 更新数据验证
    protected $update_rules = [
        'id'         => 'required|integer',  //必填  整型
        'name'        => 'required|min:2|max:50',  //必填 最小2位 最大16位
        'email'      => 'required|string|email|max:255', //必填 字符串
        'oldPwd'  => 'required|string', //必填 字符串
        'newPwd'  => 'required|string|confirmed|min:6|max:50', //必填 字符串
    ];

    // 提示信息
    protected $msgs = [
        'digits'   => ':attribute必须32位',
        'required' => ':attribute不能为空',
        'string'   => ':attribute格式错误',
        'integer'  => ':attribute必须为数字',
        'unique'   => ':attribute已经存在',
        'min'      => ':attribute最少:min字符',
        'max'      => ':attribute最多:max字符',
        'between'  => ':attribute必须在:min到:max之间',
        'integer'  => ':attribute必须为整型数字',
        'regex'    => ':attribute必须为手机格式',
    ];

    // 自定义字段名称
    protected $titles = [
        'name'   => '昵称',
        'email' => '邮箱',
        'oldPwd' => '旧密码',
        'newPwd' => '新密码',
    ];

    //构造函数
    public function __construct()
    {
    }



    /**
     * 更新密码
     * @param array $data
     * @return bool
     */
    public function resetPwd($data = [])
    {
        // 验证器
        $validator = \Validator::make($data,$this->update_rules,$this->msgs,$this->titles);
        if($validator->fails()){//验证字段失败
            throw new \Exception($validator->errors()->first(), -1);
        }

        if($data['oldPwd'] == $data['newPwd']){
            throw new \Exception('新密码和旧密码不能一样', -1);
        }

        $res = \DB::table('admin_users')->where(['id'=>$data['id']])->first();
        if(!\Hash::check($data['oldPwd'], $res->password)){
            throw new \Exception('旧密码输入错误', -1);
        }

        $data['password'] = bcrypt($data['newPwd']);
        unset($data['oldPwd'],$data['newPwd'],$data['newPwd_confirmation']);
        return \DB::table('admin_users')->where(['id'=>$data['id']])->update($data);
    }

}




