<?php
/**
 * @author Preceptor Educação a Distância <contato@preceptoread.com.br>
 * @license http://www.preceptoread.com.br
 * @package Content
 * @category Controllers
 * @version 4.0
 */
class Content_ContentController extends Application_Controller_Abstract
{
    protected $ids = array();

    public function indexAction()
    {
        $id   = Zend_Filter::filterStatic( $this->_getParam("id") , "int" );

        $user          = new Zend_Session_Namespace("user");
        $content       = new Content_Model_Content();
        $contentAccess = new Content_Model_ContentAccess();
        $glossary      = new Glossary_Model_Glossary();

        $this->view->id = $id ? $id : $contentAccess->lastContent();

        if(!$user->words) {
            $user->words = Zend_Json::encode($glossary->words());
        }

        if (!$user->contents) {
            $user->contents = Zend_Json::encode(
                $content->fetchAllOrganize($user->discipline_id));
        }

        $this->view->words	  = $user->words;
        $this->view->contents = $user->contents;

        $this->view->addHelperPath( APPLICATION_PATH . "/views/helpers/" ,
            "Preceptor_View_Helper_" );
    }

    public function viewAction()
    {
        $user   = new Zend_Session_Namespace("user");
        $id     = Zend_Filter::filterStatic( $this->_getParam("id") , "int" );
        $hide   = $this->_getParam("hide");
        $string = $this->_getParam( "string" );

        if (!$id) {
            return;
        }
        
        $restriction   = new Content_Model_Restriction();
        $content	   = new Content_Model_Content();
        $contentAccess = new Content_Model_ContentAccess();
        $narration	   = new Content_Model_Narration();

        $restrictionRs = $restriction->verify($id);

        if( !isset($restrictionRs['has']) ) {
            $this->view->rs = $content->find( $id )->current();

            if( $string ) {
                $replace = "<span style='background: #FFFF00'>\\1</span>";
                $this->view->content = preg_replace("/({$string})/i",$replace,$this->view->content);
                unset( $string );
            }

            $this->view->narration =  $narration->fetchRow( array( 'content_id = ?' => $id ) );

            $data['Content_Model_ContentAccess']['content_id'] = $id;
            $data['Content_Model_ContentAccess']['person_id'] = $user->person_id;

            $contentAccess->save( $data );
        }else {
            echo $this->view->translate( $restrictionRs['content'] ) .
                " " . $restrictionRs['value'];
            exit;
        }

        $this->_helper->layout()->setLayout("clear");
    }

    public function createInitialContent()
    {
        $user = new Zend_Session_Namespace("user");
        $content = Content_Model_Content();

        $data['Content_Model_Content']['discipline_id'] = $user->discipline_id;
        $data['Content_Model_Content']['title'] 		= "Introdução";
        $data['Content_Model_Content']['ds'] 		    = "Bem vindo ao curso!";

        if ($content->save($data)) {
            $this->_redirect("/content/content/index");
        }
    }
}