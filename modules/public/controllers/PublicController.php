<?php
class Public_PublicController extends Application_Controller_Abstract
{
    protected $_model = false;

    public function indexAction()
	{
        $content = new Content_Model_Content();
        $user    = new Zend_Session_Namespace('user');

        $contents = $content->fetchAllOrganize( $user->discipline_id );
        $this->view->contents = $this->toSelectContent( $contents );

        $this->view->rs = $content->fetchAll( array(
                                                "public =? " => Station_Model_Status::ACTIVE,
                                                "discipline_id =?" => $user->discipline_id ),
                                                array( "content_id DESC" , "position" )
                                              );

        $this->view->help = false;
	}

    /**
	 * @param array $data
	 * @param array $xhtml
	 * @param null|string $line
	 * @access public
	 * @return array
	 */
	private function toSelectContent( $data )
	{
		$select = array( "0" => "[ Selecione ]" );
		foreach( $data as $key => $val ){
			$select[$val['id']] = str_repeat('- ', $val['level']) . $val['title'];
		}
        
		return $select;
	}

    public function saveAction()
    {
        $content_id = Zend_Filter::filterStatic( $this->_getParam( "content_id" ) , "Int" );

        $this->updatePublic( $content_id , Station_Model_Status::ACTIVE );
    }

    public function deleteAction()
    {
        $content_id = Zend_Filter::filterStatic( $this->_getParam( "content_id" ) , "Int" );

        $this->updatePublic( $content_id , Station_Model_Status::INACTIVE );
    }

    private function updatePublic( $content_id , $status )
    {
        $data = array();
        $content = new Content_Model_Content();

        // update the column 'public' with value the of status
        $rs['Content_Model_Content'] = $content->fetchRow( array( "id =?" => $content_id ) )->toArray();
        $rs['Content_Model_Content']['public'] = $status;
        $content->save( $rs );

        // verify if 'content_id' has children
        $children = $content->fetchAll( array( "content_id =?" => $content_id ) )->toArray();
        if( isset( $children ) && count( $children ) ){
            foreach( $children as $key => $val ){
                $data['Content_Model_Content']['id'] = $val['id'];
                $data['Content_Model_Content']['public'] = $status;

                $content->save( $data );
            }
        }

        $this->_helper->_flashMessenger->addMessage( $this->view->translate( "registered sucessfuly" ) );
        $this->_redirect( "public/public/index" );
    }

    public function viewAction()
    {
        $id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "Int" );
        $discipline_id = Zend_Filter::filterStatic( $this->_getParam( "discipline_id" ) , "Int" );

        $content   = new Content_Model_Content();
        $narration = new Content_Model_Narration();
        
        if( $id ){
            $this->view->rs = $content->fetchRow( array( "id =?" => $id ) );
            $this->view->narration =  $narration->fetchRow( array( 'content_id = ?' => $id ) );
            $this->render( "ds" );
        }else{
            $this->view->contents = Zend_Json::encode(
                $content->fetchAllOrganize(
                    $discipline_id , null , null , null , array( "public = ?" => Station_Model_Status::ACTIVE ) )
            );
        }
                                                 
        $this->view->theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : DEFAULT_THEME;
        $this->_helper->layout->setLayout('clear');
    }
}