<?php
##
function P_CrearUser() {
    global $vConex, $enlace, $entidadCreadora, $ipHost;
	
    $email = trim(post('Usuario'));
    $nombres = post('Nombres');
    $apellidos = post('Apellidos');
    $Perfil = post('Perfil');
    $FechaNacimiento = post('FechaNacimiento');
    $Genero = post('Genero');
    $Area = post('Area');
    $Cargo = post('Cargo');
    $Alumno = post('Alumno');
    $Profesor = post('Profesor');
    $Estado = post('Estado');
    $Sede = post('Sede');
    $Escuela = post('Escuela');
    $EnvEmail = post('enviar_email');
    $Auxiliar = post('Auxiliar');
    $IDUsuario = post('IDUsuario');

    $$corporativo_estado = 0;

    if ($IDUsuario != '') {
        $CPvalidado = ValidarCodigoParlante($IDUsuario,'',$vConex);
        if ($CPvalidado) {
            
        }else{
            $IDUsuario='';
            $corporativo_estado = 1;
            
        }
    }

    $SedeSQL="SELECT SC.Codigo,SC.UnidadNegocio 
        FROM sede_sucursal AS SC 
        INNER JOIN  sedes  AS SD ON SC.UnidadNegocio=SD.Codigo 
        WHERE SC.Codigo = " . $Sede . " ";
    $rg = fetch($SedeSQL);
    $Empresa = $rg["UnidadNegocio"];

    //$password = GeneratePassword();
    $secure_password= "pass123";
    $password=_crypt($secure_password);
    
    ### Fya

    if (empty($email)) {
        W(MsgCR("(!) El campo email es obligatorio<br>"));
        CrearUsuario('CrearUser');
        WE("");
    }else{
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "(!) Invalid email format";
            W(Msg("$emailErr<br>","E"));
            CrearUsuario('CrearUser');
            WE("");
        }
    }
    if (empty($nombres)) {
        W(MsgCR("(!) El campo nombres  es obligatorio<br>"));
        CrearUsuario('CrearUser');
        WE("");
    }
    if (empty($apellidos)) {
        W(MsgCR("(!) El campo apellidos  es obligatorio<br>"));
        CrearUsuario('CrearUser');
        WE("");
    }

    $sql = " SELECT tab2.IdUsuario ,tab2.Usuario FROM 
        usuario_entidad AS tab1 LEFT JOIN usuarios AS tab2
        ON tab1.Usuario = tab2.Usuario
        WHERE 
        tab2.CodigoParlante = '" . $email . "'  AND tab1.EntidadCreadora = '" . $entidadCreadora . "' 
        GROUP BY tab2.Usuario ";
    $rg = fetch($sql);

    if (empty($rg["Usuario"])) {

        $sqlUser = "SELECT Usuario FROM usuarios WHERE Usuario = '" . $email . "' ";
        $rg = fetch($sqlUser);
        $UsuarioTab = $rg["Usuario"];

        if (empty($UsuarioTab)) {
            #insertar usuario
            $nombresStringClean = str_replace('.', '', limpiarAcentos($nombres));
            $apellidosStringClean = str_replace('.', '', limpiarAcentos($apellidos));
            $nombreCarpeta = $nombresStringClean . $apellidosStringClean;
            $urlId = $nombresStringClean . '.' . $apellidosStringClean;

            $sqlCarpeta = "SELECT COUNT( Carpeta ) AS count FROM usuarios WHERE Carpeta = '$nombreCarpeta'";
            $dataUsuarioCarpeta = fetchOne($sqlCarpeta, $vConex);

            if ($dataUsuarioCarpeta->count > 0) {
                $patron = $dataUsuarioCarpeta->count + 1;
                $nombreCarpeta = $nombreCarpeta . $patron;
                $urlId = $nombresStringClean . "." . $apellidosStringClean . "." . $patron;
            }

            $pathCarpetaAlumno = BASE_PATH . 'ArchivosAlumnos' . DS . $nombreCarpeta;
            $pathCarpetaProfesor = BASE_PATH . 'ArchivosProfesor' . DS . $nombreCarpeta;
            

            creaCarpeta($pathCarpetaAlumno);
            creaCarpeta($pathCarpetaProfesor);
            
            $AuxpathCarpetaAlumno = "ArchivosAlumnos/$nombreCarpeta/file_aux.txt";
            $AuxpathCarpetaProfesor =  "ArchivosProfesor/$nombreCarpeta/file_aux.txt";
            
            fopen(BASE_PATH . $AuxpathCarpetaAlumno, "w");
            fopen(BASE_PATH . $AuxpathCarpetaProfesor, "w");
            
            upload_file_to_S3(BASE_PATH . $AuxpathCarpetaAlumno,$AuxpathCarpetaAlumno);
            upload_file_to_S3(BASE_PATH . $AuxpathCarpetaProfesor,$AuxpathCarpetaProfesor);

            #insertamos usuario como alumno
            $idUsuario = $email . 'Alumno';
            ingresaNewUser($email, $idUsuario, $nombres, $apellidos, $nombreCarpeta, $urlId, $vConex, 'Alumno', $password, $FechaNacimiento, $Genero,$IDUsuario);

            #insertamos usuario como profesor
            $idUsuario = $email . 'Profesor';
            ingresaNewUser($email, $idUsuario, $nombres, $apellidos, $nombreCarpeta, $urlId, $vConex, 'Profesor', $password, $FechaNacimiento, $Genero,$IDUsuario);

            #insertamos en la tabla Alumno
            ingresaNewPerson($email, $nombres, $apellidos, $FechaNacimiento, $Genero, 'Alumno', $vConex);
            #insertamos en la tabla Profesor
            ingresaNewPerson($email, $nombres, $apellidos, $FechaNacimiento, $Genero, 'Profesor', $vConex);
        }

        #insertamos en la tabla Usuario Entidad
        $dataInsertUsuarioEntidad = array(
            'Usuario' => $email,
            'Perfil' => $Perfil,
            'Genero' => $Genero,
            'FechaNacimiento' => $FechaNacimiento,
            'Nombres' => $nombres,
            'Apellidos' => $apellidos,
            'Profesor' => $Profesor,
            'Alumno' => $Alumno,
            'Estado' => $Estado,
            'Empresa' => $Empresa,
            'Area' => $Area,
            'cargo_id' => $Cargo,
            'Sede' => $Sede,
            'Escuela' => $Escuela,
            'EntidadCreadora' => $entidadCreadora,
            'enviar_email' => $EnvEmail,
            'ForzarContrasena' => 'Si',
            'GuiaEstudio' => 'Si',
            'Auxiliar' => $Auxiliar
        );

        $usuarioEntidadId = insert('usuario_entidad', $dataInsertUsuarioEntidad, $vConex);

        if($EnvEmail==1){
             enviar_mail_usuario2($email, $vConex,$secure_password,$entidadCreadora);
        }

        W(MsgCR(" (!) El Usuario fue registrado " . $email));
    } else {

        W(MsgCR("(!) Este usuario ya se encuentra registrado "));
        $corporativo_estado = 0;
    }
    if ($corporativo_estado == 1) {
        W(Msg("(!) El Codigo Corporativo ya se encuentra registrado","E"));
        W(Msg("(!) Puede Actualizar el Codigo Corporativo mas Adelante","E"));
    }
    return $usuarioEntidadId;
}

function P_CrearUserAlumno($AlmacenPrograma=null) {
    global $vConex, $enlace, $entidadCreadora,$Sede;
    $CodAlmacen=get('productoId');
    $email = trim(post('Usuario'));
    $nombres = post('Nombres');
    $apellidos = post('Apellidos');
	
    $Perfil = 3;
	$Estado='activo';
    $Area = post('Area');
    $Alumno = post('Alumno');
    $Profesor = post('Profesor');
	if(post('Sede')){
		$Sede = post('Sede');
	}
    $Escuela = post('Escuela');
    $EnvEmail = post('enviar_email');
    $IDUsuario = post('IDUsuario');
    $IdJefe = post('IdJefe');

    $$corporativo_estado = 0;

    if ($IDUsuario != '') {
        $CPvalidado = ValidarCodigoParlante($IDUsuario,'',$vConex);
        if ($CPvalidado) {
            
        }else{
            $IDUsuario='';
            $corporativo_estado = 1;
            
        }
    }


    if(!$IdJefe){
        $IdJefe = "";
        
    }

    $SedeSQL="SELECT SC.Codigo,SC.UnidadNegocio
        FROM sede_sucursal AS SC
        INNER JOIN  sedes  AS SD ON SC.UnidadNegocio=SD.Codigo
        WHERE SC.Codigo = " . $Sede . " ";
    $rg = fetch($SedeSQL);
    $Empresa = $rg["UnidadNegocio"];
    $secure_password= "pass123";
    $password=_crypt($secure_password);

    if (empty($email)) {
        W(MsgCR("(!) El campo email es obligatorio<br>"));
        $corporativo_estado = 0;
        CrearUsuario('CrearUser');
        WE("");
    }else{
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "(!) Invalid email format";
            $corporativo_estado = 0;
            W(Msg("$emailErr<br>","E"));
            CrearUsuario('CrearUser');
            WE("");
        }
    }
    if (empty($nombres)) {
        W(MsgCR("(!) El campo nombres  es obligatorio<br>"));
        $corporativo_estado = 0;
        CrearUsuario('CrearUser');
        WE("");
    }
    if (empty($apellidos)) {
        W(MsgCR("(!) El campo apellidos  es obligatorio<br>"));
        $corporativo_estado = 0;
        CrearUsuario('CrearUser');
        WE("");
    }


    $sqlParlante = " SELECT  tab2.IdUsuario ,tab2.Usuario,tab2.CodigoParlante FROM
                     usuario_entidad AS tab1 LEFT JOIN usuarios AS tab2
                     ON tab1.Usuario = tab2.Usuario
                     WHERE tab2.CodigoParlante = '{$IDUsuario}'  AND tab1.EntidadCreadora = '{$entidadCreadora}'
                     GROUP BY tab2.Usuario ";
    
    $rgPA = fetch($sqlParlante);
    if($rgPA['CodigoParlante']){        
        return "Usuario";
    }

    $sql = " SELECT tab2.IdUsuario ,tab2.Usuario FROM
        usuario_entidad AS tab1 LEFT JOIN usuarios AS tab2
        ON tab1.Usuario = tab2.Usuario
        WHERE
        tab2.Usuario = '{$email}'  AND tab1.EntidadCreadora = '{$entidadCreadora}'
        GROUP BY tab2.Usuario ";
    $rg = fetch($sql);

    if (empty($rg["Usuario"])) {

        $sqlUser = "SELECT Usuario FROM usuarios WHERE Usuario = '" . $email . "' ";
        $rg = fetch($sqlUser);
        $UsuarioTab = $rg["Usuario"];

        if (empty($UsuarioTab)) {
            #insertar usuario
            $nombresStringClean = str_replace('.', '', limpiarAcentos($nombres));
            $apellidosStringClean = str_replace('.', '', limpiarAcentos($apellidos));
            $nombreCarpeta = $nombresStringClean . $apellidosStringClean;
            $urlId = $nombresStringClean . '.' . $apellidosStringClean;

            $sqlCarpeta = "SELECT COUNT( Carpeta ) AS count FROM usuarios WHERE Carpeta = '$nombreCarpeta'";
            $dataUsuarioCarpeta = fetchOne($sqlCarpeta, $vConex);

            if ($dataUsuarioCarpeta->count > 0) {
                $patron = $dataUsuarioCarpeta->count + 1;
                $nombreCarpeta = $nombreCarpeta . $patron;
                $urlId = $nombresStringClean . "." . $apellidosStringClean . "." . $patron;
            }

            $pathCarpetaAlumno = BASE_PATH . 'ArchivosAlumnos' . DS . $nombreCarpeta;
            $pathCarpetaProfesor = BASE_PATH . 'ArchivosProfesor' . DS . $nombreCarpeta;


            creaCarpeta($pathCarpetaAlumno);
            creaCarpeta($pathCarpetaProfesor);

            $AuxpathCarpetaAlumno = "ArchivosAlumnos/$nombreCarpeta/file_aux.txt";
            $AuxpathCarpetaProfesor =  "ArchivosProfesor/$nombreCarpeta/file_aux.txt";

            fopen(BASE_PATH . $AuxpathCarpetaAlumno, "w");
            fopen(BASE_PATH . $AuxpathCarpetaProfesor, "w");

            upload_file_to_S3(BASE_PATH . $AuxpathCarpetaAlumno,$AuxpathCarpetaAlumno);
            upload_file_to_S3(BASE_PATH . $AuxpathCarpetaProfesor,$AuxpathCarpetaProfesor);

            #insertamos usuario como alumno
            $idUsuario = $email . 'Alumno';
            ingresaNewUser($email, $idUsuario, $nombres, $apellidos, $nombreCarpeta, $urlId, $vConex, 'Alumno', $password, $FechaNacimiento, $Genero,$IDUsuario,$IdJefe);

            #insertamos usuario como profesor
            $idUsuario = $email . 'Profesor';
            ingresaNewUser($email, $idUsuario, $nombres, $apellidos, $nombreCarpeta, $urlId, $vConex, 'Profesor', $password, $FechaNacimiento, $Genero,$IDUsuario,$IdJefe);

            #insertamos en la tabla Alumno
            ingresaNewPerson($email, $nombres, $apellidos, $FechaNacimiento, $Genero, 'Alumno', $vConex);
            #insertamos en la tabla Profesor
            ingresaNewPerson($email, $nombres, $apellidos, $FechaNacimiento, $Genero, 'Profesor', $vConex);
        }

        #insertamos en la tabla Usuario Entidad
        $dataInsertUsuarioEntidad = array(
            'Usuario' => $email,
            'Perfil' => $Perfil,
            'Genero' => $Genero,
            'FechaNacimiento' => $FechaNacimiento,
            'Nombres' => $nombres,
            'Apellidos' => $apellidos,
            'Profesor' => $Profesor,
            'Alumno' => $Alumno,
            'Estado' => $Estado,
            'Empresa' => $Empresa,
            'Area' => $Area,
            'cargo_id' => $Cargo,
            'Sede' => $Sede,
            'Escuela' => $Escuela,
            'EntidadCreadora' => $entidadCreadora,
            'enviar_email' => $EnvEmail,
            'ForzarContrasena' => 'Si',
            'GuiaEstudio' => 'Si'
        );

        $usuarioEntidadId = insert('usuario_entidad', $dataInsertUsuarioEntidad, $vConex);

        if($EnvEmail==1){
           
            $emaill= array('correo'=>$email,'CodAlmacen'=>$CodAlmacen);
            enviar_mail_usuario2($emaill, $vConex,$secure_password,$entidadCreadora);
        }

        W(MsgCR(" (!) El Usuario fue registrado  " . $email));
    } else {

        W(MsgCR(" (!) El Usuario fue registrado anteriormente " . $email));
        W(MsgCR(" (!) Vuelva a Intentarlo "));
        $corporativo_estado = 0;
        $DataUpdate = array(
            'Estado' => 'Matriculado',
            'MailLeido' => 'No Leido'
        );
        $correo=$email;
        $email=$email.'Alumno';
        $DataWhere = array(
            'Producto' => $CodAlmacen,
            'Cliente' => $email
        );
        update('matriculas', $DataUpdate, $DataWhere);
        $usrEntidadUDP="select * from usuario_entidad where usuario like '%$correo%' and EntidadCreadora='$entidadCreadora'";
        $ftch=fetchOne($usrEntidadUDP);
        $Codigo=$ftch->Codigo;
       
        $usuarioEntidadId=array('success'=>true,'lastInsertId'=>( int )$Codigo);

    }
    if ($corporativo_estado == 1) {
         W(Msg("(!) El Codigo Corporativo ya se encuentra registrado","E"));
        W(Msg("(!) Puede Actualizar el Codigo Corporativo mas Adelante","E"));
    }

    return $usuarioEntidadId;
}

