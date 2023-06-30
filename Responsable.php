<?php
include_once 'BaseDatos.php';

class Responsable
{
    //Atributos
    private $rnumeroempleado;
    private $rnumerolicencia;
    private $rnombre;
    private $rapellido;
    private $mensajeoperacion;

    //Método constructor
    public function __construct()
    {
        $this->rnumeroempleado = 0;
        $this->rnumerolicencia = "";
        $this->rnombre = "";
        $this->rapellido = "";
    }

    //Método cargar
    public function cargar($nroempleado, $nrolicencia, $nom, $ape)
    {
        $this->setrnumeroempleado($nroempleado);
        $this->setrnumerolicencia($nrolicencia);
        $this->setrnombre($nom);
        $this->setrapellido($ape);
    }

    //Getters
    public function getrnumeroempleado()
    {
        return $this->rnumeroempleado;
    }
    public function getrnumerolicencia()
    {
        return $this->rnumerolicencia;
    }
    public function getrnombre()
    {
        return $this->rnombre;
    }
    public function getrapellido()
    {
        return $this->rapellido;
    }
    public function getmensajeoperacion()
    {
        return $this->mensajeoperacion;
    }

    //Setters
    public function setrnumeroempleado($dato)
    {
        $this->rnumeroempleado = $dato;
    }
    public function setrnumerolicencia($dato)
    {
        $this->rnumerolicencia = $dato;
    }
    public function setrnombre($dato)
    {
        $this->rnombre = $dato;
    }
    public function setrapellido($dato)
    {
        $this->rapellido = $dato;
    }
    public function setmensajeoperacion($mensajeoperacion)
    {
        $this->mensajeoperacion = $mensajeoperacion;
    }

    //Métodos de la clase

    /**
     * Recupera datos de un responsable por su número de responsable, retorna true si
     * lo encuentra, false caso contrario.
     * @param int $dni
     * @return boolean
     */
    public function buscar($nroEmpleado)
    {
        $base = new BaseDatos();
        $resp = false;
        $consulta = "SELECT * FROM responsable WHERE rnumeroempleado =" . $nroEmpleado;

        if ($base->Iniciar()) {
            if ($base->Ejecutar($consulta)) {
                if ($fila = $base->Registro()) {
                    $this->cargar($nroEmpleado, $fila["rnumerolicencia"], $fila["rnombre"], $fila["rapellido"]);
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
        $colResponsables = [];
        $consulta = "SELECT * FROM responsable ";

        if ($condicion != "") {
            $consulta .= "WHERE " . $condicion;
        }
        $consulta .= " ORDER BY rapellido";

        if ($base->Iniciar()) {
            if ($base->Ejecutar($consulta)) {
                $colResponsables = [];
                while ($fila = $base->Registro()) {
                    $responsable = new Responsable;
                    $responsable->cargar($fila["rnumeroempleado"], $fila["rnumerolicencia"], $fila["rnombre"], $fila["rapellido"]);
                    array_push($colResponsables, $responsable);
                }
            } else {
                $this->setmensajeoperacion($base->getError());
            }
        } else {
            $this->setmensajeoperacion($base->getError());
        }
        return $colResponsables;
    }

    /**
     * Inserta datos de un responsable a la base de datos, retorna
     * true si la inserción fue exitosa, false en caso contrario
     * @return boolean
     */
    public function insertar()
    {
        $base = new BaseDatos();
        $resp = false;
        $consulta = "INSERT INTO responsable (rnumerolicencia, rnombre, rapellido) 
		VALUES ('" . $this->getrnumerolicencia() . "','" . $this->getrnombre() . "','" . $this->getrapellido() . "')";

        if ($base->Iniciar()) {
            if ($idEmpleado = $base->devuelveIDInsercion($consulta)) {
                $this->setrnumeroempleado($idEmpleado);
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
        $consulta = "UPDATE responsable SET rnumerolicencia = " . $this->getrnumerolicencia() . ", rnombre = '" . $this->getrnombre() . "', rapellido = '" . $this->getrapellido() .
            "' WHERE rnumeroempleado =" . $this->getrnumeroempleado();

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
        $consulta = "DELETE FROM responsable WHERE rnumeroempleado = " . $this->getrnumeroempleado();
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

    public function __toString()
    {
        return "Número de empleado: " . $this->getrnumeroempleado() . "\nNúmero de licencia: " .
            $this->getrnumerolicencia() . "\nNombre del responsable: " . $this->getrnombre() . " " . $this->getrapellido() . "\n";
    }
}
