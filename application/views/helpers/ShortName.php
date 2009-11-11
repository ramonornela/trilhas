<?php
class Preceptor_View_Helper_ShortName
{	
	public function shortName( $name )
	{
        $names = split( " " , $name );

        if( !isset( $names[1] ) ){
            $names[1] = "";
        }
		return $names[0] . " " . $names[1];
	}
}