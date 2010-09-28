<?php
class FormFieldValue extends Table
{
	protected $_name    = "trails_form_field_value";
	protected $_primary = "id"; 
	
	protected $_dependentTables = array( "FormFieldData" );
	
	protected $_referenceMap = array( 
		'FormField' => array(
			'refTableClass' => 'FormField',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'form_field_id' )
		)
	);
	 
	public function save( $id )
	{
		parent::delete( $id , 'form_field_id' );
		
		foreach ( $_POST['value'] as $key => $val )
		{
			if ($val)
			{
				$saves['value']         = $val;
				$saves['form_field_id'] = $id;
				$saves['status'] = " ";
				
				if ( ( $_POST['type'] == "keyword" ) || ( $_POST['type'] == "text" ) )
					$saves['status'] = 	$_POST['correct'];
					
				if ( ( $_POST['type'] == "association" ) || ( $_POST['type'] == "truefalse" ) )
					$saves['status'] = 	$_POST['correct'][$key];

				if ( ( $_POST['type'] == "checkbox" ) && ( in_array( $key , $_POST['correct'] ) ) )
					$saves['status'] = "V";

				if ( ( $_POST['type'] == "radio" ) && ( $_POST['correct'] == $key ) )
					$saves['status'] = "V";
					
				$value_id = parent::save( $saves );
			}
		}
	}
}