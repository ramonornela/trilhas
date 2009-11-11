<?php
class Preceptor_View_Helper_ShowEvaluation extends Zend_View_Helper_Abstract
{
	public function showEvaluation( $question , $tutor = false , $evaluation = false )
	{
		$fields_id = array();
		$xhtml = "";

		if ( isset( $evaluation->time ) && ( !$tutor ) ){
			$xhtml .= $this->_createDivTime();
		}
		
		$xhtml .= "<div class='yui-skin-sam'>";
		
		$xhtml .= "<form action='#' id='formId_{$evaluation->id}'>\n";
		
		foreach ( $question as $key => $formField )
		{
			$intercalClass = "";
            $style = "";
            
            if ( $tutor ){
				$intercalClass = $this->view->ChangeClass( $key , array( "light_gray" , "gray" ) );
				$style = "padding: 4px;";
			}	
			
			$xhtml .= "<div class='".$intercalClass."' style='{$style}'>";
			
			if ( $tutor && ( !$formField->findDependentRowset( 'Evaluation_Model_EvaluationReply' )->count() ) ){
				$delete = null;
				$edit = "<a href='#this' onclick=\"new Preceptor.util.AjaxUpdate( 'evaluation-evaluation' , '{$this->view->url}/evaluation/question/input/id/{$formField->evaluation_id}/fieldId/{$formField->id}' , { evalScripts:true } );return false;\" >Editar</a>&nbsp";
				
				if ( $this->view->getPermission( "delete" ) ){
					$delete = "<a href='#this' onclick=\"if( !confirm( 'Deseja realmente excluir?' ) ) { return false; } ;new Preceptor.util.AjaxUpdate( 'evaluation-evaluation' , '". $this->view->url . "/evaluation/question/delete/id/$formField->id/formId/{$formField->evaluation_id}' );return false;\">Excluir</a>";
				}
				$xhtml .= "<div style='float:right;'>" . $edit . $delete . "</div>";
			}
			
			$number = $key + 1;
			$xhtml .= "<label style='display:inline;'>" . $number . ") " . $formField->label . "</label><br />";

            $select = $formField->select()->order('id ASC');
			$evaluationValue = $formField->findDependentRowset( 'Evaluation_Model_EvaluationValue' , null , $select );
			
			if ( $formField->type == 'text' ) {
				if( count($evaluationValue) && $tutor ){
					foreach ( $evaluationValue as $formValue ){
						$xhtml .= "<input type='{$formField->type}' id='id_{$formField->id}' value='{$formValue->value}' name='{$formField->id}' />";	
					}
				}else{
					foreach ( $evaluationValue as $formValue ){
						$xhtml .= "<input type='{$formField->type}' id='id_{$formField->id}' value='' name='{$formField->id}[{$formValue->id}]' />";
					}
				}
			}
			
			if ( $formField->type == 'textarea' ){
                $valueEval = isset( $evaluationValue->current()->id ) ? $evaluationValue->current()->id : null;
				$xhtml .= "<textarea id='id_{$formField->id}' name='{$formField->id}[{$valueEval}]' ></textarea>";
				
				$xhtml .= "<script>"
	    						."Editor{$formField->id} = new Preceptor.widget.Editor( 'id_{$formField->id}', { addButton:['uploadImage'] } );\n";
				$xhtml .= "</script>";
				$fields_id[] = $formField->id;
			}
			
			if ( $formField->type == 'truefalse' || $formField->type == 'association' ){
				$true_falses = array( '' , 'V' , 'F' );
				$letters = array( '' , 'a' , 'b' , 'c' , 'd' , 'e' , 'd' , 'f' , 'g' , 'h' , 'i' , 'j' , 'k' , 'l' , 'm' , 'n' , 'o' , 'p' , 'q' , 'r' , 's' , 't' , 'u' , 'v' , 'w' , 'x' , 'y' , 'z' );
			}	
			
			foreach ( $evaluationValue as $key => $formValue ){
				if ( $formField->type == 'radio' || $formField->type == 'checkbox' ){
					if ( $formField->type == 'radio' ){
						$checked = "";
						
						if ( $formValue->status == "V" && $tutor ){
							$checked = "checked='checked'";	
						}
						
						$xhtml .= "<input type='{$formField->type}' id='id_{$formField->id}' value='{$formValue->value}' name='{$formField->id}[{$formValue->id}]' $checked />";
					}else{
						$check = "";
						
						if ( $formValue->status == "V" && $tutor ){
							$check = "checked='checked'";
						}
						
						$xhtml .= "<input type='{$formField->type}' id='id_{$formField->id}' value='{$formValue->value}' name='{$formField->id}[{$formValue->id}]' $check />";
					}						
					$xhtml .= "&nbsp;{$formValue->value}";
					$xhtml .= "<br /><br />";
				}
				
				if ( $formField->type == 'keyword' ){
					if ( $tutor ){
						$value = $formValue->value;
						$xhtml .= "<label>".$this->view->translate("correct answer")."</label>";
					}
					
					$xhtml .= "<textarea id='id_{$formField->id}' name='{$formField->id}[{$formValue->id}]' style='width:700px;height:100px'>$value</textarea>";
					
					if ( $tutor ){
						$xhtml .= "<label>".$this->view->translate("key word")."</label>";
						$xhtml .= "<textarea style='width:700px;height:100px;'>$formValue->status</textarea>";
					}
				}
				
				if ( $formField->type == 'truefalse' || $formField->type == 'association' ){
					$xhtml .= "<br />";
					$xhtml .= "<select id='id_{$formField->id}' name='{$formField->id}[{$formValue->id}]' >";
					if ( $formField->type == 'association' )
					{
						foreach( $letters as $letter )
						{
							if ( ( $formValue->status == strtoupper( $letter ) ) && ( $tutor ) )
								$selected = "selected";
							else
								$selected = "";
								
							$xhtml .= "<option $selected value='". strtoupper( $letter ). "'>". strtoupper( $letter )."</option>";
						}
					}
					else
					{
						foreach( $true_falses as $true_false )
						{
							if ( ( $formValue->status == $true_false ) && ( $tutor ) )
								$selected = "selected";
							else
								$selected = "";
								
							$xhtml .= "<option $selected value='{$true_false}'>{$true_false}</option>";
						}
					}
					$xhtml .= "</select>";
					$xhtml .= " " .$formValue->value;
				}
			}
				
			$xhtml .= "</div>";
			$xhtml .= "<br/>";
		}
		
		if ( $question->count() ){
			if ( !$tutor ){
				$xhtml .= "<input type='submit' value='{$this->view->translate("reply")}' id='formSubmit' />";
			}
		}else{
			$xhtml .= '<div class="mensage-table-sorter">'.$this->view->translate("there are no records").'</div>';
		}
		
            
		$xhtml .= "<script>";
		
		$xhtml .= "YAHOO.util.Event.on( 'formId_{$evaluation->id}' , 'submit' , function( ev ){\n";
        
		if ( $fields_id ){
			foreach ( $fields_id as $field_id ) 
				$xhtml .= "Editor{$field_id}.saveHTML();\n";
		}
		
		$xhtml .= "clearTimeout( timeOutAvaliation );"
				   	."new Preceptor.util.AjaxUpdate( 'evaluation-evaluation' , '{$this->view->url}/evaluation/reply/save/' , { formId:'formId_{$evaluation->id}' } );"
					."YAHOO.util.Event.stopEvent( ev );"
			   ."});"; 
		$xhtml .= "</script>";	
		
		if ( ( $evaluation->time ) && ( !$tutor ) )
			$xhtml .= $this->_tempAvaliation( $evaluation , $fields_id );
		
		$xhtml .= "</form>";
		
		$xhtml .= "</div>";
		return $xhtml;
	}
	
