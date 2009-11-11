<?php
abstract class Application_Controller_Abstract extends Xend_Controller_Action
{
    public function init()
    {
        parent::init();
        $user     = new Zend_Session_Namespace('user');
        $log      = new Application_Model_Log();
        $resource = new Share_Model_Resource();

        if ($this->module && $this->controller && $this->action && $user->group_id)  {
            $result   = $resource->fetchAllByNames($this->module, $this->controller, $this->action);

            if ($result) {
                $data['resource_id'] = $result->id;
                $data['person_id']   = $user->person_id;
                $data['class_id']    = $user->group_id;

                $log->save(array('Application_Model_Log' => $data), false);
            }
        }
    }

    public function initViewAttributes()
    {
        parent::initViewAttributes();

        $this->view->help = $this->view->translate( "help text " . $this->view->module . " " . $this->view->controller );
        $this->view->theme = "default";
    }

    public function paginationAction()
    {
        $this->view->results = Zend_Filter::filterStatic( $this->_getParam( "results" ) , "Int" );
        $this->view->startIndex = Zend_Filter::filterStatic( $this->_getParam( "startIndex" ) , "Int" );

        $model = $this->_getModel();

        $this->count = $model->count();
        $lastPage = ceil( $this->count / $this->view->results );

        $this->rs = $model->fetchAll( null , "id" , $this->view->results , $this->view->startIndex )->toArray();

        if( !$this->notRedirect ) {
            $data = $this->view->dataTable( $this->rs , null , null , null , $actions = array( "view" => "view" , "input" => "input" , "delete" => "delete" ) , true );
            echo Zend_JSON::encode( array( "totalRecords" => $this->count , "records" => $data ) );
            exit;
        }
    }

    public function paginateSelect($count)
    {
        $select = array();

        for($i=0; $i < $count; $i++) {
            $select[] = $i+1;
        }

        return $select;
    }

    public function block(){}
}