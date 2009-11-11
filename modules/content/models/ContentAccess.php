<?php
/**
 * @author Preceptor Educação a Distância <contato@preceptoread.com.br>
 * @category Models
 * @package Content
 * @license http://www.preceptoread.com.br
 * @version 4.0
 * @final
 */
class Content_Model_ContentAccess extends Content_Model_Abstract {
/**
 * table name
 *
 * @var string $_name
 * @access protected
 */
    protected $_name    = "content_access";

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
    '*'  => array( 'StringTrim' , 'Int' )
    );

    /**
     * validators model
     *
     * @var array $validators
     * @access public
     */
    public $validators = array(
    'id'		 => array( 'Int' ),
    'content_id' => array( 'Int' ,  'NotEmpty' ) ,
    'person_id'  => array( 'Int' ,  'NotEmpty' )
    );

    /**
     * tables dependent this
     *
     * @var array $_dependentTables
     * @access protected
     */
    protected $_dependentTables = array();

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
            'refTableClass' => 'Person',
            'refColumns' => array( 'id' ),
            'columns' => array( 'person_id' )
        )
    );

    public function countByPerson( $discipline_id , $person_id = null ) {
        $user = new Zend_Session_Namespace('user');

        $select = $this->getAdapter()->select()->from( array( "ca" => $this->_name ), "c.id"  , 'trails' )
            ->distinct( true )
            ->join( array( "c" => "trails.content" ) , "c.id = ca.content_id" , null )
            ->where( "c.discipline_id = ?" , $discipline_id );
        if( $person_id ) {
            $select->where( "person_id = ?" , $person_id );
        }else {
            $select->where( "person_id = ?" , $user->person_id );
        }

        $rs = $this->getAdapter()->fetchAll( $select );

        return count($rs);
    }

    public function lastContent()
    {
        $user = new Zend_Session_Namespace('user');

        $select = $this->getAdapter()->select()
                ->from(array("ca" => $this->_name), "c.id" , $this->_schema)
                ->join(array("c" => "content"), "c.id = ca.content_id", null, $this->_schema)
                ->where("person_id = ?" , $user->person_id)
                ->where("c.discipline_id = ?" , $user->discipline_id)
                ->order('ca.id DESC')
                ->limit(1);

        $rs = $this->getAdapter()->fetchRow($select);

        return $rs['id'];
    }
}