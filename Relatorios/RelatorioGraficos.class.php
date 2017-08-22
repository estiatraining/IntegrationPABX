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
class RelatorioGraficos extends Utilitarios implements RelatorioNegociosInterface {
    public function __construct(){}
    public function gerarGraficos( $_dateIni, $_dateFim, $_timeIni , $_timeFim , $_ramal , $_timeLim , $_tipo ){
        $DadosFiltradosDao = new DadosFiltradosDao();
        $values = array( 'dI' => $_dateIni, 'dF' => $_dateFim, 'hI' => $_timeIni, 'hF' => $_timeFim, 'ramal' => $_ramal, 'lim' => $_timeLim );
        //$PHPlot = new PHPlot(250,130);
        $PHPlot = new PHPlot(1240,640);
        $PHPlot->SetGridColor('black');//seta a cor nos eixos x e y
        $PHPlot->SetFileFormat("png");//seta o formato da imagem gerada
        $PHPlot->SetImageBorderType('raised');//seta o tipo de borda do grafico
        $PHPlot->SetPlotType('bars');//seta o tipo de grafico
        $PHPlot->SetDataType('text-data');//seta o formato do texto
        $PHPlot->SetLegend(array('Quantidade'));//inserido a descrição na legenda do grafico
        $PHPlot->SetXTickLabelPos('none');//altera a disposição dos numeros no eixo x
        $PHPlot->SetXTickPos('plotdown');//altera a disposiçao dos traços no eixo x
        $PHPlot->SetDrawXGrid(true);//inseri as linhas no eixo x
        $PHPlot->SetYDataLabelPos('plotin');//mostra o valor nas barras
        $PHPlot->SetTitleColor('red');
        $PHPlot->SetTitleFontSize(5);
        $PHPlot->SetTextColor('black');
        $PHPlot->SetPrecisionX(0);
        $PHPlot->SetXGridLabelType('custom');
        if( $_timeLim != ""){
            $title = " com Tempo de Demora no Atendimento Igual ou Superior a ".$_timeLim." Segundos";
        }
        if( $_tipo == "NTR" ){
            $title = "Ligações Recebidas na Data ".$_dateIni.$title;
            $result = $DadosFiltradosDao->numTotalLigacoesRecebidas($values);
            $PHPlot->SetDataColors(array('green'));
        }
        else if( $_tipo == "NTP" ){
            $title = "Ligações Perdidas na Data ".$_dateIni.$title;
            $result = $DadosFiltradosDao->numTotalLigacoesPerdidas($values);
            $PHPlot->SetDataColors(array('red'));
        }
        else if( $_tipo == "NRR" ){
            if( $_timeLim != ""){
                $title = " com Tempo de Demora no Atendimento Igual ou \nSuperior a ".$_timeLim." Segundos";
            }
            $data = explode("/", $_dateIni);
            $title = "Ligações do Ramal ".$_ramal." no Mês ".$data[1]." do Ano de ".$data[2].$title;
        }
        $PHPlot->SetTitle( utf8_decode( $title ));//titulo
        if( $_tipo == "NTP" or $_tipo == "NTR" ){
            $PHPlot->SetNumXTicks(16);//quantidade de linhas no eixo x
            $PHPlot->SetYTitle( utf8_decode("Total de Ligações") );//descricao eixo y
            $PHPlot->SetXTitle( utf8_decode("Horários") );//descricao eixo x
            $PHPlot->SetLegendStyle('left');
            $quantidade = array();
            while ($row = $DadosFiltradosDao->resultObject($result)) {
                $quantidade[$row->hora] = $row->quantidade;
            }
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
        }
        else if( $_tipo == "NRR" ){
            $PHPlot->SetDataColors(array('green','red'));
            $PHPlot->SetLegendStyle('left', 'left');
            $PHPlot->SetNumXTicks(31);//quantidade de linhas no eixo x
            $PHPlot->SetYTitle( utf8_decode("Total de Ligações") );//descricao eixo y
            $PHPlot->SetXTitle( utf8_decode("Dias do Mês " . $data[1]) );//descricao eixo x
            $PHPlot->SetLegend(array('Quantidade Recebidas', 'Quantidade Perdidas'));//inserido a descrição na legenda do grafico
            $result = $DadosFiltradosDao->numTotalLigacoesRecebidasRamal($values);
            $result1 = $DadosFiltradosDao->numTotalLigacoesPerdidasRamal($values);
            $quantidade = array();
            $quantidade1 = array();
            while ($row = $DadosFiltradosDao->resultObject($result)) {
                $quantidade[$row->dia] = $row->quantidade;
            }
            while ($row = $DadosFiltradosDao->resultObject($result1)) {
                $quantidade1[$row->dia] = $row->quantidade;
            }
            //print_r($row);
            $dadosArray = array(
                array('01', $quantidade[1], $quantidade1[1]),
                array('02', $quantidade[2], $quantidade1[2]),
                array('03', $quantidade[3], $quantidade1[3]),
                array('04', $quantidade[4], $quantidade1[4]),
                array('05', $quantidade[5], $quantidade1[5]),
                array('06', $quantidade[6], $quantidade1[6]),
                array('07', $quantidade[7], $quantidade1[7]),
                array('08', $quantidade[8], $quantidade1[8]),
                array('09', $quantidade[9], $quantidade1[9]),
                array('10', $quantidade[10], $quantidade1[10]),
                array('11', $quantidade[11], $quantidade1[11]),
                array('12', $quantidade[12], $quantidade1[12]),
                array('13', $quantidade[13], $quantidade1[13]),
                array('14', $quantidade[14], $quantidade1[14]),
                array('15', $quantidade[15], $quantidade1[15]),
                array('16', $quantidade[16], $quantidade1[16]),
                array('17', $quantidade[17], $quantidade1[17]),
                array('18', $quantidade[18], $quantidade1[18]),
                array('19', $quantidade[19], $quantidade1[19]),
                array('20', $quantidade[20], $quantidade1[20]),
                array('21', $quantidade[21], $quantidade1[21]),
                array('22', $quantidade[22], $quantidade1[22]),
                array('23', $quantidade[23], $quantidade1[23]),
                array('24', $quantidade[24], $quantidade1[24]),
                array('25', $quantidade[25], $quantidade1[25]),
                array('26', $quantidade[26], $quantidade1[26]),
                array('27', $quantidade[27], $quantidade1[27]),
                array('28', $quantidade[28], $quantidade1[28]),
                array('29', $quantidade[29], $quantidade1[29]),
                array('30', $quantidade[30], $quantidade1[30]),
                array('31', $quantidade[31], $quantidade1[31])
            );
        }
        $PHPlot->SetDataValues($dadosArray);//inserido os falores do grafico
        //$PHPlot->is_inline = true;
        //$PHPlot->output_file = $_SERVER[ 'DOCUMENT_ROOT' ]."/System/IMG/".$_tipo.".png";
        $PHPlot->DrawGraph();//criando grafico
    }
}
?>
