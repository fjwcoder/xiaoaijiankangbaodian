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
 * 前端活动控制器
 */
class Activity extends IndexBase
{
    // private $remote_fever_activity = 'http://mami.fjwcoder.com/api.php/activity/unionidFeverActivity';
    // private $remote_finish_activity = 'http://mami.fjwcoder.com/api.php/activity/finishedFeverActivity';

    /**
     * 关注领取退热贴活动
     */
    public function subscribeFeverActivity(){

        // 一、 处理用户信息数据
        $user_id = user_is_login();
        $openid = input('openid', '', 'htmlspecialchars,trim');
        if(empty($openid)){
            return $this->redirect('index/errorPage', ['content'=>'用户信息不存在，请重新关注公众号']);
        }
        $user = $this->logicWxUser->getWxUserInfo(['wx_openid'=>$openid, 'wx_id'=>$user_id]);
        if(isset($user['unionid']) && $user['unionid'] != ''){ // 判断unionid是否存在
            $unionid = $user['unionid'];
        }else{
            return $this->redirect('index/errorPage', ['content'=>'用户信息不存在，请重新关注公众号']);
        }
        
        /**
         * 活动流程
         */
        $fever_click = 0; // 活动点击次数 1 2 3
        $activity = Db::name('fever_activity') -> where(['user_id'=>$user['user_id'], 'openid'=>$user['wx_openid'], 'unionid'=>$unionid]) -> find();
        $response = $this->unionidFeverActivity($unionid);  // 1. 查询远端数据: 0 未完成；1 已完成；

        $fever_click += intval($response['is_click']);

        if(empty($activity)){ // 本公众号没有参加活动，可以直接参加
            $data = [
                'user_id'=>$user_id, 'openid'=>$openid, 'unionid'=>$unionid,
                'is_click'=>1, 'click_time'=>intval(time())
            ];
            $insert = Db::name('fever_activity') -> insert($data);
            if($insert){
                $fever_click += 1;
                $this->assign('fever_click', $fever_click);
                return $this->fetch('fever_ok');
            }else{
                return $this->redirect('index/errorPage', ['content'=>'参加活动失败，请重试']);
            }

            
        }else{
            if($activity['is_finished'] == 1 || $response['is_finished'] == 1){ // 已经完成了活动
                $fever_click = 3; 
                $this->assign('fever_click', $fever_click);
                return $this->fetch('fever_ok');

            }else{ // 参加了活动，但是还没有完成
                $fever_click += 1;
                $this->assign('fever_click', $fever_click);
                return $this->fetch('fever_ok');
            }
        }

    }

    // 点击完成活动，领取到礼品
    public function finishFeverActivity(){
        
        $user_id = 1; //user_is_login();
        if($user_id > 0){
            $user = $this->logicWxUser->getWxUserInfo(['user_id'=>$user_id]);
            $unionid = $user['unionid'];
            // 1. 查询远端数据: 0 未完成；1 已完成；
            $response = $this->unionidFeverActivity($unionid);
// dump($response); die;
            // 2. 在获取本地的
            $activity = Db::name('fever_activity') -> where(['unionid'=>$unionid]) -> find();

            $finish_status = $this->validateStatus($response, $activity);
            // 3. 查询完成状态： true 参加了活动，但是没有修改状态  false 用户数据错误
            if($finish_status['status'] == false){ 
                return $this->redirect('index/errorPage', ['content'=>$finish_status['msg']]);
            }else{
                $finish_data = [
                    'is_finished'=>1, 'finish_time'=>intval(time())
                ];
                // 4. 先修改远端状态
                $remote = Db::connect(config('db_fever_activity'))->name('fever_activity') -> where(['unionid'=>$unionid]) ->update($finish_data);
                // 5. 再修改本地状态
                $local = Db::name('fever_activity') -> where(['unionid'=>$unionid]) ->update($finish_data);

                if($remote || $local){ // 成功
                    $this->assign('fever_click', 3);
                    return $this->fetch('fever_ok');
                }else{
                    return $this->redirect('index/errorPage', ['content'=>'用户数据错误，请重试']);
                }

            }

        }else{
            return $this->redirect('index/errorPage', ['content'=>'用户信息错误，请重试']);
        }
        
    }

    /**
     * 切换收据库
     * 查询另一个数据库中的活动表
     * 
     */ 
    private function unionidFeverActivity($unionid){
        $user = Db::connect(config('db_fever_activity'))->name('wx_user')->where(['unionid'=>$unionid])->find();
// dump($user); die;
        if(empty($user)){
            return ['is_click'=>0, 'is_finished'=>0];
        }else{
            // 用户存在的情况下，查询是否参加了活动
            $activity = Db::connect(config('db_fever_activity'))->name('fever_activity') -> where(['unionid'=>$unionid]) -> find();
            if($activity && $activity['is_click'] > 0){
                return $activity;
            }else{
                return ['is_click'=>0, 'is_finished'=>0];
            }
        }
    }

    // 验证是否可以更改为已经完成
    private function validateStatus($response, $activity){
        if($response['is_click'] == 1  && $activity['is_click'] == 1 ){
            if($response['is_finished'] == 0 && $response['is_finished'] == 0){
                return ['status'=>true];
            }else{
                return ['status'=>false, 'msg'=>'已领取奖品，不可重复参加活动'];
            }
        }else{
            return ['status'=>false, 'msg'=>'尚未关注全部公众号'];
        }

    }

    



}
