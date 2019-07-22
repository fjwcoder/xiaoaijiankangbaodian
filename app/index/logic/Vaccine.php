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
class Vaccine extends IndexBase
{


    /**
     * 根据kids生日，计算疫苗注射的时间
     */
    public function calCheckDate($birthday){
        $remind_icon = '<i class="fa fa-heartbeat"></i>';
        $remind = [];
        $check_list = $this->modelVaccine->getList(['status'=>1], 'ym_id as id, week', 'week', false);
        $start_time = strtotime($birthday);
        foreach($check_list as $k=>$v){
            $remind_date = date('Y-m-d', strtotime("+$v[week] week", $start_time));
            $remind[] = ['startDate'=>$remind_date, 'name'=>$remind_icon, 'week'=>$v['week']];

        }
   
        return $remind;
    }

    /**
     * 获取疫苗内容
     * 
     */
    public function getVaccineRemindArticle($week){
        // return $this->modelPregnantCheck->getInfo(['week'=>$week]);
        return $this->modelVaccine->getList(['week'=>$week, 'status'=>1]);
    }

    /**
     * 获取宝宝的疫苗接种记录
     */
    public function getInjectList($where){
        return $this->modelInjectRecord->getList($where, true, 'id', false);
    }

    /**
     * 获取接种详情
     */
    public function getInjectInfo($where){
        return $this->modelInjectRecord->getInfo($where);
    }

    /**
     * 保存添加的接种信息
     */
    public function saveinjectinfo($param){
        // dump($this->param); die;
        // $add = $this->logicVaccine->saveinjectinfo($this->param);
    
        $data = ['openid'=>$param['openid'],
            'kids_id'=>$param['kids_id'], 
            'vaccine_name'=>$param['vaccine_name'],
            'inject_date'=>$param['inject_date'],
            'reaction'=>$param['reaction']
        ];

        return $this->modelInjectRecord->setInfo($data);
    
    }

    /**
     * 保存添加的接种信息
     */
    public function editinjectinfo($param){
        $where = ['id'=>$param['id'], 'openid'=>$param['openid'],
            'kids_id'=>$param['kids_id']
        ];
    
        $data = [
            'vaccine_name'=>$param['vaccine_name'],
            'inject_date'=>$param['inject_date'],
            'reaction'=>$param['reaction']
        ];

        return $this->modelInjectRecord->updateInfo($where, $data);
    
    }

    /**
     * 疫苗检测提醒 add by fjw in 19.5.31
     * 默认提前一天发送消息
     */
    public function vaccineRemind($param = []){
        $t1 = microtime(true);

        $param ['remind'] = 'vaccine';
        $template_id = 'I4LCf03T8u0HfAyXhuhu03emMCl0p0NE-08GplXbIK0';
        $access_token = $this->serviceWechat->driverWxgzh->access_token($param);
        $send_url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token;
        $redirect_url = $this->serviceWechat->driverWxgzh->templateRedirectUrl($param);

        $today = date('Y-m-d', time());
        $next_check_date = date('Y-m-d', strtotime('+'.$param['before_day'].' day', time()));
        // dump($next_check_date); die;
        // 查询孕妇总数
        $where = ['status'=>1];
        $where['vaccine_check_list'] = ['like', '%'.$next_check_date.'%'];
        $kids = Db::name('kids_user') -> where($where) -> order('id') -> select();
    
        if($kids != []){
            foreach($kids as $k=>$v){
                $send_data = [
                    'touser'=>$v['openid'],
                    'template_id'=>$template_id,
                    'url'=>$redirect_url,
                    'topcolor'=> '#ff0000',
                    'data'=>[
                        'first'     =>['value'=>'小爱疫苗助手提醒您', 'color'=>''],
                        'keyword1'  =>['value'=>$v['real_name'], 'color'=>''],
                        'keyword2'  =>['value'=>$next_check_date, 'color'=>''],
                        'keyword3'  =>['value'=>'儿童疫苗接种', 'color'=>''],
                        'remark'    =>['value'=>'请携带好宝宝的相关证件和材料前往接种点进行疫苗接种~', 'color'=>'']
                    ]
                ];

                $res = httpsPost($send_url, json_encode($send_data));
            }
        }

$t2 = microtime(true);
echo '耗时'.round($t2-$t1,3).'秒'; 
    
    
    
    
    
    
    
    
    }


}
