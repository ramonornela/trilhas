<?php
class FormFieldValidate  extends Table
{
	protected $_name    = "trails_form_field_validate";
	protected $_primary = array( 'form_field_id' , 'form_validate_id' ); 
	
	protected $_referenceMap = array( 
		'FormField' => array(
			'refTableClass' => 'FormField',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'form_field_id' )
		),
		'FormValidate' => array(
			'refTableClass' => 'FormValidate',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'form_validate_id' )
		)
	);
	
	public function save( $id )
	{
		parent::delete( $id , 'form_field_id' );
		
		foreach ( $_POST['validate'] as $val )
		{
			if ($val)
			{
				$saves['form_field_id']    = $id;
				$saves['form_validate_id'] = $val;
				
				parent::save( $saves );
			}
		}
	}
}