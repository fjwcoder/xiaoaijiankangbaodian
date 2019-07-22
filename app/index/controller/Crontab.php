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

namespace app\index\controller;
use think\Db;
/**
 * 
 */
class Crontab extends IndexBase
{
    private $access_token = 'xiaoaijiankangbaodian';
    private $before_day = 1;


    public function __construct(){
        // 执行父类构造方法
        parent::__construct();
        if(isset($this->param['at']) && $this->param['at'] === $this->access_token){

        }else{
            dump('呔'); die;
        }

    }

    public function test(){
        $info = Db::name('pregnant_user') -> where(['id'=>3]) ->field('id', true) -> find();
        // dump($info); die;
        $list = [];
        for($i=0; $i<100; $i++){
            $list[] = $info;
        }
        Db::name('pregnant_user_copy') -> insertAll($list);
    }

    public function addCrontabList(){
        $line = 5;
        // 先获取最大的一天
        $last = Db::name('crontab') -> order('date_list desc') -> limit(1) -> find();
        // dump($last); die;
        if(empty($last)){
            $begin_date = date('Y-m-d', time());
        }else{
            // $begin_date = $last['date_list'];
            $begin_date = date('Y-m-d', strtotime('+1 day', strtotime($last['date_list'])));
        }
        // dump($begin_date); die;
        $crontab_list = [];
        for($i = 0; $i<5; $i++){
            $date_list =  date('Y-m-d', strtotime('+'.$i.' day', strtotime($begin_date)));
            $crontab_list[] = ['date_list'=>$date_list, 'pregnant'=>0, 'kids'=>0, 'vaccine'=>0];
        }
        // dump($crontab_list); die;
        $add = Db::name('crontab') -> insertAll($crontab_list);
        dump($add);
    }



    // 孕期检测提醒 add by fjw in 19.5.30
    public function pregnantRemind(){
        $this->param['before_day'] = $this->before_day;
        return $this->logicPregnant->pregnantRemind($this->param);
    }

    // 儿保体检提醒 add by fjw in 19.5.30
    public function kidsRemind(){
        $this->param['before_day'] = $this->before_day;
        return $this->logickids->kidsRemind($this->param);
    }
    
    // 疫苗接种提醒 add by fjw in 19.5.30
    public function vaccineRemind(){
        $this->param['before_day'] = $this->before_day;
        return $this->logicVaccine->vaccineRemind($this->param);
    }
    
}
