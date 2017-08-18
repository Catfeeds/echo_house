<?php
class RbacCommand extends CConsoleCommand
{
    public $roles = [
        'admin' => [
            'chinese' => '管理员',
            'description' => '系统管理员',
        ],
        'staff' => [
            'chinese' => '工作人员',
            'description' => '工作人员',
        ]
    ];

    public $taskOperations = [
        'jike' => [
            'chinese' => '集客管理',
            'description' => '集客管理模块',
            'operations' => [
                'guanlijike' => [
                    'chinese' => '管理集客信息',
                    'description' => '可查看和编辑集客信息'
                ],
                'maifangguwen' => [
                    'chinese' => '管理买房顾问',
                    'description'=> '对买房顾问的帐号进行管理',
                ],
                'jihuochi' => [
                    'chinese' => '管理激活池',
                    'description' => '对买房顾问认定为失效或休眠的用户单独管理'
                ]
            ],
        ],
        'xitong' => [
            'chinese' => '系统管理',
            'description' => '系统模块管理',
            'operations' => [
                'gongzuorenyuan' => [
                    'chinese' => '工作人员管理',
                    'description' => '可添加、修改工作人员信息和密码，权限管理'
                ],
                'zhandianpeizhi' => [
                    'chinese' => '站点配置',
                    'description' => '可配置站点信息、LOGO等，以及各种数据接口的配置'
                ],
                'guanggaoguanli' => [
                    'chinese' => '广告管理',
                    'description' => '可添加\删除广告'
                ]
            ]
        ],
        'neirong' => [
            'chinese' => '内容管理',
            'description' => '除集客管理之外的内容编辑管理',
            'operations' => [
                'guanlineirong' => [
                    'chinese' => '管理内容',
                    'description' => '可查看和编辑普通的非集客内容，如团购、看房团、楼盘信息等内容',
                ]
            ]
        ],
        'ershoufang' => [
            'chinese' => '二手房管理',
            'description' => '二手房模块的管理',
            'operations' => [
                'ershoufangguanli' => [
                    'chinese' => '管理二手房',
                    'description' => '可管理二手房模块',
                ]
            ]
        ]
    ];

    public function actionShow()
    {
        $items = AuthItem::model()->findAll();
        $roles = $tasks = $operations = [];
        foreach($items as $item) {
            if($item->type == CAuthItemExt::TYPE_ROLE) {
                $roles[] = $item->name;
            } elseif($item->type == CAuthItemExt::TYPE_TASK) {
                $tasks[] = $item->name;
            } elseif($item->type == CAuthItemExt::TYPE_OPERATION) {
                $operations[] = $item->name;
            }
        }
        echo '共有角色'.count($roles).'个，任务'.count($tasks).'个，操作'.count($operations).'个';
        $content = "\r\n-------------roles--------------\r\n";
        $content .= implode("\r\n", $roles);
        $content .= "\r\n-------------tasks--------------\r\n";
        $content .= implode("\r\n", $tasks);
        $content .= "\r\n-------------operations--------------\r\n";
        $content .= implode("\r\n", $operations);
        echo $content;

    }

    /**
     * 修复
     * @return [type] [description]
     */
    public function actionRepair()
    {
        $auth = Yii::app()->authManager;

        //检查、修复角色
        foreach($this->roles as $name=>$v) {
            if($role = $auth->getAuthItem($name)) {
                if($role->type == CAuthItemExt::TYPE_ROLE) {
                    echo '角色'.$name.'正常'."\r\n";
                    $role->chinese = $v['chinese'];
                    $role->description = $v['description'];
                } else {
                    echo $name."被占用为其他类型的元素\r\n";
                }
            } else {
                $this->showCreateMsg($v['chinese']);
                $auth->createRole($name, $v['chinese'], 1, $v['description']);
            }
        }

        //检查、修复任务节点
        foreach($this->taskOperations as $taskName=>$v) {
            if($task = $auth->getAuthItem($taskName)) {
                if($task->type == CAuthItemExt::TYPE_TASK) {
                    echo '任务'.$taskName.'正常'."\r\n";
                    $task->chinese = $v['chinese'];
                    $task->description = $v['description'];
                } else {
                    echo $taskName."被占用为其他类型的元素\r\n";
                }

                foreach($v['operations'] as $operateName => $vv) {
                    if($operate = $auth->getAuthItem($operateName)) {
                        if($operate->type == CAuthItemExt::TYPE_OPERATION) {
                            echo '操作'.$operateName.'正常'."\r\n";
                            $operate->chinese = $vv['chinese'];
                            $operate->description = $vv['description'];
                        } else {
                            echo $operateName."被占用为其他类型的元素\r\n";
                        }
                    } else {
                        $this->showCreateMsg($vv['chinese']);
                        $auth->createOperation($operateName, $vv['chinese'], 1, $vv['description']);
                        $task->addChild($operateName);
                    }
                }
            } else {
                $this->showCreateMsg($v['chinese']);
                $auth->createTask($taskName, $v['chinese'], 1, $v['description']);
            }
        }
    }

    public function showCreateMsg($itemName)
    {
        echo $itemName.'不存在，正在创建...'."\r\n";
    }
}
