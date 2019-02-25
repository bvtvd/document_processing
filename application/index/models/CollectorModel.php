<?php
/**
 * Created by PhpStorm.
 * User: Hans
 * Date: 2019/2/24
 * Time: 23:23
 */

namespace app\index\models;


use think\Model;

class CollectorModel extends Model
{
    /**
     * commission 佣金的存储单位为分
     *
     */

    protected $table = 'collectors';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

    protected $type = [
        'commission' => 'float'
    ];

    public function getCommissionAttr($value)
    {
        return $value / 100;
    }

    public function setCommissionAttr($value)
    {
        return $value * 100;
    }

    public function merchants()
    {
        return $this->hasMany('MerchantModel', 'collector_id', 'id');
    }
}
