<?php
class DatabaseCommand extends CConsoleCommand
{
    /**
     * 与61.160.251.70数据库进行对比
     */
    public function actionDiff()
    {
        $new_host = '61.160.251.70';
        $new_user = 'hlxdev';
        $new_pass = 'hlx_dev.123456@#';
        $new_db = 'hj_house_esf';

        preg_match('/^mysql:host=(.*)\;dbname=(.*)$/', Yii::app()->db->connectionString, $match);
        $old_host = $match[1];
        $old_user = Yii::app()->db->username;
        $old_pass = Yii::app()->db->password;
        $old_db = $match[2];

        if (!empty($new_host) && !empty($new_user) && !empty($new_pass) && !empty($new_db) && !empty($old_host) && !empty($old_user) && !empty($old_pass) && !empty($old_db)) {
            $new_errors = array();
            $old_errors = array();
            $new = $this->get_db_detail($new_host, $new_user, $new_pass, $new_db, $new_errors);
            $old = $this->get_db_detail($old_host, $old_user, $old_pass, $old_db, $old_errors);
            if (!empty($new_errors) || !empty($old_errors))
            {
                echo "错误\n";
                var_dump($new_errors);
                var_dump($old_errors);
            }
            else {
                $diff = $this->compare_database($new, $old);
                if (empty($diff['table']) && empty($diff['field']) && empty($diff['index'])) {
                    echo "两个数据库结构完全相同，无可同步项目\n";die;
                }
                else {
                    $sqls = $this->build_query($diff);
                    $this->showdiff($old_host, $old_user, $old_pass, $old_db, $sqls);
                }
            }
        }
        else {
            echo "数据库信息有误\n";die;
        }
    }

    private function get_db_detail($server, $username, $password, $database, &$errors = array()) {
        $connection = @mysql_connect($server, $username, $password);
        if ($connection === false) {
            $errors[] = '无法连接服务器:' . $server . ':' . $username . ':' . $password . ":" . $database;
            return false;
        }
        $serverset = 'character_set_connection=utf8, character_set_results=utf8, character_set_client=binary';
        $serverset .= @mysql_get_server_info($connection) > '5.0.1' ? ', sql_mode=\'\'' : '';
        @mysql_query("SET $serverset", $connection);
        if (!@mysql_select_db($database, $connection)) {
            $errors[] = '无法使用数据库:' . $database;
            @mysql_close($connection);
            return false;
        }

        $detail = array('table' => array(), 'field' => array(), 'index' => array());
        $tables = $this->query($connection, "show table status");
        if ($tables) {
            foreach ($tables as $key_table => $table) {
                $detail['table'][$table['Name']] = $table;
                //字段
                $fields = $this->query($connection, "show full fields from `" . $table['Name'] . "`");
                if ($fields) {
                    foreach ($fields as $key_field => $field) {
                        $fields[$field['Field']] = $field;
                        unset($fields[$key_field]);
                    }
                    $detail['field'][$table['Name']] = $fields;
                }
                else {
                    $errors[] = '无法获得表的字段:' . $database . ':' . $table['Name'];
                }
                //索引
                $indexes = $this->query($connection, "show index from `" . $table['Name'] . "`");
                if ($indexes) {
                    foreach ($indexes as $key_index => $index) {
                        if (!isset($indexes[$index['Key_name']])) {
                            $index['Column_name'] = array($index['Seq_in_index'] => $index['Column_name']);
                            $indexes[$index['Key_name']] = $index;
                        }
                        else
                            $indexes[$index['Key_name']]['Column_name'][$index['Seq_in_index']] = $index['Column_name'];
                        unset($indexes[$key_index]);
                    }
                    $detail['index'][$table['Name']] = $indexes;
                }
                else {
                    //$errors[]='无法获得表的索引信息:'.$database.':'.$table['Name'];
                    $detail['index'][$table['Name']] = array();
                }
            }
            @mysql_close($connection);
            return $detail;
        }
        else {
            $errors[] = '无法获得数据库的表详情:' . $database;
            @mysql_close($connection);
            return false;
        }
    }


