<?php
/**
 * @author Preceptor Educação a Distância <contato@preceptoread.com.br>
 * @category Controllers
 * @package Composer
 * @version 4.0
 * @license http://www.preceptoread.com.br
 * @final
 */
class Composer_ComposerController extends Controller {
    /**
     * @var array $uses
     * @access public
     */
    //public $uses = array( "Content" , "Narration" , "Notepad" , "File" , "Restriction" , "ContentAccess" );

    /**
     * @access public
     * @return void
     * @final
     */
    protected $_model = "Content";

    public function indexAction() {
        $id   = Zend_Filter::filterStatic( $this->_getParam("id") , "int" );
        $user = new Zend_Session_Namespace("user");

        if( $id )
            $this->view->content = $id;
        else {
            $temp = new Zend_Session_Namespace( "temp" );

            if( $temp->validate ) {
                $this->view->validate = $temp->validate;
                unset( $temp->validate );
            }
        }
        /**
         * @todo NULL FIRST incompatible with version 8.2
         */
        $rs  = $this->Content->fetchAll( array( "discipline_id = ?" => Zend_Filter::filterStatic( $user->discipline_id , 'Int' ) ) , array( "content_id" , "position" , "id" ) )->toArray();

        $this->view->contents = $this->Content->organize( $rs );

        $this->view->addHelperPath( DIR_APPLICATION . "/views/helpers/" , "Preceptor_View_Helper_" );

        $this->render( null ,  "ajax" );
    }

    /**
     * @access public
     * @return void
     * @final
     */
    public function inputAction() {
        $id   	= Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
        $parent = $this->_getParam( "parent" ) ? true : false;

        $user = new Zend_Session_Namespace("user");
        $temp = new Zend_Session_Namespace( "temp" );

        if( $id ) {
            $this->view->data 	   = $this->Content->find( $id )->current();
            $this->view->narration = $this->Narration->fetchRow( array( "content_id = ?" => $id ) );
        }
        else {
            if( $temp->data ) {
                $this->view->data = $temp->data;
                unset( $_SESSION['temp'] );
            }
        }

        /**
         * @todo NULL FIRST incompatible with version 8.2
         */
        $rs  = $this->Content->fetchAll( array( "discipline_id = ?" => Zend_Filter::filterStatic( $user->discipline_id , 'Int' ) , 'id <> ?' => $id ) , array( "content_id" , "position" , "id" ) )->toArray();

        $this->view->parent			= $parent;
        $this->view->contents 		= $this->toSelectContent( $this->Content->organize( $rs ) );
        $this->view->jsonValidate 	= Zend_Json::encode( $this->Content->validators );
        $this->render( null , "ajax" );
    }

    /**
     * @access public
     * @return void
     * @final
     */
    public function saveAction() {
        unset( $_SESSION['temp'] );

        $user  = new Zend_Session_Namespace("user");
        $input = $this->preSave();

        if( $input->isValid() && $this->uploadNarration() ) {
            $data = $this->setNull( $input->toArray() );

            if( !$data['content_id'] ) {
                unset( $data['content_id'] );
            }

            $data['discipline_id'] = $user->discipline_id;
            
            $input->id = $id = $this->Content->save( $data );
            
            if ( $id ) {
                $this->postSave( true , $input );
            }
        }

        $this->postSave( false , $input );
    }

    /**
     * @param bool $saved
     * @param object Zend_Filter_Input $input
     * @access public
     * @final
     */
    public function postSave( $saved , $input ) {
        if( $saved ) {
            $this->_helper->_flashMessenger->addMessage( $this->view->translate( "registered successfully" ) );
            $this->_redirect( "/composer/composer/input/id/" . $input->id );
        }
        else {
            $this->_helper->_flashMessenger->addMessage( $this->view->translate( "save error" ) );
            $temp 			= new Zend_Session_Namespace( "temp" );
            $temp->data  	= $input->toView();
            $temp->validate = $input->getMessages();
            $this->_redirect( "/composer/composer/input/id/" . Zend_Filter::filterStatic( $_POST['id'] , 'Int' ) );
        }
    }

