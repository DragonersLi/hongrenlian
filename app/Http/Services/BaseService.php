<?php

namespace App\Http\Services;
//服务基类
class BaseService
{
    //score红人圈
    private $score_type = [
        1=>[
            'type'=>'reg',
            'value'=>5,
            'message'=>'注册送红人圈'
        ],
        2=>[
            'type'=>'login',
            'value'=>1,
            'message'=>'登陆送红人圈'
        ],
        3=>[
            'type'=>'sign',
            'value'=>1,
            'message'=>'签到送红人圈'
        ],
        4=>[
            'type'=>'invite',
            'value'=>10,
            'message'=>'邀请送红人圈'
        ],
        5=>[
            'type'=>'vote',
            'value'=>-1,
            'message'=>'投票减红人圈'
        ],

    ];


}




