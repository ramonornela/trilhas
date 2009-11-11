<?php
class Preceptor_View_Helper_GetPermission extends Zend_View_Helper_Abstract
{	
	public function getPermission( $data )
	{
		$user = new Zend_Session_Namespace( "user" );
		
		if( !is_array( $data ) )
		{
            $action = $data;
            $data = array();
            
			$front = Zend_Controller_Front::getInstance();
            $data['action']     = $action;
			$data['module']     = $front->getRequest()->getModuleName();
			$data['controller']	= $front->getRequest()->getControllerName();
		}

        foreach( $user->resources[SYSTEM_ID] as $resource )
        {
            if( ( $resource['module'] == $data["module"] )&&( $resource['controller'] == $data["controller"] )&&( $resource['action'] == $data["action"] ) )
                return true;
        }
        
		return false;
	}
}