<?php

function pestanasLocal($arg){
		
	$menu .= "Articulos]./_vistas/se_tienda.php?Tienda=Site]panelB]Marca}";
	$menu .= "Categorías]./_vistas/se_tienda_sector.php?SectorProducto=Site]panelB}";
	// $menu .= "Tipo]./_vistas/se_tienda_tipo_producto.php?TipoProducto=Site]panelB}";
	// $menu .= "Linea]./_vistas/se_tienda_linea_producto.php?LineaProducto=Site]panelB}";
	// $menu .= "Familia]./_vistas/se_tienda_familia_producto.php?FamiliaProducto=Site]panelB}";
	
	$pestanas = menuHorizontal($menu, 'menuV1','menuV01',$arg);
	return $pestanas;
}

function pestanasBLocal($arg){
	$menu .= "General]./_vistas/se_tienda.php?Tienda=EditaArticulos{$segmentoUrl}]panelFormA1]Marca}";
	$menu .= "Presentación]./_vistas/se_tienda.php?Tienda=EditaArticulosPresentacion{$segmentoUrl}]panelFormA1}";
	$menu .= "Estructura]./_vistas/se_curricula.php?Curricula=Site{$segmentoUrl}]panelFormA1}";
	$menu .= "Adicionales]./_vistas/se_ventas.php?Ventas=Site{$segmentoUrl}]panelFormA1}";
	$pestanas = menuHorizontalPopup($menu, 'menuV1','menuV02',$arg);
	return $pestanas;	
}

function pestanasBLocalEbook($arg){
	$menu .= "General]./_vistas/se_tienda.php?Tienda=EditaArticulos{$segmentoUrl}]panelFormA1]Marca}";
	$menu .= "Presentación]./_vistas/se_tienda.php?Tienda=EditaArticulosPresentacion{$segmentoUrl}]panelFormA1}";
	// $menu .= "Capítulos]./_vistas/se_articulo_capitulos.php?Main=EditarRegistro{$segmentoUrl}]panelFormA1}";
	// $menu .= "Estructura]./_vistas/se_curricula.php?Curricula=Site{$segmentoUrl}]panelFormA1}";	
	$menu .= "Adicionales]./_vistas/se_tienda_adicionales.php?Main=Principal{$segmentoUrl}]panelFormA1}";
	$pestanas = menuHorizontalPopup($menu, 'menuV1','menuV02',$arg);
	return $pestanas;	
}


function pestanasPerfilLocal($arg){
	$menu .= "Datos del Perfil]./_vistas/se_user_d_personales.php?userPerfil=EditaPerfil{$segmentoUrl}]panelFormA1]Marca}";
	$menu .= "Foto]./_vistas/se_user_d_personales.php?userPerfil=EditarFoto{$segmentoUrl}]panelFormA1}";
	$pestanas = menuHorizontalPopup($menu, 'menuV1','menuV02',$arg);
	return $pestanas;	
}

function jsCategorias(){
	
	
	$script =" <script>
				$('#Lineaarticulo_liform select').on('focus', '', function (e) {
						
                        busqueda_combobox_subcategoria();
                 });
								 
				function busqueda_combobox_subcategoria() {

					var codigo_categoria = $('#SectorArticulo_liform select').val();
					var ruta = '/system/_vistas/se_tienda.php?CategoriasSearch=Categoria';

					var parametros = {
							'codigo_categoria': codigo_categoria
						};
	                    $('#Lineaarticulo_liform select').html('<option value=cargando>Cargando Datos..</option>');
						$.ajax({
							data: parametros,
							url: ruta,
							type: 'get',
							success: function(response) {
								console.log('resposen '+response);
								$('#Lineaarticulo_liform select').html(response);
								$('#Lineaarticulo_liform select').fadeIn(1500);
							}
						});

				}
				

				</script> ";
			
			
			
	return $script;
}

?>
