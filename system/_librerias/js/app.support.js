function supportApp(incidenciaid){
    var $supportApp = $("#support-app"),
    $supportDetail = $("#support-detail");
    
    //detail functions
    function getActivities(){
        var incidenciaid = $supportDetail.find(".support-detail").attr("data-incidenciaid");
        
        $.get("/system/_vistas/gad_reclamos.php?Incidencia=detail_activities", {incidenciaid: incidenciaid}, function(html){
            $supportDetail.find(".support-detail-item-container").empty().html(html);
        });
    }
    
    function renderDetail(html){
        $supportDetail.html(html);
        
        $supportDetail.addClass("show");
        
        getActivities();
        
        $supportDetail.find("button[data-left]").click(function(){
            getList();
            $supportDetail.removeClass("show");
        });
        $supportDetail.find("button[data-update]").click(getActivities);
        $supportDetail.find("button[data-close]").click(function(){
            $.get("/system/_vistas/gad_reclamos.php?Incidencia=cerrar_incidencia", {incidenciaid: $(this).attr("data-incidenciaid")}, function(success){
                success && $supportDetail.find("button[data-left]").click();
            }, "json");
        });
        $supportDetail.find("button[data-reopen]").click(function(){
            var incidenciaid = $(this).attr("data-incidenciaid");
            
            $.get("/system/_vistas/gad_reclamos.php?Incidencia=reabrir_incidencia", {incidenciaid: incidenciaid}, function(success){
                success && $supportApp.find(".support-list-item button[data-incidenciaid=" + incidenciaid +  "]").click();
            }, "json");
        });
        $supportDetail.find("form").on("submit", function(e){
            e.preventDefault();
            
            sendForm("/system/_vistas/gad_reclamos.php?Incidencia=registrar_actividad", this, function(success){
                success && $supportDetail.find("form textarea").val("").focus() && getActivities();
            }, "json");
        });
    }
    //end detail functions
    
    function getList(callback){
        $.get("/system/_vistas/gad_reclamos.php?Incidencia=list", function(html){
            $supportApp.find(".support-list-item-container").empty().html(html);
            
            $supportApp.find(".support-list-item button").click(function(){
                $.get("/system/_vistas/gad_reclamos.php?Incidencia=detail_incidencia", {incidenciaid: $(this).attr("data-incidenciaid")}, renderDetail);
            });
            
            if(typeof callback === "function"){
                callback();
            }
        });
    }
    
    function renderDashBoard(){
        $supportApp.find("button[support-form]").click(function(){
            $.post("/system/_vistas/gad_reclamos.php?Incidencia=list_programs", function(html){
                new $.Popup({
                    content     : $("<div>").html(html),
                    afterOpen   : function($popup){
                        var popup = this;
                        
                        $popup.find("li").bind("click", {popup: popup}, selectProgram);
                    }
                }).open();
            });
        });
        
        $supportApp.find("input[support-search]").on("keyup", function(e){
            var text = $(this).val().toLowerCase();
            
            $supportApp.find(".support-list-item").each(function(){
                if(~$(this).find(".s-l-i-d-text").text().toLowerCase().indexOf(text)){
                    $(this).show();
                }else{
                    $(this).hide();
                }
            });
        });
        $supportApp.find("input[support-search]").on("keydown", function(e){
            if(e.keyCode === 27){
                $(this).val("");
            }
        });
    }
    
    function selectProgram(e){
        e.data.popup.close();
        
        var dataProgram = $(this).attr("data-program");
        
        $.get("/system/_vistas/gad_reclamos.php?Incidencia=list_courses", {dataProgram: dataProgram} , function(html){
            new $.Popup({
                dataProgram : dataProgram,
                content     : $("<div>").html(html),
                afterOpen   : function($popup){
                    var popup = this;
                    
                    $popup.find("li").bind("click", {popup: popup}, selectCourse);
                } 
            }).open();
        });
    }
    
    function selectCourse(e){
        e.data.popup.close();
        
        var dataCourse = $(this).attr("data-course");
        
        $.get("/system/_vistas/gad_reclamos.php?Incidencia=form", {
            dataProgram : e.data.popup.o.dataProgram,
            dataCourse  : dataCourse
        }, renderForm);
    }
    
    function renderForm(html){
        var SELECT_AREA = 0,
        SELECT_CATEGORY = 1;
        
        new $.Popup({
            content     : $("<div>").html(html),
            afterOpen   : function($popup){
                var popup = this;
                
                var $selectArea = $popup.find("select").eq(SELECT_AREA),
                $selectCategory = $popup.find("select").eq(SELECT_CATEGORY);

                function makeCategoryOptions(){
                    var area = $selectArea.val();

                    $.get("/system/_vistas/gad_reclamos.php?Incidencia=list_incidencia_categoria_area", { area: area }, function(html){
                        $selectCategory.html(html);
                    });
                }
                
                function send(e){
                    e.preventDefault();
                    e.data.popup.close();
                    
                    sendForm("/system/_vistas/gad_reclamos.php?Incidencia=registrar_incidencia", this, function(success){
                        success && getList();
                    }, "json");
                }
                
                $.get("/system/_vistas/gad_reclamos.php?Incidencia=list_incidencia_area", function(html){
                    $selectArea.html(html);
                    
                    makeCategoryOptions();
                });
                
                //events
                $selectArea.change(makeCategoryOptions);
                $popup.find("form").bind("submit", {popup: popup}, send);
            }
        }).open();
    }
    
    getList(function(){
        if(incidenciaid){
            $supportApp.find(".support-list-item button[data-incidenciaid=" + incidenciaid +  "]").click();
        }
    });
    renderDashBoard();
}