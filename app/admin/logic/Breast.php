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

namespace app\admin\logic;

/**
 * 
 */
class Breast extends AdminBase
{
    public function getBreastMilkList($where = []){
        $paginate = 20;
        return $this->modelBreastMilk->getList($where, true, 'id', $paginate);

    }

    public function changeStep($where, $data){
        if($this->modelBreastMilk->updateInfo($where, $data)){
            return true;
        }else{
            return false;
        }
    }
    
}
