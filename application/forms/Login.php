<?php
class Application_Form_Login extends Zend_Form
{
    public function init()
    {
        $course = new Tri_Db_Table('course');
        $user   = new Tri_Db_Table('user');

        $validators    = $course->getValidators();
        $filters       = $course->getFilters();
        $where         = array("role = 'Admin' OR role = 'Teacher' OR role = 'Creator'");
        $users         = $user->fetchPairs('id', 'name', $where, 'name');
        $statusOptions = array_unique($course->fetchPairs('status', 'status'));
        $categories    = array_unique($course->fetchPairs('category', 'category'));

        $this->setAction('user/login')
             ->setMethod('post');

        $username = new Zend_Form_Element_Text('username');
        $username->setRequired()
                 ->setLabel('Username')
                 ->addFilters(array('StringTrim', 'StripTags'));

        $password = new Zend_Form_Element_Password('password');
        $password->setRequired()
                 ->setLabel('Password')
                 ->addFilters(array('StringTrim', 'StripTags'));

        $this->addElement($username)
             ->addElement($password)
             ->addElement('submit', 'Login');
   }
}
