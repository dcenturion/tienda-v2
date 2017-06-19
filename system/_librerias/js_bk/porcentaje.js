function porcentaje(AlmacenCurso,Cod_Curso) {
    $.ajax({
        type: 'GET',
        url: '/system/_vistas/gad_room.php',
        data: 'Porcentaje=Ver&AlmacenCurso='+AlmacenCurso+'&Curso='+Cod_Curso+'',
        dataType: 'json',
        success: function (data) {
            if(data.success){
                if(data.data != 0) {
                    $(".progress").attr("style", 'display:block');
                    $(".progress > .progress-bar").attr("aria-valuenow", data.data);
                    $(".progress > .progress-bar").attr("style", 'width:' + data.data + '%');
                    $(".progress-bar > .sr-only").html('' + data.data + '%');
                }
            }
        },
        error: function (jqXHR, exception) {
            console.log(jqXHR.responseText);
        }
    });
}

function porcentajeAsync(AlmacenCurso,Cod_Curso, cb) {
    $.ajax({
        type: 'GET',
        url: '/system/_vistas/gad_room.php',
        data: 'Porcentaje=Ver&AlmacenCurso='+AlmacenCurso+'&Curso='+Cod_Curso+'',
        dataType: 'json',
        success: function (data) {
            if(data.success){
                if(data.data != 0) {
                    $(".progress").attr("style", 'display:block');
                    $(".progress > .progress-bar").attr("aria-valuenow", data.data);
                    $(".progress > .progress-bar").attr("style", 'width:' + data.data + '%');
                    $(".progress-bar > .sr-only").html('' + data.data + '%');
                    cb(true);
                }
            }
        },
        error: function (jqXHR, exception) {
            console.log(jqXHR.responseText);
            cb(false);
        }
    });
}