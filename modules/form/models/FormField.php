<?php
class FormField extends Table
{
	protected $_name    = "trails_form_field";
	protected $_primary = "id"; 
	
	protected $_dependentTables = array( "FormGroupField" , "FormFieldValue" , "FormFieldValidate" , "FormFieldData" );

	public $filters = array(
		'*'	 => 'StringTrim',
	    'id' => 'Int'
	);
		
	public $validators = array(
	    'id'       => 'Int',
		'label'    => array( 'NotEmpty' ),
		'mask'     => array( array( 'StringLength', 0 , 255 ) ),
		'position' => array( 'Int', array( 'StringLength', 0 , 255 ) ),
		'uniq'     => array( array( 'StringLength', 0 , 1 ) ),
		'note'    => array( 'NotEmpty' ),
	);
	
	public function fetchFieldTextArea( $id )
	{
		$select = $this->select();
		
		$select->from( array( 'ff' => $this->_name ) , array( new Zend_Db_Expr( '*' ) ) )
			   ->join( array ( 'fgf' => 'trails_form_group_field' ) , 'fgf.form_field_id = ff.id' , array() )
			   ->where( "fgf.form_id =? " , $id )
			   ->where( "ff.type = 'textarea'" );
	  	
		return $this->fetchRow($select);
	}
}