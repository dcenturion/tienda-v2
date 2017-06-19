<?php
class ClsCaptcha {
    var $codigo;
    
    function generate_code(){
        session_start();
        $patron=session_id();
        //substr($str,a,b); a,posicion,b,numero de caracteres
        $d1=  substr($patron,mt_rand(1,strlen($patron)-1),1);
        $d2=  substr($patron,mt_rand(1,strlen($patron)-1),1);
        $d3=  substr($patron,mt_rand(1,strlen($patron)-1),1);
        $d4=  substr($patron,mt_rand(1,strlen($patron)-1),1);
        $d5=  substr($patron,mt_rand(1,strlen($patron)-1),1);
        $d6=  substr($patron,mt_rand(1,strlen($patron)-1),1);
        $this->codigo="{$d1}{$d2}{$d3}{$d4}{$d5}{$d6}";
    }
    function graph_captcha(){
        header("Content-type:image/jpg");
        //Almacenamos la imagen base del captcha
        $captchaimagen=imagecreatefromjpeg("captcha.jpg");
        //Definimos un color para el texto
        $negro=imagecolorallocate($captchaimagen,0,0,0);
        $negroB=imagecolorallocate($captchaimagen,255,255,255);
        //Definimos un color de linea
        $plomo=imagecolorallocate($captchaimagen,0,0,0);
        $verde=imagecolorallocate( $captchaimagen, 15, 103, 103 );
        //Recuperamos el parametro tamaño de imagen
        $imageninfo=  getimagesize("captcha.jpg");
        //Lineas a dibujar
        $num_lineas=10;
        //Añadimos lineas de forma aleatoria
        for($i=0;$i<$num_lineas;$i++){
            //Utilizamos la funcion mt_rand()
            //mt_rand(a,b) -> a: numero inicio -> b:numero limite
            $x_inicio=  mt_rand(0,$imageninfo[0]);
            $x_fin=  mt_rand(0,$imageninfo[0]);
            //Dibujamos la linea en el capcha
            imageline($captchaimagen, $x_inicio, 0, $x_fin, $imageninfo[0], $plomo);
            //imageline($imagen,$x1,$y1,$x2,$y2,$color);
        }
        imageellipse($captchaimagen,30,30, 100, 80,$negro);
        imagettftext($captchaimagen,20,5,60,40,$negroB,'../fonts/segoeuisl.ttf',$this->codigo); 
        //imagettftext($imagen,$tamaño,$angulo,$x,$y,$color,$archivottf,$texto);
        imagejpeg($captchaimagen);
        imagedestroy($captchaimagen);
        $_SESSION['id_captcha']=$this->codigo;
        
        /*
            Escribimos nuestro string aleatoriamente, utilizando una fuente true type. 
            En este caso, estamos utilizando BitStream Vera Sans Bold, pero podemos utilizar cualquier otra.
            imagettftext( $captchaImage, 20, 0, 35, 35, $textColor, “fonts/VeraBd.ttf”, $key );
            
            header ( “Content-type: image/png” );
            header(”Cache-Control: no-cache, must-revalidate”);
            header(”Expires: Fri, 19 Jan 1994 05:00:00 GMT”);
            header(”Pragma: no-cache”);
            imagepng( $captchaImage );         
         */
    }
}
?>
