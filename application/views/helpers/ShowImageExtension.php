<?php

class Preceptor_View_Helper_ShowImageExtension extends Zend_View_Helper_Abstract
{
	const EXTENSIONS_DEFAULT = "pdf , doc , rtf , ppt , txt , xls , jpg , gif , htm , exe , avi , bmp, flv, mp3, swf";

	public function getExtension( $extensions )
	{
		if( is_string( $extensions ))
			$aExtension = explode( "," ,  $extensions ); 
		else
			$aExtension = $extensions; 
		return $aExtension;
	}
	
	public function showImageExtension( $location , $extensions = self::EXTENSIONS_DEFAULT  , $addNameBody = ''  )
	{
		$xhtml = null;
		$image = new Xend_View_Helper_Image();
		$image->setView( $this->view );
		
		$location = explode( "." , $location );
		$extensions = $this->getExtension($extensions);
		foreach ( $extensions as $key => $extension ) 
		{
			if ( $location[count($location) - 1] == trim( $extension ) )
				$xhtml = $image->image('icons/'.$location[count($location) - 1].$addNameBody.'.gif');
			
			if( ! $xhtml )
				$xhtml = $image->image('icons/nenhum.gif');
		}
		return $xhtml;
	}
}
?>