<?php
class Evaluation_Model_EvaluationValue extends Evaluation_Model_Abstract
{
	protected $_name    = "evaluation_value";
	protected $_primary = "id"; 
	
	protected $_dependentTables = array( "Evaluation_Model_EvaluationReply" );
	
	protected $_referenceMap = array( 
		'EvaluationQuestion' => array(
			'refTableClass' => 'Evaluation_Model_EvaluationQuestion',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'evaluation_question_id' )
		)
	);
	 
	public function save( $id )
	{
		$this->delete( array( 'evaluation_question_id' => $id ) );

        $data = array();

        $data['Evaluation_Model_EvaluationValue']['evaluation_question_id'] = $id;
        
        $type = $_POST['data']['Evaluation_Model_EvaluationQuestion']['type'];
        
		switch( $type ){
            case "text":
                if( $_POST['value'] ){
                    $data['Evaluation_Model_EvaluationValue']['value'] = $_POST['value'];
                    parent::save( $data );
                }
            break;

            case "radio":
                foreach( $_POST['alternative'] as $key => $val ){
                    if( $val ){
                        $data['Evaluation_Model_EvaluationValue']['value'] = $val;
                        if( $key == $_POST['correct'] ){
                            $data['Evaluation_Model_EvaluationValue']['status'] = "V";
                            $data['Evaluation_Model_EvaluationValue']['justify'] = $_POST['justify'] ? $_POST['justify']: null ;
                        }
                        
                        parent::save( $data );
                        unset( $data['Evaluation_Model_EvaluationValue']['status'] );
                        unset( $data['Evaluation_Model_EvaluationValue']['justify'] );
                    }
                }
            break;

            case "checkbox":
                foreach( $_POST['alternative'] as $key => $val ){
                    if( $val ){
                        $data['Evaluation_Model_EvaluationValue']['value'] = $val;

                        foreach( $_POST['correct'] as $correct ){
                            if( $key == $correct ){
                                $data['Evaluation_Model_EvaluationValue']['status'] = "V";
                            }
                        }

                        foreach( $_POST['justify'] as $keyJustify => $justify ){
                            if( $key == $keyJustify ){
                                $data['Evaluation_Model_EvaluationValue']['justify'] = $justify;
                            }
                        }

                        if( !isset( $data['Evaluation_Model_EvaluationValue']['justify'] ) ||
                            !$data['Evaluation_Model_EvaluationValue']['justify'] ){

                            $data['Evaluation_Model_EvaluationValue']['justify'] = null;
                        }

                        parent::save( $data );
                        unset( $data['Evaluation_Model_EvaluationValue']['status'] );
                        unset( $data['Evaluation_Model_EvaluationValue']['justify'] );
                    }
                }
            break;

            case "truefalse":
                foreach( $_POST['alternative'] as $key => $val ){
                    if( $val ){
                        $data['Evaluation_Model_EvaluationValue']['value'] = $val;
                        $data['Evaluation_Model_EvaluationValue']['status'] = $_POST['correct'][$key];
                        $data['Evaluation_Model_EvaluationValue']['justify'] = $_POST['justify'][$key];

                        if( !isset( $data['Evaluation_Model_EvaluationValue']['justify'] ) ||
                            !$data['Evaluation_Model_EvaluationValue']['justify'] ){

                            $data['Evaluation_Model_EvaluationValue']['justify'] = null;
                        }

                        parent::save( $data );
                    }
                }
            break;

            case "keyword":
                if( $_POST['correct'] ){
                    $data['Evaluation_Model_EvaluationValue']['value'] = $_POST['correct'];
                    $data['Evaluation_Model_EvaluationValue']['status'] = $_POST['keyword'];
                    $data['Evaluation_Model_EvaluationValue']['justify'] = null;

                    parent::save( $data );
                }
            break;

            case "association":
                foreach( $_POST['alternative'] as $key => $val ){
                    if( $val ){
                        $data['Evaluation_Model_EvaluationValue']['status'] = $_POST['correct']["$key"];
                        
                        $data['Evaluation_Model_EvaluationValue']['value'] = $val;
                        $data['Evaluation_Model_EvaluationValue']['justify'] = $_POST['justify'][$key];

                        if( !isset( $data['Evaluation_Model_EvaluationValue']['justify'] ) ||
                            !$data['Evaluation_Model_EvaluationValue']['justify'] ){

                            $data['Evaluation_Model_EvaluationValue']['justify'] = null;
                        }

                        parent::save( $data );
                    }
                }
            break;

        }
	}

    public function fetchAlternativesQuestion( $question_id )
    {
        if( !$question_id ){
            return "inform id of the question";
        }

        $select = $this->select();
		$select->where( "evaluation_question_id = ? " , $question_id )
			   ->order( "id" );

		return $this->fetchAll( $select );

    }
}