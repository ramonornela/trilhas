<?
class File_Model_FolderFile extends File_Model_Abstract
{
	protected $_name    = "folder_file";
	protected $_primary = array( "file_id" , "folder_id" );

	public $filters = array(
		'*'  => 'StringTrim',
		'file_id' => 'Int'
	);

	public $validators = array(
		'file_id'		=> array(  'Int' ,  'NotEmpty' ),
		'folder_id'		=> array(  'Int' ,  'NotEmpty' )
	);

	protected $_dependentTables = array(  );

	protected $_referenceMap = array(
		array(
			 'refTableClass' => 'File_Model_Folder',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'folder_id' )
		),
		array(
			 'refTableClass' => 'File_Model_File',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'file_id' )
		)
	);
}