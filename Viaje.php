<?php
include_once 'BaseDatos.php';

class Viaje
{
    //Atributos
    private $idviaje;
    private $vdestino;
    private $vcantmaxpasajeros;
    private $objEmpresa;
    private $objEmpleado;
    private $vimporte;
    private $mensajeoperacion;

    //Método constructor
    public function __construct()
    {
        $this->idviaje = 0;
        $this->vdestino = "";
        $this->vcantmaxpasajeros = "";
        $this->objEmpresa = null;
        $this->objEmpleado = null;
        $this->vimporte = "";
    }

    //Método cargar
    public function cargar($id, $destino, $cantmaxpas, $objEmpre, $objEmple, $importe)
    {
        $this->setidviaje($id);
        $this->setvdestino($destino);
        $this->setvcantmaxpasajeros($cantmaxpas);
        $this->setobjEmpresa($objEmpre);
        $this->setobjEmpleado($objEmple);
        $this->setvimporte($importe);
    }

    //Getters
    public function getidviaje()
    {
        return $this->idviaje;
    }
    public function getvdestino()
    {
        return $this->vdestino;
    }
    public function getvcantmaxpasajeros()
    {
        return $this->vcantmaxpasajeros;
    }
    /**
     * @return Empresa
     */
    public function getobjEmpresa()
    {
        return $this->objEmpresa;
    }
    /**
     * @return Responsable
     */
    public function getobjEmpleado()
    {
        return $this->objEmpleado;
    }
    public function getvimporte()
    {
        return $this->vimporte;
    }
    public function getmensajeoperacion()
    {
        return $this->mensajeoperacion;
    }

    //Setters
    public function setidviaje($dato)
    {
        $this->idviaje = $dato;
    }
    public function setvdestino($dato)
    {
        $this->vdestino = $dato;
    }
    public function setvcantmaxpasajeros($dato)
    {
        $this->vcantmaxpasajeros = $dato;
    }
    public function setobjEmpresa($dato)
    {
        $this->objEmpresa = $dato;
    }
    public function setobjEmpleado($dato)
    {
        $this->objEmpleado = $dato;
    }
    public function setvimporte($dato)
    {
        $this->vimporte = $dato;
    }
    public function setmensajeoperacion($mensajeoperacion)
    {
        $this->mensajeoperacion = $mensajeoperacion;
    }

    //Métodos de la clase

    /**
     * Recupera datos de un viaje por id, retorna true si
     * lo encuentra, false caso contrario.
     * @param int $idViaje
     * @return boolean
     */
    public function buscar($idViaje)
    {
        $base = new BaseDatos();
        $resp = false;
        $consulta = "SELECT * FROM viaje WHERE idviaje =" . $idViaje;

        if ($base->Iniciar()) {
            if ($base->Ejecutar($consulta)) {
                if ($fila = $base->Registro()) {
                    $objEmpresa = new Empresa;
                    $objResponsable = new Responsable;
                    $this->cargar($idViaje, $fila["vdestino"], $fila["vcantmaxpasajeros"], $objEmpresa->buscar($fila["idempresa"]), $objResponsable->buscar($fila["rnumeroempleado"]), $fila["vimporte"]);
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
        $colViajes = [];
        $consulta = "SELECT * FROM viaje ";

        if ($condicion != "") {
            $consulta .= "WHERE " . $condicion;
        }
        $consulta .= " ORDER BY idviaje";

        if ($base->Iniciar()) {
            if ($base->Ejecutar($consulta)) {
                while ($fila = $base->Registro()) {
                    //Corregí que listar() utilice buscar()
                    $idResponsable = $fila['rnumeroempleado'];
                    $responsable = new Responsable;
                    $responsable->buscar($idResponsable);
                    if ($responsable == false) {
                        $responsable = null;
                    }

                    $idEmpresa = $fila['idempresa'];
                    $empresa = new Empresa;
                    $empresa->buscar($idEmpresa);
                    if ($empresa == false) {
                        $empresa = null;
                    }

                    $viaje = new Viaje;
                    $viaje->cargar($fila['idviaje'], $fila['vdestino'], $fila['vcantmaxpasajeros'], $empresa, $responsable, $fila['vimporte']);
                    array_push($colViajes, $viaje);
                }
            } else {
                $this->setmensajeoperacion($base->getError());
            }
        } else {
            $this->setmensajeoperacion($base->getError());
        }
        return $colViajes;
    }

    /**
     * Inserta datos de un viaje a la base de datos, retorna
     * true si la inserción fue exitosa, false en caso contrario
     * @return boolean
     */
    public function insertar()
    {
        $base = new BaseDatos();
        $resp = false;
        $consulta = "INSERT INTO viaje(vdestino, vcantmaxpasajeros, idempresa, rnumeroempleado, vimporte) 
		VALUES ('" . $this->getvdestino() . "','" . $this->getvcantmaxpasajeros() . "','" . $this->getobjEmpresa()->getidempresa() . "','" . $this->getobjEmpleado()->getrnumeroempleado() . "','" . $this->getvimporte() . "')";

        if ($base->Iniciar()) {
            if ($idViaje = $base->devuelveIDInsercion($consulta)) {
                $this->setidviaje($idViaje);
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
        $idEmpresa = $this->getobjEmpresa()->getidempresa();
        $idEmpleado = $this->getobjEmpleado()->getrnumeroempleado();

        $consulta = "UPDATE viaje SET idviaje = " . $this->getidviaje() . ", vdestino = '" . $this->getvdestino() . "', vcantmaxpasajeros = "
            . $this->getvcantmaxpasajeros() . ", idempresa =" . $idEmpresa . ", rnumeroempleado =" . $idEmpleado . ", vimporte =" . $this->getvimporte() . " WHERE idviaje =" . $this->getidviaje();

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
        $consulta = "DELETE FROM viaje WHERE idviaje = " . $this->getidviaje();
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
     * Recopila datos de todos los pasajeros vinculados al viaje, retorna
     * información de los mismos
     * @return string
     */
    public function mostrarPasajeros()
    {
        $datosPasajeros = "";
        $listado = new Pasajero;

        $condicion = "idviaje =" . $this->getidviaje();
        $colPasajeros = $listado->listar($condicion);
        if (!empty($colPasajeros)) {
            $nroPasajero = 1;
            foreach ($colPasajeros as $pasajero) {
                $datosPasajeros .= "\nPasajero n°" . ($nroPasajero++) . ": " . $pasajero;
            }
        } else {
            $datosPasajeros = "0";
        }
        return $datosPasajeros;
    }

    /**
     * Elimina pasajeros relacionados al viaje, y luego se elimina
     * a si mismo
     */
    public function eliminarViaje()
    {
        $pasajeros = new Pasajero;
        $condicion = "idviaje =" . $this->getidviaje();
        $colPasajeros = $pasajeros->listar($condicion);

        foreach ($colPasajeros as $pasajeros) {
            $pasajeros->eliminar();
        }
        $this->eliminar();
    }

    public function __toString()
    {
        return "ID viaje: " . $this->getidviaje() . "\nDestino: " . $this->getvdestino() .
            "\nCantidad máxima de pasajeros: " . $this->getvcantmaxpasajeros() . "\nID Empresa: " .
            $this->getobjEmpresa() . "\nN° de responsable: " . $this->getobjEmpleado() .
            "\nImporte: " . $this->getvimporte() . "\nPasajeros vinculados al viaje:\n" . $this->mostrarPasajeros() . "\n";
    }
}
