/**
 * Created by dcelis on 16/10/15.
 */
///////temporary.js
var nombre = [];

function addview(id){
    var dv     = document.getElementById(id).value;
    var Existe = 0;
    for(i=0;i<=data.length;i++){
        if(dv == data[i]){
            Existe = 1;
        }
    }
    if(Existe == 0){
        data.push(dv);
    }else{
        //console.log('Existe');
    }
    TrueChange("btn-"+dv);
    var html = viewblock();
    document.getElementById("gridview").innerHTML = html;
}


function closeblock(id){
    falseChange("btn-"+data[id]);
    delete data[id];
    var html = viewblock();
    document.getElementById("gridview").innerHTML = html;

}

function viewblock(){
    var html = "";
    html += "<form name=ListUser method=post id=ListUser>";
    html += "<div class='listView'>";
    var nro = 1;
    for (i=0;i<=data.length -1;i++){
        if(data[i]){
            html += "<div class=blockUser>" +
                       "<div class='datauser'>"+ nro +"</div>" +
                       "<div class='datauser'><input type='hidden' value="+ data[i] +"  name='user[]'> "+ data[i] +"</div>" +
                       "<div class='datauser'><a href=# onclick=closeblock("+ i +");> X</a></div>" +
                     "</div>";
            nro++;
        }
    }
    html += "</div>";
    html += "</form>";
    return html;
}

function OrderName(){
    data.sort();
    var html = viewblock();
    document.getElementById("gridview").innerHTML = html;
    //return html;
}

function addGroup(formid){
    var Formulario=document.getElementById(formid);
    var users = [];
    var form_elements=Formulario.elements;
    var Existe = 0;
    var dv;


    for(var i=0;i<form_elements.length;i++){
        var elem=form_elements[i],responseValue,success=true;
        if(elem.getAttribute('data-CBI')!==true && elem.name){
            switch(elem.type){
                case "hidden":
                    users.push(elem.value);
                    break;
            }
        }
    }
    for(j=0;j<=users.length;j++){
        dv = users[j];
       // console.log(dv);

        var Existe = 0;
        for(i=0;i<=data.length;i++){
            if(dv == data[i]){
                Existe = 1;
            }
        }
        if(Existe == 0){
            Existe = 0;
            TrueChange("btn-"+dv);
            data.push(dv);
        }else{
           // console.log('Existe');
        }

    }

    var html = viewblock();
    document.getElementById("gridview").innerHTML = html;
    //console.log(data);

}

function Clearblock(){
    var dv = "";
    for(i=0;i<=data.length;i++){
        dv = data[i];
        delete data[i];
        if(dv){
            //console.log("btn-"+dv);
            falseChange("btn-"+dv);
        }

    }

    var html = viewblock();
    if(document.getElementById("gridview")){
        document.getElementById("gridview").innerHTML = html;
    }

}

function TrueChange(id){
    var btn     = document.getElementById(id);
    btn.style.background="#0CAD36";
}
function falseChange(id){
    //console.log("este es el id: "+id);
    var btn     = document.getElementById(id);
   // console.log("false");
    btn.style.background="#A0A0A0";
}


/////
/// manda el mensaje nuevo
var alertSocket = undefined
function NodeJS(Soporte){

    if(alertSocket){
        alertSocket.emit('initSoporte',{
            userClient    : Soporte.userClient,
            Codigo        : Soporte.Codigo,
            Descripcion   : Soporte.Descripcion,
            Fecha         : Soporte.Fecha,
            Interaccion   : Soporte.Interaccion,
            Producto      : Soporte.Producto,
            Titulo        : Soporte.Titulo,
            TipoAnuncio   : Soporte.TipoAnuncio,
            IdTipoAnuncio : Soporte.IdTipoAnuncio,
            IDRaiz        : Soporte.IDRaiz,
            ProductoBase  : Soporte.ProductoBase,
            ProgramaAlmacen: Soporte.ProgramaAlmacen,
            EstadoAnuncio  : Soporte.EstadoAnuncio

        })
    }else{
        $.post('https://chat.owlgroup.org/support',{soporte: true},function(data){
            var Socket = io('https://chat.owlgroup.org/',{secure: true});
            Socket.emit('initSoporte',{
                userClient    : Soporte.userClient,
                Codigo        : Soporte.Codigo,
                Descripcion   : Soporte.Descripcion,
                Fecha         : Soporte.Fecha,
                Interaccion   : Soporte.Interaccion,
                Producto      : Soporte.Producto,
                Titulo        : Soporte.Titulo,
                TipoAnuncio   : Soporte.TipoAnuncio,
                IdTipoAnuncio : Soporte.IdTipoAnuncio,
                IDRaiz        : Soporte.IDRaiz,
                ProductoBase  : Soporte.ProductoBase,
                ProgramaAlmacen: Soporte.ProgramaAlmacen,
                EstadoAnuncio  : Soporte.EstadoAnuncio

            })
            alertSocket = Socket;
        });
    }
}