function P_CrearUserProfesor() {
    global $vConex, $enlace, $entidadCreadora;
    $email = trim(post('Usuario'));
    $nombres = post('Nombres');
    $apellidos = post('Apellidos');
    $Perfil = 12;
    $Area = 14;
    $Alumno = post('Alumno');
    $Profesor = 'SI';
    $Sede = post('Sede');
    $Escuela = post('Escuela');
    $EnvEmail = post('enviar_email');

    $SedeSQL="SELECT SC.Codigo,SC.UnidadNegocio
        FROM sede_sucursal AS SC
        INNER JOIN  sedes  AS SD ON SC.UnidadNegocio=SD.Codigo
        WHERE SC.Codigo = " . $Sede . " ";
    $rg = fetch($SedeSQL);
    $Empresa = $rg["UnidadNegocio"];

    $secure_password= "pass123";
    $password=_crypt($secure_password);

    if (empty($email)) {
        W(MsgCR("(!) El campo email es obligatorio<br>"));
        CrearUsuario('CrearUser');
        WE("");
    }
    if (empty($nombres)) {
        W(MsgCR("(!) El campo nombres  es obligatorio<br>"));
        CrearUsuario('CrearUser');
        WE("");
    }
    if (empty($apellidos)) {
        W(MsgCR("(!) El campo apellidos  es obligatorio<br>"));
        CrearUsuario('CrearUser');
        WE("");
    }

    $sql = " SELECT tab2.IdUsuario ,tab2.Usuario FROM
        usuario_entidad AS tab1 LEFT JOIN usuarios AS tab2
        ON tab1.Usuario = tab2.Usuario
        WHERE
        tab2.CodigoParlante = '" . $email . "'  AND tab1.EntidadCreadora = '" . $entidadCreadora . "'
        GROUP BY tab2.Usuario ";
    $rg = fetch($sql);

    if (empty($rg["Usuario"])) {

        $sqlUser = "SELECT Usuario FROM usuarios WHERE Usuario = '" . $email . "' ";
        $rg = fetch($sqlUser);
        $UsuarioTab = $rg["Usuario"];

        if (empty($UsuarioTab)) {
            #insertar usuario
            $nombresStringClean = str_replace('.', '', limpiarAcentos($nombres));
            $apellidosStringClean = str_replace('.', '', limpiarAcentos($apellidos));
            $nombreCarpeta = $nombresStringClean . $apellidosStringClean;
            $urlId = $nombresStringClean . '.' . $apellidosStringClean;

            $sqlCarpeta = "SELECT COUNT( Carpeta ) AS count FROM usuarios WHERE Carpeta = '$nombreCarpeta'";
            $dataUsuarioCarpeta = fetchOne($sqlCarpeta, $vConex);

            if ($dataUsuarioCarpeta->count > 0) {
                $patron = $dataUsuarioCarpeta->count + 1;
                $nombreCarpeta = $nombreCarpeta . $patron;
                $urlId = $nombresStringClean . "." . $apellidosStringClean . "." . $patron;
            }

            $pathCarpetaAlumno = BASE_PATH . 'ArchivosAlumnos' . DS . $nombreCarpeta;
            $pathCarpetaProfesor = BASE_PATH . 'ArchivosProfesor' . DS . $nombreCarpeta;


            creaCarpeta($pathCarpetaAlumno);
            creaCarpeta($pathCarpetaProfesor);

            $AuxpathCarpetaAlumno = "ArchivosAlumnos/$nombreCarpeta/file_aux.txt";
            $AuxpathCarpetaProfesor =  "ArchivosProfesor/$nombreCarpeta/file_aux.txt";

            fopen(BASE_PATH . $AuxpathCarpetaAlumno, "w");
            fopen(BASE_PATH . $AuxpathCarpetaProfesor, "w");

            upload_file_to_S3(BASE_PATH . $AuxpathCarpetaAlumno,$AuxpathCarpetaAlumno);
            upload_file_to_S3(BASE_PATH . $AuxpathCarpetaProfesor,$AuxpathCarpetaProfesor);

            #insertamos usuario como alumno
            $idUsuario = $email . 'Alumno';
            ingresaNewUser($email, $idUsuario, $nombres, $apellidos, $nombreCarpeta, $urlId, $vConex, 'Alumno', $password, $FechaNacimiento, $Genero);

            #insertamos usuario como profesor
            $idUsuario = $email . 'Profesor';
            ingresaNewUser($email, $idUsuario, $nombres, $apellidos, $nombreCarpeta, $urlId, $vConex, 'Profesor', $password, $FechaNacimiento, $Genero);

            #insertamos en la tabla Alumno
            ingresaNewPerson($email, $nombres, $apellidos, $FechaNacimiento, $Genero, 'Alumno', $vConex);
            #insertamos en la tabla Profesor
            ingresaNewPerson($email, $nombres, $apellidos, $FechaNacimiento, $Genero, 'Profesor', $vConex);
        }

        #insertamos en la tabla Usuario Entidad
        $dataInsertUsuarioEntidad = array(
            'Usuario' => $email,
            'Perfil' => $Perfil,
            'Genero' => $Genero,
            'FechaNacimiento' => $FechaNacimiento,
            'Nombres' => $nombres,
            'Apellidos' => $apellidos,
            'Profesor' => $Profesor,
            'Alumno' => $Alumno,
            'Estado' => "activo",
            'Empresa' => $Empresa,
            'Area' => $Area,
            'cargo_id' => $Cargo,
            'Sede' => $Sede,
            'Escuela' => $Escuela,
            'EntidadCreadora' => $entidadCreadora,
            'enviar_email' => $EnvEmail
        );
        $usuarioEntidadId = insert('usuario_entidad', $dataInsertUsuarioEntidad, $vConex);

        if($EnvEmail==1){
            enviar_mail_usuario($email, $vConex,$secure_password);
        }

        W(MsgCR(" (!) El Usuario fue registrado " . $email));
    } else {

        W(MsgCR("(!) Este usuario ya se encuentra registrado "));
    }
    return $usuarioEntidadId;
}

