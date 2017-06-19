/**
 * Created by dcelis on 09/11/15.
 */


var obj            = []  //guardo mis datos en un array
var bodyAnuncio    = ""; //guardo mi html de los anuncios


//muestra mis anuncio y guarda mis en la variable obj
function _MostrarAnuncios(data){
    bodyAnuncio = MensajesSporte(data);
    var nroAnuncio = 0;
    $.each(data, function(key, value){
       if(value.EstadoAnuncio == "Activo"){
            nroAnuncio++;
       }
    })

    if(nroAnuncio){
        $("#anuncio_indicator").text(""+ nroAnuncio +"");
        $("#anuncio_indicator").show();
    }else{
        $("#anuncio_indicator").hide();
    }

    return nroAnuncio;

}
//muestra las alertas del coordinador
function _MostrarAlertas(){
    bodyAnuncio = MensajesSporte(obj);
}

function setAlerts(data){
    obj = data
}

function setAlert(data){

    obj.reverse().push(data)
    //obj.reverse()
}

function setMostrarMensaje(onTipoAnuncio,onCodigo,onCurso,onPrograma){
    var link="";
    IniciarAnuncio();
    VerAnuncio(onCodigo);

    var pizarra = _SQE.mk("div");
    $(pizarra).attr({ "id":"Pizarra" });
    $block.html(null).append(pizarra);
    $('#Pizarra').html();
    //anuncio();

    switch (onTipoAnuncio){
        case "Soporte":
            link = "/system/_vistas/gac_bandeja.php?Bandeja=DetalleReclamo&Caso=True&CodReclamo="+onCodigo+"&Programa_almacen="+onPrograma+"&Curso_almacen="+onCurso+"";
            break
        default :
            break
    }

    window.load =  enviaReg(onCodigo,link,'Pizarra','');

}

//arma el cuerpo del html de los anuncios
function MensajesSporte(data){

    var link = "";
    var anuncio = "";
    var html = "";
    html  = "<div id=ListadoAnuncio class=EstiloAnunciosPanel >";
    html += "<div style='width:100%;float:left;'>";
    html +="<div style='float:left;width:100%;padding-bottom:1em ' class=TituloA><div id=nombrepro style=float:left;><h1><span style='font-size:1em;font-weight:300;'>REVISAR ANUNCIOS</span></h1></div><div style='float:right;width:auto;'></div><div class=linea style='float:left;'></div></div>";

    $.each(data.reverse(), function(key, value){

        switch (value.TipoAnuncio){
            case "Soporte":
                link = "enviaReg('"+value.IDRaiz+"','/system/_vistas/gac_bandeja.php?Bandeja=DetalleReclamo&Caso=True&CodReclamo="+value.IDRaiz+"&Programa_almacen="+value.ProgramaAlmacen+"&Curso_almacen="+value.ProductoBase+"','Pizarra','');VerAnuncio('"+value.Codigo+"');";
                break
            default :

        }

        switch (value.EstadoAnuncio){
            case "Activo":
                anuncio = "blockNewAnuncio";
                break
            default :
                anuncio = "blockAnuncio";
        }

        html += "<div class="+anuncio+" onclick="+link+"   >" +
                "<div class=TituloAnuncio><i class=icon-rss>  </i>"+ value.Titulo +"</div>" +
                "<div class=DescripAnuncio>"+value.Descripcion+" </div>" +
                "<div class=FechaAnuncio>"+value.Fecha+"</div>" +
                "</div>";

    });
    html += "</div>";
    html += "</div>";


  return html
}