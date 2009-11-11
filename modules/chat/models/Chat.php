<?php
class Chat_Model_Chat extends Chat_Model_Abstract
{
    protected $_name    = "chat";
    protected $_primary = "id";

    protected $_dependentTables = array();

    protected $_referenceMap = array(
        array('refTableClass' => 'Share_Model_Person',
              'refColumns'    => 'id',
              'columns'       => 'person_id'),
        array('refTableClass' => 'Chat_Model_Group',
              'refColumns'    => 'id',
              'columns'       => 'group_id')
    );

    public function fetchToUser($user_id, $ids = array(0))
    {
        $select = $this->select(true)
                       ->setIntegrityCheck(false)
                       ->join(array('cg' => 'chat_group'),
                              'cg.id = chat_group_id', array(), $this->_schema)
                       ->join(array('p' => 'person'),
                              'p.id = chat.person_id', array('name'), 'station')
                       ->where('chat.id NOT IN(?)', $ids)
                       ->where('cg.person_id = ?', $user_id);

        return $this->fetchAll($select)->toArray();
    }
}