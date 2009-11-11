<?php
class Preceptor_View_Helper_ItemEvaluatios extends Zend_View_Helper_Abstract
{
	public function itemEvaluatios( $rs )
	{
		foreach ( $rs->findDependentRowset('Bulletin_Model_Bulletin') as $evaluations )
		{
			switch ( $evaluations->module )
			{
				case Bulletin_Model_Bulletin::ACTIVITY :
					$return[$evaluations->module][$evaluations->id]['evaluation'] = $evaluations->findParentRow( 'Activity_Model_Activity' );
					$return[$evaluations->module][$evaluations->id]['bulletin']   = $evaluations;
					break;
				case Bulletin_Model_Bulletin::EVALUATION :
					$return[$evaluations->module][$evaluations->id]['evaluation'] = $evaluations->findParentRow( 'Evaluation_Model_Evaluation' );
					$return[$evaluations->module][$evaluations->id]['bulletin']   = $evaluations;
					break;
				case Bulletin_Model_Bulletin::FORUM :
					$return[$evaluations->module][$evaluations->id]['evaluation'] = $evaluations->findParentRow( 'Forum_Model_Forum' );
					$return[$evaluations->module][$evaluations->id]['bulletin']   = $evaluations;
					break;
				case Bulletin_Model_Bulletin::NOTEPAD :
					$return[$evaluations->module][$evaluations->id]['evaluation'] = "notepad";
					$return[$evaluations->module][$evaluations->id]['bulletin']   = $evaluations;
					break;
				case Bulletin_Model_Bulletin::GLOSSARY :
					$return[$evaluations->module][$evaluations->id]['evaluation'] = "glossary";
					$return[$evaluations->module][$evaluations->id]['bulletin']   = $evaluations;
					break;
			}
		}
		
		return $return;
	}
}