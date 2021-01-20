<?php
namespace app\api\controller;
use think\Cache;
use think\Controller;
use think\Session;
use think\Request;
use think\Error;
//设备接口页
class Equipment extends Controller
{

    public function send_post($url, $post_data, $token) { 
      $postdata = http_build_query($post_data); 
      //halt($token);
      $options = array( 
        'http' => array( 
          'method' => 'POST', 
          'header' => array(

          	"token:" . $token,
          	 "chan:bee-CECBNR",
          	 "Content-type: application/x-www-form-urlencoded",
          	),
          'content' => $postdata, 
          'timeout' => 15 * 60, // 超时时间（单位:s）
        ) 
      );
      $context = stream_context_create($options); 
	  //halt($url);
      $result = file_get_contents($url, false, $context); 

      echo $result; 
      return $result; 
    }
    public function send_get($url, $post_data, $token) { 
      $postdata = http_build_query($post_data); 
      //halt($token);
      $options = array( 
        'http' => array( 
          'method' => 'GET', 
          'header' => array(

            "token:" . $token,
             "chan:bee-CECBNR",
             "Content-type: application/x-www-form-urlencoded",
            ),
          'content' => $postdata, 
          'timeout' => 15 * 60, // 超时时间（单位:s）
        ) 
      );
      $context = stream_context_create($options); 
      //halt($url);
      $result = file_get_contents($url, false, $context); 

      //echo $result; 
      return $result; 
    }
	// $data = '{"cmd": 1000, "data": {"digital": 1, "msg": "run"}, "sn": "ABCDEF", "nonceStr": "123456"}'; 
	// $post_data = array( 'data' => $data );
    // send_post('http://mqtt.ibeelink.com/api/ext/tissue/pub-cmd', $post_data, md5($data . 'ce46612a305c454874f8E871385ae003'));
    