function P_CrearUserProfesor_coursera($email, $nombres,$apellidos,$Perfil,$Area,$Alumno,$Profesor,$Sede,$Escuela, $EnvEmail) {
    global $vConex, $enlace, $entidadCreadora;

    $SedeSQL="SELECT SC.Codigo,SC.UnidadNegocio
        FROM sede_sucursal AS SC
        INNER JOIN  sedes  AS SD ON SC.UnidadNegocio=SD.Codigo
        WHERE SC.Codigo = " . $Sede . " ";
    $rg = fetch($SedeSQL);
    $Empresa = $rg["UnidadNegocio"];

    $secure_password= "pass123";
    $password=_crypt($secure_password);

    if (empty($email)) {
        W(MsgCR("(!) El campo email es obligatorio<br>"));
        CrearUsuario('CrearUser');
        WE("");
    }
    if (empty($nombres)) {
        W(MsgCR("(!) El campo nombres  es obligatorio<br>"));
        CrearUsuario('CrearUser');
        WE("");
    }
    if (empty($apellidos)) {
        W(MsgCR("(!) El campo apellidos  es obligatorio<br>"));
        CrearUsuario('CrearUser');
        WE("");
    }

    $sql = " SELECT tab2.IdUsuario ,tab2.Usuario FROM
        usuario_entidad AS tab1 LEFT JOIN usuarios AS tab2
        ON tab1.Usuario = tab2.Usuario
        WHERE
        tab2.CodigoParlante = '" . $email . "'  AND tab1.EntidadCreadora = '" . $entidadCreadora . "'
        GROUP BY tab2.Usuario ";
    $rg = fetch($sql);

    if (empty($rg["Usuario"])) {

        $sqlUser = "SELECT Usuario FROM usuarios WHERE Usuario = '" . $email . "' ";
        $rg = fetch($sqlUser);
        $UsuarioTab = $rg["Usuario"];

        if (empty($UsuarioTab)) {
            #insertar usuario
            $nombresStringClean = str_replace('.', '', limpiarAcentos($nombres));
            $apellidosStringClean = str_replace('.', '', limpiarAcentos($apellidos));
            $nombreCarpeta = $nombresStringClean . $apellidosStringClean;
            $urlId = $nombresStringClean . '.' . $apellidosStringClean;

            $sqlCarpeta = "SELECT COUNT( Carpeta ) AS count FROM usuarios WHERE Carpeta = '$nombreCarpeta'";
            $dataUsuarioCarpeta = fetchOne($sqlCarpeta, $vConex);

            if ($dataUsuarioCarpeta->count > 0) {
                $patron = $dataUsuarioCarpeta->count + 1;
                $nombreCarpeta = $nombreCarpeta . $patron;
                $urlId = $nombresStringClean . "." . $apellidosStringClean . "." . $patron;
            }

            $pathCarpetaAlumno = BASE_PATH . 'ArchivosAlumnos' . DS . $nombreCarpeta;
            $pathCarpetaProfesor = BASE_PATH . 'ArchivosProfesor' . DS . $nombreCarpeta;


            creaCarpeta($pathCarpetaAlumno);
            creaCarpeta($pathCarpetaProfesor);

            $AuxpathCarpetaAlumno = "ArchivosAlumnos/$nombreCarpeta/file_aux.txt";
            $AuxpathCarpetaProfesor =  "ArchivosProfesor/$nombreCarpeta/file_aux.txt";

            fopen(BASE_PATH . $AuxpathCarpetaAlumno, "w");
            fopen(BASE_PATH . $AuxpathCarpetaProfesor, "w");

            upload_file_to_S3(BASE_PATH . $AuxpathCarpetaAlumno,$AuxpathCarpetaAlumno);
            upload_file_to_S3(BASE_PATH . $AuxpathCarpetaProfesor,$AuxpathCarpetaProfesor);

            #insertamos usuario como alumno
            $idUsuario = $email . 'Alumno';
            ingresaNewUser($email, $idUsuario, $nombres, $apellidos, $nombreCarpeta, $urlId, $vConex, 'Alumno', $password, $FechaNacimiento, $Genero);

            #insertamos usuario como profesor
            $idUsuario = $email . 'Profesor';
            ingresaNewUser($email, $idUsuario, $nombres, $apellidos, $nombreCarpeta, $urlId, $vConex, 'Profesor', $password, $FechaNacimiento, $Genero);

            #insertamos en la tabla Alumno
            ingresaNewPerson($email, $nombres, $apellidos, $FechaNacimiento, $Genero, 'Alumno', $vConex);
            #insertamos en la tabla Profesor
            ingresaNewPerson($email, $nombres, $apellidos, $FechaNacimiento, $Genero, 'Profesor', $vConex);
        }

        #insertamos en la tabla Usuario Entidad
        $dataInsertUsuarioEntidad = array(
            'Usuario' => $email,
            'Perfil' => $Perfil,
            'Genero' => $Genero,
            'FechaNacimiento' => $FechaNacimiento,
            'Nombres' => $nombres,
            'Apellidos' => $apellidos,
            'Profesor' => $Profesor,
            'Alumno' => $Alumno,
            'Estado' => "activo",
            'Empresa' => $Empresa,
            'Area' => $Area,
            'cargo_id' => $Cargo,
            'Sede' => $Sede,
            'Escuela' => $Escuela,
            'EntidadCreadora' => $entidadCreadora,
            'enviar_email' => $EnvEmail
        );
        $usuarioEntidadId = insert('usuario_entidad', $dataInsertUsuarioEntidad, $vConex);
        $usuarioEntidadId = $usuarioEntidadId['lastInsertId'];
        if($EnvEmail==1){
            enviar_mail_usuario($email, $vConex,$secure_password);
        }

        W(MsgCR(" (!) El Usuario fue registrado " . $email));
    } else {

        W(MsgCR("(!) Este usuario ya se encuentra registrado "));
    }
    return $usuarioEntidadId;
}

function P_CrearUser_standar($vConex,$data) {

    $email = $data["Usuario"];
    $nombres = $data["Nombres"];
    $apellidos = $data["Apellidos"];
    $Perfil = $data["Perfil"];
    $FechaNacimiento = $data["FechaNacimiento"];
    $Genero = $data["Genero"];
    $Area = $data["Area"]; 
    $Cargo = $data["Cargo"]; 
    $Alumno = $data["Alumno"]; 
    $Profesor = $data["Profesor"]; 
    $Estado = $data["Estado"]; 
    $Sede = $data["Sede"]; 
    $Escuela = $data["Escuela"]; 
    $entidadCreadora = $data["entidadCreadora"]; 

    $SedeSQL="SELECT SC.Codigo,SC.UnidadNegocio 
        FROM sede_sucursal AS SC 
        INNER JOIN  sedes  AS SD ON SC.UnidadNegocio=SD.Codigo 
        WHERE SC.Codigo = " . $Sede . " ";
    $rg = fetch($SedeSQL);
    $Empresa = $rg["UnidadNegocio"];

    $password = "pass123";#GeneratePassword();
    $secure_password=_crypt($password);
    
//    $txt_file_pwd = fopen("users_and_pass.txt", "a+");
//    fwrite($txt_file_pwd, "User: {$email} Password: {$password}" . PHP_EOL);
//    fclose($txt_file_pwd);

    if (empty($email)) {
        W(MsgCR("(!) El campo email es obligatorio<br>"));
        // CrearUsuario('CrearUser');
        WE("");
    }
    if (empty($nombres)) {
        W(MsgCR("(!) El campo nombres  es obligatorio<br>"));
        // CrearUsuario('CrearUser');
        WE("");
    }
    if (empty($apellidos)) {
        W(MsgCR("(!) El campo apellidos  es obligatorio<br>"));
        // CrearUsuario('CrearUser');
        WE("");
    }

    $sql = " SELECT tab2.IdUsuario ,tab2.Usuario FROM 
        usuario_entidad AS tab1 INNER JOIN usuarios AS tab2
        ON tab1.Usuario = tab2.Usuario
        WHERE 
        tab2.CodigoParlante = '" . $email . "'  AND tab1.EntidadCreadora = '" . $entidadCreadora . "' 
        GROUP BY tab2.Usuario ";
    $rg = fetch($sql);

    if (empty($rg["Usuario"])) {

        $sqlUser = "SELECT Usuario FROM usuarios WHERE Usuario = '" . $email . "' ";
        $rg = fetch($sqlUser);
        $UsuarioTab = $rg["Usuario"];

        if (empty($UsuarioTab)) {
            #insertar usuario
            $nombresStringClean = str_replace('.', '', limpiarAcentos($nombres));
            $apellidosStringClean = str_replace('.', '', limpiarAcentos($apellidos));
            $nombreCarpeta = $nombresStringClean . $apellidosStringClean;
            $urlId = $nombresStringClean . '.' . $apellidosStringClean;

            $sqlCarpeta = "SELECT COUNT( Carpeta ) AS count FROM usuarios WHERE Carpeta = '$nombreCarpeta'";
            $dataUsuarioCarpeta = fetchOne($sqlCarpeta, $vConex);

            if ($dataUsuarioCarpeta->count > 0) {
                $patron = $dataUsuarioCarpeta->count + 1;
                $nombreCarpeta = $nombreCarpeta . $patron;
                $urlId = $nombresStringClean . "." . $apellidosStringClean . "." . $patron;
            }

            $pathCarpetaAlumno = BASE_PATH . 'ArchivosAlumnos' . DS . $nombreCarpeta;
            $pathCarpetaProfesor = BASE_PATH . 'ArchivosProfesor' . DS . $nombreCarpeta;
            

            creaCarpeta($pathCarpetaAlumno);
            creaCarpeta($pathCarpetaProfesor);
            
            $AuxpathCarpetaAlumno = "ArchivosAlumnos/$nombreCarpeta/file_aux.txt";
            $AuxpathCarpetaProfesor =  "ArchivosProfesor/$nombreCarpeta/file_aux.txt";
            
            fopen(BASE_PATH . $AuxpathCarpetaAlumno, "w");
            fopen(BASE_PATH . $AuxpathCarpetaProfesor, "w");
            
            upload_file_to_S3(BASE_PATH . $AuxpathCarpetaAlumno,$AuxpathCarpetaAlumno);
            upload_file_to_S3(BASE_PATH . $AuxpathCarpetaProfesor,$AuxpathCarpetaProfesor);

            #insertamos usuario como alumno
            $idUsuario = $email . 'Alumno';
            ingresaNewUser($email, $idUsuario, $nombres, $apellidos, $nombreCarpeta, $urlId, $vConex, 'Alumno', $secure_password, $FechaNacimiento, $Genero);

            #insertamos usuario como profesor
            $idUsuario = $email . 'Profesor';
            ingresaNewUser($email, $idUsuario, $nombres, $apellidos, $nombreCarpeta, $urlId, $vConex, 'Profesor', $secure_password, $FechaNacimiento, $Genero);

            #insertamos en la tabla Alumno
            ingresaNewPerson($email, $nombres, $apellidos, $FechaNacimiento, $Genero, 'Alumno', $vConex);
            #insertamos en la tabla Profesor
            ingresaNewPerson($email, $nombres, $apellidos, $FechaNacimiento, $Genero, 'Profesor', $vConex);
        }

        #insertamos en la tabla Usuario Entidad
        $dataInsertUsuarioEntidad = array(
            'Usuario' => $email,
            'Perfil' => $Perfil,
            'Genero' => $Genero,
            'FechaNacimiento' => $FechaNacimiento,
            'Nombres' => $nombres,
            'Apellidos' => $apellidos,
            'Profesor' => $Profesor,
            'Alumno' => $Alumno,
            'Estado' => $Estado,
            'Empresa' => $Empresa,
            'Area' => $Area,
            'cargo_id' => $Cargo,
            'Sede' => $Sede,
            'Escuela' => $Escuela,
            'EntidadCreadora' => $entidadCreadora
        );

        $usuarioEntidadId = insert('usuario_entidad', $dataInsertUsuarioEntidad, $vConex);
        enviar_mail_usuario($email, $vConex,$password);
        
        W(MsgCR(" (!) El Usuario fue registrado " . $email));
    } else {

        W(MsgCR("(!) Este usuario ya se encuentra registrado "));
    }
}

