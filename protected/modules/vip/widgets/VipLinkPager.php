<?php
/**
 * 后台分页
 * @author tivon
 * @date 2015-05-07
 */
class VipLinkPager extends CLinkPager{
	public function init()
	{
		parent::init();

		$this->htmlOptions['class'] = 'pagination pagination-sm pull-right';
		// $this->htmlOptions['style'] = 'display:inline-block';
		$this->selectedPageCssClass = 'active';
		$this->prevPageLabel = '&lt;';
		$this->nextPageLabel = '&gt;';
		$this->header = '共'.$this->pageCount.'页,'.$this->itemCount.'条记录,每页'.$this->pageSize.'条';
		$this->internalPageCssClass = '';
	}

	public function run()
	{
		$this->registerClientScript();
		$buttons=$this->createPageButtons();
		if(empty($buttons))
			return;
		echo '<div class="inline pull-right" style="width:100%;"><span style="top:17px;position:relative">'.$this->header.'</span>';
		echo CHtml::tag('ul',$this->htmlOptions,implode("\n",$buttons));
		echo $this->footer;
		echo '</div>';
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
		if(($page=$currentPage-1)<0)
			$page=0;
		$buttons[]=$this->createPageButton($this->prevPageLabel,$page,$this->previousPageCssClass,$currentPage<=0,false);

		// internal pages
		for($i=$beginPage;$i<=$endPage;++$i)
			$buttons[]=$this->createPageButton($i+1,$i,$this->internalPageCssClass,false,$i==$currentPage);

		// next page
		if(($page=$currentPage+1)>=$pageCount-1)
			$page=$pageCount-1;
		$buttons[]=$this->createPageButton($this->nextPageLabel,$page,$this->nextPageCssClass,$currentPage>=$pageCount-1,false);

		// last page
		$buttons[]=$this->createPageButton($this->lastPageLabel,$pageCount-1,$this->lastPageCssClass,$currentPage>=$pageCount-1,false);

		return $buttons;
	}
}
