<?php
/**
 * Created by PhpStorm.
 * User: Hans
 * Date: 2019/2/24
 * Time: 22:33
 */

namespace app\index\controller;


use app\index\models\MerchantModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use ZipArchive;

class Document extends Base
{
    /**
     * 文档处理展示页
     */
    public function index()
    {
        return view('index');
    }

    /**
     * 文档处理
     */
    public function handle()
    {
        ini_set('max_execution_time', '0');
        $file = request()->file('file');

        if(!$file) return $this->back('请选择需要处理的 EXCEL 文档');

        $excelData = $this->getExcelDataAsArray($file);

        $merchants = $this->getMerchantsIndexCollectorValue();

        // 数据取出处理, 压缩之后  返回压缩包
        // 1. 拼接店铺名, 收单员名称加店铺名称
        // 2. 填写佣金
        // 3. 填写总计, 本金 + 佣金
        // 4. 文件命名, 京东A总计444

        // 先处理数据
        $data = [];

        $merchantName = '';
        foreach($excelData as $item){
            $merchantName = trim($item['店铺名']);

            if(isset($merchants[$merchantName])){
                $data[$merchants[$merchantName]['collector']][] = [
                    '电话号码' => $item['电话号码'],
                    '店铺名' => $merchants[$merchantName]['collector'].$item['店铺名'],
                    '关键词' => $item['关键词'],
                    '订单号' => $item['订单号'],
                    '本金' => $item['本金'],
                    '佣金' => $merchants[$merchantName]['commission'],
                    '总计' => $item['总计'],
                ];
            }else{
                $data['其他'][] = [
                    '电话号码' => $item['电话号码'],
                    '店铺名' => $item['店铺名'],
                    '关键词' => $item['关键词'],
                    '订单号' => $item['订单号'],
                    '本金' => $item['本金'],
                    '佣金' => null,
                    '总计' => $item['总计'],
                ];
            }
        }

        // 计算总计
        ob_clean();
        // 先清除残余文件
        $dir = ROOT_PATH. 'public'.DS.'static'.DS.'file'. DS;
        $this->createDir($dir);
        $this->delDirAndFile($dir);


        foreach ($data as $collecotr => $item){

            $newExcel = new Spreadsheet();  //创建一个新的excel文档
            $objSheet = $newExcel->getActiveSheet();  //获取当前操作sheet的对象
            $objSheet->setTitle('sheet1');  //设置当前sheet的标题

            //设置宽度为true,不然太窄了
            $newExcel->getActiveSheet()->getColumnDimension('A')->setWidth(22);
            $newExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
            $newExcel->getActiveSheet()->getColumnDimension('C')->setWidth(22);
            $newExcel->getActiveSheet()->getColumnDimension('D')->setWidth(22);
            $newExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
            $newExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
            $newExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);

            //设置第一栏的标题
            $objSheet->setCellValue('A1', '电话号码')
                ->setCellValue('B1', '店铺名')
                ->setCellValue('C1', '关键词')
                ->setCellValue('D1', '订单号')
                ->setCellValue('E1', '本金')
                ->setCellValue('F1', '佣金')
                ->setCellValue('G1', '总计');

            // 设置颜色
            $objSheet->getStyle('A1')->getFont()->getColor()->setARGB(Color::COLOR_RED);
            $objSheet->getStyle('B1')->getFont()->getColor()->setARGB(Color::COLOR_RED);
            $objSheet->getStyle('C1')->getFont()->getColor()->setARGB(Color::COLOR_RED);
            $objSheet->getStyle('D1')->getFont()->getColor()->setARGB(Color::COLOR_RED);
            $objSheet->getStyle('E1')->getFont()->getColor()->setARGB(Color::COLOR_RED);
            $objSheet->getStyle('F1')->getFont()->getColor()->setARGB(Color::COLOR_RED);
            $objSheet->getStyle('G1')->getFont()->getColor()->setARGB(Color::COLOR_RED);

            //第二行起，每一行的值,setCellValueExplicit是用来导出文本格式的。
            //->setCellValueExplicit('C' . $k, $val['admin_password']PHPExcel_Cell_DataType::TYPE_STRING),可以用来导出数字不变格式
            $total = 0;
            foreach ($item as $k => $val) {

                $k = $k + 2;
                $objSheet->setCellValue('A' . $k, $val['电话号码'])
                    ->setCellValue('B' . $k, $val['店铺名'])
                    ->setCellValue('C' . $k, $val['关键词'])
                    ->setCellValue('D' . $k, $val['订单号'])
                    ->setCellValue('E' . $k, $val['本金'])
                    ->setCellValue('F' . $k, $val['佣金'])
                    ->setCellValue('G' . $k, $val['总计']);

                $total += bcadd($val['本金'], $val['佣金'], 2);
            }

            $objSheet->setCellValue('G2', $total);

            $format = 'Xlsx';

            $objWriter = IOFactory::createWriter($newExcel, $format);

            $fileName = ROOT_PATH. 'public'.DS.'static'.DS.'file'. DS. $collecotr . '总计' .$total .'.' .$format;

            $objWriter->save($fileName);
        }