    private function showdiff($old_host, $old_user, $old_pass, $old_db, $sqls, $errors = array(), $keys = array()) {
        echo '共'.count($sqls)."条\n";
        foreach($sqls as $key => $sql)
        {
            echo $sql."\n";
        }
    }

    private function build_query($diff) {
        $sqls = array();
        if ($diff) {
            if (isset($diff['table']['drop'])) {
                foreach ($diff['table']['drop'] as $table_name => $table_detail) {
                    $sqls[] = "DROP TABLE `{$table_name}`";
                }
            }
            if (isset($diff['table']['create'])) {
                foreach ($diff['table']['create'] as $table_name => $table_detail) {
                    $fields = $diff['field']['create'][$table_name];
                    $sql = "CREATE TABLE `$table_name` (";
                    $t = array();
                    $k = array();
                    foreach ($fields as $field) {
                        $t[] = "`{$field['Field']}` " . strtoupper($field['Type']) . $this->sqlnull($field['Null']) . $this->sqldefault($field['Default']) . $this->sqlextra($field['Extra']) . $this->sqlcomment($field['Comment']);
                    }
                    if (isset($diff['index']['create'][$table_name]) && !empty($diff['index']['create'][$table_name])) {
                        $indexs = $diff['index']['create'][$table_name];
                        foreach ($indexs as $index_name => $index_detail) {
                            if ($index_name == 'PRIMARY')
                                $k[] = "PRIMARY KEY (`" . implode('`,`', $index_detail['Column_name']) . "`)";
                            else
                                $k[] = ($index_detail['Non_unique'] == 0 ? "UNIQUE" : "INDEX") . "`$index_name`"." (`" . implode('`,`', $index_detail['Column_name']) . "`)";
                        }
                    }
                    list($charset) = explode('_', $table_detail['Collation']);
                    $sql .= implode(', ', $t) . (!empty($k) ? ',' . implode(', ', $k) : '') . ') ENGINE = ' . $table_detail['Engine'] . ' DEFAULT CHARSET = ' . $charset;
                    $sqls[] = $sql;
                }
            }
            if(isset($diff['table']['change'])){
                foreach($diff['table']['change'] as $table_name=>$table_changes){
                    if(!empty($table_changes)){
                        $sql="ALTER TABLE `$table_name`";
                        foreach($table_changes as $option=>$value){
                            if($option=='Collation'){
                                list($charset) = explode('_', $value);
                                $sql.=" DEFAULT CHARACTER SET $charset COLLATE $value";
                            }
                            else
                                $sql.=" ".strtoupper($option)." = $value ";
                        }
                        $sqls[]=$sql;
                    }
                }
            }
            if (isset($diff['field']['drop'])) {
                foreach ($diff['field']['drop'] as $table_name => $fields) {
                    foreach ($fields as $field_name => $field_detail) {
                        $sqls[] = "ALTER TABLE `$table_name` DROP `$field_name`";
                    }
                }
            }
            if (isset($diff['field']['add'])) {
                foreach ($diff['field']['add'] as $table_name => $fields) {
                    foreach ($fields as $field_name => $field_detail) {
                        $sqls[] = "ALTER TABLE `$table_name` ADD `{$field_name}` " . strtoupper($field_detail['Type']) . $this->sqlcol($field_detail['Collation']) . $this->sqlnull($field_detail['Null']) . $this->sqldefault($field_detail['Default']) . $this->sqlextra($field_detail['Extra']) . $this->sqlcomment($field_detail['Comment']) . " AFTER `{$field_detail['After']}`";
                    }
                }
            }
            if (isset($diff['index']['drop'])) {
                foreach ($diff['index']['drop'] as $table_name => $indexs) {
                    foreach ($indexs as $index_name => $index_detail) {
                        if ($index_name == 'PRIMARY')
                            $sqls[] = "ALTER TABLE `$table_name` DROP PRIMARY KEY";
                        else
                            $sqls[] = "ALTER TABLE `$table_name` DROP INDEX `$index_name`";
                    }
                }
            }
            if (isset($diff['index']['add'])) {
                foreach ($diff['index']['add'] as $table_name => $indexs) {
                    foreach ($indexs as $index_name => $index_detail) {
                        if ($index_name == 'PRIMARY')
                            $sqls[] = "ALTER TABLE `$table_name` ADD PRIMARY KEY (`" . implode('`,`', $index_detail['Column_name']) . "`)";
                        else
                            $sqls[] = "ALTER TABLE `$table_name` ADD" . ($index_detail['Non_unique'] == 0 ? " UNIQUE " : " INDEX ") . "`$index_name`" ." (`" . implode('`,`', $index_detail['Column_name']) . "`)";
                    }
                }
            }
            if (isset($diff['field']['change'])) {
                foreach ($diff['field']['change'] as $table_name => $fields) {
                    foreach ($fields as $field_name => $field_detail) {
                        $sqls[] = "ALTER TABLE `$table_name` CHANGE `{$field_name}` `{$field_name}` " . strtoupper($field_detail['Type']) . $this->sqlcol($field_detail['Collation']) . $this->sqlnull($field_detail['Null']) . $this->sqldefault($field_detail['Default']) . $this->sqlextra($field_detail['Extra']) . $this->sqlcomment($field_detail['Comment']);
                    }
                }
            }
        }

        return $sqls;
    }

