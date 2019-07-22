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
class Pregnant extends AdminBase
{
    
    /**
     * 文章列表
     */
    public function pregnantList()
    {
        
        $where = $this->logicPregnant->getWhere($this->param);
        
        $this->assign('list', $this->logicPregnant->getPregnantList($where, 'a.*,m.nickname,c.name as category_name', 'a.create_time desc'));
        
        return $this->fetch('pregnant_list');
    }
    
    /**
     * 文章添加
     */
    public function pregnantAdd()
    {
        
        $this->pregnantCommon();
        
        return $this->fetch('pregnant_edit');
    }
    
    /**
     * 文章编辑
     */
    public function pregnantEdit()
    {
        
        $this->pregnantCommon();
        
        $info = $this->logicPregnant->getPregnantInfo(['a.id' => $this->param['id']], 'a.*,m.nickname,c.name as category_name');
        
        !empty($info) && $info['img_ids_array'] = str2arr($info['img_ids']);
        
        // dump($info); die;
        $this->assign('info', $info);
        
        return $this->fetch('pregnant_edit');
    }
    
    /**
     * 文章添加与编辑通用方法
     */
    public function pregnantCommon()
    {
        
        IS_POST && $this->jump($this->logicPregnant->pregnantEdit($this->param));
        
        $this->assign('article_category_list', $this->logicPregnant->getArticleCategoryList([], 'id,name', '', false));
    }
    


    /**
     * 数据状态设置
     */
    public function setStatus()
    {
        
        $this->jump($this->logicAdminBase->setStatus('Pregnant', $this->param));
    }
}