        $this->downloadZipFile();
    }

    public function downloadZipFile()
    {
        // 将文件打包下载
        $zipName = 'file.zip';

        $files = $this->getPackFileArray();

        $zip = new ZipArchive();

        $res = $zip->open($zipName, ZipArchive::CREATE);

        if ($res === TRUE) {
            foreach ($files as $item) {
                //这里直接用原文件的名字进行打包，也可以直接命名，需要注意如果文件名字一样会导致后面文件覆盖前面的文件，所以建议重新命名
                $zip->addFile($item, basename($item));
            }
        }

        $zip->close();

        $dir = ROOT_PATH. 'public'.DS.'static'.DS.'file'. DS;
        $this->delDirAndFile($dir);

        //这里是下载zip文件
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: Binary");

        header("Content-Length: " . filesize($zipName));
        header("Content-Disposition: attachment; filename=\"" . basename($zipName) . "\"");

        readfile($zipName);
    }

    /**
     * 将excel 的数据以关联数组的形式输出
     * @param $file
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function getExcelDataAsArray($file)
    {
        $spreadsheet = IOFactory::load($file->getFileInfo()->getPathname());

        $workSheet = $spreadsheet->getActiveSheet();

        $res = array();


        $keys = [];

        foreach ($workSheet->getRowIterator(1) as $row) {

            $tmp = array();

            foreach ($row->getCellIterator() as $cell) {

                if(1 == $row->getRowIndex()){
                    $keys[$cell->getColumn()] = trim($cell->getFormattedValue());
                }

                $tmp[$keys[$cell->getColumn()]] = trim($cell->getFormattedValue());
            }

            if($row->getRowIndex() > 1){
                if(array_filter(array_values($tmp))){
                    $res[] = $tmp;
                }
            }

        }

        return $res;
    }

    public function getMerchantsIndexCollectorValue()
    {
        $merchants = MerchantModel::with('collector')->select();

        $array = [];

        foreach($merchants as $merchant){
            $array[trim($merchant->name)] = [
                'collector' => $merchant->collector->name,
                'commission' => $merchant->collector->commission
            ];
        }

        return $array;
    }

    public function delDirAndFile($path, $delDir = FALSE) {
        if (is_array($path)) {
            foreach ($path as $subPath)
                $this->delDirAndFile($subPath, $delDir);
        }
        if (is_dir($path)) {
            $handle = opendir($path);
            if ($handle) {
                while (false !== ( $item = readdir($handle) )) {
                    if ($item != "." && $item != "..")
                        is_dir("$path" . DS ."$item") ? $this->delDirAndFile("$path" . DS ."$item", $delDir) : unlink("$path" . DS ."$item");
                }
                closedir($handle);
                if ($delDir)
                    return rmdir($path);
            }
        } else {
            if (file_exists($path)) {
                return unlink($path);
            } else {
                return FALSE;
            }
        }
        clearstatcache();
    }

    public function createDir($path)
    {
        if(!is_dir($path)){
            mkdir($path);
        }
    }

    public function getPackFileArray()
    {
        clearstatcache();

        $arr = [];

        $path = ROOT_PATH . 'public' . DS . 'static'. DS .'file' .DS;

        $handle = opendir($path);

        if ($handle) {
            while (false !== ( $item = readdir($handle) )) {
                if ($item != "." && $item != ".."){
                    array_push($arr, "$path$item");
                }
            }
            closedir($handle);
        }

        return $arr;
    }
}
