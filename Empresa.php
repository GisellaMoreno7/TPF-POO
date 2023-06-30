<?php
include_once 'BaseDatos.php';

class Empresa
{
    //Atributos
    private $idempresa;
    private $enombre;
    private $edireccion;
    private $mensajeoperacion;

    //Método constructor
    public function __construct()
    {
        $this->idempresa = 0;
        $this->enombre = "";
        $this->edireccion = "";
    }

    //Método cargar
    public function cargar($id, $nombre, $direccion)
    {
        $this->setidempresa($id);
        $this->setenombre($nombre);
        $this->setedireccion($direccion);
    }

    //Getters
    public function getidempresa()
    {
        return $this->idempresa;
    }
    public function getenombre()
    {
        return $this->enombre;
    }
    public function getedireccion()
    {
        return $this->edireccion;
    }
    public function getmensajeoperacion()
    {
        return $this->mensajeoperacion;
    }

    //Setters
    public function setidempresa($dato)
    {
        $this->idempresa = $dato;
    }
    public function setenombre($dato)
    {
        $this->enombre = $dato;
    }
    public function setedireccion($dato)
    {
        $this->edireccion = $dato;
    }
    public function setmensajeoperacion($mensajeoperacion)
    {
        $this->mensajeoperacion = $mensajeoperacion;
    }

    //Métodos de la clase

    /**
     * Recupera datos de una empresa por id, retorna true si
     * lo encuentra, false caso contrario.
     * @param int $dni
     * @return boolean
     */
    public function buscar($idEmpresa)
    {
        $base = new BaseDatos();
        $resp = false;
        $consulta = "SELECT * FROM empresa WHERE idempresa =" . $idEmpresa;

        if ($base->Iniciar()) {
            if ($base->Ejecutar($consulta)) {
                if ($fila = $base->Registro()) {
                    $this->cargar($idEmpresa, $fila["enombre"], $fila["edireccion"]);
                    $resp = true;
                } else {
                    $this->setmensajeoperacion($base->getError());
                }
            } else {
                $this->setmensajeoperacion($base->getError());
            }
        }
        return $resp;
    }

    /**
     * Lista grupo de registros, retorna colección de ellos, en caso de
     * no encontrar nada, retorna array vacío
     * @param string $consulta
     * @return array
     */
    public function listar($condicion = "")
    {
        $base = new BaseDatos();
        $colEmpresas = [];
        $consulta = "SELECT * FROM empresa ";

        if ($condicion != "") {
            $consulta .= "WHERE " . $condicion;
        }
        $consulta .= " ORDER BY idempresa";

        if ($base->Iniciar()) {
            if ($base->Ejecutar($consulta)) {
                $colEmpresas = [];
                while ($fila = $base->Registro()) {
                    $empresa = new Empresa;
                    $empresa->cargar($fila["idempresa"], $fila["enombre"], $fila["edireccion"]);
                    array_push($colEmpresas, $empresa);
                }
            } else {
                $this->setmensajeoperacion($base->getError());
            }
        } else {
            $this->setmensajeoperacion($base->getError());
        }
        return $colEmpresas;
    }

    /**
     * Inserta datos de una empresa a la base de datos, retorna
     * true si la inserción fue exitosa, false en caso contrario
     * @return boolean
     */
    public function insertar()
    {
        $base = new BaseDatos();
        $resp = false;
        $consulta = "INSERT INTO empresa (enombre, edireccion)
        VALUES ('" . $this->getenombre() . "','" . $this->getedireccion() . "')";

        if ($base->Iniciar()) {
            if ($idEmpresa = $base->devuelveIDInsercion($consulta)) {
                $this->setidempresa($idEmpresa);
                $resp = true;
            } else {
                $this->setmensajeoperacion($base->getError());
            }
        } else {
            $this->setmensajeoperacion($base->getError());
        }
        return $resp;
    }

    /**
     * Modifica datos de algún registro, retorna true en caso de que
     * la actualización haya sido exitosa, false en caso contrario
     * @return boolean
     */
    public function modificar()
    {
        $base = new BaseDatos();
        $resp = false;
        $consulta = "UPDATE empresa SET enombre = '" . $this->getenombre() . "', edireccion = '" . $this->getedireccion() . "' WHERE idempresa =" . $this->getidempresa();

        if ($base->Iniciar()) {
            if ($base->Ejecutar($consulta)) {
                $resp = true;
            } else {
                $this->setmensajeoperacion($base->getError());
            }
        } else {
            $this->setmensajeoperacion($base->getError());
        }
        return $resp;
    }

    /**
     * Elimina datos de algún registro, retorna true en caso de que
     * la eliminación haya sido exitosa, false en caso contrario
     * @return boolean
     */
    public function eliminar()
    {
        $base = new BaseDatos();
        $resp = false;
        $consulta = "DELETE FROM empresa WHERE idempresa = " . $this->getidempresa();
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consulta)) {
                $resp = true;
            } else {
                $this->setmensajeoperacion($base->getError());
            }
        } else {
            $this->setmensajeoperacion($base->getError());
        }
        return $resp;
    }

    /**
     * Elimina viajes relacionados a la empresa, y luego se elimina
     * a si misma
     */
    public function eliminarEmpresa()
    {
        $viajes = new Viaje;
        $condicion = "idempresa =" . $this->getidempresa();
        $colViajes = $viajes->listar($condicion);

        foreach ($colViajes as $viajes) {
            $viajes->eliminar();
        }
        $this->eliminar();
    }

    public function __toString()
    {
        return "ID empresa: " . $this->getidempresa() . "\nNombre: " . $this->getenombre() .
            "\nDirección: " . $this->getedireccion() . "\n";
    }
}
