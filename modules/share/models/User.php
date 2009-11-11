<?
class Share_Model_User extends Share_Model_Abstract
{
	const VISIT_USERNAME = "Visitante";

    const VISIT_PERSON_ID = 2;

    const VISIT_USER_ID = 2;

    const VISIT_IMAGE = false;

    protected $_schema  = "share";
	protected $_name    = "user";
	protected $_primary = "id";	

    public $validators = array(
		'username'      => array( 'NotEmpty' ,  array( 'StringLength' , '0' , '100' ) ),
        'password'      => array( 'NotEmpty' ),
		'confirmation'	=> array( array( 'Identical' , "Share_Model_User-password" ) )
	);

	public function savepass( $id )
	{
		$data['Share_Model_User']['id'] = $id;
		
   		if( $_POST['password'] ){
			$data['Share_Model_User']['password'] = md5(
				Preceptor_Share_User::LOGIN_TRASH . $_POST['password']
			);
		}
		
   		$data['Share_Model_User']['username'] = $_POST['email'];
   		
		return $this->save( $data );
	}
}