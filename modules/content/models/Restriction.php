<?php
class Content_Model_Restriction extends Content_Model_Abstract
{
    const TIME = "CT";
    const EVALUATION = "AV";
    
	protected $_name 	= 'restriction';
    protected $_primary = 'id';

    protected $_restriction;
    
    public $types = array(
        "AV" => "evaluation",
        "CT" => "time"
    );

	public $filters = array(
		'*'	 => 'StringTrim',
	    'id' => 'Int',
	    'content_id' => 'DefaultValue',
		'class_id'   => 'DefaultValue'
	);
		
	public $validators_time = array(
	    'id'   => 'Int',
		'content_id' => array( 'NotEmpty' , 'Int' )
	);
	
	protected $_dependentTables = array( "Content_Model_RestrictionBulletin" , "Content_Model_RestrictionTime" );
    
    protected $_referenceMap = array(
		array(
			 'refTableClass' => 'Content_Model_Content',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'content_id' )
		),
		array(
			 'refTableClass' => 'Station_Model_ClassModel',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'class_id' )
		)
		
	);
	
    public function verify($content_id)
    {
		$in = $this->_getParentContent($content_id);
		
        $this->_verifyBulletin($in);

        return $this->_restriction;
    }

	private function _getParentContent($id){
		$user = new Zend_Session_Namespace("user");

		$contents = Zend_Json::decode($user->contents);
		
		foreach($contents as $content){
			if($content['id']!=$id){
				$tmp[] = $content['id'];
			}else{
				$tmp[] = $content['id'];
				return join(',',$tmp);
			}
		}

		return 0;
	}

    private function _verifyBulletin($in)
    {
		$user = new Zend_Session_Namespace('user');
        $evaluation = new Content_Model_RestrictionBulletin();
        $result = $evaluation->fetchRestrictionByContent($in);
		
        if( count($result) ){
        	foreach ($result as $rs) {
				$group = $rs->findParentRow(
					'Bulletin_Model_BulletinGroup',
					null ,
					$rs->select()->where('discipline_id =?',$user->discipline_id)
				);
										
            	$average = $this->_countAverage($rs,$group,$user);
            	
				if ( $average < $rs->note ){
					$this->_restriction['has']     = true;
                    $this->_restriction['content'] = "restriction content, note must have less than";
                    $this->_restriction['value']   = number_format( $rs->note , 2 , "," , "." ) . " (" . $group->title . ")";
                    return false;
				}
				
				if ( $average < $rs->note_restriction ){
					$this->_verifyTime($in);
				}
            }
        }else{
            $this->_verifyTime($in);
        }
            
    }
    
    private function _verifyTime($in)
    {
    	$result = $this->fetchTimeByContent($in);
		$filter = new Xend_Filter_Date('dd/MM/yyyy','yyyy-MM-dd');
		
        if( count($result) ){
        	foreach ( $result as $rs ) {
				$started  = (float) preg_replace('/[^0-9]/','',$rs->started);
				$finished = (float) preg_replace('/[^0-9]/','',$rs->finished);
				$today    = (float) date('Ymd');

				if( $started > $today ){
					$this->_restriction['has']     = true;
					$this->_restriction['content'] = "restricted content, access after";
					$this->_restriction['value']   = $filter->filter($rs->started);
					return;
				}
                    
                if( $finished < $today ){
					$this->_restriction['has']     = true;
					$this->_restriction['content'] = "content expired since the";
					$this->_restriction['value']   = $filter->filter($rs->finished);
					return;
                }
			}
    	}
    }
    
    private function _countAverage($rs,$group,$user)
    {
    	$notes = array();
		$count = 0;
        
    	foreach ($group->findDependentRowset('Bulletin_Model_Bulletin') as $key=>$bulletin){
			$bulletinNote = $bulletin->findDependentRowset( 'Bulletin_Model_BulletinNote' , null , $bulletin->select()->where( 'person_id = ?' , $user->person_id ) );
			if( count($bulletinNote) ){
				$notes[$bulletin->id] = $bulletinNote->current()->note;
			}

			$evaluationNote = $bulletin->findDependentRowset( 'Evaluation_Model_EvaluationNote' , null , $bulletin->select()->where( 'person_id = ?' , $user->person_id ) );
			if( count($evaluationNote) ){
				$notes[$bulletin->id] = $evaluationNote->current()->note;
			}
			
			$count++;
		}

		$sum = array_sum( $notes );
		$average = round( $sum / $count , 2 );
		
		return $average;
    }
    
	public function fetchTimeByContent( $in )
	{
		$select = $this->select()->setIntegrityCheck(false);
		
		$select->from( array('r'=>$this->_name),'*','trails')
			   ->join( array('rt'=>'restriction_time'),'r.id = rt.restriction_id','*','trails')
			   ->where('content_id IN(' . $in  . ')');
		   
		return $this->fetchAll( $select );
	}
	
	public function fetchByBulletin( $id )
	{
		$select = $this->select()->setIntegrityCheck(false);
		
		$select->from(array( 'r' => $this->_name ), 
					  array('r.*', 'rb.*',
							 'r.id as rid',
							 'rb.id as bulletin_id'),
					  'trails')
			   ->join( array('rb'=>'restriction_bulletin'),
					   'r.id = rb.restriction_id',array(),'trails')
			   ->where('r.id = ?',$id);
   
		return $this->fetchRow($select);
	}
	
	public function fetchByTime( $id )
	{
		$select = $this->select()->setIntegrityCheck(false);
		
		$select->from(array('r'=>$this->_name),
					  array('r.*','r.id as rid','rt.id as bulletin_id'),'trails')
			   ->join(array('rt'=>'restriction_time'),
					  'r.id = rt.restriction_id','*','trails')
			   ->where('rt.id = ?',$id);
   
		return $this->fetchRow($select);
	}
}