function CreaEmpresa() {
    global $vConex;
    
    $RazonSocial = post('Empresa');
    $PaginaWeb = post('EntidadCreadora');
    $EmailContacto = post('Usuario');
    $NombreContacto = post('Nombres');
    $ApellidoContacto = post('Apellidos');
    $Url = post('Url');
    
    if (empty($RazonSocial)){W(MsgCR("(!) El campo Razon Social es obligatorio<br>"));CrearEmpresa('ListarEmpresa');WE("");}
    if (empty($PaginaWeb)) {W(MsgCR("(!) El campo Pagina Web  es obligatorio<br>"));CrearEmpresa('ListarEmpresa');WE("");}
    if (empty($EmailContacto)) {W(MsgCR("(!) El campo Email de Contacto  es obligatorio<br>"));CrearEmpresa('ListarEmpresa');WE("");}
    if (empty($NombreContacto)) {W(MsgCR("(!) El campo Nombre de Contacto es obligatorio<br>"));CrearEmpresa('ListarEmpresa');WE("");}
    if (empty($ApellidoContacto)) {W(MsgCR("(!) El campo Apellidos de Contacto  es obligatorio<br>"));CrearEmpresa('ListarEmpresa');WE("");}
    if (empty( $Url)) {WE(MsgCR("(!) El campo Url de Contacto  es obligatorio<br>"));CrearEmpresa('ListarEmpresa');WE("");}
    
    $Q_Empresa = "SELECT NombreEmpresa
        FROM empresa 
        WHERE NombreEmpresa='{$RazonSocial}'";
    $NombreEmpresa = (string) fetchOne($Q_Empresa, $vConex)->NombreEmpresa;
    
    if(!$NombreEmpresa){
        $Q_UserEmpresa = "SELECT Usuario 
            FROM usuarios 
            WHERE Usuario = '{$PaginaWeb}'";
        $UserEmpresa = (string) fetchOne($Q_UserEmpresa, $vConex)->Usuario;
        
        if(!$UserEmpresa){
            $Q_UsuarioContacto = "SELECT Usuario 
                            FROM usuarios 
                            WHERE Usuario = '{$EmailContacto}'";
            $UsuarioContacto = (string) fetchOne($Q_UsuarioContacto, $vConex)->Usuario;
            
            if(!$UsuarioContacto){
                //Creando una escuela principal para la sede
                $data_INSERT_escuela = array(
                    "Descripcion" => "Principal",
                    "MensajeBienvenida" => "Bienvenidos al aula virtual",
                    "Empresa" => $PaginaWeb,
                    "Categoria" => 20, //Todos
                    "Logo" => "",
                    "Direccion" => "",
                    "Numero" => "",
                    "PaginaWeb" => ""
                );
                
                insert("campus", $data_INSERT_escuela);
                
                $Escuela = mysql_insert_id($vConex);
                
                 //Creando una sede principal para la empresa (EMPRESAS)
                $data_INSERT_sede = array(
                    "Descripcion" => "Principal", 
                    "UsuarioEntidad" => $PaginaWeb, 
                    "FechaHoraCreacion" => FechaHoraSrv(), 
                    "Representate" => "{$NombreContacto} {$ApellidoContacto}", 
                    "Ruc" => "", 
                    "Telefono" => "", 
                    "Principal" => "SI", 
                    "tipo_vinculo" => "SUCURSAL"
                );
                    
                $success = insert("sedes", $data_INSERT_sede);
                $cod_sede = $success["lastInsertId"];
                
                //Creando una sede_sucursal (SEDE)
                $data_INSERT_sedesucursal = array(
                    "UnidadNegocio" => $cod_sede,
                    "Nombre" => "Principal",
                    "Representante" => "{$NombreContacto} {$ApellidoContacto}"
                );
                    
                insert("sede_sucursal", $data_INSERT_sedesucursal);
                
                $Sede = mysql_insert_id($vConex);

                //CREAR UNA CARPETA PARA LA EMPRESA
                $directory_name_enterprise = str_replace(".", "", limpiarAcentos($RazonSocial));
                $pathCarpetaEmpresa = BASE_PATH . 'ArchivosEmpresa' . DS . $directory_name_enterprise;
                creaCarpeta($pathCarpetaEmpresa);
                //CREANDO UN USUARIO MASTER PARA LA EMPRESA
                $Perfil_empresa = PERFIL_MASTER;
                //$password_empresa = GeneratePassword();
                $password_empresa = 'pass123';
                $secure_password_empresa = _crypt($password_empresa);
                $PaginaWeb = str_replace('www.','', $PaginaWeb);
                ingresaNewUser($PaginaWeb, $PaginaWeb, $RazonSocial, '', $directory_name_enterprise, $directory_name_enterprise, $vConex, 'EmpresaAdmin', $secure_password_empresa, '', '');
              //ingresaNewUser($PaginaWeb, $PaginaWeb, $RazonSocial, '', $directory_name_enterprise, $Url, $vConex, 'EmpresaAdmin', $secure_password_empresa, '', '');

                ingresaNewEmpresa($RazonSocial, $RazonSocial, $PaginaWeb);
                IngresaNewUsuarioEntidad($PaginaWeb, $Perfil_empresa, $PaginaWeb, "", "", "", "", "", $RazonSocial, "", "", "", "",$Escuela, $Sede,$Url);
                W(MsgCR("(!) La Empresa {$RazonSocial} fue registrada "));
                
                //CREAR UN USUARIO PARA EL CONTACTO
                $Perfil_contacto = PERFIL_DIRECTOR;
                //$password_contacto = GeneratePassword();
                $password_contacto = 'Pass123';
                $secure_password_contacto = _crypt($password_contacto);
                
                $nombresStringClean = limpiarAcentos($NombreContacto);
                $apellidosStringClean = limpiarAcentos($ApellidoContacto);
                $directory_name_contact = "{$nombresStringClean}{$apellidosStringClean}";
                
                $urlId = "{$nombresStringClean}.{$apellidosStringClean}";
                $pathCarpetaAlumno = BASE_PATH . "ArchivosAlumnos/{$directory_name_contact}";
                $pathCarpetaProfesor = BASE_PATH . "ArchivosProfesor/{$directory_name_contact}";
                creaCarpeta($pathCarpetaAlumno);
                creaCarpeta($pathCarpetaProfesor);
                
                #insertamos usuario como alumno
                $idUsuarioAlumno = "{$EmailContacto}Alumno";
                ingresaNewUser($EmailContacto, $idUsuarioAlumno, $NombreContacto, $ApellidoContacto, $directory_name_contact, $urlId, $vConex, 'Alumno', $secure_password_contacto, '', '');
                #insertamos usuario como profesor
                $idUsuarioProfesor = "{$EmailContacto}Profesor";
                ingresaNewUser($EmailContacto, $idUsuarioProfesor, $NombreContacto, $ApellidoContacto, $directory_name_contact, $urlId, $vConex, 'Profesor', $secure_password_contacto, '', '');
                #insertamos en la tabla Alumno
                ingresaNewPerson($EmailContacto, $NombreContacto, $ApellidoContacto, '', '', 'Alumno', $vConex);
                #insertamos en la tabla Profesor
                ingresaNewPerson($EmailContacto, $NombreContacto, $ApellidoContacto, '', '', 'Profesor', $vConex);
                #insertamos en la tabla Usuario_Entidad
                $Cargo = 2; //Director de la tabla cargos
                IngresaNewUsuarioEntidad($EmailContacto, $Perfil_contacto, $PaginaWeb, "", "", "", $Cargo, $Area, $NombreContacto, $ApellidoContacto, "", "", "",$Escuela, $Sede,$Url);
                
                //Creando los perfiles del MASTER desde Sys
                $Q_det_submenu = "SELECT Codigo, Menu, MenuDetalle, Estado
                            FROM menu_empresa_perfil
                            WHERE Perfil = {$Perfil_empresa} 
                            AND Entidad = 'Sys'";

                $MxDet_submenu = fetchAll($Q_det_submenu, $vConex);

                foreach($MxDet_submenu as $Det_submenu){
                    $codSubmenu = (int) $Det_submenu->MenuDetalle;
                    $codMenu = (int) $Det_submenu->Menu;

                    $data_INSERT = array(
                        "Menu" => $codMenu,
                        "MenuDetalle" => $codSubmenu,
                        "Estado" => "Activo",
                        "Perfil" => $Perfil_empresa,
                        "Entidad" => $PaginaWeb
                    );

                    insert("menu_empresa_perfil", $data_INSERT, $vConex);
                }
                
               
                
                
                
                //ENVIAR UN EMAIL CON EL USUARIO MASTER Y EL USUARIO DIRECTOR CREADO
                $domain = getDomain();
                $cabezeraMail .= "
                        <div style='border-bottom:2px solid #e2e2e2;margin:10px 0px 30px 0px;'></div>   
                            <div style='padding:10px 3px;'>" . FechaHoraSrv() . "</div>
                            <div style='padding:3px 3px;'>{$NombreContacto} {$ApellidoContacto}</div>
                            <div style='padding:2px 3px;font-size:0.8em;'>{$EmailContacto}</div>
                            <div style='font-size:1.5em;color:#6b6b6b;padding:5px 0px 5px 3px;'>PLATAFORMA EDUCATIVA </div>
                            <div style='font-size:1.5em;color:#6b6b6b;padding:5px 0px 5px 3px;'>{$PaginaWeb}</div>";

                $cuerpoMail = "
                            <div style='font-size:1.5em;padding:10px 0px 10px 3px;color:#4396de;'>Creación de una nueva empresa en la plataforma OWL</div>
                            <div>
                                A través del siguiente e-mail OWL Platform le da la bienvenida a la empresa <em>{$PaginaWeb}</em>
                                y otorgándole los accesos de usuario creados cuales son los siguientes:
                                <br>
                                <strong>Usuario Master</strong><br>
                                Usuario: {$PaginaWeb}<br>
                                Contraseña: {$password_empresa}<br>
                                <br>
                                <strong>Usuario Director</strong><br>
                                Este usuario fue creado a nombre de {$NombreContacto} {$ApellidoContacto}<br>
                                Usuario: {$EmailContacto}<br>
                                Contraseña: {$password_contacto}<br>
                                <br>
                                <em>Le recomendamos por su seguridad cambiar la contraseña de sus cuentas de usuario</em>
                            </div>
                            <div>Visite nuestra Plataforma : <a href='{$domain}/{$directory_name_enterprise}'>Click Aquí </a></div>";

                $footerMail = "Atentamente OWL Platform &copy;";
                $asunto = "OWL le da la bienvenida a la empresa {$PaginaWeb}";
                $body = LayouMailA($cabezeraMail, $cuerpoMail, $footerMail);
                EMail("", $EmailContacto, $asunto, $body);
            }else{
                W(MsgER("(!) Email de Contacto ya se encuentra registrado "));
                CrearEmpresa('ListarEmpresa');
            }
        }else{
            W(MsgER("(!) La Paginaweb ingresada ya se encuentra registrada "));
            CrearEmpresa('ListarEmpresa');
        }
    }else{
        W(MsgER("(!) La Empresa ingresada ya se encuentra registrada"));
        CrearEmpresa('ListarEmpresa');
    }
}

function ingresaNewUser($email, $idUsuario, $nombres, $apellidos, $nombreCarpeta, $urlId, $vConex, $perfil, $contrase, $FechaNacimiento, $Genero,$IdUser = null,$IdEmailJefe = null) {
    global $vConex, $enlace, $entidadCreadora;

    $dataInsertUsuario = array(
        'Usuario' => $email,
        'Nombres' => $nombres,
        'Apellidos' => $apellidos,
        'Contrasena' => $contrase,
        'Perfil' => $perfil,
        'Carpeta' => $nombreCarpeta,
        'IdUsuario' => $idUsuario,
        'Genero' => $Genero,
        'FechaNacimiento' => $FechaNacimiento,
        'UrlId' => $urlId,
        'CodigoParlante' => (!$IdUser)?$email:$IdUser,
        'Estado' => 'Activo',
        'FechaRegistro' => date('Y-m-d H:i:s'),
        'connected' => "desconectado",
        'unique_interaction' => "no",
        'CambiarContrasena' => "Si",
        'IdEmailJefe' => $IdEmailJefe
    );
    $usuarioIdAlumno = insert('usuarios', $dataInsertUsuario, $vConex);
}

function ingresaNewPerson($email, $nombres, $apellidos, $FechaNacimiento, $Genero, $perfil, $vConex) {
    global $vConex, $enlace, $entidadCreadora;
    $data = array(
        'Email' => $email,
        'Nombres' => $nombres,
        'FechaNac' => $FechaNacimiento,
        'Genero' => $Genero,
        'ApellidosPat' => $apellidos,
        'ApellidosPat' => $apellidos,
        'Usuario' => $email . $perfil,
    );

    if ($perfil == 'Alumno') {
        $tabla = 'alumnos';
    } else {
        $tabla = 'profesores';
    }
    $usuarioIdAlumnoId = insert($tabla, $data, $vConex);
}

function ingresaNewEmpresa($NombreEmpresa, $RazonSocial, $PaginaWeb) {
    global $vConex;
    
    $dataInsertEmpresa = array(
        'NombreEmpresa' => $NombreEmpresa,
        'RazonSocial' => $RazonSocial,
        'PaginaWeb' => $PaginaWeb,
        'IdTemaGraf' => 14,              
    );

    $empresasIdEmpresa = insert('empresa', $dataInsertEmpresa, $vConex);
}

