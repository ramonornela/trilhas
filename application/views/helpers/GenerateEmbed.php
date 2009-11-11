<?php
class Preceptor_View_Helper_GenerateEmbed extends Zend_View_Helper_Abstract
{
	const EXTENSION_IMG = 'png,gif,jpg,jpeg,jpe';
	const EXTENSION_AUDIO_VIDEO = 'mp3,wav,swf,mpeg,mpe,mpg,mp2,mpa,mpv2,mov,qt,avi,movie,flv,wma';
	const DIR = 'upload/composer/';
	const URI_DOWNLOAD = 'file/file/download/file/';
	
	public function generateEmbed( $object , $aInfo )
	{
		$divisionObject = explode( "." , $object );
		$ext  = strtolower( $divisionObject[count( $divisionObject )-1] ); 
		
		$xhtml = "<div>";
		$xhtml .= $aInfo['title']."<br />";
		if( in_array( $ext , explode( ","  , self::EXTENSION_IMG ) ) )
			$xhtml .= "<img src='".$this->view->url."/file/file/download/read/true/file/".base64_encode($object)."' alt='{$aInfo['title']}' />";
		elseif( in_array( $ext , explode( ","  , self::EXTENSION_AUDIO_VIDEO ) ) )
		{
			if( $ext == "swf" )
			{
				$xhtml .= "<object width='{$aInfo['width']}' height='{$aInfo['height']}' >".
								"<param name='wmode' value='transparent'></param>".
						   		"<param name='movie' value='".$this->view->url.self::DIR.$object."'></param>" .
						 		"<embed src='".$this->view->url.self::DIR.$object."' ".
									" width='{$aInfo['width']}' height='{$aInfo['height']} ".
						 			" wmode='transparent' ".
						 			"type='application/x-shockwave-flash'".
						 		"></embed>".
							"</object>";
			}
			elseif( $ext == "mp3" )
			{
				$xhtml .= "<object width='220' height='30' classid='clsid:d27cdb6e-ae6d-11cf-96b8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0 '>".
			    				"<param name='movie' value='".$this->view->url."swf/player_custom/mp3player.swf?src=".$this->view->url.self::DIR.$object."' /></param>".
			   		 			"<embed src='".$this->view->url."swf/player_custom/mp3player.swf?src=".$this->view->url.self::DIR.$object."' type='application/x-shockwave-flash' width='220' height='30' />".
						 "</object>";
				$xhtml .= "<a href='".$this->view->url.self::URI_DOWNLOAD.base64_encode($object )."' target='_blank'>{$aInfo['download']}</a>";
			}
			elseif( $ext == "flv" )
			{
				$xhtml .= "<object classid='clsid:d27cdb6e-ae6d-11cf-96b8-444553540000' codebase='http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0' width='320' height='274' align='middle'>".
    								"<param name='allowScriptAccess' value='sameDomain' />".
    								"<param name='movie' value='".$this->view->url."swf/player.swf' />".
    								"<param name='FlashVars' value='flv$this->view->url=".$this->view->url.self::DIR.$object."&ControlPanelColor=0x000000&buttonColor=0xfffbf0&ControlPanelPosition=0&showControlPanel=2&ShowtimeColor=0xffffff&#98;bAutoPlay=1&bAutoRepeat=0&#98;BufferTime=10&tmeColor=0xffffff&loaColor=0x666666&GroundColor=0x000000&conNum=5&bShowtime=1&startvolume=100' />".
    								"<param name='quality' value='high' />".
    								"<param name='bgcolor' value='#000000' />".
 									"<param name='scale' value='noscale'/>".
	 								"<param name='salign' value='lt' />".	
    								"<embed src='".$this->view->url."swf/player.swf' width='320' height='274'".
    												" align='middle' quality='high'". 
    												" bgcolor='#000000' scale='noscale'" .
    												" salign='lt' FlashVars='flv$this->view->url=".$this->view->url.self::DIR.$object."&ControlPanelColor=0x000000&buttonColor=0xfffbf0&ControlPanelPosition=0&showControlPanel=2&ShowtimeColor=0xffffff&bAutoPlay=1&bAutoRepeat=0&BufferTime=10&tmeColor=0xffffff&loaColor=0x666666&GroundColor=0x000000&conNum=5&bShowtime=1&startvolume=100'".
    												" allowScriptAccess='sameDomain' type='application/x-shockwave-flash' pluginspage='http://www.macromedia.com/go/getflashplayer'". 
    								"/>". 
  							"</object>";
				$xhtml .= "<a href='".$this->view->url.self::URI_DOWNLOAD.base64_encode( $object )."' target='_blank'>{$aInfo['download']}</a>";
			}
			else
			{
				$xhtml .= "<object width='{$aInfo['width']}' height='{$aInfo['height']}'".
								"<param name='src' value='".$this->view->url.self::DIR.$object.">".
								"<param name='autoplay' value='false'>".
								"<param name='controller' value='true'>".
								"<embed src='".$this->view->url.self::DIR.$object."' width='{$aInfo['width']}' height='{$aInfo['height']}'".
										"autoplay='false' controller='true'".
								">".
								"</embed>".
							"</object>";		 
				$xhtml .= "<a href='".$this->view->url.self::URI_DOWNLOAD.base64_encode( $object )."' target='_blank'>{$aInfo['download']}</a>";
			}
		}
		else
			$xhtml .= "<a href='".$this->view->url.self::URI_DOWNLOAD.base64_encode( $object )."' target='_blank'>{$aInfo['download']}</a>";
		$xhtml .= "<p>".nl2br( $aInfo['ds'] )."</p>";	
		$xhtml .= "</div>"; 
		return $xhtml;
	}
}