<?php
class RetornoController extends Application_Controller_Abstract
{
	protected $_model = false;

    public function tep_not_null($value)
    {
        if (is_array($value)) {
            if (sizeof($value) > 0) {
                return true;
            }else{
                return false;
            }
        }else{
            if (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0)) {
                return true;
            }else{
                return false;
            }
        }
    }

    public function indexAction()
    {
        $PagSeguro  = 'Comando=validar';
        $PagSeguro .= '&Token=E102A014D0240A7A7C4006C02AB98B27';
        $Cabecalho  = "";

        if( $_POST ){

            foreach ($_POST as $key => $value){
                $value = urlencode(stripslashes($value));
                $PagSeguro .= "&$key=$value";
            }

            $curl = true;

            if ($curl == true){
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, 'https://pagseguro.uol.com.br/Security/NPI/Default.aspx');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $PagSeguro);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                $resp = curl_exec($ch);
                if (!$this->tep_not_null($resp)){
                    curl_setopt($ch, CURLOPT_URL, 'https://pagseguro.uol.com.br/Security/NPI/Default.aspx');
                    $resp = curl_exec($ch);
                }

                curl_close($ch);
                $confirma = (strcmp ($resp, "VERIFICADO") == 0);
            }
            else{
                echo "$errstr ($errno)<br />\n";
                // ERRO HTTP
            }

            if ($confirma){

                $data['Application_Model_PagSeguroTransacoes']['transacaoid'] 	= $_POST['TransacaoID'];
                $data['Application_Model_PagSeguroTransacoes']['vendedoremail'] 	= $_POST['VendedorEmail'];
                $data['Application_Model_PagSeguroTransacoes']['tipofrete']       = $_POST['TipoFrete'];
                $data['Application_Model_PagSeguroTransacoes']['valorfrete']      = number_format($_POST['ValorFrete'], 2 , '.' , '' );
                $data['Application_Model_PagSeguroTransacoes']['Extras']          = number_format($_POST['Extras'], 2 , '.' , '' );
                $data['Application_Model_PagSeguroTransacoes']['Anotacao']        = $_POST['Anotacao'];
                $data['Application_Model_PagSeguroTransacoes']['DataTransacao'] 	= $_POST['DataTransacao'];
                $data['Application_Model_PagSeguroTransacoes']['TipoPagamento'] 	= $_POST['TipoPagamento'];
                $data['Application_Model_PagSeguroTransacoes']['StatusTransacao'] = $_POST['StatusTransacao'];
                $data['Application_Model_PagSeguroTransacoes']['CliNome']         = $_POST['CliNome'];
                $data['Application_Model_PagSeguroTransacoes']['CliEmail']        = $_POST['CliEmail'];
                $data['Application_Model_PagSeguroTransacoes']['CliEndereco'] 	= $_POST['CliEndereco'];
                $data['Application_Model_PagSeguroTransacoes']['CliNumero']       = $_POST['CliNumero'];
                $data['Application_Model_PagSeguroTransacoes']['CliComplemento'] 	= $_POST['CliComplemento'];
                $data['Application_Model_PagSeguroTransacoes']['CliBairro']       = $_POST['CliBairro'];
                $data['Application_Model_PagSeguroTransacoes']['CliCidade']       = $_POST['CliCidade'];
                $data['Application_Model_PagSeguroTransacoes']['CliEstado']       = $_POST['CliEstado'];
                $data['Application_Model_PagSeguroTransacoes']['CliCEP']          = $_POST['CliCEP'];
                $data['Application_Model_PagSeguroTransacoes']['CliTelefone'] 	= str_replace( " " , "" , $_POST['CliTelefone'] );
                $data['Application_Model_PagSeguroTransacoes']['NumItens']        = $_POST['NumItens'];

                $transacao = new Application_Model_PagSeguroTransacoes();
                $transacaoId = $transacao->save( $data );

                unset( $data );

                $data['Application_Model_PagSeguroTransacoesProdutos']['transacaoid']       = $_POST['TransacaoID'];
                $data['Application_Model_PagSeguroTransacoesProdutos']['prodid']            = $_POST['ProdID_1'];
                $data['Application_Model_PagSeguroTransacoesProdutos']['proddescricao']     = $_POST['ProdDescricao_1'];
                $data['Application_Model_PagSeguroTransacoesProdutos']['prodvalor']         = number_format($_POST['ProdValor_1'], 2 , '.' , '' );
                $data['Application_Model_PagSeguroTransacoesProdutos']['prodquantidade']    = $_POST['ProdQuantidade_1'];
                $data['Application_Model_PagSeguroTransacoesProdutos']['prodfrete']         = null;
                $data['Application_Model_PagSeguroTransacoesProdutos']['prodextras']        = null;

                $produto = new Application_Model_PagSeguroTransacoesProdutos();
                $transacaoProdutoId = $produto->save( $data );
                unset( $data );

                $register = explode( "-", $_POST['ProdID_1']);
                $data['Station_Model_ClassPerson']['person_id'] = $register[0];
                $data['Station_Model_ClassPerson']['class_id']  = $register[1];
                $data['Station_Model_ClassPerson']['status']    = Station_Model_Status::STUDYING;

                $classPerson = new Station_Model_ClassPerson();
                $classPerson->delete( array( "person_id" => $register[0] , "class_id" => $register[1] ) );
                $result = $classPerson->save( $data );

                if( !$result->error ){
                    $this->_helper->_flashMessenger->addMessage( $this->view->translate( 'purchase made with success!' ) );
                }

                //file_put_contents( realpath(DIR_APPLICATION . '/../../data.txt') , "----$transacaoProdutoId->message-----$transacaoProdutoId->detail['id']----" , FILE_APPEND );

            }else{
                if (strcmp ($resp, "FALSO") == 0){
                    // LOG para investigação manual
                }
            }
        }else{
            $this->_helper->_flashMessenger->addMessage( $this->view->translate( 'no product was selected.' ) );
        }

        $this->_helper->layout->setLayout( "clear" );
    }
}