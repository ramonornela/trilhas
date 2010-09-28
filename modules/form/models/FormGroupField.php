<?php
class FormGroupField extends Table
{
	protected $_name    = "trails_form_group_field";
	protected $_primary = array( 'form_group_id' , 'form_field_id' , 'form_id' ); 
	
	protected $_referenceMap = array( 
		'FormGroup' => array(
			'refTableClass' => 'FormGroup',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'form_group_id' )
		),
		'FormField' => array(
			'refTableClass' => 'FormField',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'form_field_id' )
		),
		'Form' => array(
			'refTableClass' => 'Form',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'form_id' )
		)
	);
	
	function save( $fieldId , $formId = null )
	{
		parent::delete( $fieldId  , 'form_field_id' );
		
		if( $_POST['group'] )
			$saves['form_group_id'] = $_POST['group'];
		else
			$saves['form_group_id'] = -1;
		
		if( $_POST['form_id'] )
			$saves['form_id'] = $_POST['form_id'];
		else
			$saves['form_id'] = $formId;
			
		$saves['form_field_id'] = $fieldId;
		
		parent::save($saves);
	}
	
	public function fetchFields( $formId )
	{
		$select = $this->select();
		
		$select->from( array( 'fgf' => $this->_name ) , new Zend_Db_Expr('*') )
		       ->join( array( 'ff' => 'trails_form_field' ) , "ff.id = fgf.form_field_id" , array() )
		       ->where( "fgf.form_id =?" , $formId );
		
		return $this->fetchAll( $select );
	}
}