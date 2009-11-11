<?php
class Link_Model_LinkCategory extends Link_Model_Abstract
{
	protected $_name 	= 'link_category';
    protected $_primary = array( 'link_id' , 'category_id' );

    public $filters = array(
		'*'       => 'StringTrim',
		'link_id' => 'Int'
	);

	public $validators = array(
		'link_id'	  => array(  'Int' ,  'NotEmpty' ),
		'category_id' => array(  'Int' ,  'NotEmpty' )
	);

	protected $_dependentTables = array(  );

	protected $_referenceMap = array(
		array(
			 'refTableClass' => 'Link_Model_Link',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'link_id' )
		),
		array(
			 'refTableClass' => 'Category_Model_Category',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'category_id' )
		)
	);

	public function save( $id )
	{
		$this->delete( array( 'link_id' => $id ) );

        if( $_POST['linkchecked'] ){
            foreach ( $_POST['linkchecked'] as $category )
            {
                $save['Link_Model_LinkCategory']['category_id'] = $category;
                $save['Link_Model_LinkCategory']['link_id']     = $id;

                parent::save( $save );
            }
        }
	}
}