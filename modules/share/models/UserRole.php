<?php

class Share_Model_UserRole extends Share_Model_Abstract
{
    protected $_schema  = "share";
    protected $_name    = "user_role";
	protected $_primary = array( "role_id" , "user_id" , "system_id" );

	public $filters = array(
		'role_id'   => 'Int',
		'user_id'   => 'Int',
		'system_id' => 'Int'
	);

}