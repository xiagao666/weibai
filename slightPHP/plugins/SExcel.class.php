<?php

/**
 *
 * Excel 基本调用实例
 * $excel=new SExcel();
 * $excel->file_name="导出文件名".date("Y-m-d H:i:s");
 * $excel->title=array('ID',"名称",'状态','添加日期');
 * $data[]=array(1,'名称1','正常','2014-09-05');
 * $data[]=array(2,'名称2','锁定','2014-09-06');
 * $data[]=array(3,'名称3','锁定','2014-09-06');
 * $data[]=array(4,'名称4','正常','2014-09-06');
 * $excel->exportExcel($data);                       //导出
 */
class SExcel
{

    const CELL_DATATYPE_STRING = 's';
    const CELL_DATATYPE_FORMULA = 'f';
    const CELL_DATATYPE_NUMERIC = 'n';
    const CELL_DATATYPE_BOOL = 'b';
    const CELL_DATATYPE_NULL = 's';
    const CELL_DATATYPE_INLINE = 'inlineStr';
    const MAX_ROW_NUM = 65536;

    /**
     * 创建人
     */
    public $excelCreator;

    /**
     * 标题
     */
    public $excelTitle;

    /**
     * 主题
     */
    public $excelSubject;

    /**
     * 描述
     */
    public $excelDescription;
    public $has_title = true;
    public $title = array();
    public $file_name;

    /**
     * 合并单元格
     */
    public $mergeCells = array();

    /**
     * 导出到Excel
     */
    public function exportExcel($data = array(), $cellDataType = array())
    {
        if (!$data || count($data) > self::MAX_ROW_NUM) {
            return false;
        }
        //获得execl列
        $cells = $this->_getColumnNumber();

        include_once 'Excel/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        if ($this->excelCreator) {
            $objPHPExcel->getProperties()->setCreator($this->excelCreator); //创建人
            $objPHPExcel->getProperties()->setLastModifiedBy($this->excelCreator); //最后修改人
        }
        if ($this->excelTitle) {
            $objPHPExcel->getProperties()->setTitle($this->excelTitle); //标题
        }
        if ($this->excelSubject) {
            $objPHPExcel->getProperties()->setSubject($this->excelSubject); //主题
        }
        if ($this->excelDescription) {
            $objPHPExcel->getProperties()->setDescription($this->excelDescription); //描述
        }

        $objPHPExcel->setActiveSheetIndex(0);
        $objActSheet = $objPHPExcel->getActiveSheet();

        //第二维数组中元素下标
        $attrNames = array_keys(reset($data));
        //设置标题
        if ($this->has_title) {
            if ($this->title) {
                $title = $this->title;
            } else {
                $title = $attrNames;
            }
            $i = 0;
            foreach ($title as $attr) {
                $cell = $cells[$i] . '1';
                $objActSheet->setCellValue($cell, $attr);
                $i++;
            }
            $rowStart = 1;
        } else {
            $rowStart = 0;
        }

        //遍历内容
        $colNo = 0;
        foreach ($attrNames as $attr) {
            $rowNo = $rowStart;
            $columnType = null;
            if (array_key_exists($attr, $cellDataType)) {
                $columnType = $cellDataType[$attr];
            }
            foreach ($data as $rowKey => $row) {
                $cell = $cells[$colNo] . ($rowNo + 1);
                $cvalue = $data[$rowKey][$attr];
                if ($columnType) {
                    $objActSheet->setCellValueExplicit($cell, $cvalue, $columnType);
                } else {
                    $objActSheet->setCellValue($cell, $cvalue);
                }
                $rowNo++;
            }
            $colNo++;
        }
        if ($this->mergeCells) {
            foreach ($this->mergeCells as $value) {
                $objActSheet->mergeCells($value);
            }
        }

        if ($this->file_name) {
            $file_name = $this->file_name;
        } else {
            $file_name = date('YmdHis') . str_pad(mt_rand(0, 99), 2, 0, STR_PAD_LEFT);
        }
        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file_name . '.xls"');
        header('Cache-Control: max-age=0');
        $objWriter->save("php://output");
    }

    /**
     * 导入Excel
     */
    public function importExcel($path = '', $rowRange = array(), $columnRange = array())
    {
        include_once 'Excel/PHPExcel/IOFactory.php';
//        $pathinfo = pathinfo($path);
//        if ($pathinfo['extension'] == 'xlsx') {
            $reader = PHPExcel_IOFactory::createReader('Excel2007');
//        } else {
//            $reader = PHPExcel_IOFactory::createReader('Excel5');
//        };
        $PHPExcel = $reader->load($path);
        $sheet = $PHPExcel->getSheet(0);
        $rowCount = $sheet->getHighestRow(); // 取得总行数

        $columnCount = $sheet->getHighestColumn(); // 取得总列数
        $colNumber = $this->_getColumnNumber(); //获得列
        $max_column_index = array_search($columnCount, $colNumber); //获得最大列的索引
        $colNumber = array_slice($colNumber, 0, $max_column_index + 1);

        //获取行的范围
        if (isset($rowRange[0])) {
            $rowNo = $rowRange[0] + 1;
        } else {
            $rowNo = 1;
        }
        if (isset($rowRange[1]) && $rowRange[1] >= 0 && $rowCount > $rowRange[1]) {
            $rowCount = $rowRange[1] + 1;
        }
        //获取列的范围
        if (isset($columnRange[0])) {
            if (is_string($columnRange[0])) {
                $min_index = array_search($columnRange[0], $colNumber);
                if ($min_index && $min_index < $max_column_index) {
                    $min_column_index = $min_index;
                }
            } elseif ($columnRange[0] >= 0 && $columnRange[0] < $max_column_index) {
                $min_column_index = $columnRange[0];
            }
        } else {
            $min_column_index = 0;
        }

        if (isset($columnRange[1])) {
            if (is_string($columnRange[1])) {
                $max_index = array_search($columnRange[1], $colNumber);
                if ($max_index && $max_index < $max_column_index) {
                    $colCount = $max_index;
                }
            } elseif ($columnRange[1] >= 0 && $columnRange[1] < $max_column_index) {
                $colCount = $columnRange[1];
            }
        } else {
            $colCount = $max_column_index;
        }
        //获取标题
        if ($this->has_title) {
            if ($this->title) {
                $title = $this->title;
            } else {
                for ($colNo = $min_column_index; $colNo <= $colCount; $colNo++) {
                    $val = $sheet->getCellByColumnAndRow($colNo, 1)->getValue();
                    $title[$colNo] = $val;
                }
            }
        }

        $data = array();
        for ($rowNo; $rowNo <= $rowCount; $rowNo++) {
            $data2 = array();
            for ($colNo = $min_column_index; $colNo <= $colCount; $colNo++) {
                $val = $sheet->getCellByColumnAndRow($colNo, $rowNo)->getValue();
                if ($this->has_title) {
                    $data2[$title[$colNo]] = $val;
                } else {
                    $data2[] = $val;
                }
            }
            $data[] = $data2;
        }
        return $data;
    }

    /**
     * @return array
     */
    private function _getColumnNumber()
    {
        $cells1 = range('A', 'Z');
        $cells2 = array();
        foreach ($cells1 as $value1) {
            foreach ($cells1 as $value2) {
                $cells2[] = $value1 . $value2;
                if ($value1 . $value2 == 'IV') {
                    break;
                }
            }
            if ($value1 == 'I') {
                break;
            }
        }
        $cells = array_merge($cells1, $cells2);
        return $cells;
    }
}