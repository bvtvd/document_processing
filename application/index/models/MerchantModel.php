<?php
/**
 * Created by PhpStorm.
 * User: Hans
 * Date: 2019/2/24
 * Time: 23:23
 */

namespace app\index\models;


use think\Model;

class MerchantModel extends Model
{
    protected $table = 'merchants';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

    public function collector()
    {
        return $this->belongsTo('CollectorModel', 'collector_id', 'id');
    }
}
