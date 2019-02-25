<?php
/**
 * Created by PhpStorm.
 * User: Hans
 * Date: 2019/2/24
 * Time: 23:00
 */

namespace app\index\controller;

use app\index\models\CollectorModel;
use app\index\Models\MerchantModel;
use think\Validate;

class Merchant extends Base
{
    /**
     * 商家列表
     */
    public function index()
    {
        $merchants = MerchantModel::with('collector')->select();
//        dump($merchants); return;
        return view('index', [
            'merchants' => $merchants
        ]);
    }

    /**
     * 新增商家页面
     */
    public function create()
    {
        $collectors = CollectorModel::all();

        return view('create', [
            'collectors' => $collectors
        ]);
    }

    /**
     * 新增商家提交
     */
    public function store()
    {
        $data = request()->only(['name', 'collector_id']);

        $validate = new Validate([
            'name'  => 'require|max:50|unique:merchants',
            'collector_id' => 'require|gt:0'
        ], [
            'name.require' => '商家名称不能为空',
            'name.max' => '商家名称超过50个字符',
            'name.unique' => '商家已存在',
            'collector_id.require' => '请选择收单员',
            'collector_id.gt' => '请选择收单员'
        ]);


        if (!$validate->check($data)) {
            return $this->back($validate->getError());
        }

        // 数据入库
        MerchantModel::create($data);

        return $this->redirect('index/Merchant/index', [], 302, ['success' => '添加成功']);
    }

    /**
     * 商家编辑页面
     */
    public function edit($id)
    {
        $merchant = MerchantModel::get($id);
        $collectors = CollectorModel::all();
        return view('edit', [
            'merchant' => $merchant,
            'collectors' => $collectors
        ]);
    }

    /**
     * 商家编辑提交
     */
    public function update($id)
    {
        $data = request()->only(['name', 'collector_id']);

        $validate = new Validate([
            'name'  => 'require|max:50|unique:merchants,name,'. $id,
            'collector_id' => 'require|gt:0'
        ], [
            'name.require' => '商家名称不能为空',
            'name.max' => '商家名称超过50个字符',
            'name.unique' => '商家已存在',
            'collector_id.require' => '请选择收单员',
            'collector_id.gt' => '请选择收单员'
        ]);

        if (!$validate->check($data)) {
            return $this->back($validate->getError());
        }

        // 数据修改
        $merchant = new MerchantModel();
        $merchant->save($data, ['id' => $id]);

        return $this->redirect('index/Merchant/index', [], 302, ['success' => '修改成功']);
    }

    /**
     * 商家删除
     */
    public function destroy($id)
    {
        MerchantModel::destroy($id);

        return $this->redirect('index/Merchant/index', [], 302, ['success' => '删除成功']);
    }
}
