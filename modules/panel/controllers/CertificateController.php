<?php
class Panel_CertificateController extends Tri_Controller_Action
{
    public function init()
    {
        parent::init();
        $this->view->title = "Certificate";
    }
    
    public function saveAction()
    {
        $session = new Zend_Session_Namespace('data');
        $userId  = Zend_Filter::filterStatic($this->_getParam('userId'), 'int');

        Panel_Model_Certificate::emit($userId, $session->classroom_id);

        $this->_helper->_flashMessenger->addMessage('Success');
        
        $this->_redirect('/panel');
    }

    public function validateAction()
    {
        $uniqueId = $this->_getParam('uniqueId');

        if ($uniqueId) {
            $certificate = new Tri_Db_Table('certificate');
            $select = $certificate->select(true)->setIntegrityCheck(false)
                                  ->join('classroom', 'classroom.id = certificate.classroom_id', array())
                                  ->join('course', 'course.id = classroom.course_id')
                                  ->join('user', 'user.id = certificate.user_id', array('user.name as uname'))
                                  ->where('unique_id = ?', $uniqueId);

            $this->view->data = $certificate->fetchRow($select);
        }
    }
}