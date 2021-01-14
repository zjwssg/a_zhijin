<?php
namespace app\api\model;

use think\Model;

class Message extends Model
{
	protected $field = true; //自动过滤掉不存在的字段
	protected $table = 'message'; // 设置当前模型对应的完整数据表名称
	protected $readonly = ['addtime']; // 只读字段,字段的值一旦写入，就无法更改
	protected $resultSetType = 'collection';// 设置模型的数据集返回类型
	protected $autoWriteTimestamp = true; // 开启自动写入时间戳
	protected $createTime = 'addtime';
	protected $updateTime = 'edittime'; 
}