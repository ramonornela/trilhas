<?php
/**
 * @author Preceptor Educação a Distância <contato@preceptoread.com.br>
 * @category Models
 * @package Content
 * @license http://www.preceptoread.com.br
 * @version 4.0
 * @final 
 */
class Content_Model_Content extends Content_Model_Abstract
{
	protected $_name    = "content";
	protected $_primary = "id";	
	
	public $filters = array(
		'*'  => 'StringTrim',
		'id' => 'Int',
		'content_id' => 'DefaultValue',
	);
	
	public $validators = array( 
		'title'	=> array( 'NotEmpty' ,  array( 'StringLength' , '0' , '255' ) )
	);
	
	protected $_dependentTables = array( "Content_Model_Content",
										 "Content_Model_Narration",
										 "Content_Model_Restriction" );
	
	protected $_referenceMap = array( 
		array(
			 'refTableClass' => 'Content_Model_Content',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'content_id' )
		),
		array(
			 'refTableClass' => 'Station_Model_Discipline',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'discipline_id' )
		) 
	);

	public function fetchAllOrganize($discipline_id, $content_id = null, $response = null, $level = 0, $where = array())
	{
		$select = $this->select()
                       ->from('content',  array( "id" , "title" ), 'trails')
                       ->where("discipline_id = ?", $discipline_id)
                       ->order(array("position", "id"));

		if ($content_id) {
			$select->where('content_id = ?', $content_id);
		} else {
			$select->where('content_id IS NULL');
		}

        if (isset($where) && $where) {
            if (is_array($where)) {
                foreach ($where as $key => $val) {
                    $select->where("$key", $val);
                }
            }
        }

		$result = $this->fetchAll($select)->toArray();

		if (count($result)) {
			foreach( $result as $rs ){
				$rs['level'] = $level;
				$response[] = $rs;
				$response = $this->fetchAllOrganize( $discipline_id, $rs['id'], $response , $level+1 );
			}
		}

		return $response;
	}

	public function getPositionById( $id , $data ){
		foreach( $data as $key => $val ){
			if( $val['id'] == $id ){
				return $key;
			}
		}

		return false;
	}
	
	public function searchContent( $string , $discipline_id )
	{
		$select = $this->select();
		
		$select->from( array( 'c' => $this->_name ), array( 'id', 'title' , 'co.title AS parent' ) , 'trails' )
               ->joinLeft( array( 'co' => $this->_name ) , "co.id = c.content_id" , array() , "trails" )
			   ->where(  "UPPER( c.ds ) LIKE UPPER(?) OR UPPER( c.title ) LIKE UPPER(?)", "%$string%" )
			   ->where( "c.discipline_id = ?" , $discipline_id );

		return $this->fetchAll( $select );
	}
	
}