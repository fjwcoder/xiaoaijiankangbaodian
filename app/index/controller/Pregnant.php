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

/**
 * 
 */
class Pregnant extends IndexBase
{

    public function check(){
        $openid = input('openid', '', 'htmlspecialchars,trim');
        // 1. 先查询有没有录入信息
        $pregnant_user = $this->logicPregnant->getPregnantUser(['openid'=>$openid]);
        $this->assign('openid', $openid);
        
        if(empty($pregnant_user)){
            // 没有用户信息，需要添加用户信息
            $this->assign('have_user', false);
            $this->assign('remind', json_encode([]));
            return $this->fetch('pregnant/remind');
        }else{
            // 根据用户信息，查出应该做体检的日子
            $remind = $this->logicPregnant->calCheckDate($pregnant_user['pre_birth']);
            $curr_date = date('Y-m-d', time());
            // $curr_date = '2030-01-01';
            // 处理距离下次体检还有几天
            $next_day = 0;
            foreach($remind as $v){
                if($curr_date <= $v['startDate']){
                    $next_day = $this->logicPregnant->calDay($curr_date, $v['startDate']);
                    break;
                }
            }
            $this->assign('next_day', $next_day);
            $this->assign('have_user', true);
            $this->assign('pregnant_user', $pregnant_user);
            $this->assign('remind', json_encode($remind));
            return $this->fetch('pregnant/remind');
        }

        // $this->assign('remind', json_encode($remind));
        // return $this->fetch('pregnant/remind');
    }

    /**
     * 获取孕检文章
     */
    public function getPregnantCheckArticle(){

        $week = input('week', 0, 'intval');
        $article = $this->logicPregnant->getPregnantCheckArticle($week);
        return $article;
    }

    
    public function addInfo(){
        $openid = input('openid', '', 'htmlspecialchars,trim');

        $user = $this->logicWxUser->getWxUserInfo(['wx_openid'=>$openid]);
        // dump($user); die;
        $this->assign('user', $user);
        return $this->fetch('addinfo');
    }


    public function saveUserInfo(){

        $pregnant = $this->logicPregnant->getPregnantUser($this->param);
        if($pregnant){
            return $this->error('当前用户已生成孕妇信息');
        }
        $user = $this->logicWxUser->getWxUserInfo(['wx_openid'=>$this->param['openid']]);
        if(empty($user)){
            return $this->error('当前用户不存在');
        }
        $res = $this->logicPregnant->saveUserInfo($this->param, $user);
        if($res){
            // 添加成功，跳转到check
            return $this->redirect('/pregnant/check', ['openid'=>$this->param['openid']]);
        }else{
            return $this->error('保存失败');
        }

        
    }



    public function editInfo(){
        $openid = input('openid', '', 'htmlspecialchars,trim');
        
        $pregnant = $this->logicPregnant->getPregnantUser(['openid'=>$openid]);
        
        if(empty($pregnant)){
            return $this->redirect('/pregnant/addinfo', ['openid'=>$openid]);
        }
        // dump($pregnant); die;
        $this->assign('user', $pregnant);
        return $this->fetch('editinfo');
    }

    public function editUserInfo(){
        $pregnant = $this->logicPregnant->getPregnantUser($this->param);
        // dump($pregnant); die;
        if(empty($pregnant)){
            return $this->redirect('/pregnant/addinfo', ['openid'=>$this->param['openid']]);
        }
        $res = $this->logicPregnant->editUserInfo($this->param);
        if($res){
            // 修改成功，跳转到check
            return $this->redirect('/pregnant/check', ['openid'=>$this->param['openid']]);
        }else{
            return $this->error('保存失败');
        }

        
    }

    /**
     * 母乳检测 begin
     * 
     */
    public function breastmilkcheck(){
        
        $openid = input('openid', '', 'htmlspecialchars,trim');

        $user = $this->logicWxUser->getWxUserInfo(['wx_openid'=>$openid]);
        if(empty($user)){
            return $this->redirect('index/errorPage', ['content'=>'用户不存在，请关注微信公众号“小爱健康宝典”']);
        }
        // $this->assign('openid', $openid);
        $this->assign('user', $user);
        return $this->fetch('pregnant/breastmilkcheck');
    }

    public function breastmilkcheckSave(){
        // dump($this->param); die;
        // echo '开发中'; die;
        if(!isset($this->param['openid']) ||  $this->param['openid'] == ''){
            return $this->redirect('index/errorPage', ['content'=>'用户不存在，请关注微信公众号“小爱健康宝典”']);
        }
        $res = $this->logicPregnant->breastmilkcheckSave($this->param);
        if($res){
            return $this->redirect('index/successPage', ['content'=>'母乳检测报名成功，请等待工作人员联系您~']);
        }else{
            return $this->error('报名失败');
        }

    }

    public function breastmilkchecklist(){
        // echo '开发中'; die;
        $openid = input('openid', '', 'htmlspecialchars,trim');

        // $user = $this->logicWxUser->getWxUserInfo(['wx_openid'=>$openid]);
        if($openid == ''){
            return $this->redirect('index/errorPage', ['content'=>'用户不存在，请关注微信公众号“小爱健康宝典”']);
        }

        $list = $this->logicPregnant->breastmilkchecklist(['openid'=>$openid]);

        $this->assign('openid', $openid);
        $this->assign('list', $list);
        return $this->fetch('pregnant/breastmilkchecklist');
    }


     /**
      * 母乳检测 end
      */

}