function IngresaNewUsuarioEntidad($email, $Perfil, $PaginaWeb, $Profesor, $Alumno, $Estado, $Cargo, $Area, $nombres, $apellidos, $Empresa, $Genero, $FechaNacimiento,$Escuela, $Sede,$Url) {
    global $vConex, $enlace, $entidadCreadora;

    $dataInsertUsuarioEntidad = array(
        'Usuario' => $email,
        'Perfil' => $Perfil,
        'EntidadCreadora' => $PaginaWeb,
        'Profesor' => $Profesor,
        'Alumno' => $Alumno,
        'Estado' => $Estado,
        'cargo_id' => $Cargo,
        'Area' => $Area,
        'Nombres' => $nombres,
        'Apellidos' => $apellidos,
        'Empresa' => $Empresa,
        'Genero' => $Genero,
        'FechaNacimiento' => $FechaNacimiento,
        'Escuela' => $Escuela,
        'Sede' => $Sede ,
        'Url'=>$Url,
    );

    $usuarioEntidadId = insert('usuario_entidad', $dataInsertUsuarioEntidad, $vConex);
    
        /*    Creacion lista Default         */
    
    $codigo = mysql_insert_id($vConex);
    $Fecha = date('Y-m-d H:i:s');

        $sql = "SELECT  
        U.Codigo as UECodigo,
        U.Perfil as UEPerfil,
        U.EntidadCreadora as UEEntidad,
        U.Escuela as UEEscuela,
        U.Sede as UESede,
        CONCAT(U.Nombres,' ',U.Apellidos) AS UEUsuario
        FROM ((usuario_entidad AS U 
        INNER JOIN usuario_perfil  AS P ON U.Perfil = P.Codigo)
        INNER JOIN usuarios  AS US ON U.Usuario = US.Usuario)  
        WHERE U.Codigo=".$codigo." ";

    $rg = fetch($sql);
    $UEPerfil = ($rg["UEPerfil"]);
    
    if($UEPerfil == 1){
    $UECodigo = $rg["UECodigo"];
    $UEUsuario = $rg["UEUsuario"];
    $UEEntidad = $rg["UEEntidad"];
    $UEEscuela = $rg["UEEscuela"];
    $UESede = $rg["UESede"];
    
    $sql = "INSERT INTO lista_trabajo(UsuarioCreacion,FechaHoraCreacion,Estado,Nombre,Empresa,Escuela,Sede,FechaHoraActualizacion,UsuarioActualizacion,Responsable) 
    VALUES (".$UECodigo.",' ".$Fecha." ','Trabajando','Lista Trabajo ".$UEUsuario."','".$UEEntidad."',".$UEEscuela.",".$UESede.",' ".$Fecha." ',".$UECodigo.",".$UECodigo.")";
     xSQL($sql, $vConex);
    }
}


function enviar_mail_usuario($email,$vConex,$contrasena){
    global $vConex,$entidadCreadora;
    $sql = "SELECT Nombres, Apellidos,Usuario FROM usuarios WHERE Usuario='$email' limit 1";
    $rg = fetch($sql);
    $n_a = ucwords(strtolower($rg["Nombres"])).' '.ucwords(strtolower($rg["Apellidos"]));
    
    $usuario=$rg['Usuario'];
    $sql2="select Usuario,UrlId from usuarios where Usuario='$entidadCreadora'";
    $regusu=  fetch($sql2);
    $nombreEmp=$regusu['UrlId'];
    $hoy = date('Y-m-d');
    $hoy = FormatFechaText($hoy);
    
    $body ='<div id=":2m" class="ii gt m144e03aa4c6b1558 adP adO">';
    $body .= '<div id=":1dd" class="a3s" style="overflow: hidden;">';
    $body .= '<div style="background-color:#e3e3e3;margin:0 auto;width:760px;min-height:425px;padding:20px 20px">';
    $body .= '<div style="float:left;width:90%;background-color:#fff;padding:10px 5% 0px 5%;font-size:0.9em;font-family:arial;color:#6b6b6b;min-height:100%">';
    $body .= '<div style="float:left;width:100%;padding:20px 0px;color:#6b6b6b">';
    $body .= '<div style="padding:10px 3px">'.$hoy.'</div>';
    $body .= '<div style="border-bottom:2px solid #e2e2e2;margin:10px 0px 30px 0px" class="adM"></div>';
    $body .= '<div style="font-size:1.5em;color:#6b6b6b;padding:0px 0px 15px 3px">Inscripción </div>';
    $body .= '<div style="font-size:1.2em;color:#6b6b6b;padding:5px 0px 5px 3px">Estimado (a) <span style="color:#35A9AD">' .$n_a . '</span> Su inscripción a la plataforma se realizó exitosamente.</div><br>';
    $body .= '</span> Su Usuario es: <span style="color:#35A9AD">'.$usuario .'</span> y su Password: <span style="color:#35A9AD">'.$contrasena .'</span>';
    $body .= '</div>';
    $body .= '<div style="float:left;width:100%;padding:35px 3px;color:#6b6b6b">';
    $body .= '<a href="https://owlgroup.org/'.$nombreEmp.'" style="font-size:12px;border:1px solid #006ad0;padding:8px 30px;background-color:#4396de;color:#f9f9f9;text-decoration:none" target="_blank">Ingresar</a>';
    $body .= '</div>';
    $body .= '<div style="float:left;width:100%;padding:20px 0px;color:#6b6b6b">Atentamente </div>';
    $body .= '</div>';
    $body .= '</div>';
    $body .= '</div>';
    $body .= '</div>';
    $body .= '</div>';

    $asunto = 'Inscripción a la Plataforma';
    #$emailE = EMail("",$email,$asunto, $body );
    $emailE = emailSES($n_a, $email, $asunto, $body,'','');
    return $emailE;
}
function enviar_mail_usuario_insc($email,$vConex,$contrasena,$Empresa){
    global $vConex, $entidadCreadora, $ipHost;
    
    $sql = "SELECT Nombres, Apellidos,Usuario FROM usuarios WHERE Usuario='$email' limit 1";
    $rg = fetch($sql);
    $n_a = ucwords(strtolower($rg["Nombres"])).' '.ucwords(strtolower($rg["Apellidos"]));
    
    $usuario=$rg['Usuario'];
    $sql2="select Usuario,UrlId from usuarios where Usuario='$entidadCreadora'";
    $regusu=  fetch($sql2);
    $nombreEmp=$regusu['UrlId'];
   
    $hoy = date('Y-m-d');
    $hoy = FormatFechaText($hoy);   
    
    $sqlUser = "SELECT CodEmpresa FROM empresa WHERE PaginaWeb = '{$Empresa}' ";
    $rg = fetch($sqlUser);
    $CodEmpresa = $rg["CodEmpresa"];

    $SqlRegistro = "SELECT EmailAsunto,EmailContenido,EmailNota,EmailImagen, Descripcion 
                    FROM formatoemail 
                    WHERE CodEmpresa = {$CodEmpresa} AND ActivarEmail=1";
    $rows   = fetch($SqlRegistro);
    // $Asunto = $rows["EmailAsunto"];
    $Asunto = "ACCESOS A LA PLATAFORMA";    

    $Texto  = $rows["EmailContenido"];
    $nombre_emisor  = $rows["Descripcion"];
    if(empty($nombre_emisor)){ $nombre_emisor = "OWLGROUP"; }

    $Texto1 = '<span style="color:#000">Estimado(a) <span style="color:#35A9AD">'.$n_a.'</span>;</span> <BR /><BR /> Su inscripción a la plataforma se realizó exitosamente.<span style="color:#35A9AD"></span>';
        
    $Nota  = '<strong>NOTA:</strong> 
    para obtener un mejor rendimiento de la plataforma, acceder con el navegador google chrome, si presenta alg&uacute;n
    inconveniente, contactarse con el soporte t&eacute;cnico.';


    $body  = "<table border='0' width='95%' style='font-family: arial, open sans'>";
    $body .= '<tr><td colspan="3" style="padding:8px 0px;font-size:22px;" >'.$Asunto.'</td></tr>';
    #$body .= '<tr><td colspan="3" style="padding:8px 10px;font-size:10px;" ></td></tr>';
    $body .= '<tr><td colspan="3" style="font-size:13px;color: #5D5D5D;"> <br>'.$Texto1.' <br>Su usuario es: <span style="color:#35A9AD">'.$usuario .'</span> y su contreseña: <span style="color:#35A9AD">'.$contrasena .'</span><br /></td></tr>';
    $body .= '<tr><td colspan="3" style="font-size:13px;color: #5D5D5D;" ><br>'.$Texto.'</td></tr>';
    $body .= '<tr><td colspan="3" style="padding:8px 0px;font-size:10px;color:#6b6b6b" ></td></tr>';
    
    $body .= '<tr><td colspan="3" style="padding:4px 0px;font-size:10px;color:#6b6b6b" ></td></tr>';
    $body .= '<tr>
                 <td style="font-size:12px;border:1px solid #006ad0;padding:8px 30px;background:#4396de;text-decoration:none">
                  <center><a style="color:#f9f9f9;text-decoration: none;" href="'.$ipHost . $nombreEmp . '"  target="_blank">Ingresar</a></center>
                  </td>
                 <td style="padding:8px 10px; width:30%;"></td>
                <td style="padding:8px 30px;width:50%;"></td>
               </tr>';
    $body .= '<tr><td colspan="3" style="padding:0px;font-size:10px;" >' . $Nota . '</td></tr>';
    $body .= '<tr><td colspan="3" style="font-size:12px;border-bottom: 2px solid #F3F3F3;width:100%;padding:20px 0px;color:#6b6b6b" >Atentamente <br>Dirección Académica</td></tr>';
    $body .= '<tr><td colspan="3" style="width:100%;padding:5px 0px;color:#6b6b6b;font-size:9px ">Datos de Soporte:   Correo: soporteusuario@owl-group.org | Tel&eacute;fono.: 6440640 | Anexo: 604  </td></tr>';
    $body .= '<tr><td colspan="3" style="font-size:10px;" >Fecha: ' . $hoy . '<br /></td></tr>';
    $body .= "</table>";
    
    ///////////MMMMMMMMMMMMMMMMMMMMM
    $emailE = emailSES2($n_a, $email, $Asunto, $body,'','',$nombre_emisor);
    return $emailE;
}


