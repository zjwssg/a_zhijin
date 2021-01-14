<?php
namespace app\api\controller;
use think\Cache;
use think\Controller;
use think\Session;

class User extends Common
{
    /**
         * 获取店铺列表信息
     * @return json API返回的json数据
     */
    public function get_shop_list()
    {
        /***********  获取参数  ***********/
        $data = input();
        $db_res = db('users')
            ->where(['status'=>1])
            ->select();


        if($db_res){
            //如果添加成功，提示添加成功。success也可以定义跳转链接，success('添加图片成功！','这里写人跳转的url')
            $this->return_msg(200, 'info',$db_res);
        }else{
            $this->return_msg(400, '暂无数据');
        }
    }

    /**
     * 获取店铺详情
     * @return json API返回的json数据info
     */
    public function get_shop_info()
    {
        /***********  获取参数  ***********/
        $data = input();
        $db_res = db('users')
            ->where(['id'=>$data['id'],'status'=>1])
            ->find();


        if($db_res){
            //如果添加成功，提示添加成功。success也可以定义跳转链接，success('添加图片成功！','这里写人跳转的url')
            $this->return_msg(200, 'info',$db_res);
        }else{
            $this->return_msg(400, '暂无数据');
        }
    }



}
