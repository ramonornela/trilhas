<?php
class Chat_Model_Message extends Application_Model_Abstract
{
    protected  $_name = 'message';
    protected  $_primary = 'id';

    protected $_referenceMap = array(
        "Person_Receiver" => array(
            'refTableClass' => 'Share_Model_Person',
            'refColumns' => array( 'id' ),
            'columns' => array( 'person_receiver_id' )),
        "Person_Sender" => array(
            'refTableClass' => 'Share_Model_Person',
            'refColumns' => array( 'id' ),
            'columns' => array( 'person_sender_id' ))
    );

    public function count( $id )
    {
        $db     = $this->getAdapter();
        $select = $db->select()
                     ->from($this->_name, "COUNT(0) as count", $this->_schema)
                     ->where("person_receiver_id = ?", $id);

        $row = $db->fetchAll($select);

        return $row[0]['count'];
    }

    public function _save()
    {
        $user = new Zend_Session_Namespace('user');
        $this->_data['Chat_Model_Message']['person_sender_id'] = $user->person_id;
    }
}