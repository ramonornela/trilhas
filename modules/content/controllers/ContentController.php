<?php
/**
 * @author Preceptor Educação a Distância <contato@preceptoread.com.br>
 * @license http://www.preceptoread.com.br
 * @package Content
 * @category Controllers
 * @version 4.0
 * @final
 */
class ContentController extends Controller {
    //public $uses = array( "Content" , "ContentAccess" , "Course" , "Discipline" , "Narration" , "File", "Glossary" , "Restriction" , "Evaluation" , "EvaluationNote" , "BulletinGroup" , "Bulletin" , "BulletinNote" );

    protected $ids = array();

    /**
     * @access public
     * @return void
     * @final
     */
    public function indexAction() {
        $id   = Zend_Filter::filterStatic( $this->_getParam("id") , "int" );
        $user = new Zend_Session_Namespace("user");

        if( $id ) {
            $this->view->content = $id;
        } else {
            $this->view->content = $this->ContentAccess->lastContent();
        }

        /** @todo NULL FIRST incompatible with version 8.2 */
        $rs  = $this->Content->fetchAll( array( "discipline_id = ?" => Zend_Filter::filterStatic( $user->discipline_id , 'Int' ) ) , array( "content_id" , "position" , "id" ) )->toArray();

        if( !$rs ) {
            $this->createInitialContent();
        }

        if( !$user->words ) {
            $user->words = Zend_Json::encode( $this->Glossary->words() );
        }

        //if( !$user->contents ){
        //	$user->contents = Zend_Json::encode(
        //		$content->fetchAllOrganize( $user->discipline_id ) );
        //}

        $contents = new Zend_Session_Namespace("contents");
        $contents->all = $this->Content->organizeRestrict( $rs );

        $this->view->words      = $user->words;
        $this->view->discipline = $this->Discipline->find( $user->discipline_id )->current();
        $this->view->contents   = $this->Content->organize( $rs );
        $this->view->addHelperPath( DIR_APPLICATION . "/views/helpers/" , "Preceptor_View_Helper_" );

        $this->render( null ,  "ajax" );
    }

    /**
     * @access public
     * @return void
     * @final
     */
    public function viewAction() {

        $user   = new Zend_Session_Namespace("user");
        $id     = Zend_Filter::filterStatic( $this->_getParam("id") , "int" );
        $hide   = $this->_getParam("hide");
        $string = $this->_getParam( "string" );

        //$this->inIds( null , $id  );
        //$restriction = $this->Restriction->verify( join ( "," , $this->ids ) , $id );

        if( !$restriction['has'] ) {
            $this->view->rs = $this->Content->find( $id )->current();

            if(  !$hide ) {
                $this->view->childs = $this->Content->fetchAll( array( 'content_id = ?' => $id ) , array( "position" , "id" ) );
            }

            if( $string ) {
                $replace = "<span style='background: #FFFF00'>\\1</span>";
                $this->view->content = preg_replace("/({$string})/i", $replace, $this->view->content);
                unset( $string );
            }

            $this->view->narration =  $this->Narration->fetchRow( array( 'content_id = ?' => $id ) );

            $data['content_id'] = $id;
            $data['person_id'] = $user->person_id;

            $this->ContentAccess->save( $data );
        }else {
            $this->view->restriction = $this->view->translate( $restriction['content'] ) . " " . $restriction['value'];
        }

        $this->render( null , "ajax" );
    }

    public function inIds( $contents = null , $id , $bool = true ) {
        if ( !$contents ) {
            $contents = new Zend_Session_Namespace("contents");
            $contents = $contents->all;
        }

        foreach ( $contents as $key => $content ) {

            if ( $content['value']['ID'] == $id )
                $bool = false;

            if ( $bool )
                $this->ids[$key] = $content['value']['id'];

            if ( ( $content['child'] ) ) {
                $bool = $this->inIds( $content['child'] , $id , $bool );
            }
        }

        return $bool;
    }

    public function createInitialContent() {
        $user = new Zend_Session_Namespace("user");

        $data['discipline_id'] = $user->discipline_id;
        $data['title'] 		   = "Introdução";
        $data['ds'] 		   = "Bem vindo ao curso!";

        if( $this->Content->save( $data ) ) {
            $this->_redirect( "content/index" );
        }
    }
}
