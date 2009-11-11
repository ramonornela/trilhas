<?
class File_Model_File extends File_Model_Abstract
{
	/**
	 * @internal folder root upload
	 *
	 */
	const FOLDER_UPLOAD_ROOT = "upload";

	/**
	 * @internal folder where they are files belonging the maps
	 */
	const FOLDER_MAP 		= "map";

	/**
	 * @internal folder where they are files belonging the multimedia
	 */
	const FOLDER_MULTIMEDIA = "multimedia";

	/**
	 * @internal folder where they are files belonging the narration
	 */
	const FOLDER_NARRATION  = "narration";

	/**
	 * @internal folder where they are files belonging the weblibrary
	 */
	const FOLDER_WEBLIBRARY  = "weblibrary";

	/**
	 * @internal folder where they are files belonging the composer
	 */
	const FOLDER_COMPOSER   = "composer";

	/**
	 * abbreviation meaning = Mapa Photo
	 *
	 * @internal file type column type of the map file
	 */
	const TYPE_FILE_MAP_FILE = 'MP';

	/**
	 * abbreviation meaning = Narração arquivo
	 *
	 * @internal file type column of the narration file
	 */
	const TYPE_FILE_NARRATION = 'NA';

	/**
	 * abbreviation meaning = Mapa Url
	 * @internal file type column type of the map url
	 */
	const TYPE_FILE_MAP_URL  = 'MU';

	/**
	 * abbreviation meaning = Foto Usuário
	 * @internal file type column type of the user avatar
	 */
	const TYPE_FILE_PHOTOUSER = 'FU';

	/**
	 * abbreviation meaning = Webiblioteca
	 * @internal file type column type of the weblibrary
	 */
	const TYPE_FILE_WEBLIBRARY = 'WB';

	/**
	 * abbreviation meaning = Diretório Multimídia
	 * @internal file type column type of the weblibrary
	 */
	const TYPE_FILE_MULTIMEDIA = 'DM';

	/**
	 * abbreviation meaning = Diretório Arquivos
	 * @internal file type column type of the weblibrary
	 */
	const TYPE_FILE_FILES = 'DF';

	/**
	 * abbreviation meaning = Diretório Composição
	 * @internal file type column type of the weblibrary
	 */
	const TYPE_FILE_COMPOSER = 'DC';


	const LIST_FOLDERS_COURSES 		= 1;

	const LIST_FOLDERS_DISCIPLINES 	= 2;

	const LIST_FOLDERS_GROUPS 		= 3;

	const LIST_FOLDERS_PERSONS 		= 4;

	protected $_name    = "file";
	protected $_primary = "id";

	public $filters = array(
		'*'  => 'StringTrim',
		'id' => 'Int'
	);

	public $validators = array(
		'title'		=> array(  'NotEmpty' ,  array( 'StringLength' , '0' , '255' ) ),
		'ds'		=> array(  'NotEmpty' ),
		'type'		=> array(  'NotEmpty' ,  array( 'StringLength' , '0' , '2' ) ),
		'relation'	=> array(  array( 'StringLength' , '0' , '12' ) )
	);

	public $validatorsWeblibrary = array(
		'title'		=> array(  'NotEmpty' ,  array( 'StringLength' , '0' , '255' ) ),
		'location'	=> array(  'NotEmpty' ,  array( 'StringLength' , '0' , '255' ), 'ImageUrl' ),
		'author'	=> array(  'NotEmpty' ,  array( 'StringLength' , '0' , '255' ) ),
		'type'		=> array(  'NotEmpty' ,  array( 'StringLength' , '0' , '2' ) ),
		'relation'	=> array(  array( 'StringLength' , '0' , '12' ) )
	);

	protected $_dependentTables = array( "File_Model_FolderFile" , "Narration" , "Share_Model_Person" );

	protected $_referenceMap = array(
		array(
			 'refTableClass' => 'Share_Model_Person',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'person_id' )
		)
	);

    public function _save()
    {
        $user = new Zend_Session_Namespace('user');
        $this->_data['File_Model_File']['person_id'] = $user->person_id;
    }
}