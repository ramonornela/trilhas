<?php
class Tri_Application_Resource_Cache extends Zend_Application_Resource_ResourceAbstract
{
    const OPTION_CLASS  = 'class';
    const OPTION_PARAMS = 'params';

    /**
     * Options for frontend.
     *
     * @var array
     */
    protected $_frontendOptions;

    /**
     * Options for backend.
     *
     * @var array
     */
    protected $_backendOptions;

    /**
     * Wether to share the cache object
     * to all Zend objects that accept one statically
     *
     * @var bool
     */
    protected $_shareToZendObjects = false;

    /**
     * cache object
     * @var Zend_Cache_Core
     */
    protected $_cache;

    /**
     * Set the options for frontend.
     *
     * @param  array $params
     * @return Mec_Application_Resource_Cache Provides a fluent interface
     */
    public function setFrontEnd(array $params)
    {
        if(!isset($params[self::OPTION_CLASS])) {
            throw new Zend_Application_Resource_Exception("frontend class has not been defined");
        }
        $this->_frontendOptions = $params;
        return $this;
    }

    /**
     * Set the options for backend.
     *
     * @param  array $params
     * @return Mec_Application_Resource_Cache Provides a fluent interface
     */
    public function setBackend(array $params)
    {
        if(!isset($params[self::OPTION_CLASS])) {
            throw new Zend_Application_Resource_Exception("backend class has not been defined");
        }
        $this->_backendOptions = $params;
    }

    /**
     * Retrieve the class name used as frontend.
     *
     * @return string
     */
    public function getFrontendClassName()
    {
        return $this->_frontendOptions[self::OPTION_CLASS];
    }

    /**
     * Retrieve the class name used as backend.
     *
     * @return string
     */
    public function getBackendClassName()
    {
        return $this->_backendOptions[self::OPTION_CLASS];
    }

    /**
     * Retrieve the class name used as backend.
     *
     * @return string
     */
    public function getShareToZendObjects()
    {
        return $this->_shareToZendObjects;
    }

    /**
     * (non-PHPdoc)
     * @see Zend_Application_Resource_ResourceAbstract#init()
     */
    public function init()
    {
        if ($this->_frontendOptions == null || $this->_backendOptions == null) {
            return null;
        }

        $frontendOptions = $backendOptions = array();
        if (isset($this->_frontendOptions[self::OPTION_PARAMS]) && is_array($this->_frontendOptions[self::OPTION_PARAMS])) {
            $frontendOptions = $this->_frontendOptions[self::OPTION_PARAMS];
        }
        if (isset($this->_backendOptions[self::OPTION_PARAMS]) && is_array($this->_backendOptions[self::OPTION_PARAMS])) {
            $backendOptions = $this->_backendOptions[self::OPTION_PARAMS];
        }

        $this->_cache = Zend_Cache::factory($this->getFrontendClassName(),
                                            $this->getBackendClassName(),
                                            $frontendOptions,
                                            $backendOptions);
        if ($this->_shareToZendObjects) {
            $this->_shareToZendObjects();
        }
        return $this->_cache;
    }

    /**
     * Returns the cache object.
     *
     * @return Zend_Cache_Core
     */
    public function getCache()
    {
        return $this->_cache;
    }

    /**
     * Wether to share the cache object to all Zend objects that accept one
     * statically.
     *
     * @param  boolean $share
     * @return Mec_Application_Resource_Cache Provides a fluent interface
     */
    public function setShareToZendObjects($share)
    {
        $this->_shareToZendObjects = (boolean) $share;
        return $this;
    }

    /**
     * Shares the cache instance
     * to all Zend objects that accept one statically
     *
     * @return Mec_Application_Resource_Cache Provides a fluent interface
     */
    protected function _shareToZendObjects()
    {
        Zend_Paginator::setCache($this->_cache);
        Zend_Db_Table::setDefaultMetadataCache($this->_cache);
        Zend_Date::setOptions(array('cache' => $this->_cache));
        Zend_Translate::setCache($this->_cache);
        Zend_Locale::setCache($this->_cache);

        return $this;
    }
}