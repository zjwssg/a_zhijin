<?php
namespace app\admin\validate;
use think\Validate;
class Cater extends Validate
{
  protected $rule =   [
  		'name'  => 'require|max:30'
  ];
  
  protected $message  =   [
  		'name.require' => '请输入名称',
  		'name.max'     => '名称最多不能超过30个字符'
  ];
}