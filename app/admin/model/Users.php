<?php
namespace app\admin\model;

use think\Model;

class Users extends Model
{
	protected $field = true; //自动过滤掉不存在的字段
	protected $table = 'users'; // 设置当前模型对应的完整数据表名称
	protected $resultSetType = 'collection';// 设置模型的数据集返回类型
	protected $autoWriteTimestamp = true; // 开启自动写入时间戳
	protected $readonly = ['u_account','u_password','u_is_merchants'];
}