function enviar_mail_usuario2($emaill,$vConex,$contrasena,$Empresa){
    global $vConex, $entidadCreadora, $ipHost;

    $creausrfuera=get('creausrfuera');
    $email=$emaill['correo'];

    if($creausrfuera){

        $sql = "SELECT Nombres, Apellidos,Usuario FROM usuarios WHERE Usuario='$emaill' limit 1";
    }else{
        $sql = "SELECT Nombres, Apellidos,Usuario FROM usuarios WHERE Usuario='$email' limit 1";
    }

    $rg = fetch($sql);
    $n_a = ucwords(strtolower($rg["Nombres"])).' '.ucwords(strtolower($rg["Apellidos"]));


    $usuario=$rg['Usuario'];
    $sql2="select Usuario,UrlId from usuarios where Usuario='$entidadCreadora'";
    $regusu=  fetch($sql2);
    $nombreEmp=$regusu['UrlId'];
   
    $hoy = date('Y-m-d');
    $hoy = FormatFechaText($hoy);   
	
    $sqlUser = "SELECT CodEmpresa FROM empresa WHERE PaginaWeb = '{$Empresa}' ";
    $rg = fetch($sqlUser);
    $CodEmpresa = $rg["CodEmpresa"];

    $SqlRegistro = "SELECT EmailAsunto,EmailContenido,EmailNota,EmailImagen, Descripcion 
                    FROM formatoemail 
                    WHERE CodEmpresa = {$CodEmpresa} AND ActivarEmail=1";
    $rows   = fetch($SqlRegistro);

	$Asunto = "ACCESOS A LA PLATAFORMA"; 	

    $Texto  = $rows["EmailContenido"];
    $nombre_emisor  = $rows["Descripcion"];
	if(empty($nombre_emisor)){ $nombre_emisor = "OWLGROUP"; }

    $Texto1 = '<span style="color:#000">Estimado(a) <span style="color:#35A9AD">'.$n_a.'</span>;</span> <BR /><BR /> Su inscripción a la plataforma se realizó exitosamente.<span style="color:#35A9AD"></span>';
        
	$Nota  = '<strong>NOTA:</strong> 
	para obtener un mejor rendimiento de la plataforma, acceder con el navegador google chrome, si presenta alg&uacute;n
	inconveniente, contactarse con el soporte t&eacute;cnico.';
            $CorreoUsuario=$emaill['correo'].'Alumno';
            $CorreoUsuario=$CorreoUsuario;
           
            $CodAlmacen= get('productoId');

            if($CodAlmacen){
                $MailDetalle="SELECT * FROM maildetalle WHERE CodAlmacen={$CodAlmacen} AND TipoMail='Inscripcion' ";
                $ftch=fetchOne($MailDetalle);
            }

            if($creausrfuera){
                $sqlUser = "SELECT CodEmpresa FROM empresa WHERE PaginaWeb = '$entidadCreadora' ";
                #$rg = fetch($sqlUser);
                $rg = fetch($sqlUser);
                $codigo = $rg["CodEmpresa"];

                $EmpresaMail="SELECT * FROM empresa WHERE CodEmpresa='$codigo'";
                $ftch=fetchOne($EmpresaMail);

            }

    $tipoEnvio=$ftch->TipoEnvio;
            $Imagen=$ftch->Imagen;
            $FooterMail=$ftch->FooterMail;
            $emisor=$ftch->Emisor;
            $nombre_emisor=$ftch->NombreEmisor;
            $tamanioMensaje=strlen($FooterMail);

            $sql = "SELECT Carpeta FROM usuarios  WHERE IdUsuario = '$entidadCreadora' ";
             $rg = fetch($sql);
            $CarpetaEmpresa = $rg["Carpeta"];

            if($tipoEnvio == 'Predeterminado'){
                $Texto = 'Estimado(a) <span style="color:#35A9AD">'.$n_a.'</span>, usted fue inscrito correctamente al <span style="color:#35A9AD">' . $tituloPrograma . '</span>';
                $Nota  = '<strong>NOTA:</strong> para obtener un mejor rendimiento de la plataforma acceder con el navegador google chrome, si presenta alg&uacute;n inconveniente, contactarse con el soporte t&eacute;cnico.';

                $body  = "<table border='0' width='600px' style='font-family: arial, open sans'>";
                $body .= '<tr><td colspan="3" style="padding:8px 0px;font-size:22px;" >Inscripci&oacute;on Exitosa</td></tr>';
                $body .= '<tr><td colspan="3" style="padding:8px 10px;font-size:10px;" ></td></tr>';
                $body .= '<tr><td colspan="3" style="font-size:13px;"> '.$Texto.'</td></tr>';
                $body .= '<tr><td colspan="3" style="padding:8px 10px;font-size:10px;" ></td></tr>';
                $body .= '<tr>
                            <td style="font-size:12px;border:1px solid #006ad0;padding:8px 30px;background-color:#4396de;text-decoration:none">
                              <center><a style="color:#f9f9f9;text-decoration: none;" href="'.$ipHost . $nombreEmp . '"  target="_blank">Ingresar</a></center></td>
                             <td style="padding:8px 10px; width:30%;"></td>
                            <td style="padding:8px 30px;width:50%;"></td>
                           </tr>';
                $body .= '<tr><td colspan="3" style="padding:8px 0px;font-size:10px;color:#6b6b6b" >'.$Nota.'</td></tr>';
                $body .= '<tr><td colspan="3" style="padding:8px 10px;font-size:10px;" ></td></tr>';
                $body .= '<tr><td colspan="3" style="font-size:12px;border-bottom: 2px solid #F3F3F3; float:left;width:100%;padding:20px 0px;color:#6b6b6b" >Atentamente <br>Dirección Académica</td></tr>';
                $body .= '<tr><td colspan="3" style="width:100%;padding:5px 0px;color:#6b6b6b;font-size:9px ">Datos de Soporte: '.$Soporte_Nombre.' <br>  Correo: '.$Soporte_Email.' <br> Tel&eacute;fono.: '.$Soporte_Telefono.' | Anexo: '.$Soporte_Anexo.'  </td></tr>';
                $body .= '<tr><td colspan="3" style="font-size:10px;" >Fecha: ' . $hoy . '<br /></td></tr>';
                $body .= "</table>";
            }else if($tipoEnvio == 'Personalizado'){
                $Texto = 'Estimado(a) <span style="color:#35A9AD">'.$n_a.'</span>, usted fue matriculado correctamente al <span style="color:#35A9AD">' . $tituloPrograma . '</span>';
                $Nota  = '<strong>NOTA:</strong> para obtener un mejor rendimiento de la plataforma acceder con el navegador google chrome, si presenta alg&uacute;n inconveniente, contactarse con el soporte t&eacute;cnico.';
//jj
                $body  = "<center><table border='0' width='600px' style='font-family: arial, open sans'>";
                $url ="http://owlgroup.s3-website-us-west-2.amazonaws.com/ArchivosEmpresa/".$CarpetaEmpresa."/".$Imagen."";
                $body .= '<tr><td colspan="3"><label>Si no puede visualizar la imagen correctamente, por favor haga <a href="'.$url.'" target="_blank">Clic aqu&iacute;</a></label></td></tr>';
                $body .= '<tr><td colspan="3" style="font-size:13px;"><picture><img src="'.$url.'" width="600px" height="300px"></picture> </td></tr>';
                $body .= '<tr><td colspan="3" style="padding:8px 10px;font-size:10px;" ></td></tr>';
                $body .= '<tr>
                            <td  style="font-size:12px;border:1px solid #006ad0;padding:8px 30px;background-color:#4396de;text-decoration:none">
                              <center><a style="color:#f9f9f9;text-decoration: none;" href="'.$ipHost . $nombreEmp . '"  target="_blank">Ingresar</a></center></td>
                             </td>';
                $body .= '  <td style="font-size:12px;border:1px solid #006ad0;padding:8px 30px;background-color:#4396de;text-decoration:none">
                              <center><a style="color:#f9f9f9;text-decoration: none;" href="mailto:'.$emisor.'"  target="_blank">Coordinación</a></center></td>
                             </td>';
                $body .= '  <td style="font-size:12px;border:1px solid #006ad0;padding:8px 30px;background-color:#4396de;text-decoration:none">
                              <center><a style="color:#f9f9f9;text-decoration: none;" href="mailto:soporteusuario@owl-group.org"  target="_blank">Soporte</a></center></td>
                            </td>
                           </tr>';
                $body .= '<tr><td colspan="3" style="padding:8px 0px;font-size:10px;color:#6b6b6b" ><span style="width:800px;font-size: 12px;">' .$FooterMail.'</span></td></tr>';
                if($tamanioMensaje <= 6){
                    $body .= '<tr><td colspan="3" style="padding:8px 0px;font-size:10px;color:#6b6b6b" >'.$Nota.'</td></tr>';
                }
                $body .= '<tr><td colspan="3" style="padding:8px 10px;font-size:10px;" ></td></tr>';
                $body .= '<tr><td colspan="3" style="font-size:12px;border-bottom: 2px solid #F3F3F3; float:left;width:100%;padding:20px 0px;color:#6b6b6b" >Atentamente <br>Dirección Académica</td></tr>';
                $body .= '<tr><td colspan="3" style="width:100%;padding:5px 0px;color:#6b6b6b;font-size:9px ">Datos de Soporte: '.$Soporte_Nombre.' <br>  Correo: '.$Soporte_Email.' <br> Tel&eacute;fono.: '.$Soporte_Telefono.' | Anexo: '.$Soporte_Anexo.'  </td></tr>';
                $body .= '<tr><td colspan="3" style="font-size:10px;" >Fecha: ' . $hoy . '<br /></td></tr>';
                $body .= "</table></center>";
            }else if($tipoEnvio == 'NoEnviar'){
                return;
            }
            $Asunto = "INSCRIPCION AL PROGRAMA";


    if($tipoEnvio){

        if($creausrfuera){
            $emailE = emailSES2($n_a, $emisor, $Asunto, $body,'','',$nombre_emisor);
        }else{
            $emailE = emailSES2($n_a, $emaill['correo'], $Asunto, $body,'','',$nombre_emisor);
        }

    }else{
        Msg('Configurar el Tipo de Email','E');
    }

    return $emailE;
}

#########################
# Usuario: fyupanquia 
# Fecha : 11:25 a.m. 25/01/2016
# Fecha  Mod: 11:25 a.m. 25/01/2016
# Variables : $pagweb|VARCHAR, $email|VARCHAR, $nombres|VARCHAR, $apellidos|VARCHAR, $clavedefault|VARCHAR, $Perfil|VARCHAR, $entidadCreadora|VARCHAR, $vConex|obj, $enablecrypt|boolean
#########################
    function CrearUser_Compra2($pagweb="",$email,$nombres,$apellidos,$clavedefault,$Perfil,$entidadCreadora,$vConex,$enablecrypt=true) {

        $arrayemailpass         = explode("|", $email);
        $cantidadarrayemailpass = count($arrayemailpass);
        if($cantidadarrayemailpass==1){
            $password = $clavedefault;
        }else{
            $email    = $arrayemailpass[0];
            if($arrayemailpass[1]!=""){
                $password = $arrayemailpass[1];
            }else{
                $password = $clavedefault;
            }
            
        }
        if($enablecrypt)
            $secure_password=_crypt($password);
        else
            $secure_password    = $password;
        
        $sql = "SELECT tab2.IdUsuario ,tab2.Usuario
                FROM usuario_entidad AS tab1 LEFT JOIN usuarios AS tab2
                ON tab1.Usuario = tab2.Usuario
                WHERE   tab2.CodigoParlante ='$email' AND tab1.EntidadCreadora = '$entidadCreadora' 
                GROUP BY tab2.Usuario";
        $rg = fetch($sql);

        if (empty($rg["Usuario"])) {
            $sqlUser = "SELECT Usuario FROM usuarios WHERE Usuario = '" . $email . "' ";
            $rg = fetch($sqlUser);
            $UsuarioTab = $rg["Usuario"];
            if (empty($UsuarioTab)) {
                #insertar usuario
                $nombresStringClean = str_replace('.', '', limpiarAcentos($nombres));
                $apellidosStringClean = str_replace('.', '', limpiarAcentos($apellidos));
                $nombreCarpeta = $nombresStringClean . $apellidosStringClean;
                $urlId = $nombresStringClean . '.' . $apellidosStringClean;

                $sqlCarpeta = "SELECT COUNT( Carpeta ) AS count FROM usuarios WHERE Carpeta = '$nombreCarpeta'";
                $dataUsuarioCarpeta = fetchOne($sqlCarpeta, $vConex);
                if ($dataUsuarioCarpeta->count > 0) {
                    $patron = $dataUsuarioCarpeta->count + 1;
                    $nombreCarpeta = $nombreCarpeta . $patron;
                    $urlId = $nombres . "." . $apellidos . "." . $patron;
                }

                $pathCarpetaAlumno = BASE_PATH . 'ArchivosAlumnos' . DS . $nombreCarpeta;
                $pathCarpetaProfesor = BASE_PATH . 'ArchivosProfesor' . DS . $nombreCarpeta;
                creaCarpeta($pathCarpetaAlumno);
                creaCarpeta($pathCarpetaProfesor);
                #insertamos usuario como alumno
                $idUsuario = $email . 'Alumno';
                ingresaNewUser($email, $idUsuario, $nombres, $apellidos, $nombreCarpeta, $urlId, $vConex, 'Alumno', $secure_password, $FechaNacimiento, $Genero);
                #insertamos usuario como profesor
                $idUsuario = $email . 'Profesor';
                ingresaNewUser($email, $idUsuario, $nombres, $apellidos, $nombreCarpeta, $urlId, $vConex, 'Profesor', $secure_password, $FechaNacimiento, $Genero);
                #insertamos en la tabla Alumno
                ingresaNewPerson($email, $nombres, $apellidos, $FechaNacimiento, $Genero, 'Alumno', $vConex);
                #insertamos en la tabla Profesor
                ingresaNewPerson($email, $nombres, $apellidos, $FechaNacimiento, $Genero, 'Profesor', $vConex);
                
                try {
                    enviar_mail_usuario_compra($email, $vConex,$password,$entidadCreadora,$pagweb);
                } catch (Exception $e) {
                    
                }
                
            }

            $sqlUserEntity = "SELECT Codigo FROM usuario_entidad WHERE Usuario = '" . $email . "' AND EntidadCreadora='".$entidadCreadora."' ";
            $rg = fetch($sqlUserEntity);
            $CodUserEntity = $rg["Codigo"];
            if (empty($CodUserEntity)) {
                #insertamos en la tabla Usuario Entidad
                $dataInsertUsuarioEntidad = array(
                    'Usuario' => $email,
                    'Perfil' => $Perfil,
                    'Nombres' => $nombres,
                    'EntidadCreadora' => $entidadCreadora
                );
                
                $usuarioEntidadId = insert('usuario_entidad', $dataInsertUsuarioEntidad,$conexSys);
            }


            return true;
        } else {
            return false;
            //W(MsgCR("(!) Este usuario ya se encuentra registrado "));
        }
        return false;
    }

