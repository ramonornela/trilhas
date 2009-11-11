<?php
class Preceptor_View_Helper_ShowEvaluationReply extends Zend_View_Helper_Abstract
{
	public function showEvaluationReply( $evaluation , $person = null , $student = false , $submit = false )
	{
		$value_alternative = "";
		$note = "";
        $xhtml = "";
        
		$evaluation_id = $evaluation->current()->evaluation_id;

		$xhtml .= "<form id='formCorrect_{$this->view->suffix}' action='#'>";
		$xhtml .= "<script> validateCorrect = new Preceptor.util.Validate(); </script>\n";
		
		foreach( $evaluation as $key => $formField ){
			$xhtml .= "<div class='" . $this->view->changeClass( $key , array( "light_gray" , "gray" ) ) . " padding'>";
			$number = $key + 1;
			$feedbacks = array();
			$input = "";
			$mark = "";
			
			$xhtml .= "<div class='right'>" . $this->view->translate('question'). ": " . str_replace( '.' , ',' , $formField->note ) . "</div>";
			$xhtml .= "<b>" . $number . ") " . $formField->label . "</b>";	
			
			@$value_alternative = ( $formField->note / $formField->findDependentRowset( 'Evaluation_Model_EvaluationValue' )->count() );
			
			$count = 0;
			$count_check = 0;
			$class   = "";
			$note_keyword_radio = "";
			$i = array();
			$justifys = "";
			
			foreach ( $formField->findDependentRowset( 'Evaluation_Model_EvaluationValue' ) as $key1 => $fieldValue ){
				if ( ( $formField->type == "text" ) || ( $formField->type == "keyword" ) )
					$letter = "";
				else	
					$letter = chr( $key1 + 97 ) . ") ";
				
				if ( ( $formField->type == "truefalse" ) || ( $formField->type == "association" ) )
					$mark = "(  )";
				
				
				$checkData = $fieldValue->findDependentRowset( 'Evaluation_Model_EvaluationReply' , null , $fieldValue->getTable()->select()->where( 'person_id =?' , $person )->where( 'evaluation_id =?' , $formField->evaluation_id ) )->current();
				
				if ( ( $formField->type == "radio" ) || ( $formField->type == "checkbox" ) )
				{
					if ( $checkData )
						$checked = "checked='checked'";
					else 
						$checked = "";
					
					if ( $formField->type == "radio" ) 
					{
						$input = "<input type='radio' $checked >";
						
						if ( ( $fieldValue->status == "V" ) )
						{
							$class = "correct";
							if ( $checkData )
								$note_keyword_radio = $formField->note;
						}
						else
						{
							$class = "wrong";
						}
					}
					
					if ( $formField->type == "checkbox" ) 
					{
						$input = "<input type='checkbox' $checked >";
						if ( ( ( $fieldValue->status == "V" ) && ( $checkData  ) ) || ( ( $fieldValue->status == " " ) && ( ! $checkData ) ) )
						{
							$class = "correct";
							$count++;
						}
						else
						{
							$class = "wrong";
						}
					}
					
					if ( $fieldValue->status == "V" )
						$feedbacks[$letter] = $fieldValue->value;
				}	
				if ( ( $formField->type == "truefalse" ) || ( $formField->type == "association" ) )
				{
					if ( $fieldValue->status == $checkData->value )
					{
						$class = "correct";
						$count++;
					}
					else
						$class = "wrong";
					
					$mark = "(" . $checkData->value .") ";
				}
				
				if ( $formField->type == "text" )
				{
					if ( strtoupper( $fieldValue->value ) == strtoupper( $checkData->value ) )
					{
						$class = "correct";
						$count++;
					}
					else
					{
						$class = "wrong";
					}
					if ( $checkData->value )
						$value = $checkData->value;
					else
						$value = "<b>" . $this->view->translate("not completed") . "</b>";
				}
				
				if ( $formField->type == "keyword" )
				{
					$status = split( '( )|(,)' , $fieldValue->status );
					
					$ereg = "";
					
					foreach ( $status as $statu )
					{
						$ereg = @ereg( $statu , $checkData->value );
						
						if ( $ereg )
							$i[] = 1;
					}
					
					if ( count( $i ) == count( $status) )
					{
						$class = "correct";
						$note_keyword_radio = $formField->note;
					}
					else
						$class = "wrong";
					
					if( $checkData->value )
						$value = $checkData->value;
					else
						$value = "<b>" . $this->view->translate("not completed") . "</b>";
				}
				
				if ( ( $formField->type == "truefalse" ) || ( $formField->type == "association" ) )
					$feedbacks[$letter] = $fieldValue->status;
				
				if ( ( $formField->type != "text" ) && ( $formField->type != "keyword" ) )
					$xhtml .= "<p class='{$class}'>" . $letter  . $input . $mark .  $fieldValue->value . "</p>";
				else
				{
					$feedbacks[$letter] = $fieldValue->value;
					$xhtml .= "<p class='{$class}'>" . $value . "</p>";
				}
				
				if ( $fieldValue->justify )
					$justifys[$formField->id][$fieldValue->id] = $letter . $fieldValue->justify;
			}

            $note_textarea = "";
			if ( $formField->type == "textarea" )
			{
				$reply = $formField->findDependentRowset( 'Evaluation_Model_EvaluationReply' , null , $formField->getTable()->select()->where( 'person_id =?' , $person )->where( 'evaluation_id =?' , $formField->evaluation_id ) )->current();
				$xhtml .= $this->_textarea( $reply , $person , $student );
                
				$note_textarea = $reply->findDependentRowset( 'Evaluation_Model_EvaluationReplyNote' , null , $reply->getTable()->select()->where( "person_id =?" , $person ) )->current()->note;
			}	
			
			if ( ( $justifys ) && ( $student ) )
			{
				$xhtml .= "<fieldset>";
				
				if ( $formField->type != "" )
					$xhtml .= "<legend>" . $this->view->translate("justifys ofs alternatives") . "</legend><br />";
				else
					$xhtml .= "<legend>" . $this->view->translate("justify") . "</legend><br />";
					
				foreach ( $justifys[$formField->id] as $justify ) {
					$xhtml .= "<p style='border:1px solid #3676A6;padding:6px;background:#fff;'>" . $justify . "</p>";
				}
				
				$xhtml .= "<br /></fieldset>";
			}
			
			if ( $note_keyword_radio )
				$note = $note_keyword_radio;
			elseif( $note_textarea )
				$note = $note_textarea;
			else
				$note = $value_alternative * $count;
			
			if ( $student )
			{
				$xhtml .= $this->view->translate("note") . ": " . number_format( $note  , 1 , "," , "." );
				$xhtml .= "<div>";
				$xhtml .= $this->view->translate("feedback") . ": ";
				
				foreach ( $feedbacks as $key2 => $feedback )
					$xhtml .= "<b>" .$key2 . " </b>" . $feedback . " &nbsp; ";
			
				$xhtml .= "</div>";
			}	
			
			$xhtml .= "</div>";

			$note_total[$formField->id] = $note;
		}
		
		
		$sum_note = array_sum( $note_total );
		$average    = $sum_note / count( $note_total );
		
		if ( $student )
		{
			$xhtml.= "<br />";
			$xhtml.= "<b>" . $this->view->translate("note total"). ": </b>" . number_format( $sum_note , 2 , "," , "." ) . "<br />";
			$xhtml.= "<b>" . $this->view->translate("average"). ": </b>" . number_format( $average , 2 , "," , "." );  
		}
		
		if ( ( $student ) || ( $submit ) )
			$xhtml.= $this->_submitNote( $average , $evaluation_id , $person );
		
		if ( ! $student )
			$xhtml.= $this->_submitCorrectNote();
		
		$xhtml.= "</form>";
		return $xhtml;
	}
	
