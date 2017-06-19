<?php
require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/conexiones.php');

$vConex = conexSys();

    function Slider(){
	
		$s = '                     <!-- start:sidebar --> '; 
		$s .= '                     <div id="sidebar"> '; 
		$s .= '                         <!-- start:section-module-timeline --> '; 
		$s .= '                         <section class="module-timeline"> '; 
		$s .= AreasdeConocimiento();
                                    $s .= '                         </section> '; 
		$s .= '                         <!-- end:section-module-timeline --> '; 

		$s .= '                         <!-- start:section-module-singles --> '; 
		$s .= '                         <section class="module-singles"> '; 
		$s .= Especialistas();
		$s .= '                         </section> '; 
		$s .= '                         <!-- end:section-module-singles --> '; 
		$s .= '                          '; 



		$s .= '                     </div> '; 
		$s .= '                     <!-- end:sidebar -->   '; 
        return $s;
	}
        
  function AreasdeConocimiento(){
     global $vConex;    

        $s .= '                             <!-- start:header --> '; 
        $s .= '                             <header> '; 
        $s .= '                                 <h2>Areas de Conocimiento </h2> '; 
        $s .= '                                 <span class="borderline"></span> '; 
        $s .= '                             </header> '; 
        $s .= '                             <!-- end:header --> '; 
        
          
       $sqlA = 'SELECT  COUNT(*) AS TotReg, AR.Categoria'
                    . ' FROM almacen AS AL  '
                    . ' LEFT JOIN articulos AS AR  ON AR.Codigo = AL.Articulo '   
                    . ' LEFT JOIN tipoproducto AS TP  ON AR.TipoProducto = TP.Codigo '               
                    . ' GROUP BY AR.Categoria';    
        
        $sql = 'SELECT B.Nombre as Nombre, CS1.TotReg' 
                .', B.Codigo '
                .', B. Categoria '
                .' FROM menu_nivel_b AS B'
                .' LEFT JOIN menu_nivel_a AS A ON A.Codigo = B.menu_nivel_a '
                .' LEFT JOIN ('.$sqlA.') AS CS1 ON B.Categoria = CS1.Categoria  '             
                . ' WHERE B.Estado = "Activo" AND A.Nombre = "ÁREAS DE CONOCIMIENTO" '
                . ' ORDER BY B.Orden Asc';
                
                $consulta = Matris_Datos($sql,$vConex);
                
                while ($reg =  mysql_fetch_array($consulta)) {
                    
                            $s .= '                             <!-- start:articles --> '; 
                            $s .= '                             <div class="articles"> '; 
                            $s .= '                                 <article> '; 
                             if($reg["TotReg"]!=""){
                            $s .= '                                     <span class="published">('.$reg["TotReg"].')</span> '; 
                             }else{
                             $s .= '                                     <span class="published">(0)</span> '; 
                             }
                            $s .= '                                     <span class="published-time">    </span> '; 
                            $s .= '                                     <div class="cnt"> '; 
                            $s .= '                                         <i class="bullet bullet-business" style="background: #51a3ff;"></i> '; 
                            $s .= '                                         <span class="category cat-business"><a href="#" onclick=traeDatos("./_vistas/view_productos.php?CuerpoA=Site&Busqueda=Categoria&Categoria='.$reg["Categoria"].'","cuerpo",true); style="color:#fff;">'.$reg["Nombre"].'</a></span> '; 
                            $s .= '                                         <h3><a href="#">   </a></h3> <br>'; 
                            $s .= '                                     </div>                                 '; 
                            
                            $s .= '                                 </article> '; 
                            $s .= '                             </div> '; 
                            $s .= '                             <!-- end:articles --> '; 
                            
                            
                            
                }

         return $s; 
}      

function Especialistas(){
global $vConex;      
                                    
            $s .= '                             <!-- start:header --> '; 
            $s .= '                             <header> '; 
            $s .= '                                 <h2>ESPECIALISTAS</h2> '; 
            $s .= '                                 <span class="borderline"></span> '; 
            $s .= '                             </header> '; 
            $s .= '                             <!-- end:header --> '; 
            $s .= '                              '; 
            $s .= '                             <!-- start:singles-container --> '; 
            $s .= '                             <ul class="singles-container"> '; 
            
                        $sql = "SELECT Nombres  ";
                        $sql .= ", Apellidos ";
                        $sql .=" , Codigo   ";            
                        $sql .=" FROM autores  ";

                        $consulta = Matris_Datos($sql,$vConex);
                        while ($reg =  mysql_fetch_array($consulta)) {  
                            $s .= '                                 <li> '; 
                            $s .= '                                     <span class="glyphicon glyphicon-play-circle"></span> '; 
                            $s .= '                                     <a href="#" onclick=traeDatos("./_vistas/view_especialistas.php?CuerpoA=Site&Busqueda=Autor&Autor='.$reg["Codigo"].'","cuerpo",true); >'.$reg["Apellidos"].'</a> '; 
                            $s .= '                                     <span class="author">'.$reg["Nombres"].'</span> '; 
                            $s .= '                                 </li> '; 
                        }
            
            $s .= '                             </ul> '; 
            $s .= '                             <!-- end:singles-container --> ';     

        return $s; 
}
?>
