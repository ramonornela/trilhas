<?php
class FormGroup extends Table
{
	protected $_name    = "trails_form_group";
	protected $_primary = "id"; 

	protected $_dependentTables = array( "FormGroupField" );
	
	public $filters = array(
		'*'	 => 'StringTrim',
	    'id' => 'Int'
	);
	
	public $validators = array(
	    'id'         => 'Int',
		'name'       => array( 'NotEmpty', array( 'StringLength', 0 , 255 ) ),
	);
	
	public function fetchFormGroup( $id )
	{
		$select = $this->select();
		
		$select->from( array( 'fg' => $this->_name ) , array( new Zend_Db_Expr( 'form_group_id , form_id , name, id' ) ) )
			   ->join( array ( 'fgf' => 'trails_form_group_field' ) , 'fgf.form_group_id = fg.id' , array() )
			   ->where( "fgf.form_id =? " , $id )
			   ->group( array( 'form_group_id' , 'form_id' , 'name' , 'id' ) );
	  	
		return $this->fetchAll($select);
	}
}