<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initZFDebug() {
        // Setup autoloader with namespace
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('ZFDebug');

        // Ensure the front controller is initialized
        $this->bootstrap('FrontController');

        // Retrieve the front controller from the bootstrap registry
        $front = $this->getResource('FrontController');

        // Only enable zfdebug if options have been specified for it
        if ($this->hasOption('zfdebug')) {
            $options = $this->getOption('zfdebug');
            # Instantiate the database adapter and setup the plugin.
            # Alternatively just add the plugin like above and rely on the autodiscovery feature.
            if ($this->hasPluginResource('db')) {
                $this->bootstrap('db');
                $db = $this->getPluginResource('db')->getDbAdapter();
                $options['plugins']['Database']['adapter'] = $db;
            }

            # Setup the cache plugin
            if ($this->hasPluginResource('cache')) {
                $this->bootstrap('cache');
                $cache = $this->getPluginResource('cache')->getCache();
                $options['plugins']['Cache']['backend'] = $cache->getBackend();
            }
            $zfdebug = new ZFDebug_Controller_Plugin_Debug($options);
            $front->registerPlugin($zfdebug);
        }
    }
}