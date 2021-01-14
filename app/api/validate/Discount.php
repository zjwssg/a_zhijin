<?php
namespace app\api\validate;
use think\Validate;
class Discount extends Validate
{
  protected $rule = [
		'd_jamount'  => 'require|regex:/^[1-9][0-9]+(\.[0-9]{0,2})?$/',
		'd_mamount'   => 'require|regex:/^[1-9][0-9]+(\.[0-9]{0,2})?$/',
		'd_endtime' => 'require|regex:/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/',
		'd_nums' => 'require|regex:/^[1-9][0-9]*$/',
  ];
  protected $msg = [
		'd_jamount.require' => '请输入优惠券金额',
		'd_mamount.require' => '请输入优惠券使用条件',
		'd_endtime.require' => '请输入有效期',
		'd_nums.require' => '请输入优惠券数量',
		'd_jamount' => '优惠券金额格式不正确',
		'd_mamount' => '优惠券金额格式不正确',
		'd_endtime' => '时间格式不正确',
		'd_nums' => '数量必须是正整数'
  ];
}