<?php
class Tri_View_Helper_Ajax extends Zend_View_Helper_Abstract
{
    public function ajax($text, $url, $type = 1, $confirm = NULL)
    {
        $id = uniqid();
        if ($type == 1) {
            $xhtml = '<a id="'.$id.'" href="' . $url . '">';
            $xhtml .= $this->view->translate($text);
            $xhtml .= '</a>';
        } else {
            $xhtml = '<input id="'.$id.'" type="button" ';
            $xhtml .= ' value="' . $this->view->translate($text) . '"';
            $xhtml .= ' />';
        }

        $xhtml .= '<script type="text/javascript">';
        $xhtml .= '$("#'.$id.'").click(function(){ ';
            
        if ($confirm) {
            $xhtml .= 'if( confirm("' . $this->view->translate($confirm) . '")) {';
        }

        $xhtml .= '$(this).parents(".content").load("' . $url . '");';

        if ($confirm) {
            $xhtml .= '}';
        }

        $xhtml .= 'return false; });';
        $xhtml .= '</script>';
        
        return $xhtml;
    }

}

?>