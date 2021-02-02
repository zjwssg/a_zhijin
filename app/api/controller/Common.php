<?php
namespace app\api\controller;

use think\Request; //处理参数类
use think\Controller; //控制器基础类
use think\Validate; //参数验证类
use think\Db; //数据库类
use think\Image; //处理图片类

class Common extends Controller
{
  protected $request; // 用来处理参数
  protected $validater; //用来验证数据/参数
  protected $params; //过滤后符合要求的参数（不包含time和token）
  protected $rules = array(
    'Client' => array(
      'login' => array(
         'user_phone' => ['require', 'regex' => '/^1[34578]\d{9}$/'],
          'user_code' => 'require|number|length:6'
      ),
        'getverificationcode' => array(
            'user_phone' => ['require', 'regex' => '/^1[34578]\d{9}$/'],
        ),
        'getcode' => array(),
        'get_info' => array(),
        'get_token' => array(
            'user_phone' => ['require', 'regex' => '/^1[34578]\d{9}$/'],
        ),


      'update_user_nickname' => array(
        'user_id' => 'require',
        'user_nickname' => 'require'
      ),
      'update_user_phone' => array(
      //        'user_id' => 'require|number',
      //          'user_code' => 'require|number|length:6'
      ),
        'follow_shop' => array(
      //        'user_id' => 'require|number',
      //          'user_code' => 'require|number|length:6'
      ),

    ),
    'Upload' => array(
        'upload_img' => array(
        ),
        'upload' => array(

        ),
    ),
      'User' => array(
          'get_shop_info' => array(),
          'get_shop_list' => array(),
      ),
      'Coupon' => array(
          'get_coupon_info' => array(),
          'get_coupon_list' => array(),
          'receive_coupon' => array(),
      ),

  );
  protected function _initialize()
  {
    /*********** 继承父类的构造方法 ***********/
    parent::_initialize();
    /*********** 获取接受到的参数 ***********/
    $this->request = Request::instance();
    /*********** 判断时间戳 ***********/
    // $this->check_time($this->request->only(['time']));
    /*********** 判断token ***********/
    // $this->check_token($this->request->param());
    /*********** 验证参数 ***********/
    // 这样是不能检测上传图像的文件的，需要写成下面的格式
    // $this->params = $this->check_params($this->request->except(['time', 'token']));
    //$this->params = $this->check_params($this->request->param(true));
  }
  /**
   * 验证时间戳是否超时
   *
   * @param array $arr 包含时间错的参数数组
   * @return json 检测结果
   */
  public function check_time($arr)
  {
    if (!isset($arr['time']) || intval($arr['time']) <= 1) { //判断时间戳是否存在
      $this->return_msg(400, '时间戳不正确');
    }
    if (time() - intval($arr['time']) > 60) { //判断时间戳是否超时：大于60s 
      $this->return_msg(400, '请求超时！');
    }
  }
  /**
   * api 数据返回
   *
   * @param int $code 结果码  200：正常、4**：数据问题、5**：服务器问题
   * @param string $msg 接口要返回的提示信息
   * @param array $data 接口要返回的数据
   * @return string 最终的json数据
   */
  public function return_msg($code, $msg = '', $data = [])
  {
    /*********** 组合数据 ***********/
    $return_data['code'] = $code;
    $return_data['msg'] = $msg;
    $return_data['data'] = $data;
    /*********** 返回信息并终止脚本 ***********/
    echo json_encode($return_data);
    die;
  }
  /**
   * 验证token，防止篡改数据
   *
   * @param array $arr 全部请求数据
   * @return json token验证结果
   */
  public function check_token($arr)
  {
    /*********** api传过来的token ***********/
    if (!isset($arr['token']) || empty($arr['token'])) {
      $this->return_msg(400, 'token不能为空');
    }
    $app_token = $arr['token']; //api传过来的token
    /*********** 服务器生成的token ***********/
    unset($arr['token']);
    $service_token = '';
    foreach ($arr as $key => $value) {
      $service_token .= md5($value); //每个元素MD5加密
    }
    $service_token = md5('api_' . $service_token . '_api'); //服务器即时生成的token
    /*********** 对比token，返回结果 ***********/
    if ($app_token !== $service_token) {
      $this->return_msg(400, 'token不正确');
    }
  }
  /**
   * 验证参数
   *
   * @param array $arr 除time和token外的所有参数
   * @return void
   */
  // public function check_params($arr)
  // {
  //   /*********** 获取参数的验证规则 ***********/
  //   $rule = $this->rules[$this->request->controller()][$this->request->action()];
  //   /*********** 验证参数并返回错误 ***********/
  //   $this->validater = new Validate($rule);
  //   if (!$this->validater->check($arr)) {
  //     $this->return_msg(400, $this->validater->getError());
  //   }
  //   /*********** 如果正常，通过验证 ***********/
  //   return $arr;
  // }
  /**
   * 检测用户名并返回用户名类别
   * @param string $username 用户名，可能是邮箱，也可能是手机号
   * @return string 检测结果
   */
  public function check_username($username)
  {
    /***********  判断是否为邮箱  ***********/
    $is_email = Validate::is($username, 'email') ? 1 : 0;
    /***********  判断是否为手机号  ***********/
    $is_phone = preg_match('/^1[34578]\d{9}$/', $username) ? 4 : 2;
    /***********  最终结果  ***********/
    $flag = $is_email + $is_phone;
    switch ($flag) {
      /***********  not phone not email  ***********/
      case 2:
        $this->return_msg(400, '邮箱或手机号不正确！');
        break;
      /***********  is email not phone  ***********/
      case 3:
        return 'email';
        break;
      /***********  is phone not email  ***********/
      case 4:
        return 'phone';
        break;
    }
  }
  /**
   * 判断用户名（手机/邮箱）是否应该在数据库中存在
   * @param string $value 用户名
   * @param string $type 用户名类型
   * @param int $exist 1：存在 0：不存在
   * @return json API返回的json数据
   */
  public function check_exist($value, $type, $exist)
  {
    $type_num = $type == "phone" ? 2 : 4;
    $flag = $type_num + $exist;
    $phone_res = db('user')->where('user_phone', $value)->find();
    $email_res = db('user')->where('user_email', $value)->find();
    switch ($flag) {
      /*********** 2+0 phone need no exist  ***********/
      case 2:
        if ($phone_res) {
          $this->return_msg(400, '此手机号已被占用!');
        }
        break;
      /*********** 2+1 phone need exist  ***********/
      case 3:
        if (!$phone_res) {
          $this->return_msg(400, '此手机号不存在!');
        }
        break;
      /*********** 4+0 email need no exist  ***********/
      case 4:
        if ($email_res) {
          $this->return_msg(400, '此邮箱已被占用!');
        }
        break;
      /*********** 4+1 email need  exist  ***********/
      case 5:
        if (!$email_res) {
          $this->return_msg(400, '此邮箱不存在!');
        }
        break;
    }
  }
  /**
   * 检验验证码是否超时、正确
   * @param string $user_name 用户名：手机/邮箱
   * @param int $code 验证码
   * @return json API返回的json数据
   */
  public function check_code($user_name, $code)
  {
    /*********** 检测是否超时  ***********/
    $user_name_last_send_time = session($user_name . '_last_send_time');
    if (empty($user_name_last_send_time)) {
      $this->return_msg(400, '未生成验证码!');
    } else {
      $last_time = session($user_name . '_last_send_time');
            // session($user_name . '_last_send_time', null);
    }
    if (time() - $last_time > 60) {
      $this->return_msg(400, '验证超时,请在一分钟内验证!');
    }
    /*********** 检测验证码是否正确  ***********/
    $user_name_code = session($user_name . "_code");
    if (empty($user_name_code)) {
      $this->return_msg(400, '未生成验证码!');
    } else {
      $session_code = session($user_name . "_code");
    }
    if ($session_code != $code) {
      $this->return_msg(400, '验证码不正确!');
    }
    /***********  不管正确与否，全部清除  ***********/
    session($user_name . '_code', null);
    session($user_name . '_last_send_time', null);
  }
  /**
   * 上传图片
   *
   * @param string $file 图片的路径
   * @param string $type 图片的类型，对图片进行裁剪时用到
   * @return void
   */
  public function upload_file($file, $type = '')
  {
    $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads'); //DS：当前时间
    if ($info) {
            // dump($info->getSaveName());die;
      $path = '/uploads/' . $info->getSaveName();
      /***********  裁剪图片  ***********/
      if (!empty($type)) {
        $this->image_edit($path, $type);
      }
      return str_replace('\\', '/', $path); //把斜线换成反斜线
    } else {
      $this->return_msg(400, $file->getError());
    }
  }
  /**
   * Undocumented function
   *
   * @param string $path 图片的路径
   * @param string $type 图片的类型，对图片进行裁剪时用到
   * @return void
   */
  public function image_edit($path, $type)
  {
    $image = Image::open(ROOT_PATH . 'public' . $path);
    switch ($type) {
      case 'head_img':
        $image->thumb(200, 200, Image::THUMB_CENTER)->save(ROOT_PATH . 'public' . $path);
        break;
    }
  }

    private function createScope(){
        $scopeData["scope"]="my_backupname";
        $scopeData["deadline"]=time()+3600;
        return json_encode($scopeData);
    }

    private function base64_urlSafeEncode($data){
        $find = array('+', '/');
        $replace = array('-', '_');
        return str_replace($find, $replace, base64_encode($data));
    }

    private function encodedPutPolicy(){
        return $this->base64_urlSafeEncode($this->createScope());
    }

    private function encodedSign(){
        $hmac = hash_hmac('sha1', $this->encodedPutPolicy(), input("QINIU_SK"), true);
        return $this->base64_urlSafeEncode($hmac);
    }

    public function uptokens(){
        //hidejson();
        header("Content-type: text/json; charset=utf-8");
//        $tokenData["success"]=true;
//        $tokenData["code"]=200;
//        $tokenData["msg"]="操作成功";
//        $tokenData["obj"]=null;
        $tokenData["token"] =input("QINIU_AK") . ':' . $this->encodedSign() . ':' . $this->encodedPutPolicy();
//        $tokenData["list"]=null;
        return $tokenData;

    }

    
    

}