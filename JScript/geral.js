/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 * Sistema              ///
 * Começo do desenvimento: 12/01/2010, 17:24:43                                                                                ///
 * Autor: Analista-Arquiteto de Software WEB, Cleison Ferreira de Melo                                                         ///
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 * Arquivo: geral
 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
//funcao que imprime o conteudo da pagina
function imprimir()
{
    window.print();
}
//funcao que mostras os blocos de textos ao passar o cusor do mouse.
function showRollTip(msg, e)
{
    if ( typeof RollTip == "undefined" || !RollTip.ready )
        return;
    RollTip.reveal(msg, e);
}
//funcao que retira os blocos de textos
function hideRollTip()
{
    if ( typeof RollTip == "undefined" || !RollTip.ready )
        return;
    RollTip.conceal();
}
//funcao que abre uma popup
//@param pagina e a pagina que sera aberta dentro da popup
//@param e uma identificaçao para esta pagina
//@param e o nome da popup
//@param e a altura do popup
//@param e a largura do popup
//@param e o paramatro se e para mostras a barra de rolagem Ex: yes ou no
function popup(pagina, id, origem, nome, altura, largura, scroll)
{
    var winl = (screen.width - largura) / 2;
    var wint = (screen.height - altura) / 2;
    winprops = 'height='+altura+',width='+largura+',top='+wint+',left='+winl+',scrollbars='+scroll+',resizable=false, menubar=false';
    win = window.open(pagina+'?id='+id+'&origem='+origem, nome, winprops);

}
//funcao que abre uma popup de relatorio
//@param pagina e a pagina que sera aberta dentro da popup
//@param e uma identificaçao para esta pagina
//@param e o nome da popup
//@param scroll barra de deslisar da tela
function relatorio(pagina, nome, scroll)
{
    winprops = 'height='+screen.height+',width='+((screen.width)- 10)+' top=1, left=0,scrollbars='+scroll+',resizable=false, menubar=false';
    win = window.open(pagina, nome, winprops);

}
//funcao que maximiza a tela ao maximo
function maximizar()
{
	window.moveTo( 0, 0 );
	window.resizeTo( screen.availWidth, screen.availHeight );
}
//funcao que minimiza a tela
function minimizar()
{
    window.moveTo(0,0);
    window.resizeTo(637, screen.availHeight);
}
//funcao mascaradata() e funcao que se o usuario digitar um campo text ele automaticamente coloca as barras
//@param campo e nome da tag que sofrera acao ex: this
//@param evento e o tipo de evento, essa funcao habilita voce utilizar a techa DEL e Backspace   ex: event
function mascaraData(campo,evento)
{
    var tecla = (window.event) ? event.keyCode : evento.which;
    if(validaNumeros(evento)){
        if(campo.value.length == 2)
            campo.value+="/";
        if(campo.value.length == 5)
            campo.value+="/";
    }
}
function mascaraHora(campo,evento)
{
    var tecla = (window.event) ? event.keyCode : evento.which;
    if (tecla != 9 && tecla != 8)
    {
        if(campo.value.length == 2)
            campo.value+=":";
        if(campo.value.length == 5)
            campo.value+=":";
    }
}
//funcao que valida email
//@param mail e o mail que sera validado ex: this
function validaEmail(email)
{
    if((email.indexOf("@") < 1) || (email.indexOf(".") < 1) || (email.length < 10))
    {
        return false;
    }
    else
        return true;
}
//function validasenha e a funcao que valida se a senha tem numeros e tambem é maior ou igual a 8 digitos
//ira retornar true ou false
//@param valor é o valor que vai ser validado
function validaSenha(valor)
{
    var numeros = "0123456789";
    var valida = "";
    for( i = 0; i < valor.length; i++)
    {
        if( numeros.indexOf( valor.charAt(i),0 ) != -1 )
        {
            valida = 1;
        }
    }
    if(( valida != 1 ) || (valor.length < 8 ) )
        return false;
    else
        return true;
}
//function validaNumeros e a funcao que nao aceita digitar letras em campos de numeros
//@param tecla e o evento a ser validado ex: event
function validaNumeros(evento)
{
    var tecla = (window.event) ? event.keyCode : evento.which;
    if(( tecla == 13 ) || ( tecla == 8 ) || ( tecla == 46 ) || ( tecla == 48 ) || ( tecla == 49 )  || ( tecla == 9 )|| ( tecla == 32 ) || ( tecla == 0 ) || ( tecla == 46 ) || ( tecla == 44 ) || ( tecla == 111 ) || ( tecla == 193 ) )
        return true;
    else if( ( tecla >= 48 && tecla <= 57 ) || ( tecla >= 96 && tecla <= 105 ) )
        return true;
    else
        return false;
}
//function validaLetras e a funcao que nao aceita digitar numeros em campos de letras
//@param evento e a tecla a ser validado ex: event
function validaLetras(evento)
{
    var tecla = (window.event) ? event.keyCode : evento.which;
    if(tecla >= 65 && tecla <= 90)
        return true;
    else if(tecla >= 97 && tecla <= 122)
        return true;
    else if(( tecla == 13 ) || ( tecla == 8 ) || ( tecla == 9 ) || ( tecla == 32 ) || ( tecla == 231 ) || ( tecla == 0 ) || ( tecla == 46 ))
        return true;
    else
        return false;
}
function validaHora(campo){
    hrs = (campo.value.substring(0,2));
    min = (campo.value.substring(3,5));
    seg = (campo.value.substring(6,8));
    estado = "";
    if ((hrs < 00 ) || (hrs > 23) || ( min < 00) ||( min > 59) || ( seg < 00) ||( seg > 59)){
       estado = "errada";
    }
    if (campo.value == "") {
       estado = "";
    }
    if (estado == "errada") {
       alert("Hora invalida!");
       campo.value = "";
       campo.focus();
       return false;
    }
    return true;
}
//funcao valida se a data digitada no tag tipo text e maior que a data atual se form da um erro e limpa a tag
//@param campo e nome da tag que sofrera a validacao
function validaData(campo)
{
    if (campo.value!="")
    {
        erro=0;
        hoje = new Date();
        anoAtual = hoje.getFullYear();
        barras = campo.value.split("/");
        if (barras.length == 3)
        {
            dia = barras[0];
            mes = barras[1];
            ano = barras[2];
            resultado = (!isNaN(dia) && (dia > 0) && (dia < 32)) && (!isNaN(mes) && (mes > 0) && (mes < 13)) && (!isNaN(ano) && (ano.length == 4) && (ano <= anoAtual && ano >= 1900));
            if (!resultado)
            {
                //alert("Data inválida!");
                campo.value = '';
                //campo.focus();
                return false;
            }
         }
         else
         {
             //alert("Data inválida!");
             campo.value = '';
             //campo.focus();
             return false;
         }
        return true;
    }
}
//funcao mudacorfundo muda a cor de fundo
//@param nome e o nome da tag ex:this
//@param cor e a cor que vai ser recebida
function mudacorFundo(nome, cor)
{
    nome.style.backgroundColor = cor;
}
//funcao mudacorletra muda a cor da letra
//@param nome e o nome da tag ex:this
//@param cor e a cor que vai ser recebida
function mudacorLetra(nome, cor)
{
    nome.style.color = cor;
}
//funcao mudacorborda muda a cor de borda
//@param nome e o nome da tag ex:this
//@param cor e a cor que vai ser recebida
function mudaBorda(nome, cor)
{
    nome.style.border = '1px solid ' + cor;
}