function CrearUser_Compra($email,$nombres,$apellidos,$Perfil,$entidadCreadora,$vConex) {

    $arrayemailpass =   explode("|", $email);
    $cantidadarrayemailpass = count($arrayemailpass);
    $password     = "pass123";
    if($cantidadarrayemailpass==1){
        $password = GeneratePassword();
    }else{
        $email    = $arrayemailpass[0];
        $password = $arrayemailpass[1];
    }
    $secure_password=_crypt($password);
    
    $sql = "SELECT tab2.IdUsuario ,tab2.Usuario
            FROM usuario_entidad AS tab1 LEFT JOIN usuarios AS tab2
            ON tab1.Usuario = tab2.Usuario
            WHERE   tab2.CodigoParlante ='$email' AND tab1.EntidadCreadora = '$entidadCreadora' 
            GROUP BY tab2.Usuario";
    
    $rg = fetch($sql);

    if (empty($rg["Usuario"])) {
        $sqlUser = "SELECT Usuario FROM usuarios WHERE Usuario = '" . $email . "' ";
        $rg = fetch($sqlUser);
        $UsuarioTab = $rg["Usuario"];
        if (empty($UsuarioTab)) {
            #insertar usuario
            $nombresStringClean = str_replace('.', '', limpiarAcentos($nombres));
            $apellidosStringClean = str_replace('.', '', limpiarAcentos($apellidos));
            $nombreCarpeta = $nombresStringClean . $apellidosStringClean;
            $urlId = $nombresStringClean . '.' . $apellidosStringClean;

            $sqlCarpeta = "SELECT COUNT( Carpeta ) AS count FROM usuarios WHERE Carpeta = '$nombreCarpeta'";
            $dataUsuarioCarpeta = fetchOne($sqlCarpeta, $vConex);

            if ($dataUsuarioCarpeta->count > 0) {
                $patron = $dataUsuarioCarpeta->count + 1;
                $nombreCarpeta = $nombreCarpeta . $patron;
                $urlId = $nombres . "." . $apellidos . "." . $patron;
            }

            $pathCarpetaAlumno = BASE_PATH . 'ArchivosAlumnos' . DS . $nombreCarpeta;
            $pathCarpetaProfesor = BASE_PATH . 'ArchivosProfesor' . DS . $nombreCarpeta;

            creaCarpeta($pathCarpetaAlumno);
            creaCarpeta($pathCarpetaProfesor);

            #insertamos usuario como alumno
            $idUsuario = $email . 'Alumno';
            ingresaNewUser($email, $idUsuario, $nombres, $apellidos, $nombreCarpeta, $urlId, $vConex, 'Alumno', $secure_password, $FechaNacimiento, $Genero);

            #insertamos usuario como profesor
            $idUsuario = $email . 'Profesor';
            ingresaNewUser($email, $idUsuario, $nombres, $apellidos, $nombreCarpeta, $urlId, $vConex, 'Profesor', $secure_password, $FechaNacimiento, $Genero);

            #insertamos en la tabla Alumno
            ingresaNewPerson($email, $nombres, $apellidos, $FechaNacimiento, $Genero, 'Alumno', $vConex);
            #insertamos en la tabla Profesor
            ingresaNewPerson($email, $nombres, $apellidos, $FechaNacimiento, $Genero, 'Profesor', $vConex);
        }

        #insertamos en la tabla Usuario Entidad
        $dataInsertUsuarioEntidad = array(
            'Usuario' => $email,
            'Perfil' => $Perfil,
            'Genero' => $Genero,
            'FechaNacimiento' => $FechaNacimiento,
            'Nombres' => $nombres,
            'Apellidos' => $apellidos,
            'EntidadCreadora' => $entidadCreadora
        );
        $usuarioEntidadId = insert('usuario_entidad', $dataInsertUsuarioEntidad, $vConex);
        enviar_mail_usuario_compra($email, $vConex,$password,$entidadCreadora);
    } else {
        W(MsgCR("(!) Este usuario ya se encuentra registrado "));
    }
}



function enviar_mail_usuario_compra($email,$vConex,$contrasena,$entidadCreadora,$pagweb){
    $sql = "SELECT Nombres, Apellidos,Usuario FROM usuarios WHERE Usuario='$email' limit 1";
    $rg = fetch($sql);
    $n_a = ucwords(strtolower($rg["Nombres"])).' '.ucwords(strtolower($rg["Apellidos"]));
    $usuario=$rg['Usuario'];

    $n_a = (trim($n_a)!="")?$n_a:$usuario;
    
    $sql2="select Usuario,UrlId from usuarios where Usuario='$entidadCreadora'";
    $regusu=  fetch($sql2);
    $nombreEmp=$regusu['UrlId'];
    $hoy = date('Y-m-d');
    $hoy = FormatFechaText($hoy);


    //if($pagweb=="https://eguruclub.com/" || $pagweb=="http://eguruclub.com/"){
        $asunto = 'Accesos a eGuru Club';
                        $body = '
                            <div style="width:100%;height:252px;background-color:white;text-align:center"><img src="https://eguruclub.com/_imagenes/mail01.png"></div><br/><br/>
                            <div style="width:100%;display:flex">
                                <div style="width:10%;"></div>
                                <div style="width:80%;font-size:1.2em;font-family: Arial;display:flex;">

                                <div style="width:20%"></div>
                                <div style="width:60%" >
                                    
                                    <div style="width:100%;">Felicidades ! '.$n_a.'</div><br/>
                                        <div style="width:100%;font-weight: bold;">Tu suscripción fue realizada con éxito.</div><br/><br/>
                                        <div style="width:100%;text-align:center;color:rgba(0,112,192,1)"><b>Tus accesos son:</b></div><br/>
                                        <div style="width:100%;"><b>Usuario :</b> '.$usuario.'</div><br/>
                                        <div style="width:100%;"><b>Contraseña :</b> '.$contrasena.'</div><br/>
                                        <div style="width:100%;font-weight: bold;padding: 1% 12.5%;"><div style="background-color: #1578bc;color: white;padding: .6em;width: 70%;text-align: center;margin: 0 auto;"><a href="'.$pagweb.'" style="color: white;    text-decoration: none;">INGRESAR A LA PLATAFORMA</a></div></div>

                                </div>
                                <div style="width:20%"></div>
                                        
                                </div>
                                <div style="width:10%;"></div>
                            </div>

                            <div style="width:100%;display:flex;">
                                <div style="width:10%;"></div>
                                <div style="width:80%"><div style="width: 100%;height:10px;background: #1578bc;"></div></div>
                                <div style="width:10%;"></div>
                            </div>
                        ';
    /*}else{  
            $asunto = 'Usted fue inscrito a la plataforma';
            $body ='<div id=":2m" class="ii gt m144e03aa4c6b1558 adP adO">';
            $body .= '<div id=":1dd" class="a3s" style="overflow: hidden;">';
            $body .= '<div style="background-color:#e3e3e3;margin:0 auto;width:760px;min-height:425px;padding:20px 20px">';
            $body .= '<div style="float:left;width:90%;background-color:#fff;padding:10px 5% 0px 5%;font-size:0.9em;font-family:arial;color:#6b6b6b;min-height:100%">';
            $body .= '<div style="float:left;width:100%;padding:20px 0px;color:#6b6b6b">';
            $body .= '<div style="padding:10px 3px">'.$hoy.'</div>';
            $body .= '<div style="border-bottom:2px solid #e2e2e2;margin:10px 0px 30px 0px" class="adM"></div>';
            $body .= '<div style="font-size:1.5em;color:#6b6b6b;padding:0px 0px 15px 3px">Inscripción </div>';
            $body .= '<div style="font-size:1.2em;color:#6b6b6b;padding:5px 0px 5px 3px">Estimado (a) <span style="color:#35A9AD">' .$n_a . '</span> Su inscripción a la plataforma se realizó exitosamente.</div><br>';
            $body .= '</span> Su Usuario es: <span style="color:#35A9AD">'.$usuario .'</span> y su Password: <span style="color:#35A9AD">'.$contrasena .'</span>';
            $body .= '</div>';
            $body .= '<div style="float:left;width:100%;padding:35px 3px;color:#6b6b6b">';
            $body .= '<a href="'."https://owlgroup.org/".$nombreEmp.'" style="font-size:12px;border:1px solid #006ad0;padding:8px 30px;background-color:#4396de;color:#f9f9f9;text-decoration:none" target="_blank">Ingresar</a>';
            $body .= '</div>';
            $body .= '<div style="float:left;width:100%;padding:20px 0px;color:#6b6b6b">Atentamente </div>';
            $body .= '</div>';
            $body .= '</div>';
            $body .= '</div>';
            $body .= '</div>';
            $body .= '</div>';
    }*/
    
    $emailE = emailSES_comercializadora($n_a,$email,$asunto, $body );
    
    return $emailE;
}

function GeneratePassword(){
    $len_password=10;
    
    for($i=0;$i<$len_password;$i++){
        $new_password.=GenerateChar();
    }
    
    return $new_password;
}

function BoolRandom(){
    return (bool)rand(0,1);
}

function GenerateChar(){
    if(BoolRandom()){
        //Rango ASCII de numeros
        $rand_asii=rand(97,122);
    }else{
        if(BoolRandom()){
            //Rango ASCII de alfabeto minuscula
            $rand_asii=rand(48,57);
        }else{
            //Rango ASCII de alfabeto mayuscula
            $rand_asii=rand(65,90);
        }
    }

    return chr($rand_asii);
}

