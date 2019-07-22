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
class Kids extends AdminBase
{
    
    /**
     * 文章列表
     */
    public function checkList()
    {
        
        $where = $this->logicKids->getWhere($this->param);
        $list = $this->logicKids->getKidsList($where, 'a.*,m.nickname,c.name as category_name', 'a.create_time desc');
        $this->assign('list', $list);
        
        return $this->fetch('kids_list');
    }
    
    /**
     * 文章添加
     */
    public function checkAdd()
    {
        
        $this->kidsCommon();
        
        return $this->fetch('kids_edit');
    }
    
    /**
     * 文章编辑
     */
    public function checkEdit()
    {
        
        $this->kidsCommon();
        
        $info = $this->logicKids->getKidsInfo(['a.id' => $this->param['id']], 'a.*,m.nickname,c.name as category_name');
        
        !empty($info) && $info['img_ids_array'] = str2arr($info['img_ids']);
        
        // dump($info); die;
        $this->assign('info', $info);
        
        return $this->fetch('kids_edit');
    }
    
    /**
     * 文章添加与编辑通用方法
     */
    public function kidsCommon()
    {
        
        IS_POST && $this->jump($this->logicKids->checkEdit($this->param));
        
        $this->assign('article_category_list', $this->logicKids->getArticleCategoryList([], 'id,name', '', false));
    }
    


    /**
     * 数据状态设置
     */
    public function setStatus()
    {
        
        $this->jump($this->logicAdminBase->setStatus('Kids', $this->param));
    }
}
