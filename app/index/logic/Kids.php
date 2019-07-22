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
class Kids extends IndexBase
{
    private $check_category_id = 4;
    
    /**
     * 获取儿童信息
     */
    public function getKidsList($param = []){
        // 
        return $this->modelKidsUser->getList(['openid'=>$param['openid']], true, 'id', false);

    }
    public function getKidsInfo($param = []){
        // 
        return $this->modelKidsUser->getInfo(['id'=>$param['id']], true);

    }

    /**
     * 根据出生日期计算体检时间表和疫苗时间表
     */
    private function calDateByBirthday($birthday){
        // 儿保体检
        $kids_check_info = $this->modelArticle->getList(['category_id'=>$this->check_category_id, 'status'=>1], 'name, week', 'id', false);
        $vaccine_check_info = $this->modelVaccine->getList(['status'=>1], 'ym_name as name, week', 'week', false);
        $begin_time = strtotime($birthday);
        $week_second = 604800; // 每周的秒数
        $kids_check_list = '';
        $vaccine_check_list = '';
        foreach($kids_check_info as $k=>$v){
            $time = $begin_time + $week_second * $v['week'];
            $kids_check_list .= date('Y-m-d', $time).';';
        }
        foreach($vaccine_check_info as $k=>$v){
            $time = $begin_time + $week_second * $v['week'];
            $vaccine_check_list .= date('Y-m-d', $time).';';
        }
        
        return ['kids_check_list'=>$kids_check_list, 'vaccine_check_list'=>$vaccine_check_list];
    }

    /**
     * 
     */
    public function saveUserInfo($param){

        $user = $this->logicWxUser->getWxUserInfo(['wx_openid'=>$param['openid']]);
        $calDateByBirthday = isset($param['birthday'])?$this->calDateByBirthday($param['birthday']):['kids_check_list'=>'', 'vaccine_check_list'=>''];
        /** 计算 */
        $data = [
            'user_id'=>$user['user_id'], 'openid'=>$user['wx_openid'],
            'real_name'=>$param['real_name'],
            'mobile'=>$param['mobile'], 
            'sex'=>intval($param['sex']), 
            'birthday'=>isset($param['birthday'])?$param['birthday']:'',
            'hospital'=>isset($param['hospital'])?$param['hospital']:'',
            'status'=>1,
            'allergy'=>intval($param['allergy']),
            'kids_check_list'=>$calDateByBirthday['kids_check_list'],
            'vaccine_check_list'=>$calDateByBirthday['vaccine_check_list']
        ];

        return $this->modelKidsUser->setInfo($data);
    }

    public function editUserInfo($param){

        // $user = $this->logicWxUser->getWxUserInfo(['wx_openid'=>$param['openid']]);
        $calDateByBirthday = isset($param['birthday'])?$this->calDateByBirthday($param['birthday']):['kids_check_list'=>'', 'vaccine_check_list'=>''];
        $where = ['id'=>$param['id'], 'openid'=>$param['openid']];
        $data = [
            
            'real_name'=>$param['real_name'],
            'mobile'=>$param['mobile'], 
            'sex'=>intval($param['sex']), 
            'birthday'=>isset($param['birthday'])?$param['birthday']:'',
            'hospital'=>isset($param['hospital'])?$param['hospital']:'',
            'allergy'=>intval($param['allergy']),
            'kids_check_list'=>$calDateByBirthday['kids_check_list'],
            'vaccine_check_list'=>$calDateByBirthday['vaccine_check_list']
        ];

        return $this->modelKidsUser->updateInfo($where, $data);
    }

    /**
     * 根据kids生日，计算体检的时间
     */
    public function calCheckDate($birthday){
        $remind_icon = '<i class="fa fa-heartbeat"></i>';
        $remind = [];
        $check_list = $this->modelArticle->getList(['category_id'=>$this->check_category_id, 'status'=>1], 'id, week');
        // dump($check_list); die;
        $start_time = strtotime($birthday);
        // $remind = ['startDate'=>$birthday, 'name'=>'出生', 'week'=>0];
        foreach($check_list as $k=>$v){
            $remind_date = date('Y-m-d', strtotime("+$v[week] week", $start_time));
            $remind[] = ($v['week'] == 0)?['startDate'=>$remind_date, 'name'=>'出生', 'week'=>0]:['startDate'=>$remind_date, 'name'=>$remind_icon, 'week'=>$v['week']];;
        }

        return $remind;
    }

    /**
     * 获取检查内容
     * 
     */
    public function getKidsCheckArticle($week){
        // return $this->modelPregnantCheck->getInfo(['week'=>$week]);
        return $this->modelArticle->getInfo(['category_id'=>$this->check_category_id, 'week'=>$week]);
    }


    /**
     * 儿保体检 add by fjw in 19.5.31
     * 默认提前一天发
     */
    public function kidsRemind($param = []){

        $t1 = microtime(true);


        $param ['remind'] = 'kids';
        $template_id = 'I4LCf03T8u0HfAyXhuhu03emMCl0p0NE-08GplXbIK0';
        $access_token = $this->serviceWechat->driverWxgzh->access_token($param);
        $send_url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token;
        $redirect_url = $this->serviceWechat->driverWxgzh->templateRedirectUrl($param);

        $today = date('Y-m-d', time());
        $next_check_date = date('Y-m-d', strtotime('+'.$param['before_day'].' day', time()));
        // dump($next_check_date); die;

        $where = ['status'=>1];
        $where['kids_check_list'] = ['like', '%'.$next_check_date.'%'];
        $kids = Db::name('kids_user') -> where($where) -> order('id') -> select();

        if($kids != []){
            foreach($kids as $k=>$v){
                $send_data = [
                    'touser'=>$v['openid'],
                    'template_id'=>$template_id,
                    'url'=>$redirect_url,
                    'topcolor'=> '#ff0000',
                    'data'=>[
                        'first'     =>['value'=>'小爱体检助手提醒您', 'color'=>''],
                        'keyword1'  =>['value'=>$v['real_name'], 'color'=>''],
                        'keyword2'  =>['value'=>$next_check_date, 'color'=>''],
                        'keyword3'  =>['value'=>'常规儿童健康检查', 'color'=>''],
                        'remark'    =>['value'=>'请携带好宝宝的相关证件和体检材料前往医院进行常规体检~', 'color'=>'']
                    ]
                ];

                $res = httpsPost($send_url, json_encode($send_data));
            }
        }

$t2 = microtime(true);
echo '耗时'.round($t2-$t1,3).'秒'; 

    }





}
