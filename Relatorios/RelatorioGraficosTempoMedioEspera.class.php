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
class RelatorioGraficosTempoMedioEspera extends Utilitarios implements RelatorioNegociosInterface {
    public function __construct(){}
    public function gerarGraficosTempoMedioEspera($_dateIni, $_dateFim, $_timeIni , $_timeFim , $_ramal){
        if(file_exists($_SERVER[ 'DOCUMENT_ROOT' ]."/System/IMG/grafico03.png"))
            unlink($_SERVER[ 'DOCUMENT_ROOT' ]."/System/IMG/grafico03.png");
        $DadosFiltradosDao = new DadosFiltradosDao();
        $values = array( 'dI' => $_dateIni, 'dF' => $_dateFim, 'hI' => $_timeIni, 'hF' => $_timeFim, 'ramal' => $_ramal, 'lim' => $_timeLim );
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
        $PHPlot->SetTitle( utf8_decode("Quantidade de Chamadas em Espera Superior a 30 segundos." ));
        $PHPlot->SetNumXTicks(16);//quantidade de linhas no eixo x
        $PHPlot->SetYTitle( utf8_decode("Quantidade") );//descricao eixo y
        $PHPlot->SetXTitle( utf8_decode("Horários") );//descricao eixo x
        $PHPlot->SetFont("legend", 1);
        $PHPlot->SetXLabelFontSize(2);
        $PHPlot->SetYLabelFontSize(2);
        $PHPlot->SetLegendPixels(471, 5);
        $PHPlot->SetDataColors(array('green'));
        $PHPlot->SetLegend(array('Chamadas'));//inserido a descrição na legenda do grafico
        $PHPlot->SetLegendStyle('left', 'left');
        $PHPlot->SetShading(2);//Retira o 3d das barras
        $result = array();
        $result = $DadosFiltradosDao->chamadasAtendidasPerdidasHora($values);
        $quantidade = array();
        while ($row = $DadosFiltradosDao->resultObject($result['totalLigacoesHoraEspera'])) {
            $quantidade[$row->hora] = $row->quantidade;
        }
        //print_r($quantidade);
        $dadosArray = array(
            array('07', $quantidade[7]),
            array('08', $quantidade[8]),
            array('09', $quantidade[9]),
            array('10', $quantidade[10]),
            array('11', $quantidade[11]),
            array('12', $quantidade[12]),
            array('13', $quantidade[13]),
            array('14', $quantidade[14]),
            array('15', $quantidade[15]),
            array('16', $quantidade[16]),
            array('17', $quantidade[17]),
            array('18', $quantidade[18]),
            array('19', $quantidade[19]),
            array('20', $quantidade[20]),
            array('21', $quantidade[21]),
            array('22', $quantidade[22])
        );
        $PHPlot->SetDataValues($dadosArray);//inserido os falores do grafico
        $PHPlot->is_inline = true;
        $PHPlot->output_file = $_SERVER[ 'DOCUMENT_ROOT' ]."/System/IMG/grafico03.png";
        $PHPlot->DrawGraph();//criando grafico
    }
}
?>
