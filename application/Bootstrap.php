<?php
/**
 * Trilhas - Learning Management System
 * Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @category   Application
 * @package    Application_Bootstrap
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initConstants()
    {
        if ($this->hasOption('constants')) {
            $options = $this->getOption('constants');
            foreach($options as $name => $value) {
                define($name, $value);
            }
        }
    }

    protected function _initZFDebug()
    {
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

            $options['plugins']['File']['base_path'] = APPLICATION_PATH;

            # Setup the cache plugin
            if ($this->hasPluginResource('cachemanager')) {
                $this->bootstrap('cachemanager');
                $cache  = $this->getPluginResource('cachemanager')
                       ->getCacheManager()
                       ->getCache('default');
                $options['plugins']['Cache']['backend'] = $cache->getBackend();
            }
            $zfdebug = new ZFDebug_Controller_Plugin_Debug($options);
            $zfdebug->registerPlugin(new ZFDebug_Controller_Plugin_Debug_Plugin_Auth(array('user' => 'name')));
            $zfdebug->registerPlugin(new ZFDebug_Controller_Plugin_Debug_Plugin_Session());
            $front->registerPlugin($zfdebug);
        }
    }

    public function _initCache()
    {
        $this->bootstrap('cachemanager');
        $cache  = $this->getPluginResource('cachemanager')
                       ->getCacheManager()
                       ->getCache('default');

        Zend_Paginator::setCache($cache);
        Zend_Db_Table::setDefaultMetadataCache($cache);
        Zend_Date::setOptions(array('cache' => $cache));
        Zend_Translate::setCache($cache);
        Zend_Locale::setCache($cache);

        Zend_Registry::set('cache', $cache);
    }
}
