<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Relatorioclass
 *
 * @author CleisonFerreira
 * @
 */
    require('fpdf.php');
    class Relatorio extends FPDF {
        private $widths;
        private $aligns;
        private $fontcolors;
        private $fillcolors;
        //metodo que inseri a largura da celula onde ficara o texto Ex: array(100);
        //@param $w, � a largura a ser setada
    	function SetWidths( $w )
        {
    	    $this->widths = $w;
    	}
        //metodo que inseri o alinhamento do texto na celula Ex: array("C"); array("L");array("R")
        //@param $a, � o alinhamento a ser setado
    	function SetAligns( $a )
        {
    	    $this->aligns = $a;
    	}
        //metodo que inseri a cor do texto;
        //@param $fc, � a cor a ser setada
    	function SetFontColors( $fc )
        {
    	    $this->fontcolors = $fc;
    	}
        //metodo que inseri a cor de fundo da celula;
        //@param $flc, � a cor a ser setada
    	function SetFillColors( $flc )
        {
    	    $this->fillcolors = $flc;
    	}
        //metodo que inseri uma texto com formata�oes
        //@param $_texto, � o texto que vai ser inserido Ex: array("exemplo");
        //@param $_altura, � a altura do campo em que vai ficar o texto
        //@param $_borda, � a largura da borda do retangulo que vai ficar em volta do texto
        //@param $_cFundo, � a cor do fundo do retangulo
        //@param $_cLinha, � a cor da linha do texto
        public function linha( $_texto, $_altura = 0, $_borda = 0, $_cFundo = "#FFFFFF", $_cLinha = "#000000", $_cBorda = "#000000" )
        {
        	if ( $_altura == 0 )
            {
        		for( $i = 0;$i < count( $_texto ); $i++ )
        		$nb = max( $nb,$this->NbLines( $this->widths[$i],$_texto[$i] ) );
        		$altura = 5 * $nb;
        	}
            else
            {
        		$altura = $_altura;
        	}
            $nb = 0;
            $this->CheckPageBreak( $altura );
        	for( $i = 0; $i < count( $_texto ); $i++ )
            {
        		$largura = $this->widths[$i];
        		$a = isset( $this->aligns[$i] ) ? $this->aligns[$i] : 'L';
        		$x = $this->GetX();
        		$y = $this->GetY();
        		$nb = $this->NbLines( $this->widths[$i],$_texto[$i] );
        		$hnc = 5 * $nb;
        		$fontcolor = ( $this->transHexaDec( $_cLinha ) );
                $bordaColor = ( $this->transHexaDec( $_cBorda ) );
        		$fillcolor = ( isset( $this->fillcolors ) ? $this->transHexaDec( $this->fillcolors[$i] ) : $this->transHexaDec( $_cFundo ) );
        		$this->SetTextColor( $fontcolor[ 'R' ] , $fontcolor[ 'G' ], $fontcolor[ 'B' ] );
        		$this->SetFillColor( $fillcolor['R'], $fillcolor['G'], $fillcolor['B'] );
        		$this->SetDrawColor( $bordaColor['R'], $bordaColor['G'], $bordaColor['B'] );
        		if ( $_borda == 1 )
                {
        			$this->retangulo($x,$y,$largura,$altura,"FD");
        		}
                else if( $_borda > 1 )
                {
        			$this->retangulo($x,$y,$largura,$altura,"F");
        		}
        		$this->SetXY($x,$y+(( $altura - $hnc ) / 2 ));
        		$this->MultiCell( $largura, 5, $_texto[$i],0,$a);
        		$this->SetXY( $x + $largura, $y);
        	}
        }
        //metodo que transforma para hexadecimal as cores no padrao RGB
        //@param $_cor, � a cor a ser transormada EX: transHexaDec( "#000000" );
        protected function transHexaDec( $_cor )
        {
            $r = substr($_cor, 1, 2);
            $g = substr($_cor, 3, 2);
            $b = substr($_cor, 5, 2);
            $rouge = hexdec($r);
            $vert = hexdec($g);
            $bleu = hexdec($b);
            $tabelaCores = array( 'R' => $rouge, 'G' => $vert, 'B' => $bleu );
            return $tabelaCores;
        }
        //metodo que cria um retangulo no relatorio
        //@param $_x , � a posi�ao x na tela
        //@param $_y , � a posi�ao y na tela
        //@param $_largura , � a largura do retangulo na tela
        //@param $_altura , � a algura do retangulo
        //@param $_estilo , � a o estilo do retangulo EX: "F", "FD", "DF", "D"
        public function retangulo( $_x, $_y, $_largura, $_altura, $_estilo = "" )
        {
            $this->Rect($_x, $_y, $_largura, $_altura, $_estilo = '');
        }
        //metodo que cria o cabe�alho do arquivo pdf
        //@param $_titulo � o titulo do arquivo
        //@param $_texto � o texto do cabe�alho
        public function cabecalho( $_titulo = "", $_texto = "" )
        {
            $this->SetTitle( $_titulo );
            $this->SetAuthor("Cleison Ferreira");
            $this->SetFont('helvetica','',10);
            $this->AddPage();
            $this->SetDisplayMode( 'fullwidth' );
            $this->SetFont( 'Arial', 'b', 11 );
            $this->retangulo( 5, 5, 200, 20,"DF");
            $this->SetFont( 'Arial', 'b', 11 );
            $this->pular(4);
            $this->SetXY( 5, $this->GetY());
            $this->SetWidths( array( 200 ) );
            $this->SetAligns( array( "C") );
            $this->Linha( array( $_texto ), 5 );
            //$this->imagem( $_SERVER[ 'DOCUMENT_ROOT' ]."/framework/class/FPDF/img/logosis.jpg", 10, 8, 35, 15);
            //$this->imagem( $_SERVER[ 'DOCUMENT_ROOT' ]."/framework/class/FPDF/img/logosis.jpg", 165, 8, 35, 15);
            $this->SetFont( 'Arial', 'b', 9 );
            $this->pular(13);
        }
        //metodo que cria o cabe�alho do arquivo pdf
        //@param $_titulo � o titulo do arquivo
        //@param $_texto � o texto do cabe�alho
        public function cabecalho2( $_titulo = "", $_texto = "" )
        {
            $this->SetTitle( $_titulo );
            $this->SetAuthor("Cleison Ferreira");
            $this->SetFont('helvetica','',10);
            $this->SetDisplayMode( 'fullwidth' );
            $this->SetFont( 'Arial', 'b', 11 );
            $this->retangulo( $this->GetX(), $this->GetY() - 5, 200, 20,"DF");
            $this->SetFont( 'Arial', 'b', 11 );
            $this->pular(4);
            $this->SetXY( 5, $this->GetY());
            $this->SetWidths( array( 200 ) );
            $this->SetAligns( array( "C") );
            $this->Linha( array( $_texto ), 5 );
            //$this->imagem( $_SERVER[ 'DOCUMENT_ROOT' ]."/framework/class/FPDF/img/logosis.jpg", 10, $this->GetY() - 6, 35, 15);
            //$this->imagem( $_SERVER[ 'DOCUMENT_ROOT' ]."/framework/class/FPDF/img/logosis.jpg", 165, $this->GetY() - 6, 35, 15);
            $this->SetFont( 'Arial', 'b', 9 );
            $this->pular(13);
        }
        //metodo imagem inseri uma imagem no arquivo pdf
        //@param $_arquivo � a imagem a ser inserido
        //@param $_x � a posi�ao de inicio da imagem
        //@param $_y � a posi�ao final da imagem
        //@param $_largura � a largura da imagem
        //@param $_algura � a altura da imagem
        //@param $_tipo � o tipo de imagem EX: "jpg", "png"
        //@param $_link � o link da web para ser acessado atraves de um clique na imagem
        public function imagem( $_arquivo, $_x = null, $_y = null, $_largura = 0, $_altura = 0, $_tipo = "", $_link = "" )
        {
            $this->Image( $_arquivo, $_x, $_y, $_largura, $_altura, $_tipo, $_link );
        }
        //metodo que cria um retangulo no corpo do relatorio
        public function corpo()
        {
            $this->retangulo( 5, $this->GetY(), 200, 268,"DF");
        }
        //metodo que cria o rodape no relatorio com data, mensagem e pagina
        //@param $_texto � o texto que sera impresso no centro do rodap�
        public function rodape( $_texto )
        {
            $fontcolor = ( $this->transHexaDec( "#000000" ) );
            $this->SetTextColor( $fontcolor[ 'R' ] , $fontcolor[ 'G' ], $fontcolor[ 'B' ] );
            $this->retangulo( 5, 297, 200, 7, "DF" );
            $this->SetFont('Arial','IB',7);
            $this->Text(198, 302, $this->PageNo());
            $this->Text(8, 302, @date("d/m/Y"));
            $this->SetFont('Arial','IB',9);
            $this->Text(88, 302, $_texto );
        }
    	protected function CheckPageBreak($h)
        {
    	    if($this->GetY()+$h>$this->PageBreakTrigger)
    	        $this->AddPage($this->CurOrientation);
    	}
    	protected function NbLines($w,$txt)
        {
    	    $cw = &$this->CurrentFont['cw'];
    	    if($w == 0)
    	        $w = $this->w-$this->rMargin-$this->x;
    	    $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
    	    $s = str_replace("\r",'',$txt);
    	    $nb = strlen($s);
    	    if($nb>0 and $s[$nb-1] == "\n")
    	        $nb--;
    	    $sep = -1;
    	    $i = 0;
    	    $j = 0;
    	    $l = 0;
    	    $nl = 1;
    	    while($i<$nb)
            {
    	        $c = $s[$i];
    	        if($c == "\n")
                {
    	            $i++;
    	            $sep = -1;
    	            $j = $i;
    	            $l = 0;
    	            $nl++;
    	            continue;
    	        }
    	        if($c == ' ')
    	            $sep = $i;
    	        $l += $cw[$c];
    	        if($l > $wmax)
                {
    	            if($sep == -1)
                    {
    	                if($i == $j)
    	                    $i++;
    	            }
                    else
    	                $i = $sep+1;
    	            $sep = -1;
    	            $j = $i;
    	            $l = 0;
    	            $nl++;
    	        }
                else
    	            $i++;
    	    }
    	    return $nl;
    	}
        public function pular( $_linhas = null )
        {
            $this->Ln( $_linhas );
        }
    }
?>
