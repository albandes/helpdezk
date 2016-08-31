//Tenta criar o objeto xmlHTTP
try{
    xmlhttp = new XMLHttpRequest();
}catch(ee){
    try{
        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    }catch(e){
        try{
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }catch(E){
            xmlhttp = false;
        }
    }
}

//Fila de conex�es
fila=[]
ifila=0

//Carrega via XMLHTTP a url recebida e coloca seu valor
//no objeto com o id recebido
function makeRequest(url, id, param){
    //Carregando...
    document.getElementById(id).innerHTML="<div id='Layer1' style='margin-top:200px; margin-left:50%;'><span class='carregando' >"+"<img src='/parracho/includes/images/loading_laranja.gif'></span></div>"

    //Adiciona � fila
    fila[fila.length]=[url, id, param]
    //Se n�o h� conex�es pendentes, executa
    if((ifila+1)==fila.length)ajaxRun()
}

//Executa a pr�xima conex�o da fila
function ajaxRun(){
    //Abre a conex�o
    xmlhttp.open("POST",fila[ifila][0],true);
    //Fun��o para tratamento do retorno
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4){
            //Mostra o HTML recebido
            document.getElementById(fila[ifila][1]).innerHTML=unescape(xmlhttp.responseText.replace(/\+/g," "));
			executaScript(unescape(xmlhttp.responseText.replace(/\+/g," ")));
            //Roda o pr�ximo
            ifila++
            if(ifila<fila.length)setTimeout("ajaxRun()",20)
        }
    }
    //Executa
	xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xmlhttp.send(fila[ifila][2])
}
function executaScript(texto){
	// procura script src
    var ini = 0;
    while (ini!=-1){
        // procura uma tag de script
        ini = texto.indexOf('<script', ini);
		fim_script = texto.indexOf('</script>', ini);
        // se encontrar
        if (ini >=0){
			ini = texto.indexOf('src=', ini);
	        if (ini >=0 && ini<fim_script){
				ini = texto.indexOf('"', ini)+1;
				// procura o final do script
				var fim = texto.indexOf('"', ini+1);
				src = texto.substring(ini,fim);
				novo = document.createElement("script");
				novo.src = src;
				document.body.appendChild(novo);
			}
		}
	}
	// procura script
    var ini = 0;
    while (ini!=-1){
        // procura uma tag de script
        ini = texto.indexOf('<script', ini);
        // se encontrar
        if (ini >=0){
			// define o inicio para depois do fechamento dessa tag
			ini = texto.indexOf('>', ini) + 1;
			// procura o final do script
			var fim = texto.indexOf('</script>', ini);
			codigo = texto.substring(ini,fim);
            // extrai apenas o script
            // executa o script
            novo = document.createElement("script");
			novo.text = codigo;
            document.body.appendChild(novo);
        }
    }
	// procura onload
    var ini = 0;
    while (ini!=-1){
        // procura uma tag de script
        ini = texto.indexOf('onload', ini);
        // se encontrar
        if (ini >=0){
            ini = texto.indexOf('"', ini) + 1;
            var fim = texto.indexOf('"', ini+1);
            codigo = texto.substring(ini,fim);
            novo = document.createElement("script")
            novo.text = codigo;
            document.body.appendChild(novo);
        }
    }
}