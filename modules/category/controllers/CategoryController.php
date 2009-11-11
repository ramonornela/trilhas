<?php
class Category_CategoryController extends Application_Controller_Abstract
{
    protected $_model = "Category_Model_Category";

	public function indexAction()
	{
		$type = Zend_Filter::filterStatic( $this->_getParam( "type" ) , "HtmlEntities" );
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );

		$module       = new Zend_Session_NameSpace( 'module' );
		$session_type = new Zend_Session_NameSpace( 'type' );
        $category     = new Category_Model_Category();

		if ( $id ){
			$module->id = $id;
        }
            
		if ( $type ){
			$session_type->id = $type;
        }
        
		$this->view->rs   = $category->fetchRelation( array("type =?" => $session_type->id ) , "id DESC" );
		$this->view->type = $session_type->id;
		$this->view->id   = $module->id;

        $this->view->data->id   = null;
        $this->view->data->name = null; 

		/**
		 * @todo create const in model
		 */
		if ( $session_type->id == "L" ){
			$this->view->name = "focus_link";
			$this->view->module = "link";
		}
		else{
			$this->view->name = "focus_faq";
			$this->view->module = "faq";
		}

        $this->view->help = null;
        $this->_helper->layout->setLayout( "clearbox" );
	}

	public function inputAction()
	{
		$session_type = new Zend_Session_NameSpace( 'type' );
        $category     = new Category_Model_Category();

        $this->view->data = $category->createRow();

		$this->view->type = $session_type->id;
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );

		if( $id ){
			$this->view->data = $category->fetchRow( array( "id =?" => $id ) );
        }

        $this->view->help = null;
		$this->_helper->layout->setLayout( "clearbox" );
	}

    public function saveAction()
    {
        if( !$this->getRequest()->isPost() ){
			throw new Xend_Exception( "Problem during submit. saveAction" );
		}

        $category = new Category_Model_Category();
        $type     = new Zend_Session_NameSpace( 'type' );

        $result = $category->save( $_POST['data'] );

        $this->_helper->_flashMessenger->addMessage( $result->message );

        if( !$result->error ){
            if( $type->id == Link_Model_Link::TYPE ){
                echo "<script type='text/javascript'>\n
                        layout.addPanel( 'link-link' , '".$this->view->url."/link/link/index' )\n
                      </script>";
                exit;
            }else if( $type->id == Faq_Model_Faq::TYPE ){
                echo "<script type='text/javascript'>\n
                        layout.addPanel( 'faq-faq' , '".$this->view->url."/faq/faq/index' )\n
                      </script>";
                exit;
            }
        }

        $this->_redirect( "category/category/index" );
    }

	public function deleteAction()
	{
		$id   = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );

        $categoryModel = new Category_Model_Category();
        $type          = new Zend_Session_NameSpace( 'type' );

		$category = $categoryModel->fetchRow( array( 'id = ?' => $id ) );

		/**
		 * @todo create const in model
		 */
		if ( $type->id == 'F' ){
			$model = 'Faq_Model_Faq' ;
        }else{
			$model = 'Link_Model_Link';
        }

        $values = array();
		foreach ( $category->findManyToManyRowset( $model , $model . "Category" ) as $value ){
			$values[] = $value->id;
        }
        
		if ( count( $values ) ){
			$this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted error" ) );
		}else{
            $this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted successfully" ) );
            $categoryModel->delete( $id );
        }

		if( $type->id == Link_Model_Link::TYPE ){
            echo "<script type='text/javascript'>\n
                    layout.addPanel( 'link-link' , '".$this->view->url."/link/link/index' )\n
                  </script>";
            exit;
        }else if( $type->id == Faq_Model_Faq::TYPE ){
            echo "<script type='text/javascript'>\n
                    layout.addPanel( 'faq-faq' , '".$this->view->url."/faq/faq/index' )\n
                  </script>";
            exit;
        }
	}
}