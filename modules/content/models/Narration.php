<?php
/**
 * @author Preceptor Educação a Distância <contato@preceptoread.com.br>
 * @license http://www.preceptoread.com.br
 * @category Models
 * @package Composer
 * @subpackage Narration
 * @version 4.0
 * @final 
 */
class Content_Model_Narration extends Content_Model_Abstract
{
	/**
	 * @var string $_name 
	 * @access public
	 */
	protected $_name = 'narration';
	
	/**
	 * @var string|array $_name 
	 * @access public
	 */
	protected $_primary = array( 'file_id' , 'content_id' );

	/**
	 * @var array $filters
	 * @access public
	 */
	public $filters = array(
		'*'  => 'StringTrim',
		'file_id' => 'Int'
	);
	
	/**
	 * @var array $validators
	 * @access public
	 */
	public $validators = array( 
		'file_id'		=> array(  'Int' ,  'NotEmpty' ), 
		'content_id'		=> array(  'Int' ,  'NotEmpty' ) 
	);
	
	/**
	 * @var array $_referenceMap
	 * @access protected
	 */
	protected $_referenceMap = array( 
		array(
			 'refTableClass' => 'Content_Model_Content',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'content_id' )
		),
		array(
			 'refTableClass' => 'File_Model_File',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'file_id' )
		) 
	);
}
?>