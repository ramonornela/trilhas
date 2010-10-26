<?php
class FormFieldData extends Table
{
	protected $_name    = "trails_form_field_data";
	protected $_primary = "id"; 

	protected $_referenceMap = array( 
		'FormField' => array(
			'refTableClass' => 'FormField',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'form_field_id' )
		),
		array(
			 'refTableClass' => 'FormFieldValue',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'form_field_value_id' )
		),
		array(
			 'refTableClass' => 'Person',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'person_id' )
		),
        array(
			 'refTableClass' => 'PreRegistration',
			 'refColumns' => array( 'person_id' ),
			 'columns' => array( 'person_id' )
		)
	);
	
	public function fetchEvaluationUser( $person_id , $form_id )
	{
		$select = $this->select();
		
		$select->from( array( 'ffd' => $this->_name ) , array( new Zend_Db_Expr( 'form_id , person_id' ) ) )
			   ->join( array ( 'ff' => 'trails_form_field' ) , 'ffd.form_field_id = ff.id' , array() )
			   ->join( array ( 'fgf' => 'trails_form_group_field' ) , 'fgf.form_field_id = ff.id' , array() )
			   ->where( "ffd.person_id =? " , $person_id )
			   ->where( "fgf.form_id =? " , $form_id );
	  	
		return $this->fetchRow($select);
	}
	
	public function save( $post , $id )
	{
		foreach ( $_POST as $key => $data ) {
			
			$save['form_field_id'] = $key;
			$save['person_id'] = $id;
			
			if ( is_array( $data ) )
			{
				foreach ( $data as $val )
				{
					$save['value'] = $val;
					parent::save( $save );
				}
			}
			else
			{
				$save['value'] = $data;
				parent::save( $save );
			}
		}
	}
}