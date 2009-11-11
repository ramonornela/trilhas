<?php
class Preceptor_View_Helper_FormRelation extends Zend_View_Helper_Abstract
{
	public function formRelation( $id = null , $options = null )
	{
        $xhtml = "<div id='div-relation-{$this->view->model}' style='display: none;'>";
        $xhtml .= $this->view->action( 'index' , 'relation' , null , array( 'model' => $this->view->model , 'id' => $id ) );

		$xhtml .= $this->view->element()->formButton( "save" , $this->view->translate("save") , array( "printLabel" => false ) )
                             ->endForm();

        $xhtml .= "</div>";

        $xhtml .= $this->_script( $options );

        return $xhtml;
	}

    protected function _script( $options = null )
    {
		$upload = false;
        $xhtml = "<script>";

        if ( isset($options['isUpload']) ){
            $upload =  ", isUpload:true";
        }
        /**
         * @return Ex: GroupedList_link_linkall.selectAll( 'linkchecked' );
         */
        if( isset($options['groupedList']) ){
            $groupedList = "GroupedList_"
                         ."{$this->view->controller}_{$this->view->controller}all."
                         ."selectAll( '{$this->view->controller}checked' );";
        }
		
        if ( isset($options['url']) ){
			$url = $options['url'];
		}else{
			$url = $this->view->module."/".$this->view->controller."/save";
		}
		
        $xhtml .= "$('#{$this->view->model}-save').click(function(){"
                    ."var json = relation_{$this->view->model}.save();"
                    ."document.getElementById( 'json_relation_{$this->view->model}' ).value = json;";

        $xhtml .= isset( $groupedList )? $groupedList : null ;

        $xhtml .=  "new Preceptor.util.AjaxUpdate( '{$this->view->module}-{$this->view->controller}' , '{$this->view->url}/{$url}', {formId:'{$this->view->model}' $upload } );"
               .   "});";

        $xhtml .= strtolower( $this->view->model ).".success = function(){"
                    ."$('#div-form-{$this->view->model}').hide();"
                    ."$('#div-relation-{$this->view->model}').show();"
                 ."}";
           
        $xhtml .= "</script>";

        return $xhtml;
    }
}
?>