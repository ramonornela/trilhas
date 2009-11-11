<?php
class Rss_RssController extends Application_Controller_Abstract
{
    protected $_model = "Rss_Model_Rss";

	public function indexAction()
	{
		$rss = new Rss_Model_Rss();

        $this->view->rs   = $rss->fetchRelation( NULL , "channel" );
	}

	public function viewAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , 'int' );

        $rss = new Rss_Model_Rss();
        
		$view = $rss->find( $id )->current();

		try
		{
			$feed = Zend_Feed::import( $view->url );
		}
		catch( Exception $e ){
            $this->_helper->_flashMessenger->addMessage( $this->view->translate( "error rss" ) );
		}

		if( isset( $feed ) )
		{
			foreach ( $feed as $item )
			{
			    $channel['items'][] = array(
			        'title'       => $item->title(),
			        'link'        => $item->link(),
			        'description' => $item->description()
			    );
			}
		}

        if( isset( $channel ) ){
    		$this->view->channel = $channel;
        }
	}

    public function inputAction()
    {
        $rss = new Rss_Model_Rss();
        $this->view->data = $rss->createRow();

        parent::inputAction();
    }
}

