<?php
/**
 * 缓存
 */
//缓存AR类时，对应的AR模型需要加载其他类文件，所以从缓存中取出来时会找不到类文件，因此在这加载需要的类文件
Yii::import('application.behaviors.*');
class CacheExt extends CApplicationComponent
{
    const CACHE_MAP_KEY = 'cacheMap';

    /**
     * 缓存管理map
     * @return mixed  ['缓存id'=>[过期时间戳,缓存描述]]
     */
    public static function getCacheMap()
    {
        $data = Yii::app()->cache->get(CacheExt::CACHE_MAP_KEY);
        return $data===false ? array() : $data;
    }

    /**
     * 设置缓存管理map
     * @param string $id  缓存唯一标识
     * @param integer $exp 缓存时间（秒）
     * @param string $desc 缓存描述
     */
    public static function setCacheMap($id, $exp, $desc='')
    {
        $cache = CacheExt::getCacheMap();
        if($cache===false)
            $cache = array();
        Yii::app()->cache->set(CacheExt::CACHE_MAP_KEY, CMap::mergeArray($cache, array($id=>array($exp==0 ? $exp : time()+$exp, $desc))));
    }

    /**
     * 删除缓存
     * @param  string $id 缓存唯一标识
     */
    public static function deleteCacheMap($id)
    {
        $cache = CacheExt::getCacheMap();
        if($cache!==false)
        {
            unset($cache[$id]);
            Yii::app()->cache->set(CacheExt::CACHE_MAP_KEY, $cache);
        }
    }

    /**
    * 获得缓存
    * @param  string $id           缓存唯一标识
    * @param  array  $dependencies 缓存依赖的AR模型名称
    * @return mixed | false        缓存失效的话返回false,否则将缓存的结果返回
    */
    public static function get($id, $dependencies=array())
    {
        if(!is_array($dependencies))
            $dependencies = array($dependencies);

        $modelUpdateMap = Yii::app()->cache->get('modelUpdateMap');
        if($modelUpdateMap === false)
            $modelUpdateMap = array();

        $cacheCrtDtm = Yii::app()->cache->get($id . "_crtdtm");
        if($cacheCrtDtm === false)
            return false;

        foreach($dependencies as $dependency)
        {
            if(isset($modelUpdateMap[$dependency]) && $modelUpdateMap[$dependency] >= $cacheCrtDtm)
                CacheExt::delete($id);
        }

        $cacheResult = Yii::app()->cache->get($id);
        if($cacheResult === false)
            return false;
        return $cacheResult;
    }

    /**
    * 设置缓存
    * @param string $id          缓存唯一标识
    * @param mixed $dataToCache  要缓存的数据
    * @param integer $exp        缓存有效时间（秒）
    * @param string $desc        缓存描述
    */
    public static function set($id, $dataToCache, $exp, $desc='')
    {
        Yii::app()->cache->set($id, $dataToCache, $exp);
        Yii::app()->cache->set($id . "_crtdtm", time(), $exp);
        CacheExt::setCacheMap($id, $exp, $desc);
        return true;
    }

    /**
    * 删除指定id缓存
    * @param  string $id 缓存唯一标识
    * @return true
    */
    public static function delete($id)
    {
        Yii::app()->cache->delete($id . "_crtdtm");
        Yii::app()->cache->delete($id);
        CacheExt::deleteCacheMap($id);
        return true;
    }

    /**
    * get and set(gas)
    * 获取\设置缓存
    * 推荐通过该函数获取缓存数据，当缓存时效时，将运行回调函数@cacheFunction生成数据并缓存起来
    * @param  string   $id            缓存唯一标识
    * @param  array    $dependencies  缓存依赖
    * @param  integer  $exp           过期时间
    * @param  string   $desc          缓存描述
    * @param  Callable $cacheFunction 生成数据的回调方法
    * @return mixed                   返回缓存的数据
    */
    public static function gas($id, $dependencies=array(), $exp=0, $desc='', Callable $cacheFunction)
    {
        $cacheResult = CacheExt::get($id, $dependencies);
        if($cacheResult===false)
        {
             $cacheResult = $cacheFunction();
             CacheExt::set($id, $cacheResult, $exp, $desc);
        }
        return $cacheResult;
    }
}
