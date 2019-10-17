<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | fjwcoder<fjwcoder@gmail.com>                           |
// +---------------------------------------------------------------------+
// | Repository |   |
// +---------------------------------------------------------------------+

namespace app\index\logic;

use think\Db;
/**
 * Index基础逻辑
 */
class Pregnant extends IndexBase
{
    private $pregnant_category_id = 3;
    /**
     * 获取孕妇信息
     */
    public function getPregnantUser($param = []){
        // 先看有没有录入孕妇信息
        return $this->modelPregnantUser->getInfo(['openid'=>$param['openid']]);

    }

    /**
     * 获取检查内容
     * 
     */
    public function getPregnantCheckArticle($week){
        // return $this->modelPregnantCheck->getInfo(['week'=>$week]);
        return $this->modelArticle->getInfo(['category_id'=>$this->pregnant_category_id, 'week'=>$week]);
    }

    /**
     * 根据预产期，计算体检的时间
     */
    public function calCheckDate($pre_birth){
        $remind_icon = '<i class="fa fa-heartbeat"></i>';
        $remind = [];
        // 查出体检的内容
        $check_list = $this->modelArticle->getList(['category_id'=>$this->pregnant_category_id, 'status'=>1], 'id, week');
        // dump($check_list); die;
        // 1. 根据预产期往前推40周
        $end_time = strtotime($pre_birth); // 预产期时间戳
        $start_time = $end_time - 24192000; // 怀孕期时间戳
        // $list_count = count($check_list);
        foreach($check_list as $k=>$v){
            $remind_date = date("Y-m-d",strtotime("+$v[week] week", $start_time));
            $remind[] = ($v['week'] < 40)?['startDate'=>$remind_date, 'name'=>$remind_icon, 'week'=>$v['week']]:['startDate'=>$remind_date, 'name'=>'分娩', 'week'=>$v['week']];

        }
        // dump($remind); die;
        return $remind;
    }

    /**
     * 根据预产期计算体检时间表和疫苗时间表
     */
    private function calDateByPreBirth($pre_birth){

        $check_list = $this->modelArticle->getList(['category_id'=>$this->pregnant_category_id, 'status'=>1], 'name, week', 'id', false);
        $end_time = strtotime($pre_birth);
        $begin_time = $end_time - 24192000; // 预产期 - 40周的秒数 = 怀孕时间
        $week_second = 604800; // 每周的秒数

        $check_date_list = '';
        foreach($check_list as $k=>$v){
            $time = $begin_time + $week_second * $v['week'];
            $check_date_list .= date('Y-m-d', $time).';';
        }

        return $check_date_list;
    }


    /**
     * 添加孕妇信息
     */
    public function saveUserInfo($param, $user){
        
        $check_date_list = $this->calDateByPreBirth($param['pre_birth']);
// dump($check_date_list); die;

        $data = [
            'user_id'=>$user['user_id'], 'openid'=>$user['wx_openid'],
            'real_name'=>$param['real_name'],'pre_birth'=>$param['pre_birth'],
            'mobile'=>$param['mobile'], 'sex'=>2, 'birthday'=>$param['birthday'],
            'address'=>'', 'headimgurl'=>$user['headimgurl'], 'status'=>1,
            'check_date_list'=>empty($check_date_list)?'':$check_date_list
        ];
// dump($data); die;
        return $this->modelPregnantUser->setInfo($data);
    }

    /**
     * 编辑孕妇信息
     */
    public function editUserInfo($param){

        $check_date_list = $this->calDateByPreBirth($param['pre_birth']);

        $where = ['openid'=>$param['openid']];
        $data = [
            'real_name'=>$param['real_name'],
            'pre_birth'=>$param['pre_birth'],
            'mobile'=>$param['mobile'], 'sex'=>2, 
            'birthday'=>$param['birthday'],
            'address'=>'',
            'check_date_list'=>empty($check_date_list)?'':$check_date_list
        ];
// dump($data); die;
        return $this->modelPregnantUser->updateInfo($where, $data);
    }

    /**
     * 根据孕妇信息，计算距离下次体检还有几天
     * @param time 预产期
     */
    public function calDay($date_start, $date_end){
        // 1. 根据预产期往前推40周
        $start_time = strtotime($date_start); // 预产期时间戳
        $end_time = strtotime($date_end);
        $time = $end_time-$start_time;
        return $time / 86400;

    }

