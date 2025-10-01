<?php

// Asume que tienes un archivo de conexión.php que define la función getConexion()
require_once __DIR__ . '/../config/conexion.php'; 

class Usuario {
    // Propiedades de la tabla 'usuarios'
    private $ID_usuario;
    private $usuario_nombre;
    private $usuario_correo_electronico;
    private $usuario_contraseña; // ¡Debe ser el hash de la contraseña!
    private $usuario_numero_de_celular;
    private $usuario_fecha_de_registro;
    private $RELA_persona;
    private $RELA_perfil;

    /**
     * Constructor de la clase Usuario.
     */
    public function __construct(
        $ID_usuario = null,
        $usuario_nombre,
        $usuario_correo_electronico,
        $usuario_contraseña,
        $RELA_persona,
        $RELA_perfil,
        $usuario_numero_de_celular = null,
        $usuario_fecha_de_registro = null
    ) {
        $this->ID_usuario = $ID_usuario;
        $this->usuario_nombre = $usuario_nombre;
        $this->usuario_correo_electronico = $usuario_correo_electronico;
        $this->usuario_contraseña = $usuario_contraseña;
        $this->usuario_numero_de_celular = $usuario_numero_de_celular;
        $this->usuario_fecha_de_registro = $usuario_fecha_de_registro;
        $this->RELA_persona = $RELA_persona;
        $this->RELA_perfil = $RELA_perfil;
    }

    // ------------------------------------
    //  MÉTODOS CRUD (CREATE, READ, UPDATE, DELETE)
    // ------------------------------------

    /**
     * Guarda un nuevo usuario en la base de datos.
     * @return bool True si la operación es exitosa, False en caso contrario.
     */
    public function guardar() {
        $pdo = getConexion(); // Asume que getConexion() devuelve un objeto PDO
        
        // La columna usuario_fecha_de_registro usa DEFAULT CURRENT_TIMESTAMP, no la incluimos.
        $sql = 'INSERT INTO usuarios (
                    usuario_nombre, 
                    usuario_correo_electronico, 
                    usuario_contraseña, 
                    usuario_numero_de_celular, 
                    RELA_persona, 
                    RELA_perfil
                ) VALUES (
                    :nombre, 
                    :correo, 
                    :contraseña, 
                    :celular, 
                    :rela_persona, 
                    :rela_perfil
                )';
                
        $stmt = $pdo->prepare($sql);
        
        $parametros = [
            'nombre'        => $this->usuario_nombre,
            'correo'        => $this->usuario_correo_electronico,
            'contraseña'    => $this->usuario_contraseña, // **¡Debe ser un hash!**
            'celular'       => $this->usuario_numero_de_celular,
            'rela_persona'  => $this->RELA_persona,
            'rela_perfil'   => $this->RELA_perfil
        ];
        
        $resultado = $stmt->execute($parametros);
        
        // Si la inserción fue exitosa, actualiza el ID del objeto
        if ($resultado) {
            $this->ID_usuario = $pdo->lastInsertId();
        }
        
        return $resultado;
    }

    /**
     * Obtiene un usuario por su ID y retorna un objeto Usuario.
     * @param int $ID_usuario El ID del usuario a buscar.
     * @return Usuario|null El objeto Usuario si se encuentra, o null si no existe.
     */
    public static function obtenerPorId($ID_usuario) {
        $pdo = getConexion();
        $sql = 'SELECT * FROM usuarios WHERE ID_usuario = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $ID_usuario]);
        
        $registro = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($registro) {
            // Se usa el unpacking para pasar todos los datos al constructor
            return new Usuario(
                $registro['ID_usuario'],
                $registro['usuario_nombre'],
                $registro['usuario_correo_electronico'],
                $registro['usuario_contraseña'],
                $registro['RELA_persona'],
                $registro['RELA_perfil'],
                $registro['usuario_numero_de_celular'],
                $registro['usuario_fecha_de_registro']
            );
        }
        return null;
    }

    /**
     * Actualiza los datos del usuario actual en la base de datos.
     * @return bool True si la operación es exitosa, False en caso contrario.
     */
    public function editar() {
        if (is_null($this->ID_usuario)) {
            return false; // No se puede editar si no tiene ID
        }

        $pdo = getConexion();
        $sql = 'UPDATE usuarios SET 
                    usuario_nombre = :nombre, 
                    usuario_correo_electronico = :correo, 
                    usuario_contraseña = :contraseña, 
                    usuario_numero_de_celular = :celular, 
                    RELA_persona = :rela_persona, 
                    RELA_perfil = :rela_perfil 
                WHERE ID_usuario = :id';
                
        $stmt = $pdo->prepare($sql);
        
        $parametros = [
            'nombre'        => $this->usuario_nombre,
            'correo'        => $this->usuario_correo_electronico,
            'contraseña'    => $this->usuario_contraseña,
            'celular'       => $this->usuario_numero_de_celular,
            'rela_persona'  => $this->RELA_persona,
            'rela_perfil'   => $this->RELA_perfil,
            'id'            => $this->ID_usuario
        ];
        
        return $stmt->execute($parametros);
    }
    
    /**
     * Elimina el usuario actual de la base de datos.
     * @return bool True si la operación es exitosa, False en caso contrario.
     */
    public function eliminar() {
        if (is_null($this->ID_usuario)) {
            return false; // No se puede eliminar si no tiene ID
        }

        $pdo = getConexion();
        $sql = 'DELETE FROM usuarios WHERE ID_usuario = :id';
        $stmt = $pdo->prepare($sql);
        $resultado = $stmt->execute(['id' => $this->ID_usuario]);
        
        // Si se elimina de la BD, limpia el ID del objeto
        if ($resultado) {
            $this->ID_usuario = null;
        }
        
        return $resultado;
    }


    // ------------------------------------
    //  GETTERS Y SETTERS
    // ------------------------------------
    
    // Getters para acceder a las propiedades
    public function getId() { return $this->ID_usuario; }
    public function getNombre() { return $this->usuario_nombre; }
    public function getCorreo() { return $this->usuario_correo_electronico; }
    public function getContraseña() { return $this->usuario_contraseña; }
    public function getCelular() { return $this->usuario_numero_de_celular; }
    public function getFechaRegistro() { return $this->usuario_fecha_de_registro; }
    public function getRelaPersona() { return $this->RELA_persona; }
    public function getRelaPerfil() { return $this->RELA_perfil; }

    // Setters para modificar las propiedades
    public function setId($ID_usuario) { $this->ID_usuario = $ID_usuario; }
    public function setNombre($usuario_nombre) { $this->usuario_nombre = $usuario_nombre; }
    public function setCorreo($usuario_correo_electronico) { $this->usuario_correo_electronico = $usuario_correo_electronico; }
    public function setContraseña($usuario_contraseña) { $this->usuario_contraseña = $usuario_contraseña; }
    public function setCelular($usuario_numero_de_celular) { $this->usuario_numero_de_celular = $usuario_numero_de_celular; }
    // La fecha de registro normalmente no se edita después de la creación
    public function setRelaPersona($RELA_persona) { $this->RELA_persona = $RELA_persona; }
    public function setRelaPerfil($RELA_perfil) { $this->RELA_perfil = $RELA_perfil; }
}

?>