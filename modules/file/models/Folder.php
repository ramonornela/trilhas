<?
class File_Model_Folder extends File_Model_Abstract
{
	/**
	 * @internal type int id folder composer
	 */
	const DEFAULT_FOLDER_COMPOSER 		  = 1;

	/**
	 * @internal type int id folder multimedia
	 */
	const DEFAULT_FOLDER_MULTIMEDIA 	  = 2;

	/**
	 * @internal type int id subfolder multimedia corresponding images
	 */
	const DEFAULT_FOLDER_MULTIMEDIA_IMG   = 4;

	/**
	 * @internal type int id subfolder multimedia corresponding audio
	 */
	const DEFAULT_FOLDER_MULTIMEDIA_AUDIO = 5;

	/**
	 * @internal type int id subfolder multimedia corresponding video
	 */
	const DEFAULT_FOLDER_MULTIMEDIA_VIDEO = 6;

	/**
	 * @internal type int id folder file
	 */
	const DEFAULT_FOLDER_FILE 			  = 3;

	/**
	 * @internal flag composer
	 */
	const FLAG_COMPOSER = 'C';

	/**
	 * @internal flag file
	 */
	const FLAG_FILE 	= 'F';

	/**
	 * @internal flag multimedia
	 */
	const FLAG_MULTIMEDIA = 'M';

	/**
	 * @internal visible folder public
	 */
	const VISIBILITY_PUBLIC = 'P';

	/**
	 * @internal visible folder restrict
	 */
	const VISIBILITY_RESTRICT = 'R';

	/**
	 * Account number of calls from method getLocation
	 *
	 * @var int $countCall
	 */
	protected $_countCallBreadCrumbs = 0;

	protected $_name    = "folder";
	protected $_primary = "id";

	public $filters = array(
		'*'  => 'StringTrim',
		'id' => 'Int'
	);

	public $validators = array(
		'title'		 => array(  'NotEmpty' ,  array( 'StringLength' , '0' , '255' ) ),
		'folder_id'	 => array(  'Int' ,  'NotEmpty' ),
		'visibility' => array(  array( 'StringLength' , '0' , '1' ) ),
		'module' 	 => array(  array( 'StringLength' , '0' , '1' ) )
	);

	protected $_dependentTables = array( "File_Model_FolderFile" , "File_Model_Folder" );

