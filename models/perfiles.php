<?php

require_once __DIR__ . '/../config/conexion.php';

class Perfil {
    private $id_perfil;
    private $perfil_rol;

    public function __construct($id_perfil, $perfil_rol){
        $this->id_perfil = $id_perfil;
        $this->perfil_rol = $perfil_rol;
    }

    public function guardar(){
        $pdo = getConexion();
        $sql = 'INSERT INTO perfiles (perfil_rol) VALUES (:perfil_rol)';
        $stmt = $pdo->prepare($sql);
        $resultado = $stmt->execute(['perfil_rol' => $this->perfil_rol]);
        return $resultado;
    }

    public function getNombre(){
        return $this->perfil_rol;
    }

    public function setNombre($perfil_rol){
        $this->perfil_rol = $perfil_rol;
    }

    public function getId(){
        return $this->id_perfil;
    }

    public function setId($id_perfil){
        $this->id_perfil = $id_perfil;
    }
}
