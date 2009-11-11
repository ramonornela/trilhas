<?php
class Log_ComunicationController extends Controller
{
	public function init()
	{
		parent::init();

		$this->view->help = "Este relatório mostra o número de acessos do aluno em cada modalidade de comunicação (chat, fórum ou mensagens). Clicando sobre as \"fatias da pizza\" você conseguirá visualizar esses grupos em destaque.";

		$this->objService = new ComunicationService();
	}

	public function indexAction()
	{
		$this->render( "index" );
	}

	public function viewAction()
	{
		$this->objService->option = $this->_getParam( "comunication" );
		$this->view->result = $this->objService->showByReportComunication();
		$this->view->option = $this->_getParam( "comunication" );
		$this->render( "view" , "ajax" );
	}

	public function reportAction()
	{
		$xml 	= new SimpleXMLElement( "<pie></pie>" );
		$report = new Zend_Session_Namespace( "Report" );

		foreach( $report->data as $key => $val )
		{
			$slice = $xml->addChild( 'slice' , $val['vl'] );
			$slice->addAttribute( 'title' , utf8_encode( $val['turma_nome'] ) );
			unset( $report->data[$key] );
		}

		header( 'Content-type: application/xml' );
		echo $xml->asXML();
		exit;
	}
}

