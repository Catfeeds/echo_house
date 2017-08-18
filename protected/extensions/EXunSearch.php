<?php
/**
 * Yii-Xunsearch class file
 *
 * @author Hightman <hightman2[at]yahoo[dot]com[dot]cn>
 * @link http://www.xunsearch.com/
 * @version 1.0
 */

/**
 * Xunsearch wrapper as an application component for YiiFramework
 *
 * @method XSIndex getIndex()
 * @method XSSearch getSearch()
 *
 * @author hightman
 * @version $Id$
 * @since 1.0
 */
class EXunSearch extends CApplicationComponent
{
	public $xsRoot, $project, $charset;
	private $_xs, $_scws;

	public function __call($name, $parameters)
	{
		// check methods of xs
		if ($this->_xs !== null && method_exists($this->_xs, $name)) {
			return call_user_func_array(array($this->_xs, $name), $parameters);
		}
		// check methods of index object
		if ($this->_xs !== null && method_exists('XSIndex', $name)) {
			$ret = call_user_func_array(array($this->_xs->index, $name), $parameters);
			if ($ret === $this->_xs->index) {
				return $this;
			}
			return $ret;
		}
		// check methods of search object
		if ($this->_xs !== null && method_exists('XSSearch', $name)) {
			$ret = call_user_func_array(array($this->_xs->search, $name), $parameters);
			if ($ret === $this->_xs->search) {
				return $this;
			}
			return $ret;
		}
		return parent::__call($name, $parameters);
	}

	/**
	 * 魔术方法，通过Yii::app()->search->xxx获得xxx迅搜项目文档
	 * @param  string $projectName 迅搜ini配置文件名
	 * @return EXunsearch
	 */
	public function __get($projectName)
	{
		$this->loadIniConfig($projectName);
		// $this->project = Yii::getPathOfAlias('application.config.xunsearch.'.$projectName) . '.ini';
		if ($this->xsRoot === null) {
			$lib = dirname(__FILE__) . '/../../lib/XS.class.php';
		} else {
			if (strpos($this->xsRoot, '.') !== false && strpos($this->xsRoot, DIRECTORY_SEPARATOR) === false) {
				$this->xsRoot = Yii::getPathOfAlias($this->xsRoot);
			}
			$lib = $this->xsRoot . '/' . (is_dir($this->xsRoot . '/sdk') ? '' : 'xunsearch-') . 'sdk/php/lib/XS.php';
		}
		if (!file_exists($lib)) {
			throw new CException('"XS.php" or "XS.class.php" not found, please check value of ' . __CLASS__ . '::$xsRoot');
		}
		require_once $lib;
		$this->_xs = new XS($this->project);
		$this->_xs->setDefaultCharset($this->charset);
		return $this;
	}

	/**
	 * Quickly add a new document (without checking key conflicts)
	 * @param mixed $data XSDocument object or data array to be added
	 */
	public function add($data)
	{
		$this->update($data, true);
	}

	/**
	 * @param mixed $data XSDocument object or data array to be updated
	 * @param boolean $add whether to add directly, default to false
	 */
	public function update($data, $add = false)
	{
		if ($data instanceof XSDocument) {
			$this->_xs->index->update($data, $add);
		} else {
			$doc = new XSDocument($data);
			$this->_xs->index->update($doc, $add);
		}
	}

	/**
	 * @return XSTokenizerScws get scws tokenizer
	 */
	public function getScws()
	{
		if ($this->_scws === null) {
			$this->_scws = new XSTokenizerScws;
		}
		return $this->_scws;
	}

	/**
	 * 加载自定义映射配置文件
	 * 为了避免本地开发与测试公用迅搜服务器导致的冲突问题，这里允许自定义一个映射配置文件config.php
	 * 返回一个数组，映射ini文件中定义的项目名称与开发时所用的临时项目配置名称。
	 * 数组格式如下：
	 * ```code```
	 *<?php return ['plot_house' => 'plot_house_esf', 'plot_article' => 'plot_article_esf']; ?>
	 * ```code```
	 * @param  string $projectName 迅搜项目名称
	 * @return void
	 */
	public function loadIniConfig($projectName)
	{
		$configDir = Yii::getPathOfAlias('application.config.xunsearch') ;
		$this->project = $configDir . DIRECTORY_SEPARATOR . $projectName .'.ini';
		$customConfig = $configDir . DIRECTORY_SEPARATOR . 'config.php';
		if(file_exists($customConfig)) {
			try {
				$map = require($customConfig);
			} catch(Exception $e) {

			}
			if(isset($map[$projectName]) && file_exists($this->project)) {
				$newProjectName = $map[$projectName];
				$iniContent = file_get_contents($this->project);
				$this->project = str_replace($projectName, $newProjectName, $iniContent);
			}
		}
	}
}
