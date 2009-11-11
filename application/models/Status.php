<?
class Application_Model_Status extends Application_Model_Abstract
{
	const ACTIVE      = 1;
	const INACTIVE    = 2;
    const BLOCKED     = 3;
    const SUSPENDED   = 4;
    const APPROVED    = 5;
    const DISAPPROVED = 6;
    const CLOSED      = 7;
    const ALL         = "all";
	
	protected $_name    = "status";
	protected $_primary = "id";	
	
	protected $_dependentTables = array("Activity_Model_Activity" ,
										"Activity_Model_ActivityGroupPerson" ,
										"Bibliography_Model_Bibliography" ,
										"Calendar_Model_Calendar" ,
										"Chat_Model_ChatMessage" ,
										"Chat_Model_ChatRoomLogged" ,
										"Chat_Model_ChatRoomMessage" ,
										"Share_Model_Course" ,
										"Application_Model_Relation" ,
										"Dictionary_Model_Dictionary" ,
										"Share_Model_Discipline" ,
										"File_Model_File" ,
										"File_Model_Folder" ,
										"Forum_Model_Forum" ,
										"Glossary_Model_Glossary" ,
										"Application_Model_Log" ,
										"Notepad_Model_Notepad" ,
										"Share_Model_Person" ,
										"Rss_Model_Rss" ,
										"_Model_User" );
}