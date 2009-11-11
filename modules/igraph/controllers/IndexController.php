<?php
class Igraph_IndexController extends Application_Controller_Abstract
{
	protected $_model = false;

	public function indexAction()
	{
        $this->view->axis_x = array('Participação de mercado',
                                    'Crescimento de participação',
                                    'Qualidades dos produtos',
                                    'Reputação da marca',
                                    'Rede de distribuição',
                                    'Eficácia promocional',
                                    'Pesquisa e desenvolvimento',
                                    'Capacidade gerencial');
        $this->view->axis_y = array('Tamanho do mercado',
                                    'Taxa anual de crescimento',
                                    'Margem de lucro histórica',
                                    'Intensidade da concorrência',
                                    'Exigências tecnológicas',
                                    'Números de fornecedores',
                                    'Necessidade de propaganda e promoção',
                                    'Impacto ambiental');
    }
}