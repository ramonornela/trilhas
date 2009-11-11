<?php
class Faq_Model_Faq extends Faq_Model_Abstract
{
	const TYPE = "F";

    protected $_name 	= 'faq';
    protected $_primary = 'id';

    public $filters = array(
		'*'		=> 'StringTrim',
	    'id'    => 'Int'
	);

	public $validators = array(
		'question'	=> array( 'NotEmpty' ),
		'answer' 	=> array( 'NotEmpty' )
	);

	protected $_dependentTables = array( "Faq_Model_FaqCategory" );
    
    public function _save()
    {
        $user = new Zend_Session_NameSpace( 'user' );
        
        $this->_data['Faq_Model_Faq']['person_id'] = $user->person_id;
    }

    public function _postSave()
    {
        $faqCategory = new Faq_Model_FaqCategory();

        $id = $this->_data['Faq_Model_Faq']['id'];

        $faqCategory->save( $id );
    }
}
?>
