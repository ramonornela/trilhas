<?php
class ThemeController extends Application_Controller_Abstract
{
	public $_model = false;

	public function indexAction()
	{
        $this->view->user = $user = new Zend_Session_Namespace("user");
		
        $this->view->rs   = array( 'cupertino' , 'excite-bike' , 'ui-darkness' ,
								   'start' , 'redmond' , 'swanky-purse' ,
								   'hot-sneaks' , 'mint-choc' , 'smoothness' ,
								   'ui-lightness' , 'south-street' , 'blitzer' ,
								   'vader' , 'humanity' , 'dot-luv' );

		$cookieName = "theme-".$user->group_id;

		$this->view->theme = $_COOKIE[$cookieName] ? $_COOKIE[$cookieName] : DEFAULT_THEME;
	}
}