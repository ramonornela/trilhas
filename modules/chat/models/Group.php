<?php
class Chat_Model_Group extends Chat_Model_Abstract
{
    protected $_name    = "chat_group";
    protected $_primary = array("id" , "person_id");

    protected $_dependentTables = array();

    protected $_referenceMap = array(
        array('refTableClass' => 'Share_Model_Person',
              'refColumns'    => array('id'),
              'columns'       => array('person_id'))
    );
}