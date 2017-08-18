<?php
/**
 * 排序元素
 * 用于列表页上的排序按钮 - 售价、开盘时间
 */
class SortItems extends CComponent
{
    /**
     * 正序 or 倒序
     * @var boolean 如果当前选择了排序字段，则该字段值表示是正序还是倒序
     */
    public $desc = true;
    /**
     * 当前排序字段
     * @var string|null 如果当前选择了排序字段，则该字段值为选择的字段名称
     */
    public $item;
    /**
     * 设置的排序元素
     * @var array
     */
    public $items;
    /**
     * 地址解析器
     * @var UrlConstructor
     */
    public $urlConstructor;

    /**
     * 构造函数
     * @param array $items[] 每个元素也是一个数组，格式为  中文名称=>array(字段名称,正序值，倒序值)
     */
    public function __construct(array $items, &$urlConstructor)
    {
        $this->items = $items;
        $this->urlConstructor = &$urlConstructor;
        $this->parseParams();
    }

    /**
     * 解析当前选择的排序参数
     */
    public function parseParams()
    {
        if($order = $this->urlConstructor->order){
            foreach($this->items as $item){
                if(in_array($order, $item)){
                    if($item[1]==$order) $this->desc = false;
                    $this->item = $item[0];
                }
            }
        }
    }

    /**
     * 创建排序元素
     * @param  string $itemName 要创建链接的元素名称
     * @return string  创建的链接地址
     */
    public function createUrl($itemName)
    {
        if(isset($this->items[$itemName])){
            $item = $this->items[$itemName];
            $value = $item[2];//默认倒序
            //如果是现在选择的元素则要判断更改
            if($item[0]==$this->item){
                $value = $this->desc ? $item[1] : $item[2];
            }
            return $this->urlConstructor->add('order', $value);
        }else{
            return '';
        }
    }
}
