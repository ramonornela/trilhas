<?
class Share_Model_Resource extends Preceptor_Share_Resource
{
    public function fetchAllByNames($module, $controller, $action)
    {
        $select = $this->select(true)
                       ->where('module = ?', $module)
                       ->where('controller = ?', $controller)
                       ->where('action = ?', $action);
        return $this->fetchRow($select);
    }
}