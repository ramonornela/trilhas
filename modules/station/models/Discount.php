<?
class Station_Model_Discount extends Station_Model_Abstract
{
	protected $_schema   = "station";
	protected $_name     = "discount";
	protected $_primary  = "id";

	protected $_referenceMap = array(
	);

	public function fetchPromotion( $code )
	{
        $query = $this->select()->setIntegrityCheck( "false" );

        $query->where( 'UPPER (name) ILIKE UPPER(?)' , "$code" )
              ->where( 'status = ?' , Station_Model_Status::ACTIVE );

        $result = $this->fetchRow( $query );
        
        if( $result ){
            return $result->id;
        }

        return false;
	}
    
	public function fetchDiscountByPerson( $person_id , $class_id )
	{
        $query = $this->select()->setIntegrityCheck( "false" );

        $query->from( array( "d" => "discount" ) , array( "d.*" ) , "station" )
              ->join( array( "dp" => "discount_person" ) , "d.id = dp.discount_id" , array() , "station" )
              ->where( "dp.person_id =?" , $person_id )
              ->where( "dp.class_id =?" , $class_id );

        $result = $this->fetchRow( $query );

        if( $result ){
            return $result;
        }
        
        return false;
	}
}
