<?php


namespace app\admin\model;


use think\Model;

class Coupon extends Model
{
    protected $field = true; //自动过滤掉不存在的字段
    protected $table = 'coupon'; // 设置当前模型对应的完整数据表名称
    protected $readonly = ['c_create_time']; // 只读字段,字段的值一旦写入，就无法更改
    protected $resultSetType = 'collection';// 设置模型的数据集返回类型
    protected $autoWriteTimestamp = true; // 开启自动写入时间戳
    public function getOTypeAttr($value)
    {
        $status = [1=>'满减券',2=>'叠加满减券',3=>'无门槛券'];
        return $status[$value];
    }

}