    private function compare_database($new, $old) {
        $diff = array('table' => array(), 'field' => array(), 'index' => array());
        //table
        foreach ($old['table'] as $table_name => $table_detail) {
            if (!isset($new['table'][$table_name]))
                $diff['table']['drop'][$table_name] = $table_name; //删除表
        }
        foreach ($new['table'] as $table_name => $table_detail) {
            if (!isset($old['table'][$table_name])) {
                //新建表
                $diff['table']['create'][$table_name] = $table_detail;
                $diff['field']['create'][$table_name] = $new['field'][$table_name];
                $diff['index']['create'][$table_name] = $new['index'][$table_name];
            }
            else{
                //对比表
                $old_detail=$old['table'][$table_name];
                $change=array();
                if($table_detail['Engine']!=$old_detail['Engine'])
                    $change['Engine']=$table_detail['Engine'];
                if($table_detail['Row_format']!=$old_detail['Row_format'])
                    $change['Row_format']=$table_detail['Row_format'];
                if($table_detail['Collation']!=$old_detail['Collation'])
                    $change['Collation']=$table_detail['Collation'];
                //if($table_detail['Create_options']!=$old_detail['Create_options'])
                //  $change['Create_options']=$table_detail['Create_options'];
                if($table_detail['Comment']!=$old_detail['Comment'])
                    $change['Comment']=$table_detail['Comment'];
                if(!empty($change))
                    $diff['table']['change'][$table_name]=$change;
            }
        }

        //fields
        foreach ($old['field'] as $table => $fields) {
            if (isset($new['field'][$table])) {
                $new_fields = $new['field'][$table];
                foreach ($fields as $field_name => $field_detail) {
                    if (!isset($new_fields[$field_name])) {
                        //字段不存在，删除字段
                        $diff['field']['drop'][$table][$field_name] = $field_detail;
                    }
                }
            }
            else {
                //旧数据库中的表在新数据库中不存在，需要删除
            }
        }
        foreach ($new['field'] as $table => $fields) {
            if (isset($old['field'][$table])) {
                $old_fields = $old['field'][$table];
                $last_field = '';
                foreach ($fields as $field_name => $field_detail) {
                    if (isset($old_fields[$field_name])) {
                        //字段存在，对比内容
                        if ($field_detail['Type'] != $old_fields[$field_name]['Type'] || $field_detail['Collation'] != $old_fields[$field_name]['Collation'] || $field_detail['Null'] != $old_fields[$field_name]['Null'] || $field_detail['Default'] != $old_fields[$field_name]['Default'] || $field_detail['Extra'] != $old_fields[$field_name]['Extra'] || $field_detail['Comment'] != $old_fields[$field_name]['Comment']) {
                            $diff['field']['change'][$table][$field_name] = $field_detail;
                        }
                    }
                    else {
                        //字段不存在，添加字段
                        $field_detail['After'] = $last_field;
                        $diff['field']['add'][$table][$field_name] = $field_detail;
                    }
                    $last_field = $field_name;
                }
            }
            else {
                //新数据库中的表在旧数据库中不存在，需要新建
            }
        }

        //index
        foreach ($old['index'] as $table => $indexs) {
            if (isset($new['index'][$table])) {
                $new_indexs = $new['index'][$table];
                foreach ($indexs as $index_name => $index_detail) {
                    if (!isset($new_indexs[$index_name])) {
                        //索引不存在，删除索引
                        $diff['index']['drop'][$table][$index_name] = $index_name;
                    }
                }
            }
            else {
                if (!isset($diff['table']['drop'][$table])) {
                    foreach ($indexs as $index_name => $index_detail) {
                        $diff['index']['drop'][$table][$index_name] = $index_name;
                    }
                }
            }
        }
        foreach ($new['index'] as $table => $indexs) {
            if (isset($old['index'][$table])) {
                $old_indexs = $old['index'][$table];
                foreach ($indexs as $index_name => $index_detail) {
                    if (isset($old_indexs[$index_name])) {
                        //存在，对比内容
                        if ($index_detail['Non_unique'] != $old_indexs[$index_name]['Non_unique'] || $index_detail['Column_name'] != $old_indexs[$index_name]['Column_name'] || $index_detail['Collation'] != $old_indexs[$index_name]['Collation'] || $index_detail['Index_type'] != $old_indexs[$index_name]['Index_type']) {
                            $diff['index']['drop'][$table][$index_name] = $index_name;
                            $diff['index']['add'][$table][$index_name] = $index_detail;
                        }
                    }
                    else {
                        //不存在，新建索引
                        $diff['index']['add'][$table][$index_name] = $index_detail;
                    }
                }
            }
            else {
                if (!isset($diff['table']['create'][$table])) {
                    foreach ($indexs as $index_name => $index_detail) {
                        $diff['index']['add'][$table][$index_name] = $index_detail;
                    }
                }
            }
        }

        return $diff;
    }

