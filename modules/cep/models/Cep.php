<?
class Cep_Model_Cep extends Cep_Model_Abstract
{
	protected $_schema  = "cep";
	protected $_name    = "log_logradouro";
	protected $_primary = "log_nu";

    public function find( $cep )
    {
        $where = $this->select();
        
        $where->from( array( "log" => "log_logradouro" ), new Zend_Db_Expr( "*" ) , $this->_schema )
              ->join( array( "loc" => "$this->_schema.log_localidade" ), "loc.loc_nu = log.loc_nu" , array( "" ) )
              ->join( array( "lob" => "$this->_schema.log_bairro" ) , " lob.bai_nu = log.bai_nu_ini " , array() )
              ->where( "UPPER( log.cep ) LIKE UPPER( ? )" , "%$cep%" );

        return $this->fetchAll( $where );
    }
}