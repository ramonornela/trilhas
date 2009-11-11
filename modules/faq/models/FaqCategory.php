<?php
class Faq_Model_FaqCategory extends Faq_Model_Abstract
{
	protected $_name 	= 'faq_category';
    protected $_primary = array( 'faq_id' , 'category_id' );

    public $filters = array(
		'*'  => 'StringTrim',
		'faq_id' => 'Int'
	);

	public $validators = array(
		'faq_id'	  => array(  'Int' ,  'NotEmpty' ),
		'category_id' => array(  'Int' ,  'NotEmpty' )
	);

	protected $_dependentTables = array(  );

	protected $_referenceMap = array(
		array(
			 'refTableClass' => 'Faq_Model_Faq',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'faq_id' )
		),
		array(
			 'refTableClass' => 'Category_Model_Category',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'category_id' )
		)
	);

	public function save( $id )
	{
		$this->delete( array( 'faq_id' => $id ) );
        
		if( $_POST['faqchecked'] ){
            foreach ( $_POST['faqchecked'] as $category )
            {
                $save['Faq_Model_FaqCategory']['category_id'] = $category;
                $save['Faq_Model_FaqCategory']['faq_id']      = $id;

                parent::save( $save );
            }
        }
	}
}

?>
