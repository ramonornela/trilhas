<?php
class Tri_Application_Resource_Menu extends Zend_Application_Resource_ResourceAbstract
{
    const RESOURCE_SEPARATOR = '+';

    protected $_names;

    /**
     * (non-PHPdoc)
     * @see Zend_Application_Resource_ResourceAbstract#init()
     */
    public function init()
    {
        foreach ($this->_names as $name => $parent) {
            foreach ($parent as $child => $value) {
                $data = explode('.', $value);
                $menu[$name][$child]['module']     = $data[0];
                $menu[$name][$child]['controller'] = $data[1];
                $menu[$name][$child]['action']     = $data[2];
            }
        }

        Zend_Registry::set('menu', $menu);
        return $menu;
    }

    public function setNames(array $names)
    {
        $this->_names = $names;
        return $this;
    }
}
