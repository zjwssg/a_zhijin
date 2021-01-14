<?php
namespace app\admin\model;

use think\Model;

class Admin extends Model
{
	protected $field = true; //自动过滤掉不存在的字段
	protected $table = 'admin'; // 设置当前模型对应的完整数据表名称
	protected $readonly = ['addtime']; // 只读字段,字段的值一旦写入，就无法更改
	protected $autoWriteTimestamp = true; // 开启自动写入时间戳
	protected $createTime = 'addtime';
	// 权限表
	public function group(){
		return $this->belongsTo('group','admin_gid','id')->field('id,group_name,group_roleid');
	}
	public function getLogintimeAttr($value){
		return $value==""?'无':date('Y-m-d H:i:s',$value);
	}
}