function ConceptoEvaluacion($vConex, $CodCursoAlmacen){
					
		$sql = 'SELECT 
		
		ACTIVIDAD.Codigo  AS ActividadCod
		, RECURSO.Codigo AS CodRecurso
		, RECURSO.RecursoTipo 
		, CONFIG_CONCEPTO.Codigo AS ConfigConcepto
		
		FROM elevaluaciondetallecurso AS ACTIVIDAD
		INNER JOIN elevaluacionconfcurso AS CONFIG_CONCEPTO  ON ACTIVIDAD.EvalConfigCurso = CONFIG_CONCEPTO.Codigo
		INNER JOIN elrecursoevaluacion AS RECURSO  ON ACTIVIDAD.Codigo = RECURSO.EvaluacionDetalleCurso
		WHERE CONFIG_CONCEPTO.Almacen = '.$CodCursoAlmacen.' ';
		$consultaB = Matris_Datos( $sql, $vConex );
		while ( $regB = mysql_fetch_array( $consultaB ) ) {
			$RecursoTipo = $regB["RecursoTipo"];	// 1 = Examen , 2 = Cuestionario, 5 = ArchivoAdjunto	
			$CodRecurso = $regB["CodRecurso"];	
			$Actividad = $regB["ActividadCod"];	
		    
				if($RecursoTipo == 1 ||  $RecursoTipo == 5){
				
					$sqlC = 'SELECT  Codigo FROM elpregunta WHERE RecursoEvaluacion= '.$CodRecurso.' ';
					$consultaC = Matris_Datos( $sqlC, $vConex );
					while ( $regC = mysql_fetch_array( $consultaC ) ) {		
						 $CodigoPregunta = $regC["Codigo"];
						 DReg( "elrespuesta","Pregunta", "" . $CodigoPregunta . "", $vConex );					 
					}				
					  
					DReg( "eltransrespuesta","RecursoEvaluacion", "" . $CodRecurso . "", $vConex );
					DReg( "elpregunta","RecursoEvaluacion", "" . $CodRecurso . "", $vConex );
				}
				
				 DReg( "eltransrespuesta_cab","Recurso", "" . $CodRecurso . "", $vConex );			
				 DReg( "elrecursoevaluacion","Codigo", "" . $CodRecurso . "", $vConex );			
				 DReg( "elevaluacionalumno","EvalDetCurso", "" . $Actividad . "", $vConex );			
				 DReg( "elevaluaciondetallecurso","Codigo", "" . $Actividad . "", $vConex );			
				 DReg( "elevaluacionconfcurso","Almacen", "" . $CodCursoAlmacen . "", $vConex );

                 W("entre al recurso ");

		}
	return;	
}

function eliminarUsuario($CodAlumno,$vConex){

		$sql = "SELECT Usuario FROM usuario_entidad WHERE Codigo = $CodAlumno ";
		$rg = fetch($sql);
		$Alumno = $rg["Usuario"];

		$deleltransrespuesta_cab="DELETE FROM eltransrespuesta_cab WHERE  Alumno='". $Alumno .'Alumno'."' OR  Alumno='".$Alumno.'Profesor'."'";
		xSQL($deleltransrespuesta_cab, $vConex);

		$deleltransrespuesta= "DELETE FROM eltransrespuesta WHERE  Usuario='".$Alumno.'Alumno'."' OR Usuario='".$Alumno.'Profesor'."'";
		xSQL( $deleltransrespuesta, $vConex);

		$delevaluacionalumno="DELETE FROM elevaluacionalumno WHERE  Alumno='".$Alumno.'Alumno'."' OR  Alumno='".$Alumno.'Profesor'."'";
		xSQL($delevaluacionalumno, $vConex);

		$delusuarios = "DELETE from usuarios WHERE Usuario='".$Alumno."'";
		xSQL($delusuarios, $vConex);

		$delusuario_det = "DELETE from usuario_entidad WHERE Usuario='".$Alumno."'";
		xSQL($delusuario_det, $vConex);

		$delmatricula = "DELETE from matriculas WHERE Cliente='".$Alumno.'Alumno'."' OR Cliente='".$Alumno.'Profesor'."'";
		xSQL($delmatricula, $vConex);

		$delalmacen_entidad = "DELETE from almacen_entidad WHERE Entidad='".$Alumno.'Alumno'."' OR Entidad='".$Alumno.'Profesor'."'";
		xSQL($delalmacen_entidad, $vConex);

		$delalumnos = "DELETE from alumnos WHERE Usuario='".$Alumno.'Alumno'."' OR Usuario='".$Alumno.'Profesor'."'";
		xSQL($delalumnos, $vConex);

		$delprofesores = "DELETE from profesores WHERE Usuario='".$Alumno.'Alumno'."' OR Usuario='".$Alumno.'Profesor'."'";
		xSQL($delprofesores, $vConex);

		$SqlAsis=" SELECT Codigo FROM asistencia WHERE Participante = '".$Alumno."' ";
		$Mx = fetchAll($SqlAsis, $vConex);
		foreach ($Mx as $Asis) {
			$Codigo = $Asis->Codigo;
			$Delete = "DELETE FROM interacciones WHERE Asistencia = ".$Codigo." ";
			xSQL($Delete, $vConex);
		}
		$Delete = "DELETE FROM asistencia WHERE Participante = '".$Alumno."' ";
		xSQL($Delete, $vConex);

		$SqlAsis_2=" SELECT Codigo FROM asistencia_alumno_control WHERE Profesor = '".$Alumno.'Profesor'."' ";
		$Mx = fetchAll($SqlAsis_2, $vConex);
		foreach ($Mx as $Asis) {
			$Codigo = $Asis->Codigo;
			$Delete = "DELETE FROM asistencia_alumno WHERE Control = ".$Codigo." ";
			xSQL($Delete, $vConex);
		}
		$Delete = "DELETE FROM asistencia_alumno_control WHERE Profesor = '".$Alumno.'Profesor'."' ";
		xSQL($Delete, $vConex);

		$Delete = "DELETE FROM programapresentaciones WHERE Alumno = '".$Alumno.'Alumno'."' ";
		xSQL($Delete, $vConex);

		W(MsgE('Usuario Eliminado: '.$Alumno).'<br>');

		/** NOTIFICANDO A ECOMMERCE DE ELIMINACION DE UN USUARIO **/
		// fyupanquia 05:57 p.m. 20/01/2016
		// ALUMNO
		$email=$Alumno;
		$fields = array(
			'action'        => urlencode("delUsuario"),
			'email'         => urlencode($email),
		);

		$fields_string = '';
		foreach ($fields as $key => $value) {
			$fields_string .= $key.'='.$value.'&';
		}
		rtrim($fields_string, '&');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://owlecomerce.com/_vistas/Services/Usuarios.php");
		curl_setopt($ch, CURLOPT_POST, count($fields));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
		$result = curl_exec($ch);
		curl_close($ch);
	return "termino";			
}

function eliminarPrograma($AlmacenCod,$vConex){

	$sql = 'SELECT ProductoCod, ProgramaCod FROM curricula WHERE CodProgAlmacen = '.$AlmacenCod.' ';
	$consulta = Matris_Datos( $sql, $vConex );
	while ( $reg = mysql_fetch_array( $consulta ) ) {

		$sql =  "SELECT AL.AlmacenCod AS CodAlmacenCurso, AR.ProductoFab, AR.IdArticulo FROM almacen AS AL ";
		$sql .=  " INNER JOIN articulos AS AR ON AL.Producto = AR.Producto   ";
		$sql .=  " WHERE  AL.AlmacenCod = ".$reg["ProductoCod"]."  ";

		$rg = fetch($sql);
		$CodAlmacenCurso = $rg["CodAlmacenCurso"];
		if(empty($CodAlmacenCurso )){  $CodAlmacenCurso = 0;}						
		$ProductoFab = $rg["ProductoFab"];
		if(empty($ProductoFab )){  $ProductoFab = 0;}
		$IdArticulo = $rg["IdArticulo"];
		if(empty($IdArticulo )){  $IdArticulo = 0;}		
		$ProgramaCod = $reg["ProgramaCod"];
		if(empty($ProgramaCod )){  $ProgramaCod = 0;}	
		
		ConceptoEvaluacion($vConex,$CodAlmacenCurso);
	
		DReg( "programas","CodPrograma", "" . $ProgramaCod . "", $vConex );
		DReg( "introduccion","Producto", "" . $ProgramaCod . "", $vConex );
		DReg( "almacen", "AlmacenCod", "" . $CodAlmacenCurso . "", $vConex );
		DReg( "articulos", "IdArticulo", "" . $IdArticulo . "", $vConex );
		DReg( "cursos", "CodCursos", "" . $ProductoFab. "", $vConex );
		
			$sql = 'SELECT CodTema FROM tema WHERE Curso = '.$ProductoFab.' ';
			$consultaB = Matris_Datos( $sql, $vConex );
			while ( $regB = mysql_fetch_array( $consultaB ) ) {
				 DReg( "subtema", "Tema", "" . $regB["CodTema"]. "", $vConex );				
			}			
		
		DReg( "tema", "Curso", "" . $ProductoFab. "", $vConex );
		DReg( "elconfiguracioncronograma", "CodCurso", "" . $ProductoFab. "", $vConex );	
		DReg( "almacen_entidad", "CodAlmacenContenedor", "" . $AlmacenCod. "", $vConex );						
			
	}
		DReg( "curricula", "CodProgAlmacen", "" . $AlmacenCod. "", $vConex );			
		DReg( "matriculas", "Producto", "" . $AlmacenCod. "", $vConex );			
		DReg( "lista_trabajo_det", "CodigoAlmacen", "" . $AlmacenCod. "", $vConex );				
}

function programa_solo($AlmacenCod,$vConex){

	$sql =  "SELECT AL.AlmacenCod AS CodAlmacenCurso, AR.ProductoFab, AR.IdArticulo FROM almacen AS AL ";
	$sql .=  " INNER JOIN articulos AS AR ON AL.Producto = AR.Producto   ";
	$sql .=  " WHERE  AL.AlmacenCod = ".$AlmacenCod."  ";
	$rg = fetch($sql);
	$CodAlmacenCurso = $rg["CodAlmacenCurso"];
	$ProductoFab = $rg["ProductoFab"];
	$IdArticulo = $rg["IdArticulo"];
	// WE($ProductoFab);
	if($ProductoFab == 0 ||  empty($ProductoFab)) {  $ProductoFab = 0; }
	if($IdArticulo == 0 ||  empty($IdArticulo)) {  $IdArticulo = 0; }
	if($CodAlmacenCurso == 0 ||  empty($CodAlmacenCurso)) {  $CodAlmacenCurso = 0; }
	
			
	DReg( "programas","CodPrograma", "" . $ProductoFab . "", $vConex );
	DReg( "almacen", "AlmacenCod", "" . $CodAlmacenCurso . "", $vConex );
	DReg( "articulos", "IdArticulo", "" . $IdArticulo . "", $vConex );				
	DReg( "lista_trabajo_det", "CodigoAlmacen", "" . $AlmacenCod. "", $vConex );							
	DReg( "matriculas", "Producto", "" . $AlmacenCod. "", $vConex );							
	DReg( "almacen_entidad", "CodAlmacenContenedor", "" . $AlmacenCod. "", $vConex );		
	W(xSQL($sql, $vConex));
	
}

function elimina_curso($AlmacenCod, $vConex ){

		$sql =  "SELECT AL.AlmacenCod AS CodAlmacenCurso, AR.ProductoFab, AR.IdArticulo FROM almacen AS AL ";
		$sql .=  " INNER JOIN articulos AS AR ON AL.Producto = AR.Producto   ";
		$sql .=  " WHERE  AL.AlmacenCod = ".$AlmacenCod."  ";
		$rg = fetch($sql);
		$CodAlmacenCurso = $rg["CodAlmacenCurso"];
		$ProductoFab = $rg["ProductoFab"];
		$IdArticulo = $rg["IdArticulo"];
				
		DReg( "cursos", "CodCursos", "'" . $ProductoFab. "'", $vConex );
		DReg( "almacen", "AlmacenCod", "" . $CodAlmacenCurso . "", $vConex );
		DReg( "articulos", "IdArticulo", "" . $IdArticulo . "", $vConex );		
		
			$sql = 'SELECT CodTema FROM tema WHERE Curso = '.$ProductoFab.' ';
			$consultaB = Matris_Datos( $sql, $vConex );
			while ( $regB = mysql_fetch_array( $consultaB ) ) {
			DReg( "subtema", "Tema", "" . $regB["CodTema"]. "", $vConex );				
			}			

			DReg( "tema", "Curso", "" . $ProductoFab. "", $vConex );
			DReg( "elconfiguracioncronograma", "CodCurso", "" . $ProductoFab. "", $vConex );	
			
		ConceptoEvaluacion($vConex,$CodAlmacenCurso);	
		DReg( "lista_trabajo_det", "CodigoAlmacen", "" . $AlmacenCod. "", $vConex );	
		DReg( "curricula", "ProductoCod", "" . $AlmacenCod. "", $vConex );	
		

}
