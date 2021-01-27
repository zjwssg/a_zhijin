<?php
namespace app\api\controller;
use think\Cache;
use think\Controller;
use think\Session;

class Coupon extends Common
{
    /**
         * 获取优惠劵列表信息
     * @return json API返回的json数据
     */
    public function get_coupon_list()
    {


        //$where['c_available_num'] = ['>',0];
        /***********  获取参数  ***********/
        $data = input();
        $db_res = db('coupon')
            ->where(['c_with_sn'=>$data['c_with_sn']])
            ->where($data['c_available_num'] > 0)
            ->select();

        foreach ($db_res as $key => &$val){
             $user_coupon_num = db('user_coupon')->where(['c_id'=>$val['id'],'u_id'=>$data['user_id']])->select();
//             $user_coupon_num=5;
             if($user_coupon_num){
//                 var_dump(2);
                 $val['c_status']=2; //已经领取
             }else{
//                 var_dump(1);
                 $val['c_status']=1; //未领取
             }

         }

        if($db_res){
            //如果添加成功，提示添加成功。success也可以定义跳转链接，success('添加图片成功！','这里写人跳转的url')
            $this->return_msg(200, 'info',$db_res);

        }else{
            $this->return_msg(400, '暂无数据');
        }
    }




    /**
     * 领取优惠劵
     * @return json API返回的json数据info
     */
    public function receive_coupon()
    {

        /***********  获取参数  ***********/
        $data = input();

        $inserData=[
            'c_id'=>$data['c_id'],
            'u_id'=>$data['user_id'],
            'shop_id'=>$data['shop_id'],
        ];
        $user_coupon_num = db('user_coupon')->where(['u_id'=>$data['user_id']])->count();
        if($user_coupon_num >= 5){
            $this->return_msg(400, '您还不是会员暂时只能领五张优惠劵');
        }
        $db_res = db('user_coupon')->insertGetId($inserData);
        if($db_res){
            $couponinfo = db('coupon')->field(['c_end_time'])->where(['id'=>$data['c_id']])->find();
//            $couponinfo = db('coupon')->where(['id'=>$data['c_id']])->update(['c_available_num']);
            db('coupon')->where(['id'=>$data['c_id']])->setDec('c_available_num');  // 字段原值默认减1
            //如果添加成功，提示添加成功。success也可以定义跳转链接，success('添加图片成功！','这里写人跳转的url')
            $this->return_msg(200, 'info', $couponinfo);
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
