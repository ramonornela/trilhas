<?php
class Faq_FaqController extends Application_Controller_Abstract
{
    protected $_model = "Faq_Model_Faq";
    
	public function indexAction()
	{
		$module     = new Zend_Session_NameSpace( 'module' );
        $category   = new Category_Model_Category();

        $session_type = new Zend_Session_NameSpace( 'type' );
        $session_type->id = Faq_Model_Faq::TYPE;

		unset( $module->id );

		/**
		 * @todo create const in model
		 */
		$this->view->rs   = $category->fetchRelation( array("type = 'F'") , "id DESC" );
	}

	public function inputAction()
	{
		$id             = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
        $faqModel       = new Faq_Model_Faq();
        $categoryModel  = new Category_Model_Category();

		if ( $id )
		{
			$faq = $faqModel->fetchRow( array( 'id =?' => $id ) , "id DESC" );

			foreach ( $faq->findManyToManyRowset( 'Category_Model_Category' , 'Faq_Model_FaqCategory' ) as $category )
				$categoryIn[] = $category->id;

			$this->view->checked = Preceptor_Util::toSelect( $categoryModel->fetchRelation( array( "id IN(" . join( "," , $categoryIn ) . ")" , "type = 'F'" ) , "name" )->toArray() , array( 'id' , 'name' , null ) );

			$this->view->all = Preceptor_Util::toSelect( $categoryModel->fetchRelation( array( "id NOT IN(" . join( "," , $categoryIn ) . ")" , "type = 'F'" ) , "name" )->toArray()  , array( 'id' , 'name' , null ) );
			if ( ! $this->view->all)
				$this->view->all = array();
		}
		else
		{
            $this->view->all     = Preceptor_Util::toSelect( $categoryModel->fetchRelation( array( "type = 'F'" ) , "name" )->toArray()  , array( 'id' , 'name' , null ) );
			$this->view->checked = array();
			if ( ! $this->view->all)
				$this->view->all = array();
		}

		if( $id ){
			$this->view->data = $faqModel->find( $id )->current();
        }else{
            $this->view->data = $faqModel->createRow();
        }

		$categories = Zend_Filter::filterStatic( $this->_getParam( "categories" ) , "int" );
		if ( $categories )
		{
			$this->_redirect( "faq/faq/updatecategory" );
			return false;
		}
	}

	public function viewAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "Int" );

        $faqModel       = new Faq_Model_Faq();
        $categoryModel  = new Category_Model_Category();

		$category = $categoryModel->fetchRow( array( 'id =?' => $id , "type = 'F'" ) , "id DESC" );
        $faqsIn = array();
		foreach ( $category->findManyToManyRowset( 'Faq_Model_Faq' , 'Faq_Model_FaqCategory' ) as $faq ){
			$faqsIn[] = $faq->id;
		}
        
		if ( !$faqsIn ){
			$faqsIn = array(0);
        }

		$this->view->rs = $faqModel->fetchRelation( array( "id IN(" . join( "," , $faqsIn ) . ")" ) , "id DESC" );
		$this->view->category = $category;
	}

	public function deleteAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "Int" );
		$category_id = Zend_Filter::filterStatic( $this->_getParam( "category_id" ) , "Int" );

        $faqModel       = new Faq_Model_Faq();
        $faqCategory    = new Faq_Model_FaqCategory();

		$faqCategory->delete( array( 'faq_id' => $id ) );
		$faqModel->delete( array( 'id' => $id ) );

		if( !$this->notRedirect ){
			$this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted successfully" ) );
			$this->_redirect( "/faq/faq/view/id/" . $category_id );
		}

	}
}