function _AlertApp (Anuncios,cliente){

    $.each(Anuncios, function(key, value){
        //console.log(key, value);
    });

    var html = "";

    html  = "<div class=EstiloAnunciosPanel>";
    html += "<div style='width:100%;float:left;'>";
    html +="<div style='float:left;width:100%;padding-bottom:1em ' class=TituloA><div id=nombrepro style=float:left;><h1><span style='font-size:1em;font-weight:300;'>REVISAR ANUNCIOS</span></h1></div><div style='float:right;width:auto;'></div><div class=linea style='float:left;'></div></div>";

    $.each(Anuncios, function(key, value){

        html += "<div class=blockAnuncio>" +
            "<div class=TituloAnuncio><i class=icon-rss>  </i>"+ value.Titulo +"</div>" +
            "<div class=DescripAnuncio>"+value.Descripcion+" </div>" +
            "<div class=FechaAnuncio>"+value.Fecha+"</div>" +
            "</div>";
    });

    html += "</div>";
    html += "</div>";

    $("#anuncio_indicator").hide();
    $('#Pizarra').html(html);
}

//////////////////////////////////////

function addview2(id,cliente){
    var dv     = document.getElementById(id).value;
    var Existe = 0;
    for(i=0;i<=data.length;i++){
        if(dv == data[i]){
            Existe = 1;
        }
    }
    if(Existe == 0){
        data.push(dv);
        nombre.push(cliente);
    }else{
//console.log('Existe');
    }
    TrueChange("btn-"+dv);
    var html = viewblock2();
    document.getElementById("gridview").innerHTML = html;
}

function viewblock2(){
    var html = "";
    html += "<form name=ListUser method=post id=ListUser>";
    html += "<div class='listView'>";
    var nro = 1;
    for (i=0;i<=data.length -1;i++){
        if(data[i]){
            html += "<div class=blockUser>" +
                       "<div class='datauser'>"+ nro +"</div>" +
                       "<div class='datauser'><input type='hidden' value="+ data[i] +"  name='user[]'> "+ nombre[i] +"</div>" +
                       "<div class='datauser'><a href=# onclick=closeblock2("+ i +");> X</a></div>" +
                     "</div>";
            nro++;
        }
    }
    html += "</div>";
    html += "</form>";
    return html;
}

function closeblock2(id){
    falseChange("btn-"+data[id]);
    delete data[id];
    var html = viewblock2();
    document.getElementById("gridview").innerHTML = html;

}

function addGroup2(formid){
    var Formulario=document.getElementById(formid);
    var users = [];
    var nombres = [];
    var form_elements=Formulario.elements;
    var Existe = 0;
    var dv;
    var nam;


    for(var i=0;i<form_elements.length;i++){
        var elem=form_elements[i],responseValue,success=true;
        if(elem.getAttribute('data-CBI')!==true && elem.name){
            switch(elem.type){
                case "hidden":
                    if (elem.name == 'users[]') {
                        users.push(elem.value);
                    };
                    if (elem.name == 'names[]') {
                        nombres.push(elem.value);
                    };
                    break;
            }
        }
    }
    for(j=0;j<=users.length;j++){
        dv = users[j];
        nam = nombres[j];
       // console.log(dv);

        var Existe = 0;
        for(i=0;i<=data.length;i++){
            if(dv == data[i]){
                Existe = 1;
            }
        }
        if(Existe == 0){
            Existe = 0;
            TrueChange("btn-"+dv);
            data.push(dv);
            nombre.push(nam);
        }else{
          //  console.log('Existe');
        }

    }

    var html = viewblock2();
    document.getElementById("gridview").innerHTML = html;
   // console.log(data);

}

function Clearblock2(){
   
    var dv = "";
    for(i=0;i<=data.length;i++){
       
        dv = data[i];
        delete data[i];
        

        delete nombre[i];
       

        if(dv){
            //console.log("btn-"+dv);
            falseChange("btn-"+dv);
    

        }

    }

    var html = viewblock2();
    if(document.getElementById("gridview")){
        document.getElementById("gridview").innerHTML = html;
    }

}

/*********var html = viewblock(); document.getElementById("gridview").innerHTML = html;***********/
function _AppUserEmail() {
    
    var a = this;
    a.user = [],    
    a.cuadro = "";
    a.Existe = 0;
    a.addUser = function(user){

        //console.log(0)
    

        for(i=0;i<=a.user;i++){
          //  console.log(1)
            if(user == a.user[i]){
                a.Existe = 1;
            }            
        }
    

       // console.log(2)
        if(a.Existe == 0){ a.user.push(user);  }else{ console.log('Existe') }                                  
        //a.user.push(user);  
       // console.log(a.user);       
    },    
    a.deleteUser = function(id){        
        delete a.user[id];
        
    },
    a.viewUser = function(){

        a.cuadro = "";
        a.cuadro += "<form name=ListUser method=post id=ListUser>";
        a.cuadro += "<div class='listView'>";        
        
        var nro = 1;

        a.cuadro += "<div >" +              
                     "<textarea rows=15 cols=100% style='min-width:99%;'>";
                     
        for (i=0;i<=a.user.length -1;i++){
            if(a.user[i]){
                a.cuadro +=  a.user[i]+";";
                  
                nro++;
            }
        }
        a.cuadro +="</textarea>"+
        "</div>";
       
        a.cuadro += "</div>";
        a.cuadro += "</form>";
        // console.log(a.cuadro);
        return a.cuadro;       
    }




}