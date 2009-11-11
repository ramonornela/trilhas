<?php
class Preceptor_View_Helper_ShowForm extends Zend_View_Helper_Abstract
{
	public function showForm( $form , $role = false , $url = null , $person = null , $guest = false , $roles = array() , $userRoles = array() , $status = array() )
	{
		require_once(  DIR_LIBRARY . "/Preceptor/View/Helper/IntercalClass.php" );
		$changeClass = new Preceptor_View_Helper_IntercalClass();
		
		$xhtml .= "<form action='#' id='formId_{$form->current()->form_id}'>\n";
		$xhtml .= "<script> try{ validate{$form->current()->form_id} = new Preceptor.util.Validate(); }catch(e){} </script>\n";

        if( ! $guest ){
            $xhtml .= "<label for='role_id'>" . $this->view->translate("profile") . ": </label>"
		       	 .$this->view->formCheckboxList( "role_id" , null , $roles , $userRoles );
        }

		$xhtml .= "<label for='name'>" . $this->view->translate("name") . ": </label>"
		       	 .$this->view->formText( "name" , $person->name );

		$xhtml .= "<label for='email'>" . $this->view->translate("email") . ":&nbsp;<span id='load_ajax_email'></span> </label>"
		       	 .$this->view->formText( "email" , $person->email );

		$xhtml .= "<script>"
						."validate{$form->current()->form_id}.addValidate( 'name'  , validate{$form->current()->form_id}.notEmpty     , " . Zend_Json::encode( array ( 'notEmpty' => $this->view->translate("required field") ) ) . " , null , 'notEmpty' );\n"
						."validate{$form->current()->form_id}.addValidate( 'name'  , validate{$form->current()->form_id}.stringLength , " . Zend_Json::encode( array ( 'stringLength' => $this->view->translate("maxlength") ) ) . " , " . Zend_Json::encode( array ( 'stringLength' , 0 , 255 ) )  . ", 'stringLength' );\n"
						."validate{$form->current()->form_id}.addValidate( 'email' , validate{$form->current()->form_id}.notEmpty     , " . Zend_Json::encode( array ( 'notEmpty' => $this->view->translate("required field") ) ) . " , null , 'notEmpty' );\n"
						."validate{$form->current()->form_id}.addValidate( 'email' , validate{$form->current()->form_id}.stringLength , " . Zend_Json::encode( array ( 'stringLength' => $this->view->translate("maxlength") ) ) . " , " . Zend_Json::encode( array ( 'stringLength' , 0 , 255 ) ) . " , 'stringLength' );\n"
						."validate{$form->current()->form_id}.addValidate( 'email' , validate{$form->current()->form_id}.emailAddress , " . Zend_Json::encode( array ( "emailAddress" => $this->view->translate("invalid email") ) ) . " , null , 'emailAddress' );\n"
				 ."</script>";
		$xhtml .= "<br /><br />";
        
		foreach ( $form as $formGroup )
		{
			if ( $formGroup->form_group_id != -1 )
			{
				$xhtml .= "<fieldset>\n";
				$xhtml .= "<legend id='group_{$formGroup->form_group_id}'>$formGroup->name</legend>\n<br/>";
			}
			
			foreach ( $formGroup->findManyToManyRowset( 'FormField' , 'FormGroupField' , null , null , $formGroup->getTable()->select()->where( 'form_id =?' , $formGroup->form_id )->order('position')->order('form_field_id') ) as $key => $formField )
			{
				if ( $role )
				{
					$intercalClass = $changeClass->intercalClass( array( "light_gray" , "gray" ) , $key );
					$style = "padding: 4px;";
				}	
				
				$xhtml .= "<div class='".$intercalClass."' style='{$style}'>";
				
				if ( $role )
				{	
					$xhtml .= "<div style='float:right;'>"
				 	   			."<a href='#this' onclick=\"new Preceptor.util.AjaxUpdate( 'list_field' , '{$url}/form/formfield/input/id/$formGroup->form_id/fieldId/{$formField->id}' , { evalScripts:true } );return false;\" >Editar</a>&nbsp;"
				       			."<a href='#this' onclick=\"if( !confirm( 'Deseja realmente excluir?' ) ) { return false; } ;new Preceptor.util.AjaxUpdate( 'form' , '". $url . "/form/formfield/delete/id/$formField->id/formId/{$formGroup->form_id}' );return false;\">Excluir</a>"
						 	."</div>";
				}
				
				$xhtml .= "<label style='display:inline;'>{$formField->label}</label>&nbsp;<span id='load_ajax_id_{$formField->id}'></span><br />";
				if ( ( $formField->type == 'text' ) || ( $formField->type == 'password' ) ) 
				{
					if( $formField->findDependentRowset( 'FormFieldValue' )->count() )
					{
						foreach ( $formField->findDependentRowset( 'FormFieldValue' ) as $formValue )
						{
							$xhtml .= "<input type='{$formField->type}' id='id_{$formField->id}' value='{$formValue->value}' name='{$formField->id}' />";	
						}
					}
					else
					{
						if ( $person )
						{
							$value= "";
							foreach ( $formField->findDependentRowset( 'FormFieldData' , null , $formField->getTable()->select()->where( 'person_id =?' , $person->id ) ) as $fieldData )
								$value = $fieldData->value;
							
						}	
						$xhtml .= "<input type='{$formField->type}' id='id_{$formField->id}' value='{$value}' name='{$formField->id}' />";
					}
				}
				
				if ( $formField->type ==  'textarea' )
				{
					if( $formField->findDependentRowset( 'FormFieldValue' )->count() )
					{
						foreach ( $formField->findDependentRowset( 'FormFieldValue' ) as $formValue )
						{
							$xhtml .= "<textarea id='id_{$formField->id}' name='{$formField->id}' >$formValue->value</textarea>";	
						}
					}
					else
					{
						if ( $person )
						{
							$value= "";
							foreach ( $formField->findDependentRowset( 'FormFieldData' , null , $formField->getTable()->select()->where( 'person_id =?' , $person->id ) ) as $fieldData )
								$value = $fieldData->value;
							
						}	
						$xhtml .= "<textarea id='id_{$formField->id}' name='{$formField->id}' >". $value . "</textarea>";
					}
				}
				
				if ( $formField->type ==  'select' )
					$xhtml .= "<select id='id_{$formField->id}' name='{$formField->id}' >";
				
				foreach ( $formField->findDependentRowset( 'FormFieldValue' ) as $key => $formValue )
				{
					if ( ( $formField->type == 'radio' ) ||  ( $formField->type == 'checkbox' ) )
					{
						if ( $formField->type == 'radio' )
						{
							if ( $person )
							{
								foreach ( $formField->findDependentRowset( 'FormFieldData' , null , $formField->getTable()->select()->where( 'person_id =?' , $person->id ) ) as $fieldData )
								{
									if ( $formValue->value == $fieldData->value )
										$checked = "checked='checked'";
									else
										$checked = "";
								}
							}	
							$xhtml .= "<input type='{$formField->type}' id='id_{$formField->id}' value='{$formValue->value}' name='{$formField->id}' $checked />";
						}
						else
						{
							if ( $person )
							{
								foreach ( $formField->findDependentRowset( 'FormFieldData' , null , $formField->getTable()->select()->where( 'person_id =?' , $person->id ) ) as $fieldData )
								{
									$datas[] = $fieldData->value;
									if ( in_array( $formValue->value , $datas ) )
										$check = "checked='checked'";
									else
										$check = "";
								}
							}	
							
							$xhtml .= "<input type='{$formField->type}' id='id_{$formField->id}' value='{$formValue->value}' name='{$formField->id}[]' $check />";
						}						
						$xhtml .= "&nbsp;$formValue->value";
						$xhtml .= "<br /><br />";
					}
					
					if ( $formField->type ==  'select' )
					{
						if ( $person )
						{
							foreach ( $formField->findDependentRowset( 'FormFieldData' , null , $formField->getTable()->select()->where( 'person_id =?' , $person->id ) ) as $fieldData )
							{
								if ( $formValue->value == $fieldData->value )
									$select = "selected='selected'";
								else
									$select = "";
							}
						}	
						
						$xhtml .= "<option $select value='{$formValue->value}'>{$formValue->value}</option>";
					}
				}
				if ( $formField->type ==  'select' )
					$xhtml .= "</select>";
				
				if ( $formField->unique ==  'S' )
					echo $this->verificationField( "id_" . $formField->id , $url );
				
				
				if ( $formField->mask )
				{
					$xhtml .= "<script>";
					$xhtml .= "new Preceptor.util.Mask( 'id_{$formField->id}' , {mask:'" . $formField->mask . "'} );\n";
					//$xhtml .= "Object{$formField->id}.init( 'id_{$formField->id}' , Object{$formField->id} , '{$formField->mask}' );";
					$xhtml .= "</script>";
				} 
				
				foreach ( $formField->findManyToManyRowset( 'FormValidate' , 'FormFieldValidate' ) as $formValidate )
				{
					$xhtml .= "<script>";
					$xhtml .= "try{";
					
					if ( $formValidate->name == 'notEmpty' )
						$xhtml .= "validate{$formGroup->form_id}.addValidate( 'id_{$formField->id}' , validate{$formGroup->form_id}.{$formValidate->name} , " . Zend_Json::encode( array ( 'notEmpty' => $this->view->translate("required field") ) ) . " , null , '{$formValidate->name}' );\n";
					else
						$xhtml .= "validate{$formGroup->form_id}.addValidate( 'id_{$formField->id}' , validate{$formGroup->form_id}.{$formValidate->name} , " . Zend_Json::encode( array ( "{$formValidate->name}" => $this->view->translate("invalid field") ) ) . " , null , '{$formValidate->name}' );\n";
					
					$xhtml .= "}catch(e){}";
					$xhtml .= "</script>";
				}
				$xhtml .= "</div>";
				$xhtml .= "<br/>";
			}
			if ( $formGroup->form_group_id != -1 )
				$xhtml .= "</fieldset>";
			
			$xhtml .= "<br />";
			
			$form_id = $formGroup->form_id;
		}
		
		if( ! $guest ){
			$xhtml .= "<label for='status'>{$this->view->translate( 'status' )}:</label>"
				   .$this->view->formSelect( 'status' , $person->status , null , $status )
				   ."<br /><br />";
			
			if( $person->id )
				$xhtml .= $this->_alterkey();
        }
        
		if ( ! $role )
			$xhtml .= "<input type='submit' value='Salvar' id='formSubmit'  />";
		
		if ( $guest )
			$xhtml .= "<input type='button' value='Cancelar' onclick=\"new Preceptor.util.AjaxUpdate( 'user' , '{$url}/form/formfielddata/indexguest/ajax/true' );\" />";
		
		
		$xhtml .= "<script>";
		
		if( $guest ) $action = "saveguest";
		else $action = "save";
		
		$xhtml .= "YAHOO.util.Event.on( 'formId_{$form_id}' , 'submit' , function( ev ){
				   		if( validate{$form_id}.verify( $( 'formId_{$form_id}' ) ) )
				   		{
				   			new Preceptor.util.AjaxUpdate( 'user' , '{$url}/form/formfielddata/{$action}/' , { formId:'formId_$form_id' } );
						}
						YAHOO.util.Event.stopEvent( ev );
				  	});"; 
		$xhtml .= "</script>";	
		$xhtml .= "</form>";
		
		echo $this->verificationField( "email" , $url );
		
		return $xhtml;
	}
	
	public function verificationField( $id , $url )
	{
		$xhtml .= "<script>"
						."YAHOO.util.Event.on( '{$id}' , 'blur' , function( ev ) {"
							."if( YAHOO.util.Dom.get( '{$id}' ).value )"
							."{"
								."carregando = $( 'load_ajax_{$id}' );"
								."carregando.innerHTML = '<strong>{$this->view->translate('looking')}</strong>';"
								."YAHOO.util.Dom.setStyle( carregando , 'backgroundColor' , '#FFF1A8' );"
								."YAHOO.util.Dom.setStyle( carregando , 'color' , 'black' );"
								."YAHOO.util.Dom.setStyle( carregando , 'padding' , '2px' );"
								."if ( $('formSubmit') ){"
								."$('formSubmit').disabled=true;"
								."$('formSubmit').style.background = '\#ddd';"
								."}"
							  	."var el = $( '{$id}' );"
								."YAHOO.util.Connect.asyncRequest( 'GET' , '{$url}/person/verifydata/data/' + el.value ," 
								."{" 
									."success: function( o ){"
										."carregando.innerHTML  = '';" 
										."carregando.removeAttribute( 'style' );"
										."if ( $('formSubmit') ){"
										."$('formSubmit').disabled=false;"
										."$('formSubmit').style.background = '\#3676A6';" 
										."}"
										."if( o.responseText )"
										."{"
											."el.value = '';"
											."el.focus();"
											."var json = eval( o.responseText );"
											."YAHOO.util.Dom.setStyle( carregando , 'color' , 'red' );"
											."carregando.innerHTML = '".$this->view->translate('registerd email')."';"
										."}"
									."}"
								."} );"
								."YAHOO.util.Event.stopEvent( ev );"
							."}"
						  ."} );"
				 ."</script>";
				 
		return $xhtml;
	}
	
	protected function _alterkey()
	{
		$xhtml .= "<a onclick=\"showHidden( 'alterkey' );\" href='#this'>{$this->view->translate( 'change password' )}</a>"; 
		
		$xhtml .= "<div id='alterkey' style='display:none;'>"
			   		."<br /><br />"
					."<label for='password' style='display: inline'>{$this->view->translate( 'password' )}:</label>&nbsp;&nbsp;&nbsp;"
					."<label  style='margin-left: 100px; display: inline;' for='cpassword'>{$this->view->translate('reply password')}:</label><br />"
					.$this->view->formPassword( "password"  , null , array( style => "width: 100px;" ) )
					.$this->view->formPassword( "cpassword" , null , array( style => "width: 100px; margin-left: 50px;" ) )
				 ."</div>";

		$xhtml .= "<br /><br />"; 
		
		return $xhtml;
	}
}
