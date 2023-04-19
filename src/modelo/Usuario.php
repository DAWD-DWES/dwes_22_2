<?php

namespace App\Modelo;

// PDO se usa para interaccionar con la base de datos relacional
use \PDO as PDO;

/**
 * Usuario representa al usuario que está usando la aplicación
 */
class Usuario {

    /**
     * Identificador del usuario
     */
    private $id;

    /**
     * nombre del usuario
     */
    private $nombre;

    /**
     * Clave del usuario
     */
    private $clave;

    /**
     * Email del usuario
     */
    private $email;

    /**
     * Constructor de la clase Usuario
     * 
     * @param string $nombre Nombre del usuario
     * @param string $clave Clave del usuario
     * @param string $email Email del usuario
     * 
     * @returns Hangman
     */
    public function __construct(string $nombre = null, string $clave = null, string $email = null) {
        if (!is_null($nombre)) {
            $this->nombre = $nombre;
        }
        if (!is_null($clave)) {
            $this->clave = $clave;
        }
        if (!is_null($email)) {
            $this->email = $email;
        }
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getNombre(): string {
        return $this->nombre;
    }

    public function setNombre(string $nombre) {
        $this->nombre = $nombre;
    }

    public function getClave(): string {
        return $this->clave;
    }

    public function setClave(string $clave) {
        $this->clave = $clave;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $email) {
        $this->email = $email;
    }

    

    /**
     * Persiste la información del objeto usuario en la base de datos
     * 
     * @param PDO $bd Conexión a la base de datos
     * 
     * @returns bool Verdadero si ya se ha persistido correctamente y falso en caso contrario
     */
    public function persiste(PDO $bd): bool {
        $sql = "update usuarios set nombre = :nombre, clave = :clave, email = :email where id = :id";
        $sth = $bd->prepare($sql);
        $result = $sth->execute([":nombre" => $this->nombre, ":clave" => $this->clave, ":email" => $this->email, ":id" => $this->id]);
        return ($result);
    }

}
