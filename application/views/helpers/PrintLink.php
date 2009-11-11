<?php
class Preceptor_View_Helper_PrintLink
{
	public function printLink( $value )
	{
		$doc = new DOMDocument();
		$doc->formatOutput = true;
		
		foreach( $value as $val )
		{
			$str = $val['value']['title'] . "<br /><br />";
			
			if( !$val['child'] )	
				$str .= $val['value']['ds'];
			
			/**
			 * @internal bug internal DOMDocument php requires conversion from UTF-8 to ISO-8859-1 
			 */	
			$str = iconv( "UTF-8" , "ISO-8859-1" , $str );
			@$doc->loadHTML( $str );
			
			$linkNodes = $doc->getElementsByTagName('a');
			
	        foreach ( $linkNodes as $linkNode ) 
	        {
	            if ( $linkNode->hasAttribute('href') )
	            { 
	            	$span = new DOMElement('span');
	            	$span->nodeValue = $linkNode->nodeValue . " (". $linkNode->getAttribute('href') . ")";
	            	
	            	$linkNode->nodeValue = null;
	                $linkNode->appendChild( $span );
	                $span->setAttributeNode( new DOMAttr( "style" , "text-decoration: underline;" ) ); 
	            }
	        }
	        
	        $xhtml .= $doc->saveHTML();

	        if( $val['child'] )
				$xhtml .= $this->printLink( $val['child'] );
		}
			        
		return $xhtml;
	}
}