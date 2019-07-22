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
 * 前端首页控制器
 */
class Index extends IndexBase
{
    /**
     * 孕检提醒：/pregnant/check?openid=o20RC1RcDMBYPdwPkfP9dCXkJz0g     完成，未测试
     * 体检提醒：/kids/kidslist?openid=o20RC1RcDMBYPdwPkfP9dCXkJz0g     完成，未测试
     * 接种提醒：/kids/kidslist?openid=o20RC1RcDMBYPdwPkfP9dCXkJz0g     未完成（接种提醒需要填写更多信息），未测试
     * 
     * 宝妈配方：
     * 天使配方：
     * 母乳检测：/pregnant/breastmilkcheck?openid=o20RC1RcDMBYPdwPkfP9dCXkJz0g   前台完成，已测试，后台没做
     * 疫苗保险：/vaccine/insurance     完成
     * 抗体检测：/vaccine/antibodycheck     完成
     * 
     * 健康知识：
     */
    
    // 首页
    public function index($cid = 0)
    {
        return $this->redirect('index/category?cid='.$cid);

    }

    public function category(){
        $cid = input('cid', 1, 'intval');
        $where = [];
        
        !empty((int)$cid) && $where['a.category_id'] = $cid;
        
        $this->assign('article_list', $this->logicArticle->getArticleList($where, 'a.*,m.nickname,c.name as category_name', 'a.create_time desc'));
        $cate_where['id'] = ['<', 3];
        $this->assign('category_list', $this->logicArticle->getArticleCategoryList($cate_where, true, 'create_time asc', false));
        $this->assign('cid', $cid);
        return $this->fetch('category');
    }
    /**
     * 文章列表  add by fjw in 19.5.13
     */
    public function getArticleList(){
        $cid = input('cid', 1, 'intval');
        $where = [];
        
        !empty((int)$cid) && $where['a.category_id'] = $cid;

        $list =  $this->logicArticle->getArticleList($where, 'a.*,m.nickname,c.name as category_name', 'a.create_time desc');
    
        return $list;
    }
    
    /**
     * 文章详情  add by fjw in 19.5.13
     */
    public function details($cid = 0, $id = 0)
    {
        $cid = input('cid', 1, 'intval');
        $cid = input('id', 1, 'intval');
        $list_where = [];

        !empty((int)$cid) && $list_where['a.category_id'] = $cid;

        $list =  $this->logicArticle->getArticleList($list_where, 'a.*,m.nickname,c.name as category_name', 'a.create_time desc');
    
        $this->assign('article_list', $list);

        
        $where = [];
        
        !empty((int)$id) && $where['a.id'] = $id;
        
        $data = $this->logicArticle->getArticleInfo($where);
        // dump($data); die;
        $this->assign('article_info', $data);
        $cate_where['id'] = ['<', 3];
        $this->assign('category_list', $this->logicArticle->getArticleCategoryList($cate_where, true, 'create_time asc', false));
        
        return $this->fetch('details');
    }






    /**
     * 错误页面
     */
    public function errorPage(){

        $this->assign('content', $this->param['content']);
        return $this->fetch('public/error');
    }

    /**
     * 成功该页面
     */
    public function successPage(){

        $this->assign('content', $this->param['content']);
        return $this->fetch('public/success');
    }


}