    //文档3.3.01接口指令下发		POST
    public function directive_issue(){
    	//接受app post传过来的参数
    	//$data =  '{"sn": "ookkma", "nonceStr": "123456"}';
        $data = input();
        //$data = $aa['data'];
    	//处理
    	$post_data = array( 'data' => $data );
    	//调用Common.php函数
    	$res = $this->send_post("https://en-iot.ibeelink.com/api/ext/tissue/pub-cmd", $post_data, md5($data.'0907b9450D3Ff5b4da9631A474DfA26c'));
        //halt($res);
    	return $res;
    }
    //文档3.3.02	获取指令执行结果		GET
    public function directive_result(){
    	//接受app get传过来的参数
        $data = input('post.data');

    	//$data = input();
        //return $data;
    	//处理
    	$post_data = array( 'data' => $data );
    	//调用Common.php函数
    	$res = $this->send_get('https://en-iot.ibeelink.com/api/ext/tissue/cmd', $post_data, md5($data.'0907b9450D3Ff5b4da9631A474DfA26c'));
    	return $res;
    }
    //文档3.3.03	获取设备数据		GET
    /*
    	sn 	设备sn码
    	key 需要查询的数据
    	nonoceStr	参数随机码
		page 	页码
		size 	页面大小
     */
    public function facility_data(){
    	//接受app get传过来的参数
    	$data = input();
    	//处理
    	$post_data = array( 'data' => $data );
    	//调用Common.php函数
    	$res = $this->send_get('https://mqtt.ibeelink.com/api/ext/tissue/data//', $post_data, md5($data.'0907b9450D3Ff5b4da9631A474DfA26c'));
    	return $res;
    }
    //3.3.04 获取设备信息接口		GET
    // 	sn 	设备sn码
    // 	nonoceStr	参数随机码
    public function facility_message(){
    	//接受app get传过来的参数
    	$data = input('get.data');
        //halt($data);
    	//处理
    	$post_data = array( 'data' => $data );
    	//调用Common.php函数
    	$res = $this->send_get("https://en-iot.ibeelink.com/api/ext/tissue/device/info", $post_data, md5($data.'0907b9450D3Ff5b4da9631A474DfA26c'));
    	return $res;
    }
    //3.3.05 获取设备IMEI	GET
    // 	sn 	设备sn码
    // 	nonoceStr	参数随机码
    public function facility_imei(){
    	//接受app get传过来的参数
    	$data = input();
    	//处理
    	$post_data = array( 'data' => $data );
    	//调用Common.php函数
    	$res = $this->send_get('https://mqtt.ibeelink.com/api/ext/tissue/device/imei', $post_data, md5($data.'0907b9450D3Ff5b4da9631A474DfA26c'));
    	return $res;
    }
    //3.3.06 获取附近设备接口		GET
    //lat 	圆心纬度值
    //lng 	圆心经度值
    //nonoceStr	参数随机码
    //distance 	圆心搜索半径m,最大100KM
    public function facility_nearby_port(){
    	//接受app get传过来的参数
    	$data = input();
    	//处理
    	$post_data = array( 'data' => $data );
    	//调用Common.php函数
    	$res = $this->send_get('https://mqtt.ibeelink.com/api/ext/tissue/vicinity/list', $post_data, md5($data.'0907b9450D3Ff5b4da9631A474DfA26c'));
    	return $res;
    }
    //3.3.07 获取设备通信TOPIC //发布消息的目的地	GET
    // 	sn 	设备sn码
    // 	nonoceStr	参数随机码
    public function facility_message_topic(){
    	//接受app get传过来的参数
    	$data = input();
    	//处理
    	$post_data = array( 'data' => $data );
    	//调用Common.php函数
    	$res = $this->send_get('https://mqtt.ibeelink.com/api/ext/tissue/sn-exchange/topic', $post_data, md5($data.'0907b9450D3Ff5b4da9631A474DfA26c'));
    	return $res;
    }
    //3.3.08 获取批量设备信息接口 //发布消息的目的地	GET
    // 	snList 	设备sn码(数组)
    // 	nonoceStr	参数随机码
    public function facility_message_port(){
    	//接受app get传过来的参数
    	$data = input();
    	//处理
    	$post_data = array( 'data' => $data );
    	//调用Common.php函数
    	$res = $this->send_get('https://mqtt.ibeelink.com/api/ext/tissue/devices/info', $post_data, md5($data.'0907b9450D3Ff5b4da9631A474DfA26c'));
    	return $res;
    }
    //3.3.09 品牌商获取监听项列表		GET
    //	nonoceStr	参数随机码
    public function brand_get_monitor(){
    	//接受app get传过来的参数
    	$data = input('get.data');
        //halt($data);
    	//处理
    	$post_data = array( 'data' => $data );
    	//调用Common.php函数
    	$res = $this->send_get('https://en-iot.ibeelink.com/api/ext/tissue/monitor/data/list', $post_data, md5($data.'0907b9450D3Ff5b4da9631A474DfA26c'));
    	return $res;
    }
    //3.3.10 品牌设置监听回调		POST
    //	notify 	品牌设置的回调地址
    //	nonoceStr	参数随机码
    //	ruleList	监听项，从3.3.9获取需要的项
    public function brand_get_monitor_callback(){
        $data = '{
            "notify":"http://www.zhijin.com:8080/api/equipment/data",
            "nonceStr":"1564165",
            "ruleList":["c-state","c-cmd","a-MOTOR","a-Nothing","lng"]
        }';
    	//接受app get传过来的参数
    	//$data = input();
    	//处理
    	$post_data = array( 'data' => $data );
    	//调用Common.php函数
    	$res = $this->send_post('https://en-iot.ibeelink.com/api/ext/tissue/monitor/add/rule', $post_data, md5($data.'0907b9450D3Ff5b4da9631A474DfA26c'));
    	return $res;
    }
    //3.3.11 品牌获取已经配置的监听项 		GET
    //	nonoceStr	参数随机码
    public function get_already_monitor(){
    	//接受app get传过来的参数
    	$data = input('get.data');
    	//处理
    	$post_data = array( 'data' => $data );
    	//调用Common.php函数
    	$res = $this->send_get('https://en-iot.ibeelink.com/api/ext/tissue/monitor/rule/list', $post_data, md5($data.'0907b9450D3Ff5b4da9631A474DfA26c'));
    	return $res;
    }
    //3.3.12 数据监听回调说明		POST
    //	Token  md5(data+发放的密钥)
    //	data   传递参数的json字符串
    //	用户设置回调地址
    public function data_monitor_callback(){
        $data= '{"data":{"ruleName": "设备状态", 
                "sn":"3Q6NNO", 
                "notifyTime":15922300000, 
                "respData": {"state": 1} , 
                "cmd":"4832",
                "cmdId":"5c3ec8b7f091930abc125785" 
            }
        }';
    	//接受app get传过来的参数
    	//$data = input();
    	//处理
    	$post_data = array( 'data' => $data );
    	//调用Common.php函数
    	$res = $this->send_post('http://www.zhijin.com:8080/api/equipment/data', $post_data, md5($data.'0907b9450D3Ff5b4da9631A474DfA26c'));
    	return $res;
    }
    public function data(){
        $data = input();
        halt($data);
    }
}