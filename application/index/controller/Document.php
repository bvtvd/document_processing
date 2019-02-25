<?php
/**
 * Created by PhpStorm.
 * User: Hans
 * Date: 2019/2/24
 * Time: 22:33
 */

namespace app\index\controller;


use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

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

        dump($excelData);
        return ;
        // 数据取出处理, 压缩之后  返回压缩包


        $adminList = [
            ['admin_id' => 1, 'admin_username' => 'asfasdf', 'admin_password' => '123414', 'create_time' => 1551070090],
        ];
        //return $adminList;

        $newExcel = new Spreadsheet();  //创建一个新的excel文档
        $objSheet = $newExcel->getActiveSheet();  //获取当前操作sheet的对象
        $objSheet->setTitle('管理员表');  //设置当前sheet的标题

        //设置宽度为true,不然太窄了
        $newExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);

        //设置第一栏的标题
        $objSheet->setCellValue('A1', 'id')
            ->setCellValue('B1', '用户名')
            ->setCellValue('C1', '密码')
            ->setCellValue('D1', '时间');

        //第二行起，每一行的值,setCellValueExplicit是用来导出文本格式的。
        //->setCellValueExplicit('C' . $k, $val['admin_password']PHPExcel_Cell_DataType::TYPE_STRING),可以用来导出数字不变格式
        foreach ($adminList as $k => $val) {
            $k = $k + 2;
            $objSheet->setCellValue('A' . $k, $val['admin_id'])
                ->setCellValue('B' . $k, $val['admin_username'])
                ->setCellValue('C' . $k, $val['admin_password'])
                ->setCellValue('D' . $k, date('Y-m-d H:i:s', $val['create_time']));
        }

        $this->downloadExcel($newExcel, '管理员表', 'Xls');
    }



    //公共文件，用来传入xls并下载
    function downloadExcel($newExcel, $filename, $format)
    {
        // $format只能为 Xlsx 或 Xls
        if ($format == 'Xlsx') {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        } elseif ($format == 'Xls') {
            header('Content-Type: application/vnd.ms-excel');
        }

        header("Content-Disposition: attachment;filename="
            . $filename . date('Y-m-d') . '.' . strtolower($format));
        header('Cache-Control: max-age=0');
        $objWriter = IOFactory::createWriter($newExcel, $format);

        $objWriter->save('php://output');

        //通过php保存在本地的时候需要用到
        //$objWriter->save($dir.'/demo.xlsx');

        //以下为需要用到IE时候设置
        // If you're serving to IE 9, then the following may be needed
        //header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        //header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        //header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        //header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        //header('Pragma: public'); // HTTP/1.0
        exit;
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
                    $keys[$cell->getColumn()] = $cell->getFormattedValue();
                }

                $tmp[$keys[$cell->getColumn()]] = $cell->getFormattedValue();
            }

            if($row->getRowIndex() > 1){
                $res[] = $tmp;
            }

        }

        return $res;
    }
}
