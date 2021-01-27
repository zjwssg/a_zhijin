<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
// 验证生成密码， 参数(密码，随机字符串)
function password($password, $password_salt)
{
  return md5(md5($password) . md5($password_salt));
}
// 随机字符串， 参数(长度，类型，大小写)
function random($length = 10, $type = 'letter', $convert = 0)
{
  $config = array(
    'number' => '1234567890',
    'letter' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
    'string' => 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789',
    'all'    => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
  );
  if (!isset($config[$type])) {
    $type = 'letter';
  }
  $string = $config[$type];
  $code   = '';
  $strlen = strlen($string) - 1;
  for ($i = 0; $i < $length; $i++) {
    $code .= $string{mt_rand(0, $strlen)};
  }
  if (!empty($convert)) {
    $code = ($convert > 0) ? strtoupper($code) : strtolower($code);
  }
  return $code;
}
// 无限栏目， 参数(所有数据，父级的字段名
function wx($a, $n, $fid=0, $k=0, $arr=[])
{
    foreach ($a as $i=>$b) {
        if ($b[$n]==$fid) {
            $b['k']=$k;
            $arr[] = $b;
            unset($a[$i]);
            $arr = wx($a, $n, $b['id'], $k+1, $arr);
        }
    }
    return $arr;
}
// 获取当前栏目下的所有子栏目，参数(当前栏目id、所有栏目的id,fid)
function zid($id, $a, $n, &$zid=[])
{
    foreach ($a as $i=>$v) {
        if ($v[$n]==$id) {
            unset($a[$i]);
            array_push($zid, $v['id']);
            zid($v['id'], $a, $n, $zid);
        }
    }
    return $zid;
}
// 上传图片
function upload($img)
{
	if($_FILES[$img]['type']=="image/png" || $_FILES[$img]['type']=="image/jpeg" || $_FILES[$img]['type']=="image/gif" || $_FILES[$img]['type']=="image/jpg" || $_FILES[$img]['type']=="image/bmp"){
		// 获取表单上传文件 例如上传了001.jpg
		$file = request()->file($img);
		if ($file) {
		    $info = $file->move(ROOT_PATH . 'public' . DS . 'static' . DS . 'img');
		    if ($info) {
		        return str_replace('\\', '/', $info->getSaveName());
		    } else {
		        // 上传失败获取错误信息
		        $this->error($file->getError());
		    }
		}
	}else{
		$this->error('文件类型不是图片');
	}
}
// 获取验证码
function yzm($sjh){
	$value['code'] = 0;
	$time = cache('yzm_'.$sjh)['f_time'];
	if($time>=time()){
		$value['msg'] = '等待'.($time-time()).'秒后再发送';
	}else if(empty($sjh)){
		$value['msg'] = '请输入手机号';
	}else{
		$accessKeyId = db('pz')->where(['ename'=>'accessKeyId'])->value('mrz');
		$accessKeySecret = db('pz')->where(['ename'=>'accessKeySecret'])->value('mrz');
		$code = db('pz')->where(['ename'=>'code'])->value('mrz');
		$sign = db('pz')->where(['ename'=>'sign'])->value('mrz');
		$SmsDemo = new \duan\api_demo\SmsDemo($accessKeyId,$accessKeySecret,$code,$sign);
		$yzm = rand(100000,999999);
		// // 参数：手机号、验证码、签名、code
		$a = $SmsDemo->sendSms($sjh,$yzm);
		$a = object_to_array($a);
		if($a['Code']=='OK'){
			$fTime = db('pz')->where(['ename'=>'fTime'])->value('mrz'); // 每n秒发送一次
			$dxTime = db('pz')->where(['ename'=>'dxTime'])->value('mrz'); //过期时间
			// 'yzm11111111111'=>下次发送时间,验证码
			cache('yzm_'.$sjh,['f_time'=>time()+$fTime,'yzm'=>$yzm],300);
			$value['code'] = 1;
			// $value['data'] = $yzm;
			$value['msg'] = '发送成功';
		}else{
			$value['msg'] = $a['Message'];
		}
	}
	return $value;
}
// 生成token
function token($id){
	$token = md5($id.time().random());
	$token = strtoupper($token);
	$t = cache('yh_token');
	$t[$id] = ['token'=>$token,'time'=>time()+cache('mrz')['tokenTime']*86400,'id'=>$id];
	cache('yh_token',$t);
	return $token;
}
// 截取字符串 aaa...
function jq($a, $b)
{
    if (mb_strlen($a)>$b) {
        $a = mb_substr($a, 0, $b).'...';
    }
    return $a;
}
// 截取字符串 aaa...aaa
function jqq($a)
{
    if (mb_strlen($a)>10) {
        $a = mb_substr($a, 0, 3).'...'.mb_substr($a, mb_strlen($a)-4);
    }
    return $a;
}

// 菜单栏显示
function wx1($a, $n, $fid=0)
{
    $arr = [];
    foreach ($a as $i=>$b) {
        if ($b[$n]==$fid) {
            unset($a[$i]);
            $b['items'] = wx1($a, $n, $b['id']);
            $arr[] = $b;
        }
    }
    return $arr;
}
// 无限栏目,$items为所有数据，按fid从小到大排序，以保证父节点在前，子节点在后。$n为父级的字段名
function xh($items, $n)
{
    $menu = array();
    foreach ($items as $v) {
        $menu[$v['id']] = $v;
        $menu[$v['id']]['items'] = array();//items存放当前节点的所有子节点。
        if ($v[$n] != 0) {
            $menu[$v[$n]]['items'][$v['id']] = &$menu[$v['id']];
        }
    }
    foreach ($menu as $k=>$v) {
        if ($v[$n] != 0) {
            unset($menu[$k]);
        }
    }
    return $menu;
}


// 对象 转 数组
function object_to_array($obj) {
  return json_decode(json_encode($b,320),true);
}

 /*// curl请求
function http_request($url,$data = null,$header = null){
    $curl = curl_init();
    if(!empty($header)){
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_HEADER, 0);//返回response头部信息
    }
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)) {
			curl_setopt($curl,CURLOPT_POST,1);
			curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
        // curl_setopt($curl, CURLOPT_HTTPGET, 1);
        // curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}*/
// 数组 转 对象
function array_to_object($arr) {
  return json_decode(json_encode($a,320));
}
//日志
function text($txt){
	$myfile = fopen("../log/log.txt", "a") or die("Unable to open file!");
	fwrite($myfile, $txt);
	$txt = "\n";
	fwrite($myfile, $txt);
	fclose($myfile);
}