    /**
     * @access public
     * @return void
     * @final
     */
    public function deleteAction() {
        $id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );

        try {
            if( $id ) {
                $this->ContentAccess->delete( $id , 'content_id' );

                $notepads = $this->Notepad->fetchAll( array( 'content_id =?' => $id ) );

                if ( $notepads->count() ) {
                    foreach ( $notepads as $notepad ) {
                        $this->Notepad->save( array( 'id' => $notepad->id , 'content_id' => null ) );
                    }
                }
                $this->Content->delete( $id );

                $this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted successfully" ) );
            }
        }
        catch( Exception $e ) {
            $this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted error" ) );
        }

        exit("ok");
    }

    /**
     * @param array $data
     * @param array $xhtml
     * @param null|string $line
     * @access public
     * @return array
     */
    private function toSelectContent( $data , $xhtml = array( "0" => "Principal" ) , $line = null ) {
        foreach( $data as $key => $val ) {
            if( !$val['child'] ) {
                $xhtml[$val['value']['id']] = $line . ". " . $val['value']['title'];
            }
            else {
                $xhtml[$val['value']['id']] = $line . ". " . $val['value']['title'];

                $xhtml = $this->toSelectContent( $val['child'] , $xhtml , $line . ".." );
            }
        }

        return $xhtml;
    }

    /**
     * @access private
     * @return bool
     */
    private function uploadNarration() {
        if(  $_FILES['narration']['name'] ) {
            Zend_Loader::loadClass( "Upload" , DIR_LIBRARY . "/Upload/" );
            $upload 	= new Upload( $_FILES['narration'] );

            $user = new Zend_Session_Namespace( 'user' );
            if ( $upload->processed ) {
                if( !$upload->file_src_size ) {
                    $this->_helper->_flashMessenger->addMessage( $this->view->translate( "file size exceeded" ) );
                    return false;
                }

                if( !( $upload->file_src_mime == "audio/mpeg" ) ) {
                    $this->_helper->_flashMessenger->addMessage( $this->view->translate( "extension invalid" ) );
                    return false;
                }

                $upload->process( PUBLIC_HTML . "upload/" . File::FOLDER_NARRATION );

                if( !$upload->processed ) {
                    $this->_helper->_flashMessenger->addMessage( $this->view->translate( "error upload" ) );
                    return false;
                }

                $upload->clean();

                $data['title'] 	   = substr( $_FILES['narration']['name'] , 0 , strrpos( $_FILES['narration']['name']  , "." ) );
                $data['location']  = $upload->file_dst_name;
                $data['person_id'] = $user->person_id;
                $data['type']	  = Folder::TYPE_FILE_NARRATION;

                $content = Zend_Filter::filterStatic( $_POST['id'] , 'Int' );

                if( $content ) {
                    $row = $this->Narration->fetchRow( array( "content_id = ?" => $content ) );
                    $this->Narration->delete( $content , 'content_id' );

                    if( $row )
                        $this->File->delete( $row->file_id );
                }

                $id = $this->File->save( $data );

                $dataNarration = array( "file_id" => $id , "content_id" => $content );

                $inputNarration = new Zend_Filter_Input( $this->Narration->filters , $this->Narration->validators , $dataNarration );
                $inputNarration->addFilterPrefixPath( "Zend_Filter_" , "Preceptor/Filter" );
                $inputNarration->addValidatorPrefixPath( "Preceptor_Validate_" , "Preceptor/Validate" );

                if( $inputNarration->isValid() ) {
                    try {
                        $this->Narration->save( $inputNarration->toArray() );
                    }catch( Exception $e ) {

                    }
                }
                else
                    return false;
            }
            return true;
        }
        return true;
    }

    /**
     * delete narration content
     *
     * @access public
     * @return void
     * @final
     */
    public function deletenarrationAction() {
        $file    = Zend_Filter::filterStatic( $this->_getParam( 'file' ) ,  'Int' );
        $content = Zend_Filter::filterStatic( $this->_getParam( 'content' ) ,  'Int' );

        $this->Narration->delete( $file , 'file_id' );
        $this->File->delete( $file );

        $this->_redirect( "/composer/composer/index/id/{$content}" );
    }
}
