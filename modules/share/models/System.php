<?php

class Share_Model_System extends Share_Model_Abstract
{
    const TRAILS = 1;

    protected $_schema  = "share";
    protected $_name    = "system";
	protected $_primary = "id";

	public $filters = array(
		'*'    => 'StringTrim',
		'id'   => 'Int'
	);

}