	protected function _tempAvaliation( $evaluation , $fields_id )
	{
        $xhtml = "<script>"
					 ."clearTimeout( timeOutAvaliation );\n"
				   	 ."timeOutAvaliation = setTimeout( function(){\n";
						if ( $fields_id )
						{
							foreach ( $fields_id as $field_id ) 
								$xhtml .= "Editor{$field_id}.saveHTML();\n";
						}
					
		$xhtml .=	 "try { new Preceptor.util.AjaxUpdate( 'evaluation-evaluation' , '{$this->view->url}/evaluation/reply/save/' , {formId: 'formId_{$evaluation->id}'} );}catch( e ){}\n" 
					 ."} , {$evaluation->time} * 60000 );"
			   		 ."clearTimeout( timeOutAvaliation );"
			   		 ."tempRegressiveAvaliation( 'load_evaluation' , {$evaluation->time} * 60 );"
			   ."</script>";

		return $xhtml;
	}
	
	protected function _createDivTime()
	{
		$xhtml = "<script type='text/javascript'>"
					."var time = document.createElement('DIV');\n"
					."time.id = 'load_evaluation';\n"
					."YAHOO.util.Dom.setStyle( time , 'top' , document.documentElement.scrollTop + 'px' );\n"
					."YAHOO.util.Dom.setStyle( time , 'right' , 0 );\n"
					."YAHOO.util.Dom.setStyle( time , 'position' , 'absolute' );\n"
					."YAHOO.util.Dom.setStyle( time , 'backgroundColor' , '#FFF1A8' );\n"
					."YAHOO.util.Dom.setStyle( time , 'padding' , '5px' );\n"
					."YAHOO.util.Event.on( window , 'scroll' , function(){\n"
						."YAHOO.util.Dom.setStyle( time , 'top' , document.documentElement.scrollTop + 'px' );"	
					."});"
					
					."document.getElementById( 'main' ).appendChild( time );"
                    ."timeOutAvaliation = '';"
				."</script>";
		
		return $xhtml;
	}
}