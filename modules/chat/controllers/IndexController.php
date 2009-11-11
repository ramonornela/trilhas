<?php
class Chat_IndexController extends Application_Controller_Abstract
{
    public function indexAction()
    {
        
    }

    public function inputAction()
    {

    }

    public function saveAction()
    {

    }

    public function createGroupAction()
    {
        $id = Zend_Filter::filterStatic($this->_getParam('id') , 'int');
        $user = new Zend_Session_NameSpace('user');
        $chatGroup = new Chat_Model_Group();

        $row = $chatGroup->createRow();
        $row->person_id = $id;
        $row->save();

        $otherRow = $chatGroup->createRow();
        $otherRow->id = $row->id;
        $otherRow->person_id = $user->person_id;
        $otherRow->save();

        exit($row->id);
    }
}