function prettyDate(time) {
    var date = new Date(time);
    
    //Valid if is a Date
    if(isNaN(date.getTime())) return "Hace un momento";
    
    var curDate = new Date(),
    diff = Math.floor((curDate.getTime() - date.getTime()) / 1000),
    minute_diff = Math.floor(diff / 60),
    hour_diff = Math.floor(minute_diff / 60),
    day_diff = Math.floor(hour_diff / 24),
    week_diff = Math.floor(day_diff / 7),
    month_diff = Math.floor(week_diff / 4);
    
    if(diff < 60){
        return "Hace un momento";
    }else if(minute_diff < 60){
        return "Hace " + minute_diff + ((minute_diff === 1)? " minuto" : " minutos");
    }else if(hour_diff < 24){
        return "Hace " + hour_diff + ((hour_diff === 1)? " hora" : " horas");
    }else if(day_diff < 7){
        return "Hace " + day_diff + ((day_diff === 1)? " día" : " días");
    }else if(week_diff < 4){
        return "Hace " + week_diff + ((week_diff === 1)? " semana" : " semanas");
    }else if(month_diff < 12){
        return "Hace " + month_diff + ((month_diff === 1)? " mes" : " meses");
    }else{
        return "Hace " + Math.floor(month_diff / 12) + " años";
    }
}