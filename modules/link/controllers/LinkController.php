<?php
class Link_LinkController extends Application_Controller_Abstract
{
    protected $_model = "Link_Model_Link";

	public function indexAction()
	{
		$module = new Zend_Session_NameSpace( 'module' );
		unset( $module->id );

        $session_type = new Zend_Session_NameSpace( 'type' );
        $session_type->id = Link_Model_Link::TYPE;

        $category = new Category_Model_Category();

		$this->view->rs = $category->fetchRelation( array("type = 'L'") , "id DESC" );
	}

	public function inputAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "Int" );
        
        $linkModel      = new Link_Model_Link();
        $categoryModel  = new Category_Model_Category();

        $this->view->data = $linkModel->createRow();

		if ( $id ){
			$link = $linkModel->fetchRow( array( 'id =?' => $id ) );

            $categoryIn = array();
            
			foreach ( $link->findManyToManyRowset( 'Category_Model_Category' , 'Link_Model_LinkCategory' ) as $category ){
				$categoryIn[] = $category->id;
            }

            if( count( $categoryIn ) ){
                $this->view->checked = Preceptor_Util::toSelect( $categoryModel->fetchRelation( array( "id IN(" . join( "," , $categoryIn ) . ")" , "type = 'L'" ) , "name" ) , array( 'id' , 'name' , null ) );
                $this->view->all = Preceptor_Util::toSelect( $categoryModel->fetchRelation( array( "id NOT IN(" . join( "," , $categoryIn ) . ")" , "type = 'L'" ) , "name" )  , array( 'id' , 'name' , null ) );
            }

			if ( !$this->view->all )
				$this->view->all = array();
		}else{
			$this->view->all     = Preceptor_Util::toSelect( $categoryModel->fetchRelation( array( "type = 'L'" ) , "name" )  , array( 'id' , 'name' , null ) );
			$this->view->checked = array();
			if ( ! $this->view->all)
				$this->view->all = array();
		}

		if( $id ){
			$this->view->data = $linkModel->find( $id )->current();
        }

		$categories = Zend_Filter::filterStatic( $this->_getParam( "categories" ) , "int" );

		if ( $categories ){
			$this->render( "link/updatecategory" , "ajax" );
			return false;
		}
	}

	public function viewAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );

        $categoryModel  = new Category_Model_Category();
        $linkModel      = new Link_Model_Link();

        $linksIn = array();

		$category = $categoryModel->fetchRow( array( 'id =?' => $id , "type = 'L'" ) , "id DESC" );
		foreach ( $category->findManyToManyRowset( 'Link_Model_Link' , 'Link_Model_LinkCategory' ) as $link ){
			$linksIn[] = $link->id;
		}

		if ( !count( $linksIn ) ){
			$linksIn = array(0);
        }

        $this->view->category_id    = $category->id;
		$this->view->rs             = $linkModel->fetchRelation( array( "id IN(" . join( "," , $linksIn ) . ")" ) , "id DESC" );
	}

	public function deleteAction()
	{
		$id          = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "Int" );
		$category_id = Zend_Filter::filterStatic( $this->_getParam( "category_id" ) , "Int" );

        $linkModel      = new Link_Model_Link();
        $linkCategory   = new Link_Model_LinkCategory();

		$linkCategory->delete( array( 'link_id' => $id , 'category_id' => $category_id ) );
		$linkModel->delete( array( 'id' => $id ) );

		if( !$this->notRedirect ){
			$this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted successfully" ) );
			$this->_redirect( "link/link/view/id/" . $category_id );
		}

	}

}