    /**
     * 母乳检测报名信息保存
     */
    public function breastmilkcheckSave($param = []){
        $data = [
            'openid'=>$param['openid'],
            // 宝妈信息
            'mather_name'=>$param['mather_name'],
            'mather_age'=>intval($param['mather_age']),
            'g'=>intval($param['g']),
            'p'=>intval($param['p']),
            'after_birth_month'=>intval($param['after_birth_month']),
            'after_birth_day'=>intval($param['after_birth_day']),
            'id_card'=>$param['id_card'],
            'mobile'=>$param['mobile'],
            'mather_allergy'=>$param['mather_allergy'],
            // 宝宝信息
            'baby_birthday'=>$param['baby_birthday'],
            'gestational_age_week'=>intval($param['gestational_age_week']),
            'gestational_age_day'=>intval($param['gestational_age_day']),
            'baby_nation'=>$param['baby_nation'],
            'baby_name'=>$param['baby_name'],
            'baby_sex'=>intval($param['baby_sex']),
            'baby_birth_weight'=>floatval($param['baby_birth_weight']),
            'baby_birth_height'=>floatval($param['baby_birth_height']),
            'baby_birth_head'=>floatval($param['baby_birth_head']),

            'baby_now_weight'=>floatval($param['baby_now_weight']),
            'baby_now_height'=>floatval($param['baby_now_height']),
            'baby_now_head'=>floatval($param['baby_now_head']),


            'baby_allergy'=>intval($param['baby_allergy']),
            'address'=>$param['address'],
            
            'status'=>1,
            'create_time'=>time()

        ];

        $wxUserInfo = $this->logicWxUser->getWxUserInfo(['wx_openid'=>$param['openid']]);

        if($wxUserInfo['is_vip'] == 2){
            $this->modelWxUser->updateInfo(['wx_openid'=>$param['openid']],['is_vip'=>1]);
            $data['is_pay'] = 1;
        }else{
            $data['is_pay'] = 0;
        }

        return $this->modelBreastMilk->setInfo($data);
    }

    /**
     * 查询母乳检测记录
     */
    public function breastmilkchecklist($param){

        return $this->modelBreastMilk->getList(['openid'=>$param['openid']], true, 'id desc', false);
    }

    /**
     * 查看母乳检测详情
     * by fqm in 19.10.17
     */
    public function checkbreastmilkinfo($param = [])
    {
        return $this->modelBreastMilk->getInfo(['id'=>$param['id'],'openid'=>$param['openid']]);
    }


    /**
     * 计算今天是否应该提醒孕检
     */
    // private function calISRemind($today, $check_date_list){
    //     $isRemind = false;
    //     $check_date = '';
    //     foreach($check_date_list as $k=>$v){
    //         $today_plus = date('Y-m-d', strtotime('+1 day', $today));
    //         if($today_plus === $v){
    //             $isRemind = true;
    //             $check_date = $v;
    //             break;
    //         }
    //     }
    //     return ['isRemind'=>$isRemind, 'checkDate'=>$check_date];
    // }

    /**
     * 孕期检测提醒 add by fjw in 19.5.30
     * 默认提前一天发送消息
     */
    public function pregnantRemind($param = []){

$t1 = microtime(true);

        $param ['remind'] = 'pregnant';
        $template_id = 'I4LCf03T8u0HfAyXhuhu03emMCl0p0NE-08GplXbIK0';
        $access_token = $this->serviceWechat->driverWxgzh->access_token($param);
        $send_url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token;
        $redirect_url = $this->serviceWechat->driverWxgzh->templateRedirectUrl($param);

        $today = date('Y-m-d', time());
        $next_check_date = date('Y-m-d', strtotime('+'.$param['before_day'].' day', time()));
        // dump($next_check_date); die;
        // 查询孕妇总数
        $where = ['status'=>1];
        $where['pre_birth'] = ['>', $today];
        $where['check_date_list'] = ['like', '%'.$next_check_date.'%'];

        $pregnant = Db::name('pregnant_user') -> where($where) -> order('id') -> select();
        
// ... 执行代码 ...
// $t2 = microtime(true);
// echo '耗时'.round($t2-$t1,3).'秒'; die;

        if($pregnant != []){
            foreach($pregnant as $k=>$v){
                $send_data = [
                    'touser'=>$v['openid'],
                    'template_id'=>$template_id,
                    'url'=>$redirect_url,
                    'topcolor'=> '#ff0000',
                    'data'=>[
                        'first'     =>['value'=>'小爱孕检助手提醒您', 'color'=>''],
                        'keyword1'  =>['value'=>$v['real_name'], 'color'=>''],
                        'keyword2'  =>['value'=>$next_check_date, 'color'=>''],
                        'keyword3'  =>['value'=>'常规孕期检查', 'color'=>''],
                        'remark'    =>['value'=>'请携带好您的相关证件和材料前往医院进行常规孕检~', 'color'=>'']
                    ]
                ];
                $res = httpsPost($send_url, json_encode($send_data));
                // dump($res);
            }
        }
        
$t2 = microtime(true);
echo '耗时'.round($t2-$t1,3).'秒'; 
        
    }



}
