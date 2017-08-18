<?php
class ExportCommand extends CConsoleCommand
{
    private $excel;

    public function init()
    {
        // set_time_limit(0);
    }

    public function actionPlot()
    {
        $page = 0;
        begin:
        // $dataArr = $this->queryAll('plot', $page++);
        $data = $this->queryAllByModel('PlotExt', $page++);
        if($data) {
            foreach($data as $k=>$v) {
                $row[$k] = [
                    'id' => $v->id,
                    '楼盘名称' => $v->title,
                    '楼盘拼音' => $v->pinyin,
                    '拼音首字母' => $v->fcode,
                    '销售状态（标签id）' => $v->sale_status,
                    '对应的论坛标签id' => $v->tag_id,
                    '是否新房' => $v->is_new,
                    '是否分销' => $v->is_coop,
                    '一级区域id' => $v->area,
                    '二级区域id' => $v->street,
                    '开盘时间' => $v->open_time,
                    '交付时间' => $v->delivery_time,
                    '楼盘地址' => $v->address,
                    '售楼地址' => $v->sale_addr,
                    '售楼电话' => $v->sale_tel,
                    '经度' => $v->map_lng,
                    '纬度' => $v->map_lat,
                    '楼盘封面图' => $v->image,
                    '楼盘价格' => $v->price,
                    '楼盘价格单位' => $v->unit,
                    '楼盘价格标识' => $v->price_mark,
                    '楼盘状态' => $v->status,
                    '浏览数' => $v->views,
                    '楼盘删除时间' => $v->deleted,
                    '楼盘添加时间' => $v->created,
                    '楼盘更新时间' => $v->updated,
                    '建筑面积' => @$v->data_conf['buildsize'],           //建筑面积
                    '占地面积' => @$v->data_conf['size'],                //占地面积
                    '容积率' => @$v->data_conf['capacity'],            //容积率
                    '绿化率' => @$v->data_conf['green'],               //绿化率
                    '物业费' => @$v->data_conf['manage_fee'],         //物业费
                    '物业公司' => @$v->data_conf['manage_company'],     //物业公司
                    '开发商' => @$v->data_conf['developer'],          //开发商
                    '代理商' => @$v->data_conf['agent'],              //代理商
                    '许可证' => @$v->data_conf['license'],            //许可证
                    '交通状况' => @$v->data_conf['transit'],            //交通状况
                    '项目介绍' => @$v->data_conf['content'],            //项目介绍
                    '项目配套' => @$v->data_conf['peripheral'],         //项目配套
                    'SEO标题' => @$v->data_conf['seo_title'],          //SEO title
                    'SEO关键词' => @$v->data_conf['seo_keywords'],       //SEO keywords
                    'SEO描述' => @$v->data_conf['seo_description'],    //SEO description
                    '备案名称'=> @$v->data_conf['recordname'],           //备案名
                ];
            }
            $this->writeFile('plot', $row);
        }
    }

    public function queryAllByModel($modelName, $page, $limit=3000)
    {
        return $modelName::model()->findAll([
            'condition' => 'id>:offset',
            'limit' => $limit,
            'params' => [':offset'=>$page*$limit]
        ]);
    }

    private function writeFile($tableName, array $data)
    {
        $dirPath = Yii::app()->getRuntimePath().DIRECTORY_SEPARATOR.'exportdata';
        $this->ensureDirectory($dirPath);

        $header = array_keys($data[0]);
        ExcelHelper::write_file($dirPath.DIRECTORY_SEPARATOR.$tableName.'.csv',$tableName,$header,$data);
    }

    private function queryAll($tableName, $page, $limit=3000)
    {
        return Yii::app()->db->createCommand('SELECT * FROM :tableName WHERE `id`>:offset LIMIT :limit')->queryAll(true, [':tableName'=>$tableName, ':offset'=>$page*$limit, ':limit'=>$limit]);
    }

    private function Export(array $data, array $attributes)
    {
        if($this->excel===null) {
            // $this->excel = new PHPExcel;
        }

    }

}
