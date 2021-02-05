<?php
namespace app\api\controller;
use think\Cache;
use think\Controller;
use think\Session;
require __DIR__ .'/vendor/autoload.php';
use Twilio\Rest\Client as note; 

class Client extends Common
{


    /**
     * 获取验证
     * @return json API返回的json数据
     */
    public function getverificationcode()
    {
        /***********  获取参数  ***********/ //Twilio
        /*$data =input();
        $to = $data['user_phone'];
        //halt($to);
        $account_sid    = "AC9c11d0af67a64d881203a63be2aade7b"; 
        $auth_token  = "c06c9f66d7b0212807f0793aeeb255e9"; 

        //$twilio_number = "+15005550006";


        $client = new note($account_sid, $auth_token);
        $code = rand(100000,999999);
        $end = $client->messages->create(
            // Where to send a text message (your cell phone?)
            "+60".$to,
            array(
                'from' => '+1 920 781 1119',
                'body' => $code
            )
        );
        halt($end->body);
        if($end->body){
            return json(['code'=>200,'msg'=>'返回验证码成功'，'data'=>$end->body]);
        }else{
            return json(['code'=>400,'msg'=>'返回验证码失败'])
        }*/
        $data = input();
        //halt($data);
        $user_phone = $data['user_phone'];
        $code = rand(100000,999999);
        $sessage = 'RM0%20Giver%20'.$code;
        //halt($sessage);
        $durl = "http://ezsms2u.com/websmsapi/ISendSMS.aspx?username=giver&password=123456&message=".$sessage."&mobile=".$data['user_phone']."&sender=Test&type=1";
        //$durl = "http://ezsms2u.com/websmsapi/ISendSMS.aspx?username=giver&password=123456&message=RM0%20Giver%20HiRichard&mobile=601110673396&sender=Test&type=1"
        
        $res = $this->curl_file_get_contents($durl);
        if($res){
            return json(['code'=>200,'msg'=>'返回验证码成功','data'=>$sessage]);
        }else{
            return json(['code'=>400,'msg'=>'返回验证码失败']);
        }
        
    }
    //访问url
    function curl_file_get_contents($durl){  

        $ch = curl_init();  

        curl_setopt($ch, CURLOPT_URL, $durl);  

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回    

        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回    

        $data = curl_exec($ch);  

        curl_close($ch);  

        return $data;  

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
        //$token= $this->uptokens();
        /***********  获取参数  ***********/
        $data = input();
        //halt($data);
        $dataselet = db('client_user')->where('user_phone',$data['user_phone'])->select();
        if(!empty($dataselet)){
            return json(['code' => 200, 'msg' => '登陆成功','data'=>$dataselet]);
        }
        $dataInsert=[
            'user_phone'=>$data['user_phone'],
            'user_rtime'=>time(),
            'user_icon'=>1,
            'user_nickname'=>$this->generate_password(),
        ];
        $res = db('client_user')->insertGetId($dataInsert);
        
        if($res){
            return json(['code' => 200, 'msg' => '登陆成功','data'=>$res]);
        }else{
            return json(['code' => 400, 'msg' => '登陆失败']);
        }
        
    }
    //登陆成功后获取个人信息
    public function getuserlist(){
        $data = input();
        //halt($data);
        $res = db('client_user')->where('user_id',$data['user_id'])->select();
        if($res){
            $this->return_msg(200, 'info',json_decode($res,true));
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


        $db_res = db('client_user')
            ->field('user_phone')
            ->where('user_phone', $data['user_phone'])
            ->find();
        if($db_res){
            $this->return_msg(402, '该手机号已经存在！');
        }
        $add=db('client_user')->where(['user_id'=>$data['user_id']])->update(['user_phone'=>$data['user_phone']]);
        if($add){
            //如果添加成功，提示添加成功。success也可以定义跳转链接，success('添加图片成功！','这里写人跳转的url')
            $this->return_msg(200, '修改手机号成功！',$data['user_phone']);
        }else{
            $this->return_msg(400, '修改手机号失败！');
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
