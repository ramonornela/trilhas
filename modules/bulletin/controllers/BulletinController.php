<?php
class Bulletin_BulletinController extends Controller {

    public function indexAction() {
        $user = new Zend_Session_Namespace('user');
        
        if(!empty($_POST['search']) && $_POST['search'] != 'all') {
            $this->view->persons = $this->Person->fetchAllPersonByGroup( null, $_POST);
        }else {
            $this->view->persons = $this->Person->fetchAllPersonByGroup();
        }

        $this->view->rs      = $this->BulletinGroup->fetchAll( array( 'group_id =?' => $user->group_id ) , 'id' );
        
        $this->view->role    = $user->role_id;

        if(!empty($_POST['search']) || Zend_Filter::filterStatic( $this->_getParam( "tpl" ) , "HtmlEntities" )) {
            $this->render( null , 'ajax' );
        }else {
            $this->render( null , $this->getLayout() );
        }
    }

    public function inputAction() {
        $id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );

        $this->view->modules       = array(
                "" => $this->view->translate("select"),
                Bulletin::NOTEPAD => $this->view->translate("notepad"),
        );

        $this->view->modules_itens = array(
                "" => $this->view->translate("select"),
                Bulletin::EVALUATION => $this->view->translate("evaluation"),
                Bulletin::FORUM      => $this->view->translate("forum"),
                Bulletin::ACTIVITY   =>$this->view->translate("activity")
        );

        if( $id ) {
            $this->view->data = $this->BulletinGroup->find( $id )->current();
            $result = $this->Bulletin->fetchAll( array( "bulletin_group_id = ?" => $this->view->data->id ) );
        }

        $this->view->jsonRelation = Zend_Json::encode( array() );
        $add = array();
        if ( $result )
            $add = $this->add_modules( $result );


        $this->view->jsonRelation = Zend_Json::encode( $add );
        $this->view->jsonValidate = Zend_Json::encode( $this->BulletinGroup->validators );
        $this->render();
    }

    public function listAction() {
        $modules = Zend_Filter::filterStatic( $this->_getParam( 'modules' ) , 'int' );
        $module  = new Zend_Session_Namespace('module');

        switch ( $modules ) {
            case Bulletin::ACTIVITY :
                $this->view->rs = $this->toSelect( $this->Activity->fetchRelation( null , array( 'title' , 'finished' ) ) , "id" , "title" );
                break;
            case Bulletin::EVALUATION :
                $this->view->rs = $this->toSelect( $this->Evaluation->fetchRelation( null , "id DESC" ) );
                break;
            case Bulletin::FORUM :
                $this->view->rs = $this->toSelect( $this->Forum->fetchRelation( array( "forum_id IS NULL" ) , array( "status" , "created DESC" ) ) , "id" , "title" );
                break;
        }

        $this->view->module   = $modules;
        $this->view->group_id = Zend_Filter::filterStatic( $this->_getParam( 'group_id' ) , 'int' );
        $this->view->jsonRelation = Zend_Json::encode( array() );

        $this->render( null , 'ajax');
    }

    public function saveAction() {
        $user = new Zend_Session_NameSpace( 'user' );

        $input = $this->preSave();

        if( $input->isValid() ) {
            $data                  = $this->setNull( $input->toArray() );
            $data['person_id']     = $user->person_id;
            $data['discipline_id'] = $user->discipline_id;
            $data['group_id']      = $user->group_id;
            
            $id = $this->BulletinGroup->save( $data );
            if ( $id ) {
                $this->Bulletin->save( $id );
                $this->_helper->_flashMessenger->addMessage( $this->view->translate( "registered successfully" ) );
                $this->_redirect( "/bulletin/bulletin/index/" );
            }
            else
                $this->postSave( false , $input );
        }

        $this->postSave( false , $input );
    }

    public function savenoteAction() {
        $person_id   = Zend_Filter::filterStatic( $this->_getParam( "person_id" )  , "int" );
        $bulletin_id = Zend_Filter::filterStatic( $this->_getParam( "bulletin_id" ), "int" );
        $id		     = Zend_Filter::filterStatic( $this->_getParam( 'id' ) , 'int' );

        $data['id']          = $id;
        $data['person_id']   = $person_id;
        $data['bulletin_id'] = $bulletin_id;
        $data['note'] = str_replace( "." , "," , $_POST['note'] );

        $id = $this->BulletinNote->save( $data );
        echo "ok";
        exit;
    }

    public function deleteAction() {
        $id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );

        $result = $this->Bulletin->fetchAll( array( "bulletin_group_id = ?" => $id ) );

        foreach ( $result as $rs )
            $this->BulletinNote->delete( $rs->id , 'bulletin_id' );

        $this->Bulletin->delete( $id , 'bulletin_group_id' );
        $this->BulletinGroup->delete( $id );

        $this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted successfully" ) );

        $this->_redirect( "/bulletin/bulletin/" );

    }

    public function add_modules( $result ) {
        foreach( $result as $value ) {
            switch ( $value->module ) {
                case Bulletin::EVALUATION:
                    $text = $value->findParentRow( 'Evaluation' )->name;
                    break;
                case Bulletin::FORUM:
                    $text = $value->findParentRow( 'Forum' )->title;
                    break;
                case Bulletin::ACTIVITY:
                    $text = $value->findParentRow( 'Activity' )->title;
                    break;
                case Bulletin::NOTEPAD:
                    $text = $this->view->translate( 'notepad' );
                    break;
                case Bulletin::GLOSSARY:
                    $text = $this->view->translate( 'glossary' );
                    break;
            }

            if ( $value->item ) {
                $add[] = array(
                        "type" 	=> $value->module,
                        "id" 	=> $value->item,
                        "text" 	=> $text
                );
            }
            else {
                $add[] = array(
                        "type" 	=> $value->module,
                        "id" 	=> "0",
                        "text" 	=> $text
                );
            }
        }

        return $add;
    }

    public function saveStatusAction(){
        $user        = new Zend_Session_Namespace('user');
        $person_id   = Zend_Filter::filterStatic( $this->_getParam( "person_id" )  , "int" );
        $status      = Zend_Filter::filterStatic( $this->_getParam( "status" ), "int" );

        if( !empty($status) && !empty($person_id) && $user->group_id){
            $personGroupModel = new PersonGroup();
            $personGroup = $personGroupModel->fetchRow( array( 'person_id =?' => $person_id , 'group_id =?' => $user->group_id, 'role_id =?' => Role::STUDENT ) )->toArray();
            if ($status == Status::APPROVED) {
                $personGroup['certificate'] = md5( $personGroup['person_id']."-".$personGroup['group_id'] );
                $personGroup['status']      = Status::APPROVED;
            } else {
                $personGroup['certificate'] = null;
                $personGroup['status']      = Status::DISAPPROVED;
            }

            $personGroupModel->update( $personGroup, array( 'person_id =?' => $person_id , 'group_id =?' => $user->group_id, 'role_id =?' => Role::STUDENT ) );
        }
        exit;
    }
}