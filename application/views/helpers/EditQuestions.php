<?php
class Preceptor_View_Helper_EditQuestions extends Zend_View_Helper_Abstract
{
	public function EditQuestions( $fields )
	{
		return $this->_verifyTypeField( $fields );
	}
	
	protected function _verifyTypeField( $fields )
	{
		switch ( $fields->type )
		{
			case "text":        $xhtml  = $this->_returnText( $fields );break;
			case "radio":       $xhtml = $this->_returnMultiple( $fields );break;
			case "checkbox":    $xhtml = $this->_returnMultiple( $fields );break;
			case "truefalse":   $xhtml = $this->_returnAlternatives( $fields );break;
			case "association": $xhtml = $this->_returnAlternatives( $fields );break;
			case "keyword":     $xhtml = $this->_returnKeyWords( $fields );break;
		}
		
		return $xhtml;
	}
	
	protected function _returnText( $fields )
	{
		$value = $fields->findDependentRowset( 'Evaluation_Model_EvaluationValue' )->current();
		
		$xhtml = "<div id='div{$value->id}'>"
					."<label>{$this->view->translate('correct answer')}:</label>"
					."<input type='text' name='value[]' value='{$value->value}'>"
				."</div>";	

		return $xhtml;
	}
	
	protected function _returnMultiple( $fields )
	{
		if ( $fields->type == "radio" ){
			$type = "radio";
            $array_checked = "";
		}else{
			$type = "checkbox";
			$array_checked = "[]";
		}
			
		$alternative = 0;
        $xhtml       = "";
		
		foreach ( $fields->findDependentRowset( 'Evaluation_Model_EvaluationValue' ) as $key => $evaluationValue )
		{
			if ( $evaluationValue->status == "V" )
				$checked = "checked='checked'";	
			else
				$checked = "";
			
			$alternative++;
			$xhtml .= "<div id='div{$evaluationValue->id}' class='{$this->view->changeClass( $key , array( "light_gray_alternative" , "gray_alternative" ) )}'>"
						."<label id='label{$key}' >{$this->view->translate('alternative')} {$alternative}</label>"
						."<input id='{$type}{$key}' type='$type' {$checked} name='correct{$array_checked}' value='{$key}'/>"
						."<input id='input{$key}' type='text' name='value[]' class='input_alternative' value='{$evaluationValue->value}' />"
						."<label id='label{$key}' >{$this->view->translate('justify of alternative')} {$alternative}</label>"
						."<input id='justify{$key}' type='text' name='justify[]' class='input_justify' value='{$evaluationValue->justify}' /><br /><br />"
						."<a  href='#this' id='remove$key' >{$this->view->translate('remove the alternative')} {$alternative}</a>"
					."</div>";
			
			$xhtml .= $this->_remove_alternative( "div{$evaluationValue->id}" , $key );
		}	
			
		
		return $xhtml;
	}
	
	protected function _returnAlternatives( $fields )
	{
		if ( $fields->type == "truefalse" )
			$letters = array( 'V' => 'V' , 'F' => 'F' );
		else
		{
			for ( $i = 65; $i <= 90; $i++  )
				$letters[chr($i)] = chr($i);
		}	
		
		$alternative = 0;
		foreach ( $fields->findDependentRowset( 'Evaluation_Model_EvaluationValue' ) as $key => $evaluationValue )
		{
			$alternative++;
			$xhtml .= "<div id='div{$evaluationValue->id}' class='{$this->view->changeClass( $key , array( "light_gray_alternative" , "gray_alternative" ) )}'>"
						."<label id='label{$key}' >{$this->view->translate("alternative")} {$alternative}</label>"
						.$this->view->formSelect( 'correct[]' , $evaluationValue->status , array( 'id' => 'select' . $key , 'multiple' => false ) , $letters )
						."<input id='input{$key}' class='input_alternative' type='text' name='value[]' value='{$evaluationValue->value}' />"
						."<label id='labeljustify{$key}' >{$this->view->translate('justify of alternative')} {$alternative}</label>"
						."<input id='inputjustify{$key}' type='text' name='justify[]' class='input_justify' value='{$evaluationValue->justify}'/><br /><br />"
						."<a  href='#this' id='remove$key' >{$this->view->translate('remove the alternative')} {$alternative}</a>"
					."</div>";
					
			$xhtml .= $this->_remove_alternative( "div{$evaluationValue->id}" , $key );
		}	
		
		return $xhtml;
	}
	
	protected function _returnKeyWords( $fields )
	{
		$value = $fields->findDependentRowset( 'Evaluation_Model_EvaluationValue' )->current();
		
		$xhtml = "<div id='div{$value->id}' class='alternatives'>"
					."<label>{$this->view->translate('correct answer')}:</label>"
					."<textarea class='textarea_evaluation' name='value[]' >{$value->value}</textarea>"
					."<label>{$this->view->translate('key word')}:</label>"
					."<textarea class='textarea_evaluation' name='correct' >{$value->status}</textarea>"
				."</div><br/>";	

		return $xhtml;
	}
	
	protected function _remove_alternative( $div , $key )
	{
		$xhtml = "<script>"
					."YAHOO.util.Event.on( 'remove{$key}' , 'click' , function(ev){\n"
						."$('#{$div}').remove();\n"
				 	."});"
				 ."</script>";
		
		return $xhtml;
	}
}