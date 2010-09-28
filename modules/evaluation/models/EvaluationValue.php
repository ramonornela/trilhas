<?php
class EvaluationValue extends Table
{
    protected $_name    = "trails_evaluation_value";
    protected $_primary = "id";

    protected $_dependentTables = array( "EvaluationReply" );

    protected $_referenceMap = array(
        'EvaluationQuestion' => array(
            'refTableClass' => 'EvaluationQuestion',
            'refColumns'	=> array( 'id' ),
            'columns'		=> array( 'evaluation_question_id' )
        )
    );

    public function save( $id )
    {
        //parent::delete( $id , 'evaluation_question_id' );

        $data = array();

        $data['evaluation_question_id'] = $id;

        $type = $_POST['type'];

        $ids = $this->fetchAll( array( 'evaluation_question_id = ?' => $id ), 'id' )->toArray();

        switch( $type ){
            case "text":
                if( $_POST['value'] ){
                    $data['id'] = $ids[0]['id'];
                    $data['value'] = $_POST['value'];
                    parent::save( $data );
                }
                break;

                case "radio":
                    foreach( $_POST['alternative'] as $key => $val ){
                        if( $val ){
                            $data['value'] = $val;
                            $data['id']    = $ids[$key-1]['id'];

                            if( $key == $_POST['correct'] ){
                                $data['status'] = "V";
                                $data['justify'] = $_POST['justify'] ? $_POST['justify']: null ;
                            }else{
                                $data['status'] = "";
                                $data['justify'] = "";
                            }

                            parent::save( $data );
                            unset( $data['status'] );
                            unset( $data['justify'] );
                        }
                    }
                    break;

                    case "checkbox":
                        foreach( $_POST['alternative'] as $key => $val ){
                            if( $val ){
                                $data['value'] = $val;
                                $data['id']    = $ids[$key-1]['id'];

                                $data['status'] = "";

                                foreach( $_POST['correct'] as $correct ){
                                    if( $key == $correct ){
                                        $data['status'] = "V";
                                    }
                                }

                                foreach( $_POST['justify'] as $keyJustify => $justify ){
                                    if( $key == $keyJustify ){
                                        $data['justify'] = $justify;
                                    }
                                }

                                if( !isset( $data['justify'] ) ||
                                    !$data['justify'] ){

                                    $data['justify'] = null;
                                }

                                parent::save( $data );
                                unset( $data['status'] );
                                unset( $data['justify'] );
                            }
                        }
                        break;

                        case "truefalse":
                            foreach( $_POST['alternative'] as $key => $val ){
                                if( $val ){
                                    $data['value'] = $val;
                                    $data['id']    = $ids[$key-1]['id'];
                                    $data['status'] = $_POST['correct'][$key];
                                    $data['justify'] = $_POST['justify'][$key];

                                    if( !isset( $data['justify'] ) ||
                                        !$data['justify'] ){

                                        $data['justify'] = null;
                                    }

                                    parent::save( $data );
                                }
                            }
                            break;

                            case "keyword":
                                if( $_POST['correct'] ){
                                    $data['value'] = $_POST['correct'];
                                    $data['id']    = $ids[0]['id'];
                                    $data['status'] = $_POST['keyword'];
                                    $data['justify'] = null;

                                    parent::save( $data );
                                }
                                break;

                                case "association":
                                    foreach( $_POST['alternative'] as $key => $val ){
                                        if( $val ){
                                            $data['id']     = $ids[$key-1]['id'];
                                            $data['status'] = $_POST['correct']["$key"];

                                            $data['value']   = $val;
                                            $data['justify'] = $_POST['justify'][$key];

                                            if( !isset( $data['justify'] ) ||
                                                !$data['justify'] ){

                                                $data['justify'] = null;
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