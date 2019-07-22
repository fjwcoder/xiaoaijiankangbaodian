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
class Vaccine extends IndexBase
{
    /**
     * 
     */
    public function kidsVaccineRemind(){
        $openid = input('openid', '', 'htmlspecialchars,trim');
        $kids_id = input('kids_id', 0, 'intval');
        if(empty($openid)){
            return $this->redirect('index/errorPage', ['content'=>'用户信息不存在，请关注公众号']);
        }
        $kids = $this->logicKids->getKidsInfo(['id'=>$kids_id]);
        if(empty($kids)){
            return $this->redirect('kids/kidslist', ['openid'=>$openid]);
        }
        
        $remind = $this->logicVaccine->calCheckDate($kids['birthday']);
// dump($remind); die;
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
        $this->assign('kids', $kids);
        $this->assign('openid', $openid);

        // 查询接种记录
        $inject_list = $this->logicVaccine->getInjectList(['openid'=>$openid, 'kids_id'=>$kids_id]);
        // dump($inject_list); die;
        $this->assign('inject_total', count($inject_list));
        $this->assign('inject_list', $inject_list);

        return $this->fetch('vaccine/remind');
    }

    /**
     * 获取体检文章 getkidsCheckArticle
     */
    public function getVaccineRemindArticle(){

        $week = input('week', 0, 'intval');
        $article = $this->logicVaccine->getVaccineRemindArticle($week);
        return $article;
    }


    /**
     * 添加接种记录
     * 
     */
    public function addInject(){
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

        $this->assign('openid', $openid);

        return $this->fetch('vaccine/addinject');

    }

    /**
     * 保存添加的接种信息
     */
    public function saveinjectinfo(){
        // dump($this->param); die;
        $res = $this->logicVaccine->saveinjectinfo($this->param);
        if($res){
            // 添加成功，跳转到check
            return $this->redirect('/vaccine/kidsVaccineRemind', ['openid'=>$this->param['openid'], 'kids_id'=>$this->param['kids_id']]);
        }else{
            return $this->error('保存失败');
        }
    }

    /**
     * 接种日记详情
     */
    public function editInject(){
        $openid = input('openid', '', 'htmlspecialchars,trim');
        $kids_id = input('kids_id', 0, 'intval');
        $id = input('id', 0, 'intval');
        $kids = $this->logicKids->getKidsInfo(['id'=>$kids_id]);
        $inject = $this->logicVaccine->getInjectInfo(['id'=>$id]);
        $this->assign('kids', $kids);
        $this->assign('inject', $inject);
        $this->assign('openid', $openid);

        return $this->fetch('vaccine/editinject');

    }
    
    /**
     * 保存添加的接种信息
     */
    public function editinjectinfo(){
        // dump($this->param); die;
        $res = $this->logicVaccine->editinjectinfo($this->param);
        if($res){
            // 添加成功，跳转到check
            return $this->redirect('/vaccine/kidsVaccineRemind', ['openid'=>$this->param['openid'], 'kids_id'=>$this->param['kids_id']]);
        }else{
            return $this->error('保存失败');
        }
    }



    /**
     * 疫苗保险
     */
    public function insurance(){
        
        return $this->fetch('vaccine/insurance');

    }

    public function antibodycheck(){
        
        return $this->fetch('vaccine/antibodycheck');

    }



}