	protected function _submitNote( $average , $evaluation_id , $person )
	{
		$xhtml  = "<script>";
		$xhtml .= "new Preceptor.util.AjaxUpdate( 't' , '{$this->view->url}/evaluation/reply/savenote/evaluation_id/{$evaluation_id}/note/{$average}/person_id/$person' )";
		$xhtml .= "</script>";
		
		return $xhtml;
	}
	
	protected function _submitCorrectNote()
	{
		$xhtml  = "<input type='submit' value='{$this->view->translate("save")}' />";
		
		$xhtml .= "<script>";
		
		$xhtml .= "YAHOO.util.Event.on( 'formCorrect_{$this->view->suffix}' , 'submit' , function( ev ){
						
						new Preceptor.util.AjaxUpdate( 'evaluation-evaluation' , '{$this->view->url}/evaluation/correct/savenote/' , { formId:'formCorrect_{$this->view->suffix}' } );
						
						YAHOO.util.Event.stopEvent( ev );
				  	});"; 
		$xhtml .= "</script>";	
		
		
		return $xhtml;
	}
	
	protected function _textarea( $reply , $person , $student )
	{
        $note = $reply->findDependentRowset( 'Evaluation_Model_EvaluationReplyNote' , null , $reply->getTable()->select()->where( "person_id =?" , $person ) )->current();
		$xhtml   = "<br />";
		$xhtml  .= "<br /><fieldset>";
		$xhtml  .= "<legend> <b>" . $this->view->translate("answer") . "</b> </legend>";
		
		if ( $reply->value )
			$xhtml  .= "<p>" . $reply->value . "</p>";
		else 
			$xhtml  .= "<p class='wrong'>". $this->view->translate("not completed")."</p>";
		
		$xhtml  .= "</fieldset>";
		
		if ( ! $student ){
            $value = isset( $note->note )? $note->note : 0;
			$xhtml  .= "Atribuir nota: <input type='text' name='note[$reply->id]' id='id_{$reply->id}' value='". number_format( $value , 2 , "," , "." ) . "'/>";
			$xhtml  .= "<script> validateCorrect.addValidate( 'id_{$reply->id}'  , validateCorrect.notEmpty ," . Zend_Json::encode( array ( 'notEmpty' => $this->view->translate("required field") ) ) . " , null , 'notEmpty' )</script>";
		}
		return $xhtml;
	}
}