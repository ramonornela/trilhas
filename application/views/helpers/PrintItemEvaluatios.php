<?php
class Preceptor_View_Helper_PrintItemEvaluatios extends Zend_View_Helper_Abstract
{
    public function printItemEvaluatios( $persons , $evaluations , $person_id , $role = null )
    {
        $xhtml = "";
        foreach( $persons as $person ) {
            $xhtml .= "<tr class='" . $this->view->changeClass( $persons->key() , array( "light_gray" , "white" ) ) . "'>";
            $xhtml .= "<td>{$person->name}</td>";

            $i = 0;
            $total = array();
            $note = 0;

            foreach( $evaluations as $evaluation ) {
                $average = array();
                $xhtml .= "<td>";
                if ( $evaluation ) {
                    $count_item = 0;
                    foreach( $evaluation as $values ) {
                        foreach( $values as $key1 => $value ) {
                            $note = 0;
                            $i++;
                            $xhtml .= "<div class='left evaluations'>";
                            $xhtml .= "<div class='evaluations_name'>";
                            $xhtml .= $this->printTitle( $value['bulletin'] , $value['evaluation'] , $person->id , $role );
                            $xhtml .= "</div>";

                            $xhtml .=  "<div id='id_{$person->id}{$key1}' class='bulletin_note'>";

                            if ( Bulletin_Model_Bulletin::EVALUATION != $value['bulletin']->module ) {
                                $bulletinNoteRs = $value['bulletin']->findDependentRowset( 'Bulletin_Model_BulletinNote' , null , $value['bulletin']->select()->where( 'person_id =?' , $person->id ) );
                                if(count($bulletinNoteRs)) {
                                    $note = $bulletinNoteRs->current()->note;
                                    $average[] = $note;
                                }
                            }else {
                                $evaluationNoteRs = $value['bulletin']->findDependentRowset( 'Evaluation_Model_EvaluationNote' , null , $value['bulletin']->select()->where( 'person_id =?' , $person->id ) );
                                if(count($evaluationNoteRs)) {
                                    $note = $evaluationNoteRs->current()->note;
                                    $average[] = $note;
                                }
                            }

                            if ($note) {
                                $xhtml .= $note;
                            }

                            $xhtml .= "</div>";
                            $xhtml .= "<script>";

                            if ( ( $this->view->getPermission( "savenote" ) ) && ( Bulletin_Model_Bulletin::EVALUATION != $value['bulletin']->module ) ) {
                                $bulletinNoteId = null;
                                $bulletinNoteRs = $value['bulletin']->findDependentRowset('Bulletin_Model_BulletinNote' , null , $value['bulletin']->select()->where( 'person_id =?' , $person->id ) );
                                if( count($bulletinNoteRs) ) {
                                    $bulletinNoteId = $bulletinNoteRs->current()->id;
                                }
                                $xhtml .= "Use.text( 'note' , 'id_{$person->id}{$key1}' , '{$this->view->url}/bulletin/bulletin/savenote/person_id/{$person->id}/bulletin_id/{$key1}/id/{$bulletinNoteId}' );";
                            }
                            $xhtml .= "</script>";
                            $xhtml .= "</div>";
                        }
                        $count_item++;
                    }
                }
                $xhtml .= "</td>";
                $xhtml .= "<td>";
                $sum_partial = array_sum($average);
                if ($sum_partial) {
                    $total[] = $pre_total = round($sum_partial/$count_item, 2);
                    $xhtml .= $pre_total;
                }
                $xhtml .= "</td>";
            }

            $xhtml .= "<td>";
            $sum    = array_sum($total);
            $xhtml .= round($sum/count($evaluations), 2);
            $xhtml .= "</td></tr>";
        }

        return $xhtml;
    }

    public function printTitle( $module , $evaliation , $person_id , $role = null ) {
        switch ( $module->module ) {
            case Bulletin_Model_Bulletin::EVALUATION:
                if ( Share_Model_Role::STUDENT == $role ) {
                    $controller = "reply";
                }else {
                    $controller = "correct";
                }
                return "<a id='tooltip_id_{$module->id}_{$person_id}' title='{$evaliation->name}' onclick=\"layout.addPanel( 'evaluation-evaluation' , '{$this->view->url}/evaluation/{$controller}/view/evaluation_id/{$evaliation->id}/person_id/$person_id' )\" href='#this'>". $this->view->translate('assessments') ."</a>";
            case Bulletin_Model_Bulletin::FORUM:
                return "<a id='tooltip_id_{$module->id}_{$person_id}' title='{$evaliation->title}' onclick=\"layout.addPanel( 'forum-forum' , '{$this->view->url}/forum/forum/view/id/{$evaliation->id}' )\" href='#this'>". $this->view->translate('forum') ."</a>";
            case Bulletin_Model_Bulletin::ACTIVITY:
                if ( $evaliation->composition_type == Activity_Model_Activity::GROUPED ) {
                    $group_id = $evaliation->findDependentRowset('Activity_Model_ActivityGroupPerson' , null , $evaliation->select()->where( "person_id =?" , $person_id ) )->current()->group_id;
                    if ( $group_id ) {
                        return "<a id='tooltip_id_{$module->id}_{$person_id}' title='{$evaliation->title}' onclick=\"layout.addPanel( 'activity-activity' , '{$this->view->url}/activity/text/view/id/{$evaliation->id}/person_id/{$person_id}/group_id/{$group_id}' )\" href='#this'>". $this->view->translate('activ') ."</a>";
                    }
                }else {
                    return "<a id='tooltip_id_{$module->id}_{$person_id}' title='{$evaliation->title}' onclick=\"layout.addPanel( 'activity-activity' , '{$this->view->url}/activity/text/view/id/{$evaliation->id}/person_id/{$person_id}' )\" href='#this'>". $this->view->translate('activ') ."</a>";
                }
            case Bulletin_Model_Bulletin::NOTEPAD:
                return "<a id='tooltip_id_{$module->id}_{$person_id}' title='{$this->view->translate('notepad')}' onclick=\"layout.addPanel( 'notepad-notepad' , '{$this->view->url}/notepad/notepad/index/person_id/{$person_id}' )\" href='#this'>". $this->view->translate('notepad') ."</a>";
        }
    }
}