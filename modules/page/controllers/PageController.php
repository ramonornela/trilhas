<?php
class Page_PageController extends Controller 
{
	private $objUpload;
	//public $uses = array( "File" , "User" );
	
	public function indexAction()
	{
		$this->view->help = "Aqui você acessa seus dados pessoais, podendo atualizá-los e modificá-los, se necessário. Também pode visualizar os perfis de professores e colegas e verificar as mensagens enviadas e recebidas. ";

		$filter = new Zend_Filter();

		$this->view->files = $this->File->fetchRelation();
		
		$id = $filter->get( $this->_getParam( "id" ) , 'int' );
		$id = ( $id )?( $id ):( $user->id );

		$this->view->sessionUser =  $user->id;

		$this->view->user 	 = $this->User->showByUser( $id );
		$this->view->teacher = $this->User->showByType( $id , 2 );
		$this->view->friend  = $this->User->showByType( $id , 1 );
		
		//$count = $this->Message->fetchAll( array( "usuario_id_destinatario = ?" => $id ) );
		
		//$this->view->countMessage = count( $count );
		
		$this->render();
	}
	
	public function inputAction()
	{
		$id = Filter::get( $this->_getParam( "id" ) , 'int' );
		
		$user = $this->User->find( $id );
		$this->view->type = $this->Session->user->type;
		$this->view->edit = $user->current();
		
		$this->render();
	}
	
	public function keepAction()
	{
		if( $_FILES['arquivo'] )
		{
			Zend_Loader::loadClass( "Upload" , DIR_LIBRARY . "/Upload/" );
			$Upload = new Upload( $_FILES['arquivo'] );
				
			if ( $Upload->processed )
			{
				if( ( ! $Upload->uploaded ) || $mimeType  = !( $Upload->file_src_mime == "image/jpeg" || $Upload->file_src_mime == "image/pjpeg" ||  $Upload->file_src_mime == "image/png"  || $Upload->file_src_mime == "image/x-png" || $Upload->file_src_mime == "image/gif"  )  )
				{
					$this->view->edit = (object) $_POST;
					$this->view->messages = $mimeType ? ( array( $this->view->translate( "extension invalid" ) ) ) : ( array( $this->view->translate( "file size exceeded" )));
					
					$this->render( "input" );
					return false;
				}
				
				$this->generationThumb( $Upload );
				
				$data['arquivo_titulo'] = "Imagem do usu�rio";
				$data['arquivo_caminho'] = $Upload->file_dst_name;
				$data['arquivo_tipo']    = 'I';
				
				$arquivo_id = $this->File->save( $data , false );
				
				$Upload->clean();
			}
		}
		
		$id = $this->User->save( $arquivo_id );
		
		$this->_redirect( "/page/page" );
	}

	public function generationThumb( $upload )
	{
		$upload->image_resize = true;
        $upload->image_ratio_y = true;
		$upload->image_x = '100px';
		$upload->file_name_body_add = 'thumb_small_';
		$upload->process( PUBLIC_HTML . "/upload/" );
		
		$upload->image_resize = true;
		$upload->image_ratio_y = true;
		$upload->image_x = '200px';
		$upload->file_name_body_add = 'thumb_medium_';
		$upload->process( PUBLIC_HTML . "/upload/" );
		
		$upload->image_resize = true;
		$upload->image_ratio_y = true;
		$upload->image_x = '300px';
		$upload->file_name_body_add = '_';
		$upload->process( PUBLIC_HTML . "/upload/"  );
	}
}

