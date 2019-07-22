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
class Kids extends AdminBase
{
    /**
     * 获取文章列表
     */
    public function getKidsList($where = [], $field = 'a.*,m.nickname,c.name as category_name', $order = '')
    {
        
        $this->modelKidsCheck->alias('a');
        
        $join = [
                    [SYS_DB_PREFIX . 'member m', 'a.member_id = m.id'],
                    [SYS_DB_PREFIX . 'article_category c', 'a.category_id = c.id'],
                ];
        
        $where['a.' . DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        $where['a.category_id'] = 4;
        
        $this->modelKidsCheck->join = $join;
        
        return $this->modelKidsCheck->getList($where, $field, $order);
    }
    
    /**
     * 获取文章列表搜索条件
     */
    public function getWhere($data = [])
    {
        
        $where = [];
        
        !empty($data['search_data']) && $where['a.name|a.describe'] = ['like', '%'.$data['search_data'].'%'];
        
        return $where;
    }
    
    /**
     * 文章信息编辑
     */
    public function checkEdit($data = [])
    {
        
        $validate_result = $this->validateArticle->scene('edit')->check($data);
        
        if (!$validate_result) {
            
            return [RESULT_ERROR, $this->validateArticle->getError()];
        }
        
        $url = url('checkList');
        
        empty($data['id']) && $data['member_id'] = MEMBER_ID;
        
        $result = $this->modelKidsCheck->setInfo($data);
        
        $handle_text = empty($data['id']) ? '新增' : '编辑';
        
        $result && action_log($handle_text, '文章' . $handle_text . '，name：' . $data['name']);
        
        return $result ? [RESULT_SUCCESS, '文章操作成功', $url] : [RESULT_ERROR, $this->modelKidsCheck->getError()];
    }

    /**
     * 获取文章信息
     */
    public function getKidsInfo($where = [], $field = 'a.*,m.nickname,c.name as category_name')
    {
        
        $this->modelKidsCheck->alias('a');
        
        $join = [
                    [SYS_DB_PREFIX . 'member m', 'a.member_id = m.id'],
                    [SYS_DB_PREFIX . 'article_category c', 'a.category_id = c.id'],
                ];
        
        $where['a.' . DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        
        $this->modelKidsCheck->join = $join;
        
        return $this->modelKidsCheck->getInfo($where, $field);
    }
    
    /**
     * 获取分类信息
     */
    // public function getKidsCategoryInfo($where = [], $field = true)
    // {
        
    //     return $this->modelKidsCheckCategory->getInfo($where, $field);
    // }
    
    /**
     * 获取文章分类列表
     */
    public function getArticleCategoryList($where = [], $field = true, $order = '', $paginate = 0)
    {
        $where['id'] = 4;
        return $this->modelArticleCategory->getList($where, $field, $order, $paginate);
    }
    
    /**
     * 文章分类删除
     */
    // public function kidsCategoryDel($where = [])
    // {
        
    //     $result = $this->modelKidsCheckCategory->deleteInfo($where);
        
    //     $result && action_log('删除', '文章分类删除，where：' . http_build_query($where));
        
    //     return $result ? [RESULT_SUCCESS, '文章分类删除成功'] : [RESULT_ERROR, $this->modelKidsCheckCategory->getError()];
    // }
    
    /**
     * 文章删除
     */
    public function kidsDel($where = [])
    {
        
        $result = $this->modelKidsCheck->deleteInfo($where);
        
        $result && action_log('删除', '文章删除，where：' . http_build_query($where));
        
        return $result ? [RESULT_SUCCESS, '文章删除成功'] : [RESULT_ERROR, $this->modelKidsCheck->getError()];
    }
}
