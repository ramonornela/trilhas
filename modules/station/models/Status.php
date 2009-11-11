<?
class Station_Model_Status extends Station_Model_Abstract
{
	protected $_name    = "status";
	protected $_primary = "id";

	const ACTIVE   = 1;

	const INACTIVE = 2;

	const LOCKED = 3;

	const SUSPEND = 4;

	const APPROVED = 5;

	const DISAPPROVED = 6;

	const WAITING = 7;

	const STARTED = 8;

	const COMPLETED = 9;

	const UNAVAILABLE = 10;

	const AVAILABLE	= 11;

	const OPEN	 = 12;

	const CLOSED = 13;

    const READ = 14;

    const UNREAD = 15;

    const ANNUL	 = 16;

    const STUDYING = 17;
}