<?php
/**
 * Created by PhpStorm.
 * User: Hans
 * Date: 2019/2/24
 * Time: 23:01
 */

namespace app\index\controller;



use app\index\models\CollectorModel;
use think\Validate;

class Collector extends Base
{

    public function index()
    {
        $collectors = CollectorModel::with('merchants')->select();
        return view('index', [
            'collectors' => $collectors
        ]);
    }

    public function create()
    {
        return view('create');
    }

    public function store()
    {
        $data = request()->only(['name', 'commission']);

        $validate = new Validate([
            'name'  => 'require|max:50|unique:collectors',
            'commission' => 'require|number'
        ], [
            'name.require' => '收单员姓名不能为空',
            'name.max' => '收单员姓名超过50个字符',
            'name.unique' => '该收单员已存在',
            'commission.require' => '佣金不能为空',
            'commission.number' => '佣金必须为数字'
        ]);


        if (!$validate->check($data)) {
            return $this->back($validate->getError());
        }

        // 数据入库
        CollectorModel::create($data);

        return $this->redirect('index/Collector/index', [], 302, ['success' => '添加成功']);
    }

    public function edit($id)
    {
        $colletcor = CollectorModel::get($id);

        return view('edit', [
            'collector' => $colletcor
        ]);
    }

    /**
     * 商家编辑提交
     */
    public function update($id)
    {
        $data = request()->only(['name', 'commission']);

        $validate = new Validate([
            'name'  => 'require|max:50|unique:collectors,name,' . $id,
            'commission' => 'require|number'
        ], [
            'name.require' => '收单员姓名不能为空',
            'name.max' => '收单员姓名超过50个字符',
            'name.unique' => '该收单员已存在',
            'commission.require' => '佣金不能为空',
            'commission.number' => '佣金必须为数字'
        ]);


        if (!$validate->check($data)) {
            return $this->back($validate->getError());
        }

        // 数据修改
        $collector = new CollectorModel();
        $collector->save($data, ['id' => $id]);

        return $this->redirect('index/Collector/index', [], 302, ['success' => '修改成功']);
    }

    public function destroy($id)
    {
        CollectorModel::destroy($id);

        return $this->redirect('index/Collector/index', [], 302, ['success' => '删除成功']);
    }
}
