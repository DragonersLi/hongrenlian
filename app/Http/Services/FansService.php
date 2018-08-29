<?php

namespace App\Http\Services;
use App\Models\Admin\FansModel;
class FansService extends BaseService
{
    private $default_password = '123456';//添加粉丝，默认密码
    //添加或修改form表单字段
    public $fields = [
        'id'                 => 0,
        'name'               => '',
        'truename'           => '',
        'mobile'             => '',
        'email'              => '',
        'qq'                 => '',
        'sex'                => 0,
        'birthday'           => '',
        'contacts_name'     => '',
        'contacts_mobile'   => '',
        'avatar'             => '',
    ];
    // 插入数据验证
    protected $insert_rules = [

        'name'        => 'required|min:2|max:50',  //必填 最小2位 最大16位
        'truename'   => 'required|string', //必填 字符串
        'mobile'     => 'required|regex:/^1[34578][0-9]{9}$/', //必填 string 手机号格式
        'email'      => 'required|string', //必填 字符串
        'qq'         => 'required|string', //必填 字符串
        'sex'        => 'required|integer|between:0,2', //必填 数字 最大2
        'birthday'  => 'required|string', //必填 字符串
        'avatar'    => 'required|string', //必填 字符串
        'password'  => 'required|string', //必填 字符串
    ];
    // 更新数据验证
    protected $update_rules = [
        'id'         => 'required|integer',  //必填 最小2位 最大16位
        'name'       => 'required|min:2|max:50',  //必填 最小2位 最大16位
        'truename'  => 'required|string', //必填 字符串
        'mobile'    => 'required|regex:/^1[34578][0-9]{9}$/', //必填 string 手机号格式
        'email'     => 'required|string', //必填 字符串
        'qq'        => 'required|string', //必填 字符串
        'sex'       => 'required|integer|between:0,2', //必填 数字 最大2
        'birthday' => 'required|string', //必填 字符串
        'avatar'   => 'required|string', //必填 字符串
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
        'mobile' => '手机号',
        'email' => '邮箱',
        'qq' => 'QQ',
        'password' => '密码',
        'truename' => '真实姓名',
        'avatar' => '头像',
        'birthday' => '出生日期',
        'sex' => '性别',
    ];

    //构造函数
    public function __construct()
    {

    }

    /**
     * 验证并插入数据库
     * @param array $data
     * @return bool
     */
    public function insertData($data = [])
    {
        $time = date('Y-m-d H:i:s');
        $data['password'] = md5($this->default_password);
        $data['create_time'] = $time;
        $data['update_time'] = $time;
        // 验证器
        $validator = \Validator::make($data,$this->insert_rules,$this->msgs,$this->titles);
        if($validator->fails()){//验证字段失败
            throw new \Exception($validator->errors()->first(), -1);
        }
        return \DB::table('fans')->insert($data);
    }
    /**
     * 验证并更新数据库
     * @param array $data
     * @return bool
     */
    public function updateData($data = [])
    {
        // 验证器
        $validator = \Validator::make($data,$this->update_rules,$this->msgs,$this->titles);
        if($validator->fails()){//验证字段失败
            throw new \Exception($validator->errors()->first(), -1);
        }
        $data['update_time'] = date('Y-m-d H:i:s');
        return \DB::table('fans')->where(['id'=>$data['id']])->update($data);
    }

    /**
     * 数据库取数据
     * @param array $data
     * @return bool
     */
    public function selectData($id){

        \DB::connection()->enableQueryLog();  // 开启QueryLog
        FansModel::get();
        dump(\DB::getQueryLog());
    }
}




