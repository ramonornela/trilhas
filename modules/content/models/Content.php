<?php
/**
 * @author Preceptor Educação a Distância <contato@preceptoread.com.br>
 * @category Models
 * @package Content
 * @license http://www.preceptoread.com.br
 * @version 4.0
 * @final 
 */
class Content extends Table
{
	/**
	 * table name
	 * 
	 * @var string $_name
	 * @access protected
	 */
	protected $_name    = "trails_content";
	//public    $fileSave = 'ds';
	/**
	 * primary key 
	 * 
	 * @var string $_primary
	 * @access protected
	 */
	protected $_primary = "id";	
	
	/**
	 * @var array $filters
	 * @access public
	 */
	public $filters = array(
		'*'  => 'StringTrim',
		'id' => 'Int'
	);
	
	/**
	 * validators model
	 * 
	 * @var array $validators
	 * @access public
	 */
	public $validators = array( 
		'id'		 	=> array( 'Int' ), 
		'title'		 	=> array( 'NotEmpty' ,  array( 'StringLength' , '0' , '255' ) )
	);
	
	/**
	 * tables dependent this 
	 * 
	 * @var array $_dependentTables
	 * @access protected
	 */
	protected $_dependentTables = array( "Content" , "Narration" , "Notepad");
	
	/**
	 * configuration 
	 * 
	 * @var array $_referenceMap
	 * @access public
	 */
	protected $_referenceMap = array( 
		array(
			 'refTableClass' => 'Content',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'content_id' )
		),
		array(
			 'refTableClass' => 'Discipline',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'discipline_id' )
		) 
	);
	
	/**
	 * 
	 * @param array $datas
	 * @param bool|string $comparation 
	 * @access public 
	 * @return array
	 */
	public function organize( $datas )
	{
		$count = count($datas);

		for( $i = 0; $i < $count; $i++ ){			
			if( !$datas[$i]["content_id"] ){
				$contents[] = $datas[$i];
				unset( $datas[$i] );
			}
		}
		
		$datas = array_merge( $contents , $datas );
		foreach( $datas as $data ){
			if( !$data['content_id']  ){
				$return[$data['id']]['value'] = $data;
			} else {
				$return = $this->recursion( $return , $data );
			}
		}
		return $return;
	}
	
	public function organizeRestrict( $datas )
	{
		$count = count($datas);
		
		for( $i = 0; $i < $count; $i++ )
		{
			unset( $datas[$i]["title"] );
			unset( $datas[$i]["ds"] );
			unset( $datas[$i]["discipline_id"] );
			unset( $datas[$i]["position"] );
			
			if( !$datas[$i]["content_id"] ){
				$contents[] = $datas[$i];
				unset( $datas[$i] );
			}
		}
		
		$datas = array_merge( $contents , $datas );
		
		foreach( $datas as $data ){
			if( !$data['content_id']  ){
				$return[$data['id']]['value'] = $data;
			} else {
				$return = $this->recursion( $return , $data );
			}
		}
		return $return;
	}
	
	/**
	 * @param array $datas
	 * @param int $id
	 * @return array
	 */
	public function organizePrint( $datas , $id )
	{
		$count = count( $datas );
		$positionNulls = 0;
		
		for( $i = 0; $i <  $count; $i++ )
		{
			if( !$datas[$i]['self_content_id'] )
			{
				$temp = array( $datas[$i] );
				unset( $datas[$i] );
				array_splice( $datas , $positionNulls , 0 , $temp );
				$positionNulls++;
			}		
		}
		
		foreach( $datas as $data )
		{
			$groups[] = array( 
							 'id' 			=> $data['self_id'],
							 'title' 		=> $data['self_title'],
							 'ds'    		=> $data['self_ds'],
							  'content_id'	=> $data['self_content_id']
			);

			$groups[] = array( 
							 'id' 			=> $data['id'],
							 'title' 		=> $data['title'],
							 'ds'    		=> $data['ds'],
							 'content_id'	=> $data['content_id']
			);  
		}
		
		foreach( $groups as $data )
		{
			if( $data['id'] == $id )
				$return[$data['id']]['value'] = $data;
			else
				$return = $this->recursion( $return , $data );
		}
		
		return $return;
	}
	
	/**
	 * recursion child content
	 * 
	 * @access private 
	 * @return array
	 */
	private function recursion( $return , $data )
	{
		//debug( $return , 1 );
		foreach( $return as $key => $val )
		{
			if( @$return[$data['content_id']]['value'] ){
				$return[$data['content_id']]['child'][$data['id']]['value'] = $data;
				return $return;
			} else {
				if( @$val['child'] ) {
					$return[$val['value']['id']]['child'] = $this->recursion( $val['child'] , $data );
				}
			}
		}
		return $return;
	}
	
	public function searchContent( $string , $discipline_id )
	{
		$select = $this->select();
		
		$select->from( array( 'c' => $this->_name ), array( 'id', 'title' ) )
				->where(  "UPPER( ds ) LIKE UPPER(?) OR UPPER( title ) LIKE UPPER(?)", "%$string%" )
				->where( "discipline_id = ?" , $discipline_id );

		return $this->fetchAll( $select );
	}

    public function fetchAllOrganize( $discipline_id , $content_id = null , $response = null , $level = 0, $not_level = false )
	{
		$select = $this->select()
			->from( $this->_name,  array( new Zend_Db_Expr("id, title, content_id")))
			->where( "discipline_id = ?" , $discipline_id )
			->order( array( "position" , "id", 'content_id') );

		if( $content_id ){
			$select->where( 'content_id = ?' , $content_id );
		}else{
			$select->where( 'content_id IS NULL' );
		}

		$result = $this->fetchAll( $select )->toArray();

		if( count($result) ){
			foreach( $result as $rs ){
				$rs['level'] = $level;
				$response[] = $rs;
                                if(!(isset($not_level) && $not_level == ($level+1))){
                                    $response = $this->fetchAllOrganize( $discipline_id, $rs['id'], $response , $level+1, $not_level );
                                }
				
			}
		}

		return $response;
	}
	
}