    function sqlkey($val) {
        switch ($val) {
            case 'PRI':
                return ' PRIMARY';
            case 'UNI':
                return ' UNIQUE';
            case 'MUL':
                return ' INDEX';
            default:
                return '';
        }
    }

    function sqlcol($val) {
        switch ($val) {
            case null:
                return '';
            default:
                list($charset) = explode('_', $val);
                return ' CHARACTER SET ' . $charset . ' COLLATE ' . $val;
        }
    }

    function sqldefault($val) {
        switch ($val) {
            case null:
                return '';
            default:
                return " DEFAULT '" . stripslashes($val) . "'";
        }
    }

    function sqlnull($val) {
        switch ($val) {
            case 'NO':
                return ' NOT NULL';
            case 'YES':
                return ' NULL';
            default:
                return '';
        }
    }

    function sqlextra($val) {
        switch ($val) {
            case '':
                return '';
            default:
                return ' ' . strtoupper($val);
        }
    }

    function sqlcomment($val) {
        switch ($val) {
            case '':
                return '';
            default:
                return " COMMENT '" . stripslashes($val) . "'";
        }
    }

    function query($connection, $sql) {
        if ($connection) {
            $result = @mysql_query($sql, $connection);
            if ($result) {
                $result_a = array();
                while ($row = @mysql_fetch_assoc($result))
                    $result_a[] = $row;
                return $result_a;
            }
        }
        return false;
    }
}
