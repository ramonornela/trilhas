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
class Narration extends Table
{
	/**
	 * @var string $_name 
	 * @access public
	 */
	protected $_name = 'trails_narration';
	
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
			 'refTableClass' => 'Content',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'content_id' )
		),
		array(
			 'refTableClass' => 'File',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'file_id' )
		) 
	);
}
?>