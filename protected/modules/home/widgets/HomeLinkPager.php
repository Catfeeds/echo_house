<?php
/**
 * 前台home模块所用的分页类，继承CLinkPager
 * @author tivon
 * @date 2015-05-28
 */
class HomeLinkPager extends CLinkPager
{
	public function init()
	{
		parent::init();

		// $this->htmlOptions['style'] = 'display:inline-block';
		$this->selectedPageCssClass = 'active';
		$this->prevPageLabel = '上一页';
		$this->nextPageLabel = '下一页';
		$this->firstPageLabel = '首页';
		$this->lastPageLabel = '尾页';
		$this->header = '共'.$this->pageCount.'页,'.$this->itemCount.'条记录,每页'.$this->pageSize.'条';
		$this->internalPageCssClass = '';
		$this->maxButtonCount = $this->owner->iswap==0?8:4;
	}

	public function run()
	{
		$this->registerClientScript();
		$buttons=$this->createPageButtons();
		if(empty($buttons))
			return;
		echo '<div class="pagination" style="width:100%"><ul>';
		// echo '<span class="tip">'.$this->header.'</span>';
		if($buttons)
			foreach ($buttons as $key => $value) {
				echo $value;
			}
		echo $this->footer;
		echo '</ul></div>';
	}

	/**
	 * @return array the begin and end pages that need to be displayed.
	 */
	protected function getPageRange()
	{
		$currentPage=$this->getCurrentPage();
		$pageCount=$this->getPageCount();

		$beginPage=max(0, $currentPage-(int)($this->maxButtonCount/2));
		if(($endPage=$beginPage+$this->maxButtonCount-1)>=$pageCount)
		{
			$endPage=$pageCount-1;
			$beginPage=max(0,$endPage-$this->maxButtonCount+1);
		}
		return array($beginPage,$endPage);
	}

	/**
	 * 创建分页按钮
	 * @return array 分页按钮html
	 */
	protected function createPageButtons()
	{
		if(($pageCount=$this->getPageCount())<=1)
			return array();

		list($beginPage,$endPage)=$this->getPageRange();
		$currentPage=$this->getCurrentPage(false); // currentPage is calculated in getPageRange()
		$buttons=array();

		// first page
		$buttons[]=$this->createPageButton($this->firstPageLabel,0,$this->firstPageCssClass,$currentPage<=0,false);

		// prev page
		// if(($page=$currentPage-1)<0)
		// 	$page=0;
		// $buttons[]=$this->createPageButton($this->prevPageLabel,$page,$this->previousPageCssClass,$currentPage<=0,false);

		// internal pages
		for($i=$beginPage;$i<=$endPage;++$i) {
			$buttons[]=$this->createPageButton($i+1,$i,$this->internalPageCssClass,false,$i==$currentPage);
		}

		// next page
		if(($page=$currentPage+1)>=$pageCount-1)
			$page=$pageCount-1;
		$buttons[]=$this->createPageButton($this->nextPageLabel,$page,$this->nextPageCssClass,$currentPage>=$pageCount-1,false);

		// last page
		$buttons[]=$this->createPageButton($this->lastPageLabel,$pageCount-1,$this->lastPageCssClass,$currentPage>=$pageCount-1,false);

		return $buttons;
	}

	protected function createPageButton($label,$page,$class,$hidden,$selected)
	{
		if($hidden || $selected)
			$class = $this->selectedPageCssClass;
		else
			$class = '';
		if($class) {
			return '<li class="'.$class.'"><span>'.$label.'</span></li>';
		}
		return '<li class="'.$class.'">'.CHtml::link($label,$this->createPageUrl($page)).'</li>';
	}
}
