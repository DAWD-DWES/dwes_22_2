<?php

namespace App\DAO;

use PDO;
use App\Modelo\Usuario;

class UsuarioDAO {

    private $bd;

    function __construct($bd) {
        $this->bd = $bd;
    }

    function crea($usuario) {
        
    }

    function modifica($usuario) {
        $sql = "update usuarios set nombre = :nombre, clave = :clave, email = :email where id = :id";
        $sth = $this->bd->prepare($sql);
        $result = $sth->execute([":nombre" => $usuario->nombre, ":clave" => $usuario->clave, ":email" => $usuario->email, ":id" => $usuario->id]);
        return ($result);
    }

    function elimina($nombre) {
        
    }

    function recuperaPorCredencial($nombre, $pwd) {
        $this->bd->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $sql = 'select * from usuarios where nombre=:nombre and clave=:pwd';
        $sth = $this->bd->prepare($sql);
        $sth->execute([":nombre" => $nombre, ":pwd" => $pwd]);
        $sth->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Usuario::class);
        $usuario = ($sth->fetch()) ?: null;
        return $usuario;
    }

}
