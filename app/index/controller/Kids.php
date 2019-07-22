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
class Kids extends IndexBase
{
    
    /**
     * 宝宝信息第二版 add by fjw in 19.5.27
     * 
     */
    public function kidsList(){ // 宝宝列表页面 add by fjw in 19.5.27
        // echo '开发中'; die;
        $openid = input('openid', '', 'htmlspecialchars,trim');
        if(empty($openid)){
            return $this->redirect('index/errorPage', ['content'=>'用户信息不存在，请关注公众号']);
        }
        $list = $this->logicKids->getKidsList(['openid'=>$openid]);
        $this->assign('list', $list);
        $this->assign('openid', $openid);
        // dump($list); die;
        return $this->fetch('kids/list');
    }


    public function addInfo(){ // add by fjw in 19.5.27
        $openid = input('openid', '', 'htmlspecialchars,trim');
        if(empty($openid)){
            return $this->redirect('index/errorPage', ['content'=>'用户信息不存在，请关注公众号']);
        }
        $this->assign('openid', $openid);

        return $this->fetch('kids/addinfo');
    }

    public function saveKidsInfo(){

        $res = $this->logicKids->saveUserInfo($this->param);
        if($res){
            // 添加成功，跳转到check
            return $this->redirect('/kids/kidslist', ['openid'=>$this->param['openid']]);
        }else{
            return $this->error('保存失败');
        }

    }

    public function kidsInfo(){
        $openid = input('openid', '', 'htmlspecialchars,trim');
        if(empty($openid)){
            return $this->redirect('index/errorPage', ['content'=>'用户信息不存在，请关注公众号']);
        }
        $kids_id = input('kids_id', 0, 'intval');

        $kids = $this->logicKids->getKidsInfo(['id'=>$kids_id]);
// dump($kids); die;
        if(empty($kids)){
            return $this->redirect('index/errorPage', ['content'=>'宝宝信息不存在']);
        }

        $this->assign('kids', $kids);
        $this->assign('openid', $openid);

        return $this->fetch('kids/editinfo');
    }

    public function editKidsInfo(){
// dump($this->param); die;
        $res = $this->logicKids->editUserInfo($this->param);
        if($res){
            // 添加成功，跳转到check
            return $this->redirect('/kids/kidslist', ['openid'=>$this->param['openid']]);
        }else{
            return $this->error('保存失败');
        }

    }

    /**
     * end 宝宝信息第二版
    */



    /**
     * 儿童体检check
     */
    public function kidsCheckRemind(){
        $openid = input('openid', '', 'htmlspecialchars,trim');
        $kids_id = input('kids_id', 0, 'intval');
        if(empty($openid)){
            return $this->redirect('index/errorPage', ['content'=>'用户信息不存在，请关注公众号']);
        }
        $kids = $this->logicKids->getKidsInfo(['id'=>$kids_id]);
        if(empty($kids)){
            return $this->redirect('kids/kidslist', ['openid'=>$openid]);
        }
        $this->assign('kids', $kids);

        $remind = $this->logicKids->calCheckDate($kids['birthday']);
        $this->assign('remind', json_encode($remind));
        // $this->assign('remind', json_encode([]));
        $curr_date = date('Y-m-d', time());
        $next_day = 0;
        foreach($remind as $v){
            if($curr_date <= $v['startDate']){
                $next_day = $this->logicPregnant->calDay($curr_date, $v['startDate']);
                break;
            }
        }
        $this->assign('next_day', $next_day);


        $this->assign('openid', $openid);

        return $this->fetch('kids/remind');
    }

    /**
     * 获取体检文章 getkidsCheckArticle
     */
    public function getkidsCheckArticle(){

        $week = input('week', 0, 'intval');
        $article = $this->logicKids->getKidsCheckArticle($week);
        return $article;
    }





    
    



}
