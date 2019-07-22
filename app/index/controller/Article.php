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
class Article extends IndexBase
{

    public function category(){
        $cid = input('cid', 1, 'intval');
        $where = [];
        
        !empty((int)$cid) && $where['a.category_id'] = $cid;
        $article_list = $this->logicArticle->getArticleList($where, 'a.*,m.nickname,c.name as category_name, p.path as cover_path', 'a.create_time desc');
        // if($cid === 5){
        //     dump('here'); die;
        //     array_reverse($article_list);
        // }
        // dump($article_list); die;
        $this->assign('article_list', $article_list);
        $cate_where['id'] = $cid;
        $category = $this->logicArticle->getArticleCategoryInfo($cate_where, true);
        $this->assign('category', $category);
        $this->assign('cid', $cid);
        return $this->fetch('category');
    }

    /**
     * 文章列表  add by fjw in 19.5.13
     * layui 流加载 获取文章列表  
     */
    public function getArticleList(){
        $cid = input('cid', 1, 'intval');
        $where = [];
        
        !empty((int)$cid) && $where['a.category_id'] = $cid;
        if($cid == 5){
            $order = 'a.create_time';
        }else{
            $order = 'a.create_time desc';
        }
        $list =  $this->logicArticle->getArticleList($where, 'a.*,m.nickname,c.name as category_name, p.path as cover_path', $order);
        


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
        $cate_where['id'] = $cid;
        $category = $this->logicArticle->getArticleCategoryInfo($cate_where, true);
        $this->assign('category', $category);
        
        return $this->fetch('details');
    }


}
