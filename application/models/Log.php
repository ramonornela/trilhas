<?php
class Application_Model_Log extends Application_Model_Abstract
{
	protected $_name    = "log";
	protected $_primary = array('person_id','resource_id','class_id');
        protected $_sequence = false;
	
	protected $_dependentTables = array();
	
	protected $_referenceMap = array( 
            array('refTableClass' => 'Share_Model_Person',
                  'refColumns'    => 'id',
                  'columns'       => 'person_id'),
            array('refTableClass' => 'Share_Model_Resource',
                  'refColumns'    => 'id',
                  'columns'       => 'resource_id'),
            array('refTableClass' => 'Station_Model_Class',
                  'refColumns'    => 'id',
                  'columns'       => 'class_id')
	);

    public function fetchAccess($group_id, $person_id, $controller, $action, $started, $finish)
    {
        $select = $this->getAdapter()->select();
        $user   = new Zend_Session_Namespace('user');

        $select->from(array('l' => $this->_name), array('l.created as log_created', 'l.*'), 'trails')
               ->join(array('g' => 'station.class'), 'l.class_id = g.id', array('name as cname'))
               ->join(array('p' => 'station.person'), 'l.person_id = p.id', array('name'))
               ->where("(controller <> 'content' AND controller <> 'index') AND ( action <> 'index' OR action <> 'view')")
               ->order(array("controller", "action", "l.created"));

        if ($group_id) {
            $select->where('class_id = ?', $group_id);
        } else {
            $select->where('class_id IN('.$user->group->all.')');
        }

        if ($person_id) {
            $select->where('l.person_id = ?', $person_id);
        }

        if ($controller) {
            $select->where('controller = ?', $controller);
        }

        if ($action) {
            $select->where('action = ?' , $action);
        }

        if ($started) {
            $select->where('l.created > ?' , $started);
        }

        if ($finish) {
            $select->where('l.created < ?' , $finish);
        }
		
        return $this->getAdapter()->fetchAll($select);
    }
}