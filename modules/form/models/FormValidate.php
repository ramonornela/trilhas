<?php
class FormValidate  extends Table
{
	protected $_name    = "trails_form_validate";
	protected $_primary = "id"; 
	
	protected $_dependentTables = array( "FormFieldValidate" );
	
}