<?php
/**
 * Project: sample
 * User: chenzhidong
 * Date: 14-9-23
 * Time: 20:36
 */
Yii::import('application.vendor.phpexcel.PHPExcel', true);

class ExcelHelper {
    public static function read($file, $headline = 0, $verticle = false) {
        $headline += 1;
        $excel_arr = array();
        $excel = PHPExcel_IOFactory::load($file);
        if ($excel)
        {
            $sheet_count = $excel->getSheetCount();
            if ($sheet_count > 0)
            {
                //循环sheet
                for ($i = 0; $i < $sheet_count; $i++)
                {
                    $current_sheet = $excel->getSheet($i);
                    $row_num = $current_sheet->getHighestRow();
                    $col_num = $current_sheet->getHighestColumn();
                    //循环row
                    if ($verticle)
                    {
                        //列
                        for ($c = 'A'; $c <= $col_num; $c++)
                        {
                            $cell_values = array();
                            for ($l = $headline; $l <= $row_num; $l++)
                            {
                                $address = $c . $l;
                                $cell_values[$address] = $current_sheet->getCell($address)->getFormattedValue();
                            }
                            $excel_arr[$i][$c] = $cell_values;
                        }
                    }
                    else
                    {
                        //排
                        for ($l = $headline; $l <= $row_num; $l++)
                        {
                            $cell_values = array();
                            for ($c = 'A'; $c <= $col_num; $c++)
                            {

                                $address = $c . $l;
                                $cell_values[$address] = $current_sheet->getCell($address)->getFormattedValue();
                            }
                            $excel_arr[$i][$l] = $cell_values;
                        }
                    }
                }
            }
        }
        unset($excel);

        return $excel_arr;
    }

    public static function write_browser($title, $header = array(), $data = array()) {
        $excel = new PHPExcel();
        $excel->getProperties()->setCreator('Hualongxiang');
        $excel->getProperties()->setTitle($title);
        $excel->getProperties()->setSubject($title);
        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle($title);
        $start_s = 'A';
        $start_i = 1;
        if ($header)
        {
            foreach ($header as $item)
            {
                $sheet->setCellValue($start_s . $start_i, $item);
                $start_s++;
            }
            $start_i++;
        }
        if ($data)
        {
            foreach ($data as $item)
            {
                $start_s = 'A';
                foreach ($item as $value)
                {
                    $sheet->setCellValue($start_s . $start_i, $value);
                    $start_s++;
                }
                $start_i++;
            }
        }
        $writer = new PHPExcel_Writer_Excel2007($excel);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment;filename = ' . $title . '.xlsx');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check = 0, pre-check = 0');
        header('Pragma: no-cache');
        $writer->save('php://output');
    }

    public static function write_file($filepath, $title, $header = array(), $data = array()) {
        $excel = new PHPExcel();
        $excel->getProperties()->setCreator('Hualongxiang');
        $excel->getProperties()->setTitle($title);
        $excel->getProperties()->setSubject($title);
        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle($title);
        $start_s = 'A';
        $start_i = 1;
        if ($header)
        {
            foreach ($header as $item)
            {
                $sheet->setCellValue($start_s . $start_i, $item);
                $start_s++;
            }
            $start_i++;
        }
        if ($data)
        {
            foreach ($data as $item)
            {
                $start_s = 'A';
                foreach ($item as $value)
                {
                    $sheet->setCellValue($start_s . $start_i, $value);
                    $start_s++;
                }
                $start_i++;
            }
        }
        $writer = new PHPExcel_Writer_Excel2007($excel);
        $writer->save($filepath);
    }

    public static function cvs_write_browser($title, $header = array(), $data = array()) {
        $str = '';
        array_unshift($data, $header);
        foreach ($data as $key => $val)
        {
            foreach ($val as $kkey => $vval)
            {
                str_replace(array("\t", "\n"), ';', $vval);
                $str .= '"' . mb_convert_encoding($vval, 'gbk', 'utf-8') . '",';
            }
            $str .= "\n";
        }
        header('Content-Description: File Transfer');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename = ' . $title . '.csv');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check = 0, pre-check = 0');
        header('Pragma: no-cache');
        echo $str;
        exit();
    }
}
