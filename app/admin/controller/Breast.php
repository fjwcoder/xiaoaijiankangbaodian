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

namespace app\admin\controller;

/**
 * 
 */
class Breast extends AdminBase
{
    
    /**
     * 母乳检测列表
     */
    public function list()
    {
        
        // $where = $this->logicKids->getWhere($this->param);
        $where = ['status'=>1];
        $list = $this->logicBreast->getBreastMilkList($where);
        $this->assign('list', $list);
        
        return $this->fetch('list');
    }

    public function changeStep(){
        $id = input('id', 0, 'intval');
        $step = input('step', 0, 'intval');
        $where = ['id'=>$id, 'status'=>1];
        $data = ['step'=>$step];
        return $this->logicBreast->changeStep($where, $data);
    }
    

}
