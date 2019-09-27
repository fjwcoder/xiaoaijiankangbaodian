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
        // $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxfcb19a60a1a9523f&redirect_uri=".urlencode('http://xiaoai.mamitianshi.com/index.php/wechat/mamilogin?c=activity&a=mamiSubscribeFeverActivity')."&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
        // echo $url; die;
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
        // dump( $this->param); die;
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

    /**
     * 公众号二维码
     */
    public function subscribePlease(){

        return $this->fetch('index/subscribe');
    }



    /**
     * 模拟小票打印
     */
    public function imitateInjectQrcode($no='A123'){

        // dump(get_access_token()); die;
        $root = 'http://xiaoai.mamitianshi.com/';
        $url = $root.'wechat/loginPlus?c=index&a=scanInjectQrcode&ts='.strtotime(date('Y-m-d', time())).'&uc=08:00:20:0A:8C:6E&no='.$no;

        $wx_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx3bfada96a932f9e1&redirect_uri='.urlencode($url).'&response_type=code&scope=snsapi_base&state=1#wechat_redirect';
        
        // dump(create_qrcode($wx_url)); die;
        // return create_qrcode($wx_url);

        $this->assign('qrcode', create_qrcode($wx_url));
        return $this->fetch('imitate');
        // $this->assign('', create_qrcode($url);
    }

    /**
     * 扫描取号小票上的二维码，查看排队人数
     * @param $ts 时间戳
     * @param $unique_code
     * @param $mac_address
     * @param $number 小票上的号码
     */
    public function scanInjectQrcode(){

        $openid = input('openid', '');
        $timestamp = input('ts', '');
        $unique_code = input('uc', '');
        $number = input('no', '');


        $today = date('Y-m-d H:i:s', $timestamp);

        $queue = Db::name('inject_queue_list') 
            -> where('unique_code="'.$unique_code.'" and status=1 and create_time > "'.$today.'"') 
            ->order('id desc') ->limit(1) -> find();
        
        if(empty($queue) || !isset($queue['queue_list']) || empty($queue['queue_list'])){
            // 队列为空
            $return = ['status'=>false, 'msg'=>'等待队列不存在'];

            $this->assign('return', $return);
            return $this->fetch('scan_inject_qrcode');
        }

        $queues_list = json_decode($queue['queue_list'], true);


        $pos = array_search($number, $queues_list);

        if($pos && $pos > -1){
            $return = [
                'status'=>true,
                'self'=>$pos + 1,
                'before'=>$pos,
                'total'=>count($queues_list)
            ];
            
        }else{

            $return = ['status'=>false, 'msg'=>'等待队列不存在'];
        }

        $this->assign('return', $return);
        return $this->fetch('scan_inject_qrcode');

    }



}