	protected $_referenceMap = array(
		array(
			 'refTableClass' => 'File_Model_Folder',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'folder_id' )
		),
		array(
			 'refTableClass' => 'Share_Model_Person',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'person_id' )
		)
	);

	protected function _save()
	{
		$user = new Zend_Session_NameSpace( 'user' );
        
		$this->_data['File_Model_Folder']['person_id'] = $user->person_id;
        if( !isset( $this->_data['File_Model_Folder']['module'] ) ){
            $this->_data['File_Model_Folder']['module']    = self::FLAG_COMPOSER;
        }
				
		if( $this->_data['File_Model_Folder']['visibility'] != self::VISIBILITY_PUBLIC ){
			$this->_data['File_Model_Folder']['visibility'] = self::VISIBILITY_RESTRICT;
		}
	}

	/**
	 * @param array $config
	 * @access public
	 * @return array
	 */
	public function getFoldersFiles( array $config )
	{
		$user = new Zend_Session_Namespace( 'user' );

		if( !$config['id'] ) $config['id'] = $config['default'];

		$where = array(
			"folder_id = ?" => $config['id'],
			"( visibility = '".self::VISIBILITY_PUBLIC."' ". Zend_Db_Select::SQL_OR ." person_id = ? )" => $user->person_id ,
			"module = '{$config['module']}'"
		);

		$rs = $this->fetchRow( array( "id = ?" => $config['id'] ) );

		if( !isset($config['relation']) ){
			$folders = $this->fetchAll( $where , "title" );
        }
		else{
			$folders = $this->fetchRelation( array( "folder_id = ?" => $config['id'] ) , "title" );
        }

		return array(
		  "files" 		=> $rs->findManyToManyRowset( "File_Model_File" , "File_Model_FolderFile" ) ,
		  "rs"    		=> $rs ,
		  "folders" 	=> $folders ,
		  "location" 	=> $this->getLocation( $rs , true , $config ) ,
		  "user"        => $user
		);
	}

	/**
	 * gets the current location of the folders => Imagens - JPG
	 *
	 * @param object Zend_Db_Table_Row $folder
	 * @param bool $firstCall
	 * @param array $config
	 * @access private
	 * @return string HTML
	 */
	public function getLocation( $folder  , $firstCall , array $config = array( "div" => "folder" , "uri" => "/file/folder/index/id/" ) )
	{
		$location = null;
		if( $folder->folder_id ){
			$location .= $this->getLocation( $this->find( $folder->folder_id )->current() , false  , $config );
        }

		if( !$firstCall ){
			$location .= "<a href='#this' onclick='new Preceptor.util.AjaxUpdate( \"{$config['div']}\" , \"{$config['uri']}{$folder->id}\" );'>{$folder->title}</a> - ";
        }
        
		$this->_countCallBreadCrumbs++;

		return $location;
	}

	/**
	 * verify allow folder
	 *
	 * @param $id int
	 * @return bool
	 */
	public function allowDelete( $id )
	{
		if( ( $id != self::DEFAULT_FOLDER_COMPOSER ) 	   	 &&
			( $id != self::DEFAULT_FOLDER_MULTIMEDIA ) 	   	 &&
			( $id != self::DEFAULT_FOLDER_FILE ) )
				return true;

		return false;
	}

	/**
	 *
	 * @param string $name
	 * @param array $values
	 */
	public function __call( $name , $values )
	{
        $method = preg_replace(  '/^(findBy|saveRelation)\w+$/i' , '\\1' , $name );
		$name   = preg_replace( '/^(findBy|saveRelation)/i' , '' , $name );
		$name 	= explode( "_" , $name );
        
		if( count( $name ) === 1 )
			$name = strtolower($name[0]) . "_id";

		if( is_array( $name ) ){
			foreach( $name as $key => $val ){
                $name[$key] = strtolower($val) . "_id";
            }
        }
        
		array_unshift( $values , $name );
		return call_user_func_array( array( $this  , $method  ) , $values );
	}

	/**
	 * find folder course_id or group_id or course_id
	 *
	 * @param string $column
	 * @param array $values
	 * @return Zend_Db_Table_Row|Zend_Db_Table_RowSet
	 */
	public function findBy( $column , $value , $folder_id = NULL )
	{
        $select = $this->select();

		$select->from( array( "f" => $this->_name ) , new Zend_Db_Expr( 'r.* , f.*' ) , $this->_schema )
			   ->join( array( "r" => "$this->_schema.relation" ) , "r.relation = f.relation" , array() )
			   ->where( "module = ?" , self::FLAG_FILE );

		if( $folder_id ){
			$select->where( "folder_id = ?" , $folder_id );
		}
        
		if( is_array( $value ) ){

			if( !is_array( $column ) ){

				$select->where( "r." . $column . " IN(".implode( "," , $value ).")" );
				$select->order( "f.title" );

			}else{

				foreach( $column as $key => $val ){
					$select->where( "r." . $val . " = ? " , $value[$key] );
				}

				$select->order( "f.id" );
			}
		}
		else{

			if( $value ){
				$select->where( "r." . $column . " = ?" , $value );
			}

			$select->order( "f.id" );
		}
        
		return $this->fetchAll( $select );
	}

	/**
	 * Enter description here...
	 *
	 * @param string|array $column
	 * @param null|Zend_Db_Table_Row $folderExists
	 * @param string $folder
	 * @param string|array $value
	 * @param int $folder_id
	 * @return int
	 */
	public function saveRelation( $column , $folderExists , $folder , $value , $folder_id )
	{
		$user = new Zend_Session_Namespace( "user" );
        $id = "";

		if( !$folderExists ){
			if( is_array( $column ) ){
				$dataRelation['Application_Model_Relation'] = array_combine( $column , (array) $value );
            }
			else{
				$dataRelation['Application_Model_Relation'][$column] = $value;
            }

			/**
			 * @todo size column relation 13
			 */
			$dataRelation['Application_Model_Relation']['relation']     = substr( uniqid() , 0 , 12 );
            
			$relation = new Application_Model_Relation();
			$relation->save( $dataRelation );

			$data['File_Model_Folder']['title']         = $folder;
			$data['File_Model_Folder']['folder_id']     = $folder_id;
			$data['File_Model_Folder']['visibility']    = self::VISIBILITY_PUBLIC;
			$data['File_Model_Folder']['module']        = self::FLAG_FILE;
			$data['File_Model_Folder']['relation']      = $dataRelation['Application_Model_Relation']['relation'];
			$data['File_Model_Folder']['person_id']     = $user->person_id;
            
 			$id = $this->save( $data );
		}

		return $id;
	}

	/**
	 * create hierarchy course/discipline/group/student
	 *
	 * @return void
	 */
	public function create( $infosUser , $user )
	{
		$relation = new Application_Model_Relation();

		if( !$user->folderHierarchyCreate && $user->role_id != Share_Model_Role::INSTITUTION ){
			$person = new Share_Model_Person();
			$nameUser = $person->find( $user->person_id )->current()->name;
		}
        
		foreach( $infosUser as $keyCourse => $disciplines ){
			$folderCourse      = $this->findByCourse( $keyCourse )->current();
			$idFolderCourse    = $this->saveRelationCourse( $folderCourse , $disciplines['course'] , $keyCourse , self::DEFAULT_FOLDER_FILE );
			$idFolderCourse    = ( $folderCourse ) ? $folderCourse->id : $idFolderCourse ;
            
			foreach( $disciplines['disciplines'] as $keyDiscipline => $groups  ){

				$folderDiscipline   = $this->findByCourse_Discipline( array( $keyCourse , $keyDiscipline ) )->current();
				$idFolderDiscipline = $this->saveRelationCourse_Discipline( $folderDiscipline , $groups['discipline'] , array( $keyCourse , $keyDiscipline ) , $idFolderCourse );
				$idFolderDiscipline = ( isset( $folderDiscipline ) && $folderDiscipline ) ? $folderDiscipline->id : $idFolderDiscipline ;
				foreach( $groups['groups'] as $keyClass => $group ){

					$folderGroup   = $this->findByCourse_Discipline_Class( array( $keyCourse , $keyDiscipline , $keyClass ) )->current();
					$idFolderGroup = $this->saveRelationCourse_Discipline_Class( $folderGroup , $group['group']  , array( $keyCourse , $keyDiscipline , $keyClass ) , $idFolderCourse );
					$idFolderGroup = ( isset( $folderGroup ) && $folderGroup ) ? $folderGroup->id : $idFolderGroup ;
					if( $user->role_id != Share_Model_Role::INSTITUTION ){
						$this->saveRelationCourse_Discipline_Class_Person( $this->findByCourse_Discipline_Class_Person( array( $keyCourse , $keyDiscipline , $keyClass , $user->person_id ) )->current() , $nameUser , array( $keyCourse , $keyDiscipline ,  $keyClass , $user->person_id ) , $idFolderGroup );
					}
				}
			}
		}
        
		/**
		 * @todo test correctly true
		 */
		$user->folderHierarchyCreate = true;
	}

	/**
	 * Get the number of calls the method getLocation()
	 *
	 * @return int
	 */
	public function getNumberCallBreadCrumbs()
	{
		return $this->_countCallBreadCrumbs;
	}

}