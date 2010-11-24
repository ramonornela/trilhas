<?php
/**
 * Trilhas - Learning Management System
 * Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @category   Content
 * @package    Content_Controller
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class Content_PrintController extends Application_Controller_Abstract {
    protected $_model = false;
    /**
     * load models method init class parent
     *
     * @var array $uses
     * @access public
     */
    //public $uses = array( "Content" );

    /**
     * @access public
     * @return void
     */
    public function indexAction() {
        $content = new Content_Model_Content();
        $user = new Zend_Session_Namespace("user");

        $select = $this->mountSelect( $user->discipline_id ,
                array( "id" , "title" , "content_id" ) );

        $rs = $content->fetchAll($select)->toArray();

        $this->view->contents = $content->organize($rs);
    }

    /**
     * @access public
     * @return void
     */
    public function viewAction() {
        $content = new Content_Model_Content();
        $user = new Zend_Session_Namespace("user");
        $id = Zend_Filter::filterStatic( $this->_getParam( 'id' ) , 'Int' );

        $select = $this->mountSelect( $user->discipline_id ,
                array( "tcs.id AS self_id",
                "tcs.title AS self_title",
                "tcs.content_id AS self_content_id",
                "tcs.ds AS self_ds" , "tc.*" ),
                $id );

        $rs = $content->fetchAll( $select )->toArray();

        if( !$rs ) {
            $this->view->contents = array( array( 'value' => $content->fetchRow( array( "id = ?" => $id ))->toArray() ) );
        }else {
            $this->view->contents = $content->organizePrint( $rs , $id );
        }

        $this->_helper->layout->setLayout('clear');
    }

    /**
     * return select table content
     *
     * @param int $discipline => id discipline
     * @param mixed $columns => columns apresentation select
     * @param null|int $content => id content
     * @access private
     * @return object Zend_Db_Table_Select
     */
    private function mountSelect( $discipline , $columns , $content_id = null ) {
        $content = new Content_Model_Content();
        $select = $content->select()->setIntegrityCheck(false)
                ->from( array( "tcs" => "content" ) , $columns , 'trails' )
                ->where( "tcs.discipline_id = ?" , Zend_Filter::filterStatic( $discipline , 'Int' ) )
                ->order( array( "tcs.content_id" , "tcs.position" , "tcs.id" ) );

        if( $content_id ) {
            $select->join( array( "tc" => "trails.content" ) , "tcs.id = tc.content_id" , array() )
                    ->where( "( tcs.content_id = ?" , $content_id )
                    ->orWhere( "tcs.id = ? )" , $content_id );

        }

        return $select;
    }
}

