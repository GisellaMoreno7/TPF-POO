<?php
include_once 'BaseDatos.php';

class Pasajero
{
    //Atributos
    private $pdocumento;
    private $pnombre;
    private $papellido;
    private $ptelefono;
    private $idviaje;
    private $mensajeoperacion;

    //Método constructor
    public function __construct()
    {
        $this->pdocumento = "";
        $this->pnombre = "";
        $this->papellido = "";
        $this->ptelefono = "";
        $this->idviaje = "";
    }

    //Método cargar
    public function cargar($docu, $nom, $ape, $tel, $idv)
    {
        $this->setpdocumento($docu);
        $this->setpnombre($nom);
        $this->setpapellido($ape);
        $this->setptelefono($tel);
        $this->setidviaje($idv);
    }

    //Getters
    public function getpdocumento()
    {
        return $this->pdocumento;
    }
    public function getpnombre()
    {
        return $this->pnombre;
    }
    public function getpapellido()
    {
        return $this->papellido;
    }
    public function getptelefono()
    {
        return $this->ptelefono;
    }
    public function getidviaje()
    {
        return $this->idviaje;
    }
    public function getmensajeoperacion()
    {
        return $this->mensajeoperacion;
    }

    //Setters
    public function setpdocumento($dato)
    {
        $this->pdocumento = $dato;
    }
    public function setpnombre($dato)
    {
        $this->pnombre = $dato;
    }
    public function setpapellido($dato)
    {
        $this->papellido = $dato;
    }
    public function setptelefono($dato)
    {
        $this->ptelefono = $dato;
    }
    public function setidviaje($dato)
    {
        $this->idviaje = $dato;
    }
    public function setmensajeoperacion($mensajeoperacion)
    {
        $this->mensajeoperacion = $mensajeoperacion;
    }

    //Métodos de la clase

    /**
     * Recupera datos de un pasajero por dni, retorna true si
     * lo encuentra, false caso contrario.
     * @param int $dni
     * @return boolean
     */
    public function buscar($dni)
    {
        $base = new BaseDatos();
        $resp = false;
        $consulta = "SELECT * FROM pasajero WHERE pdocumento =" . $dni;

        if ($base->Iniciar()) {
            if ($base->Ejecutar($consulta)) {
                if ($fila = $base->Registro()) {
                    // $objViaje = new Viaje;
                    // $objViaje->buscar($fila["idviaje"]);
                    $this->cargar($dni, $fila["pnombre"], $fila["papellido"], $fila["ptelefono"], $fila["idviaje"]);
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
        $colPasajeros = [];
        $consulta = "SELECT * FROM pasajero ";

        if ($condicion != "") {
            $consulta .= "WHERE " . $condicion;
        }
        $consulta .= " ORDER BY papellido";

        if ($base->Iniciar()) {
            if ($base->Ejecutar($consulta)) {
                $colPasajeros = [];
                while ($fila = $base->Registro()) {
                    // $objViaje = new Viaje;
                    // $objViaje->buscar($fila["idviaje"]);
                    $pasajero = new Pasajero;
                    $pasajero->cargar($fila["pdocumento"], $fila["pnombre"], $fila["papellido"], $fila["ptelefono"], $fila["idviaje"]);
                    array_push($colPasajeros, $pasajero);
                }
            } else {
                $this->setmensajeoperacion($base->getError());
            }
        } else {
            $this->setmensajeoperacion($base->getError());
        }
        return $colPasajeros;
    }

    /**
     * Inserta datos de un pasajero a la base de datos, retorna
     * true si la inserción fue exitosa, false en caso contrario
     * @return boolean
     */
    public function insertar()
    {
        $base = new BaseDatos();
        $resp = false;
        $consulta = "INSERT INTO pasajero (pdocumento, pnombre, papellido, ptelefono, idviaje)
        VALUES (" . $this->getpdocumento() . ",'" . $this->getpnombre() . "','" . $this->getpapellido() . "'," . $this->getptelefono() . "," . $this->getidviaje() . ")";

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
     * Modifica datos de algún registro, retorna true en caso de que
     * la actualización haya sido exitosa, false en caso contrario
     * @return boolean
     */
    public function modificar()
    {
        $base = new BaseDatos();
        $resp = false;
        $consulta = "UPDATE pasajero SET papellido = '" . $this->getpapellido() . "', pnombre = '" . $this->getpnombre() . "', ptelefono =" . $this->getptelefono() .
            ", idviaje =" . $this->getidviaje() . " WHERE pdocumento =" . $this->getpdocumento();

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
        $consulta = "DELETE FROM pasajero WHERE pdocumento = " . $this->getpdocumento();
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
        return "\nNombre del pasajero: " . $this->getpnombre() . " " . $this->getpapellido() .
            "\nNúmero de documento: " . $this->getpdocumento() . "\nTeléfono: " . $this->getptelefono() .
            "\nID viaje: " . $this->getidviaje() . "\n";
    }
}
