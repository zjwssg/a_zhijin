<?php
namespace app\admin\validate;
use think\Validate;
class Users extends Validate
{
  protected $rule =   [
  		'u_m_name'  => 'require|max:30',
  		'u_m_brief'  => 'require|max:50',
  		'u_m_starttime'  => 'require|regex:/^([0-9]{1,2}):([0-9]{2})$/',
  		'u_m_endtime'  => 'require|regex:/^([0-9]{1,2}):([0-9]{2})$/',
			'u_m_address'  => 'require|max:50',
			'u_m_typeid'  => 'require',
  ];
  
  protected $message  =   [
  		'u_m_name.require' => '请输入店铺名称',
  		'u_m_name.max'     => '店铺名称最多不能超过30个字符',
  		'u_m_brief.require' => '请输入店铺简介',
  		'u_m_brief.max'     => '店铺简介最多不能超过50个字符',
  		'u_m_starttime.require' => '请输入营业开始时间',
  		'u_m_endtime.require' => '请输入营业结束时间',
			'u_m_typeid.require' => '请选择餐饮种类',
			'u_m_address.require' => '请输入店铺地址',
			'u_m_address.max' => '店铺地址最多不能超过50个字符',
			'u_m_starttime.regex' => '时间格式错误',
			'u_m_endtime.regex' => '时间格式错误',
  ];
}