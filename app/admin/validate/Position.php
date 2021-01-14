<?php
namespace app\admin\validate;
use think\Validate;
class Position extends Validate
{
  protected $rule =   [
  		'p_title'  => 'require|max:30',
  		'p_cpm'  => 'require|regex:/^[1-9][0-9]*(\.[0-9]{0,2})?$/',
  		'p_cpc'  => 'require|regex:/^[1-9][0-9]*(\.[0-9]{0,2})?$/',
  		'p_size'  => 'require|max:200',
  		'p_note'  => 'require|max:200',
  ];
  
  protected $message  =   [
  		'p_title.require' => '请输入标题',
  		'p_title.max'     => '标题最多不能超过30个字符',
  		'p_cpm.require' => '请输入cpm推广费',
  		'p_cpm.regex'     => 'cpm推广费必须为金额',
  		'p_cpc.require' => '请输入cpc推广费',
  		'p_size.require' => '请输入尺寸说明',
  		'p_note.require' => '请输入描述',
  		'p_note.max' => '描述最多不能超过200个字符',
  		'p_cpc.regex' => 'cpc推广费必须为金额',
  		'p_size.max' => '尺寸说明最多不能超过200个字符',
  ];
}