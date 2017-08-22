<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
ini_set('max_execution_time','0');
include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Utilitarios/Ambiente.php";
include_once $_SERVER[ 'DOCUMENT_ROOT' ]."/System/Utilitarios/LoadClass.class.php";
include_once "RelatorioNegociosInterface.php";
require_once $_SERVER[ 'DOCUMENT_ROOT' ].'/System/PHPGrafics/phplot.php';
require_once $_SERVER[ 'DOCUMENT_ROOT' ].'/System/FPDF/Relatorio.class.php';
$__autoload = new LoadClass();
$__autoload->carregar('Excecoes,Logs,Utilitarios,DadosFiltradosDao');
class RelatorioGraficoPorcentagem extends Utilitarios implements RelatorioNegociosInterface {
    public function __construct(){}
    public function gerarGraficoPorcentagem($_dateIni, $_dateFim, $_timeIni , $_timeFim){
        if(file_exists($_SERVER[ 'DOCUMENT_ROOT' ]."/System/IMG/grafico04.png"))
            unlink($_SERVER[ 'DOCUMENT_ROOT' ]."/System/IMG/grafico04.png");
        $DadosFiltradosDao = new DadosFiltradosDao();
        $values = array( 'dI' => $_dateIni, 'dF' => $_dateFim, 'hI' => $_timeIni, 'hF' => $_timeFim, 'lim' => $_timeLim );
        $PHPlot = new PHPlot(547,230);
        $PHPlot->SetGridColor('black');//seta a cor nos eixos x e y
        $PHPlot->SetFileFormat("png");//seta o formato da imagem gerada
        $PHPlot->SetImageBorderType('plain');//seta o tipo de borda do grafico
        $PHPlot->SetPlotType('bars');//seta o tipo de grafico
        $PHPlot->SetDataType('text-data');//seta o formato do texto
        $PHPlot->SetXTickLabelPos('none');//altera a disposição dos numeros no eixo x
        $PHPlot->SetXTickPos('plotdown');//altera a disposiçao dos traços no eixo x
        $PHPlot->SetDrawXGrid(true);//inseri as linhas no eixo x
        $PHPlot->SetYDataLabelPos('plotin');//mostra o valor nas barras
        $PHPlot->SetTitleColor('black');
        $PHPlot->SetTextColor('black');
        $PHPlot->SetPrecisionX(0);
        $PHPlot->SetXGridLabelType('custom');
        $PHPlot->SetFont("title", 2);
        $PHPlot->SetTitle( utf8_decode("Percentual de Ligações Atendidas com Menos e Mais de 30 Segundos." ));
        $PHPlot->SetNumXTicks(2);//quantidade de linhas no eixo x
        $PHPlot->SetYTitle( utf8_decode("Percentual") );//descricao eixo y
        $PHPlot->SetXTitle( utf8_decode("Classificação") );//descricao eixo x
        $PHPlot->SetFont("legend", 1);
        $PHPlot->SetXLabelFontSize(2);
        $PHPlot->SetYLabelFontSize(2);
        $PHPlot->SetLegendPixels(471, 5);
        $PHPlot->SetLegend(array('Menos', 'Mais'));//inserido a descrição na legenda do grafico
        $PHPlot->SetDataColors(array('green','red'));
        $PHPlot->SetLegendStyle('left', 'left');
        $PHPlot->SetShading(2);//Retira o 3d das barras
        $result = array();
        $result = $DadosFiltradosDao->chamadasAtendidasPerdidasHora($values);
        $quantidade = array();
        $quantidade1 = array();
        while ($row = $DadosFiltradosDao->resultObject($result['totalLigacoes'])) {
            $count = $row->quantidade;
        }
        while ($row = $DadosFiltradosDao->resultObject($result['totalLigacoesRecebidas'])) {
            $count2 = $row->quantidade;
        }
        $taxasDesempenho = $DadosFiltradosDao->taxasDesempenho($values);
        $taxasDesempenho['superior'] = ( $taxasDesempenho['superior'] * 100 ) / $count2;
        $taxasDesempenho['inferior'] = ( $taxasDesempenho['inferior'] * 100 ) / $count2;
        $taxasDesempenho['superior'] = explode(".", $taxasDesempenho['superior']);
        $taxasDesempenho['superior'] = $taxasDesempenho['superior'][0].".".substr($taxasDesempenho['superior'][1], 0, 2);
        $taxasDesempenho['inferior'] = explode(".", $taxasDesempenho['inferior']);
        $taxasDesempenho['inferior'] = $taxasDesempenho['inferior'][0].".".substr($taxasDesempenho['inferior'][1], 0, 2);
        $dadosArray = array(
            array('-30   +30', $taxasDesempenho['inferior'], $taxasDesempenho['superior'])
        );
        $PHPlot->SetDataValues($dadosArray);//inserido os falores do grafico
        $PHPlot->is_inline = true;
        $PHPlot->output_file = $_SERVER[ 'DOCUMENT_ROOT' ]."/System/IMG/grafico04.png";
        $PHPlot->DrawGraph();//criando grafico
    }
}
?>
