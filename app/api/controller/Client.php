<?php
namespace app\api\controller;
use think\Cache;
use think\Controller;
use think\Session;

class Client extends Common
{


    /**
     * 获取验证
     * @return json API返回的json数据
     */
    public function getverificationcode()
    {
        /***********  获取参数  ***********/
        $data =input();
        /*********** 查询数据库   ***********/
//    $token=$this->check_token($data['token']);
//        $this->return_msg(200, '验证码发送成功！',$token);


        $db_res = db('client_user')
            ->field('user_phone')
            ->where('user_phone', $data['user_phone'])
            ->find();
        /***********  判断用户是否已经注册 ***********/
        if (!$db_res['user_phone']) {


            if($data['type']==0){
                $dataInsert=[
                    'user_phone'=>$data['user_phone'],
                    'user_rtime'=>time(),
                    'user_icon'=>1,
                    'user_nickname'=>$this->generate_password(),
                ];
                $res = db('client_user')->insertGetId($dataInsert);
                $tree = $this->getcode(6, $dataInsert['user_phone']);
                Cache::set($dataInsert['user_phone'], $tree, 60);
            }else{
                $tree = $this->getcode(6, $data['user_phone']);
                Cache::set($data['user_phone'], $tree, 60);

            }

        } else {

            if (Cache::get($db_res['user_phone'])) {
//                var_dump('缓存');
                $tree = Cache::get($db_res['user_phone']);
            } else {
//                var_dump('写入缓存');
                $tree = $this->getcode(6, $db_res['user_phone']);
                Cache::set($db_res['user_phone'], $tree, 3600);
            }
        }
        $this->return_msg(200, '验证码发送成功！', $tree);
    }

    //随机生成昵称
    function generate_password( $length = 8 ) {
        // 密码字符集，可任意添加你需要的字符
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        for ( $i = 0; $i < $length; $i++ )
        {
            // 这里提供两种字符获取方式
            // 第一种是使用 substr 截取$chars中的任意一位字符；
            // 第二种是取字符数组 $chars 的任意元素
            // $password .= substr($chars, mt_rand(0, strlen($chars) – 1), 1);
            $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
        }
        return $password;
    }


    /**
     * 生成验证
     * @return json API返回的json数据
     * $num   生成几位数
     * $user_phone   手机号
     */
    function getcode($num, $user_phone)
    {
        $code = "";
        for ($i = 0; $i < $num; $i++) {
            $code .= rand(0, 9);
        }
        return $code;
    }
    /**
     * 用户登录
     * @return json API返回的json数据
     */
    public function login()
    {
      $token= $this->uptokens();
        /***********  获取参数  ***********/
        $data = input();
        /***********  获取缓存 ***********/
        $cacheCode = Cache::get($data['user_phone']);
        if ($data['user_code'] != $cacheCode) {
            $this->return_msg(400, '验证码不正确！');
        } else {
            $tokenData=[
                'user_token'=>$token['token']
            ];
            $add=db('client_user')->where('user_phone',$data['user_phone'])->update($tokenData);
            $user=db('client_user')->where('user_phone',$data['user_phone'])->find();

            $this->return_msg(200, '登陆成功！',['token'=>$token['token'],'user_id'=>$user['user_id']]);
        }
    }
    /**
     * 修改昵称
     * @return json API返回的json数据
     * $user_phone   手机号
     */
    function update_user_nickname()
    {
        /***********  获取参数  ***********/
        $data = input();


        $add = db('client_user')->where(['user_id'=>$data['user_id']])->update(['user_nickname'=>$data['user_nickname']]);
        if($add){


            //如果添加成功，提示添加成功。success也可以定义跳转链接，success('添加图片成功！','这里写人跳转的url')
            $this->return_msg(200, '修改昵称成功！',$data['user_nickname']);
        }else{
            $this->return_msg(400, '修改昵称失败！');
        }
    }


    /**
     * 修改手机号
     * @return json API返回的json数据
     */
    public function update_user_phone()
    {
        /***********  获取参数  ***********/
        $data =input();
        /***********  获取缓存 ***********/
        $cacheCode = Cache::get($data['user_phone']);
//        var_dump($data['user_code']);
//        var_dump($data['user_phone']);
//        var_dump($cacheCode);
        if ($data['user_code'] != $cacheCode) {
            $this->return_msg(400, '验证码不正确！');
        } else {

            $db_res = db('client_user')
                ->field('user_phone')
                ->where('user_phone', $data['user_phone'])
                ->find();
            if($db_res){
                $this->return_msg(400, '该手机号已经存在！');
            }
            $add=db('client_user')->where(['user_id'=>$data['user_id']])->update(['user_phone'=>$data['user_phone']]);
            if($add){
                //如果添加成功，提示添加成功。success也可以定义跳转链接，success('添加图片成功！','这里写人跳转的url')
                $this->return_msg(200, '修改手机号成功！',$data['user_phone']);
            }else{
                $this->return_msg(400, '修改手机号失败！');
            }
        }
    }



    /**
     * token-
     * @return json API返回的json数据
     */
    public function get_token()
    {
        /***********  获取参数  ***********/
        $data = $this->params;
        $db_res = db('client_user')
            ->field('user_token')
            ->where('user_phone', $data['user_phone'])
            ->find();
        if($db_res){
            //如果添加成功，提示添加成功。success也可以定义跳转链接，success('添加图片成功！','这里写人跳转的url')
            $this->return_msg(200, 'token',$db_res['user_token']);
        }else{
            $this->return_msg(400, '暂无数据');
        }
    }


    /**
         * 获取个人信息
     * @return json API返回的json数据
     */
    public function get_info()
    {
        /***********  获取参数  ***********/
        $data = input();
        $db_res = db('client_user')
            ->where('user_id', $data['user_id'])
            ->find();
        $db_res['is_follow']=json_decode($db_res['is_follow']);
        if($db_res){
            //如果添加成功，提示添加成功。success也可以定义跳转链接，success('添加图片成功！','这里写人跳转的url')
            $this->return_msg(200, 'info',$db_res);
        }else{
            $this->return_msg(400, '暂无数据');
        }
    }

    /**
     * 关注店铺
     * @return json API返回的json数据
     */
    public function follow_shop()
    {
        /***********  获取参数  ***********/
        $data = input();
        $db_user_res = db('client_user')->field(['is_follow'])->where('user_id', $data['user_id'])->find();
        if($db_user_res['is_follow']){
            $datauser=json_decode($db_user_res['is_follow']);
            $datauser[]=$data['shop_id'];
            $resdata=json_encode($datauser);
        }else{
            $datauser[]=$data['shop_id'];
            $resdata=json_encode($datauser);
        }
         $datas['is_follow']=$resdata;
        $db_res = db('client_user')
            ->where('user_id', $data['user_id'])->update($datas);
        if($db_res){
            //如果添加成功，提示添加成功。success也可以定义跳转链接，success('添加图片成功！','这里写人跳转的url')
            $this->return_msg(200, 'info',$db_res);
        }else{
            $this->return_msg(400, '暂无数据');
        }
    }



}
