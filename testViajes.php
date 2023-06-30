<?php
include_once 'BaseDatos.php';
include_once 'Pasajero.php';
include_once 'Responsable.php';
include_once 'Viaje.php';
include_once 'Empresa.php';

/**
 * Presenta menú de opciones, retorna opción elegida
 * @return int
 */
function menuOpciones()
{
    echo "\n╔════════════════════════════════╗";
    echo "\n║ >> MENÚ DE OPCIONES            ║\n";
    echo "║ [1] Administrar empresas       ║\n";
    echo "║ [2] Administrar viajes         ║\n";
    echo "║ [3] Administrar responsables   ║\n";
    echo "║ [4] Administrar pasajeros      ║\n";
    echo "║ [5] Salir                      ║\n";
    echo "║                                ║";
    echo "\n╚════════════════════════════════╝\n";
    echo "Seleccione opción: ";
    $rta = trim(fgets(STDIN));
    return $rta;
}

/**
 * Presenta submenú de opciones de empresas, retorna
 * opción elegida
 * @return int
 */
function menuEmpresas()
{
    echo "\n╭───────────────────────────────╮";
    echo "\n│  OPCIONES DE EMPRESAS         │\n";
    echo "│ [1] Ingresar una empresa      │\n";
    echo "│ [2] Modificar una empresa     │\n";
    echo "│ [3] Mostrar empresas cargadas │\n";
    echo "│ [4] Eliminar una empresa      │\n";
    echo "│ [5] Atrás                     │";
    echo "\n╰───────────────────────────────╯\n";
    echo "Seleccione opción: ";
    $rta = trim(fgets(STDIN));
    return $rta;
}

/**
 * Imprime listado de elementos, retorna cadena string,
 * puede tener o no condición de ordenamiento
 * @return string
 */
function mostrarElementos($obj, $condicion)
{
    $coleccion = [];
    if ($condicion != "") {
        $coleccion = $obj->listar($condicion);
        foreach ($coleccion as $elemento) {
            echo "\n▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔";
            echo "\n" . $elemento;
            echo "\n▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔";
        }
    } else {
        $coleccion = $obj->listar();
        foreach ($coleccion as $elemento) {
            echo "\n▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔";
            echo "\n" . $elemento;
            echo "\n▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔";
        }
    }
}

/**
 * Inserta una empresa a la BD
 */
function insertarEmpresa($objEmpresa)
{
    echo "Nombre de la empresa: ";
    $nombre = trim(fgets(STDIN));
    echo "Dirección de la empresa: ";
    $direccion = trim(fgets(STDIN));
    $objEmpresa->cargar("", $nombre, $direccion);
    $objEmpresa->insertar();
    echo "Empresa insertada correctamente. ✔️";
}

/**
 * Modifica una empresa de la BD
 */
function modificarEmpresa($objEmpresa)
{
    $colEmpresas = $objEmpresa->listar();
    if (!empty($colEmpresas)) {
        echo "Empresas cargadas en la BD: ";
        mostrarElementos($objEmpresa, "");

        echo "\nID de la empresa a modificar: ";
        $id = trim(fgets(STDIN));
        if ($objEmpresa->buscar($id)) {
            echo "Nuevo nombre de la empresa: ";
            $nombre = trim(fgets(STDIN));
            echo "Nueva dirección de la empresa: ";
            $direccion = trim(fgets(STDIN));
            $objEmpresa->cargar($id, $nombre, $direccion);
            $objEmpresa->modificar();
            echo "Empresa modificada correctamente. ✔️";
        } else {
            echo "ID de empresa inválido/innexistente. 🛑";
        }
    } else {
        echo "Sin empresas cargadas en la BD. 🛑\n";
    }
}

/**
 * Elimina cualquier registro de una empresa
 */
function eliminarEmpresa($objEmpresa, $objViaje)
{
    $colEmpresas = $objEmpresa->listar();
    if (!empty($colEmpresas)) {
        //Mostrar empresas cargadas en la BD
        echo "Empresas cargadas en la BD: ";
        mostrarElementos($objEmpresa, "");

        echo "\nID de la empresa a eliminar: ";
        $id = trim(fgets(STDIN));

        if ($objEmpresa->buscar($id)) {
            echo "🛑 ¡Alerta! :: Se borrarán todos los viajes vinculados a esta empresa\n🛑 ¿Continuar? (s/n): ";
            $rta = trim(fgets(STDIN));
            if ($rta == "s") {
                $objEmpresa->eliminarEmpresa();
                $objViaje->eliminarViaje();
                echo "Empresa eliminada correctamente. ✔️";
            } else {
                echo "Operación cancelada. 🛑";
            }
        } else {
            echo "ID de empresa inválido/innexistente. 🛑";
        }
    } else {
        echo "Sin empresas cargadas en la BD. 🛑\n";
    }
}

/**
 * Presenta submenú de opciones de viajes, retorna
 * opción elegida
 * @return int
 */
function menuViajes()
{
    echo "\n╭─────────────────────────────╮";
    echo "\n│  OPCIONES DE VIAJES         │\n";
    echo "│ [1] Ingresar un viaje       │\n";
    echo "│ [2] Modificar un viaje      │\n";
    echo "│ [3] Mostrar viajes cargados │\n";
    echo "│ [4] Eliminar un viaje       │\n";
    echo "│ [5] Atrás                   │";
    echo "\n╰─────────────────────────────╯\n";
    echo "Seleccione opción: ";
    $rta = trim(fgets(STDIN));
    return $rta;
}

/**
 * Inserta una viaje a la BD
 */
function insertarViaje($objEmpresa, $objViaje, $objResponsable)
{
    $colEmpresas = $objEmpresa->listar();
    if (!empty($colEmpresas)) {
        echo "\nEmpresas disponibles:";
        mostrarElementos($objEmpresa, "");
        echo "\nID de la empresa que quiere administrar los viajes: ";
        $idEmpresa = trim(fgets(STDIN));

        //Verifico si existe la empresa elegida
        if ($objEmpresa->buscar($idEmpresa)) {

            //Verifico que hayan responsables registrados en la BD
            $colResponsable = $objResponsable->listar();
            if (!empty($colResponsable)) {
                echo "Nombre del destino: ";
                $destino = trim(fgets(STDIN));
                echo "Cantidad máxima de pasajeros: ";
                $cantMax = trim(fgets(STDIN));
                echo "Importe del viaje: $";
                $importe = trim(fgets(STDIN));
                echo "\nResponsables disponibles: ";
                mostrarElementos($objResponsable, "");
                echo "\nSeleccione número de empleado: ";
                $id = trim(fgets(STDIN));

                //Verifico si existe el responsable elegido
                if ($objResponsable->buscar($id)) {
                    //Si todos los datos están bien, cargo e inserto el viaje a la BD
                    $objViaje->cargar("", $destino, $cantMax, $objEmpresa, $objResponsable, $importe);
                    $objViaje->insertar();
                    echo "Viaje insertado correctamente. ✔️\n";
                } else {
                    echo "ID de responsable inválido/innexistente. 🛑\n";
                }
            } else {
                echo "Sin responsables cargados en la BD. 🛑";
            }
        } else {
            echo "ID de empresa inválido/innexistente. 🛑";
        }
    } else {
        echo "Sin empresas cargadas en la BD. 🛑\n";
    }
}

/**
 * Modifica un viaje de la BD
 */
function modificarViaje($objViaje, $objEmpresa, $objResponsable, $objPasajero)
{
    $colViajes = $objViaje->listar();
    if (!empty($colViajes)) {
        //Verifico que haya viajes en la BD
        echo "Viajes cargados a la BD: ";
        mostrarElementos($objViaje, "");

        echo "ID del viaje a modificar: ";
        $idViaje = trim(fgets(STDIN));

        if ($objViaje->buscar($idViaje)) {
            echo "Nuevo lugar de destino: ";
            $destino = trim(fgets(STDIN));
            echo "Cantidad máxima de pasajeros: ";
            $cantMax = trim(fgets(STDIN));

            //Evalúo que la cantidad máxima de pasajeros no sea
            //menor que la cantidad de pasajeros actuales
            $condicion = "idviaje =" . $idViaje;
            $colPasajeros = $objPasajero->listar($condicion);
            $n = count($colPasajeros);

            if ($cantMax < $n) {
                echo "La cantidad máxima de pasajeros no puede ser menor a la cantidad actual. 🛑";
            } else {
                //Pido datos del responsable y verifico si existe
                echo "Responsables cargados a la BD: ";
                mostrarElementos($objResponsable, "");
                echo "Número de empleado: ";
                $idEmpleado = trim(fgets(STDIN));

                //Si existe responsable, verifico que exista la empresa
                if ($objResponsable->buscar($idEmpleado)) {
                    echo "Empresas cargadas a la BD: ";
                    mostrarElementos($objEmpresa, "");

                    echo "Seleccione ID empresa para mudar el viaje: ";
                    $idEmpresa = trim(fgets(STDIN));

                    //Si la empresa existe, pido importe nuevo y modifico el viaje
                    if ($objEmpresa->buscar($idEmpresa)) {
                        echo "Importe: ";
                        $importe = trim(fgets(STDIN));
                        $objViaje->cargar($idViaje, $destino, $cantMax, $objEmpresa, $objResponsable, $importe);
                        $objViaje->modificar();
                        echo "Viaje modificado correctamente. ✔️";
                    } else {
                        "ID de empresa inválido/innexistente. 🛑";
                    }
                } else {
                    echo "ID responsable inválido/innexistente. 🛑";
                }
            }
        }
    } else {
        echo "Sin viajes cargados en la BD. 🛑";
    }
}

/**
 * Elimina un viaje de la BD
 */
function eliminarViaje($objViaje)
{
    $colViajes = $objViaje->listar();
    if (!empty($colViajes)) {
        echo "\nViajes cargados a la BD: ";
        mostrarElementos($objViaje, "");

        echo "\nID del viaje a eliminar: ";
        $idViaje = trim(fgets(STDIN));

        if ($objViaje->buscar($idViaje)) {
            $objViaje->eliminarViaje();
            echo "Viaje eliminado correctamente. ✔️";
        } else {
            echo "ID de viaje inválido/innexistente. 🛑";
        }
    } else {
        echo "Sin viajes cargados en la BD. 🛑\n";
    }
}

/**
 * Presenta submenú de opciones de responsables, retorna
 * opción elegida
 * @return int
 */
function menuResponsable()
{
    echo "\n╭───────────────────────────────────╮";
    echo "\n│  OPCIONES DE RESPONSABLES         │\n";
    echo "│ [1] Ingresar un responsable       │\n";
    echo "│ [2] Modificar un responsable      │\n";
    echo "│ [3] Mostrar responsables cargados │\n";
    echo "│ [4] Eliminar un responsable       │\n";
    echo "│ [5] Atrás                         │";
    echo "\n╰───────────────────────────────────╯\n";
    echo "Seleccione opción: ";
    $rta = trim(fgets(STDIN));
    return $rta;
}

/**
 * Inserta un responsable a la BD
 */
function insertarResponsable($objResponsable)
{
    echo "Nombre del responsable: ";
    $nombre = trim(fgets(STDIN));
    echo "Apellido del responsable: ";
    $apellido = trim(fgets(STDIN));
    echo "Número de licencia: ";
    $nroLicencia = trim(fgets(STDIN));

    $objResponsable->cargar("", $nroLicencia, $nombre, $apellido);
    if ($objResponsable->insertar()) {
        echo "Responsable insertadado correctamente. ✔️";
    } else {
        echo "No se pudo insertar al responsable correctamente. 🛑";
    }
}

/**
 * Modifica un responsable de la BD
 */
function modificarResponsable($objResponsable)
{
    $colResponsables = $objResponsable->listar();

    //Verifico si hay responsables cargados
    if (empty($colResponsables)) {
        echo "Sin responsables cargados en la BD. 🛑\n";
    } else {
        //Muestro listado de responsables disponibles para modificar
        echo "Responsables cargados a la BD: ";
        mostrarElementos($objResponsable, "");

        echo "\nN° de empleado del responsable a modificar: ";
        $idEmpleado = trim(fgets(STDIN));

        //Verifico que exista algún empleado con ese ID
        if ($objResponsable->buscar($idEmpleado)) {
            echo "Nuevo nombre del responsable: ";
            $nombre = trim(fgets(STDIN));
            echo "Nuevo apellido del responsable: ";
            $apellido = trim(fgets(STDIN));
            echo "Nuevo n° del responsable: ";
            $nroLicencia = trim(fgets(STDIN));

            $objResponsable->cargar($idEmpleado, $nroLicencia, $nombre, $apellido);
            $objResponsable->modificar();
            echo "Responsable modificado correctamente. ✔️";
        } else {
            echo "ID responsable inválido/inválido. 🛑";
        }
    }
}

/**
 * Elimina un responsable de la BD y todos los viajes vinculados
 * a él
 */
function eliminarResponsable($objResponsable)
{
    $colResponsable = $objResponsable->listar();
    if (!empty($colResponsable)) {
        echo "Responsables disponibles: ";
        mostrarElementos($objResponsable, "");

        echo "\nID del responsable a eliminar: ";
        $id = trim(fgets(STDIN));

        if ($objResponsable->buscar($id)) {
            echo "🛑 ¡Alerta! :: Se borrarán los viajes vinculados a este responsable\n🛑 ¿Continuar? (s/n): ";
            $rta = trim(fgets(STDIN));
            if ($rta == "s") {
                $objResponsable->eliminar();
                echo "Responsable eliminado correctamente. ✔️";
            }
        } else {
            echo "ID de responsable inválido/innexistente. 🛑";
        }
    } else {
        echo "Sin responsables cargados en la BD. 🛑\n";
    }
}

/**
 * Presenta submenú de opciones de pasajeros, retorna
 * opción elegida
 * @return int
 */
function menuPasajero()
{
    echo "\n╭────────────────────────────────╮";
    echo "\n│  OPCIONES DE PASAJEROS         │\n";
    echo "│ [1] Ingresar un pasajero       │\n";
    echo "│ [2] Modificar un pasajero      │\n";
    echo "│ [3] Mostrar pasajeros cargados │\n";
    echo "│ [4] Eliminar un pasajero       │\n";
    echo "│ [5] Atrás                      │";
    echo "\n╰────────────────────────────────╯\n";
    echo "Seleccione opción: ";
    $rta = trim(fgets(STDIN));
    return $rta;
}

/**
 * Inserta un pasajero a la BD
 */
function insertarPasajero($objViaje, $objPasajero)
{
    $colViajes = $objViaje->listar();
    if (!empty($colViajes)) {
        echo "Viajes disponibles: ";
        mostrarElementos($objViaje, "");

        echo "\nID del viaje a incluirse: ";
        $idViaje = trim(fgets(STDIN));

        //Compruebo si este viaje existe
        if ($objViaje->buscar($idViaje)) {
            $condicion = "idviaje= " . $idViaje;
            $colPasajeros = $objPasajero->listar($condicion);
            $cantActual = count($colPasajeros);
            $max = $objViaje->getvcantmaxpasajeros();
            $asientosDisponibles = $max - $cantActual;

            //Evalúo que no se exceda el límite de pasajeros máximos
            if ($max == $cantActual) {
                echo "Límite máximo alcanzado. \n";
            } elseif ($asientosDisponibles > 0) {
                echo "¡Asiento disponible!\nNúmero de documento: ";
                $dni = trim(fgets(STDIN));
                if ($objPasajero->buscar($dni)) {
                    echo "Error, el DNI ya existe. 🛑";
                } else {
                    echo "Ingrese el nombre del pasajero: ";
                    $nombre = trim(fgets(STDIN));
                    echo "Ingrese el apellido del pasajero: ";
                    $apellido = trim(fgets(STDIN));
                    echo "Ingrese el telefono del pasajero: ";
                    $telefono = trim(fgets(STDIN));
                    $pasajero = new Pasajero;
                    $pasajero->cargar($dni, $nombre, $apellido, $telefono, $idViaje);
                    $pasajero->insertar();
                    echo "Pasajero insertado correctamente. ✔️\n";
                }
            }
        } else {
            echo "ID de viaje inválido/innexistente. 🛑\n";
        }
    } else {
        echo "Sin viajes cargados en la BD. 🛑";
    }
}

/**
 * Modifica un pasajero de la BD
 */
function modificarPasajero($objViaje, $objPasajero)
{
    $colPasajeros = $objPasajero->listar();

    //Verifico si hay pasajeros cargados
    if (empty($colPasajeros)) {
        echo "Sin pasajeros cargados en la BD. 🛑\n";
    } else {
        //Muestro listado de pasajeros disponibles para modificar
        echo "Pasajeros cargados a la BD: ";
        mostrarElementos($objPasajero, "");

        echo "\nDNI del pasajero a modificar: ";
        $dni = trim(fgets(STDIN));

        //Verifico que exista algún pasajero con ese dni
        if ($objPasajero->buscar($dni)) {
            echo "Nuevo nombre del pasajero: ";
            $nombre = trim(fgets(STDIN));
            echo "Nuevo apellido del pasajero: ";
            $apellido = trim(fgets(STDIN));
            echo "Nuevo n° de teléfono: ";
            $telefono = trim(fgets(STDIN));

            //Muestro colección de viajes
            echo "Viajes cargados a la BD: ";
            mostrarElementos($objViaje, "");
            echo "\nID del viaje a incorporarse: ";
            $idViaje = trim(fgets(STDIN));

            //Compruebo si el viaje existe
            if ($objViaje->buscar($idViaje)) {
                $condicion = "idviaje= " . $idViaje;
                $colPasajeros = $objPasajero->listar($condicion);
                $cantActual = count($colPasajeros);
                $max = $objViaje->getvcantmaxpasajeros();
                $asientosDisponibles = $max - $cantActual;

                //Evalúo que no se exceda el límite de pasajeros máximos
                if ($max == $cantActual) {
                    echo "Límite máximo alcanzado. 🛑";
                } elseif ($asientosDisponibles > 0) {

                    $objPasajero->cargar($dni, $nombre, $apellido, $telefono, $idViaje);
                    $objPasajero->modificar();
                    echo "Pasajero modificado correctamente. ✔️";
                }
            } else {
                echo "ID de viaje inválido/innexistente. 🛑";
            }
        } else {
            echo "DNI de pasajero inválido/innexistente. 🛑";
        }
    }
}

/**
 * Elimina un pasajero de la BD
 */
function eliminarPasajero($objPasajero)
{
    //Verifico si hay pasajeros cargados en la BD
    $colPasajeros = $objPasajero->listar();
    if (!empty($colPasajeros)) {
        echo "Pasajeros disponibles: ";
        mostrarElementos($objPasajero, "");

        echo "\nDNI del pasajero a eliminar: ";
        $id = trim(fgets(STDIN));

        if ($objPasajero->buscar($id)) {
            $objPasajero->eliminar();
            echo "Pasajero eliminado correctamente. ✔️";
        } else {
            echo "DNI de pasajero inválido/innexistente. 🛑";
        }
    } else {
        echo "Sin pasajeros cargados en la BD. 🛑\n";
    }
}

//PROGRAMA PRINCIPAL

$objEmpresa = new Empresa;
$objViaje = new Viaje;
$objResponsable = new Responsable;
$objPasajero = new Pasajero;

echo "\n   PROGRAMA INICIADO\n🗺️  ... Viaje Feliz ... 🌎\n";
$opcionMenu = menuOpciones();

while ($opcionMenu != 5) {
    switch ($opcionMenu) {
        case 1:
            //Opciones de empresa
            $opcionEmpresa = menuEmpresas();
            while ($opcionEmpresa != 5) {
                switch ($opcionEmpresa) {
                    case 1:
                        //Insertar una empresa
                        insertarEmpresa($objEmpresa);
                        break;
                    case 2:
                        //Modificar una empresa
                        modificarEmpresa($objEmpresa);
                        break;
                    case 3:
                        //Mostrar empresas
                        if (empty($objEmpresa->listar())) {
                            echo "Actualmente no hay empresas cargadas en la BD. 🛑\n";
                        } else {
                            mostrarElementos($objEmpresa, "");
                        }
                        break;
                    case 4:
                        //Eliminar una empresa
                        eliminarEmpresa($objEmpresa, $objViaje);
                        break;
                }
                $opcionEmpresa = menuEmpresas();
            }
            break;
        case 2:
            //Opciones de viajes
            $opcionViajes = menuViajes();
            while ($opcionViajes != 5) {
                switch ($opcionViajes) {
                    case 1:
                        //Insertar un viaje
                        insertarViaje($objEmpresa, $objViaje, $objResponsable);
                        break;
                    case 2:
                        //Modificar un viaje
                        modificarViaje($objViaje, $objEmpresa, $objResponsable, $objPasajero);
                        break;
                    case 3:
                        //Mostrar viajes
                        if (empty($objViaje->listar())) {
                            echo "Actualmente no hay viajes cargados en la BD. 🛑\n";
                        } else {
                            mostrarElementos($objViaje, "");
                        }
                        break;
                    case 4:
                        //Eliminar un viaje
                        eliminarViaje($objViaje);
                        break;
                }
                $opcionViajes = menuViajes();
            }
            break;
        case 3:
            //Opciones de responsable
            $opcionResponsable = menuResponsable();
            while ($opcionResponsable != 5) {
                switch ($opcionResponsable) {
                    case 1:
                        //Insertar un responsable;
                        insertarResponsable($objResponsable);
                        break;
                    case 2:
                        //Modificar un responsable
                        modificarResponsable($objResponsable);
                        break;
                    case 3:
                        //Mostrar responsables
                        if (empty($objResponsable->listar())) {
                            echo "Actualmente no hay responsables cargados en la BD. 🛑\n";
                        } else {
                            mostrarElementos($objResponsable, "");
                        }
                        break;
                    case 4:
                        //Eliminar un responsable
                        eliminarResponsable($objResponsable);
                        break;
                }
                $opcionResponsable = menuResponsable();
            }
            break;
        case 4:
            //Opciones de pasajero
            $opcionPasajero = menuPasajero();
            while ($opcionPasajero != 5) {
                switch ($opcionPasajero) {
                    case 1:
                        //Insertar un pasajero
                        insertarPasajero($objViaje, $objPasajero);
                        break;
                    case 2:
                        //Modificar un pasajero
                        modificarPasajero($objViaje, $objPasajero);
                        break;
                    case 3:
                        //Mostrar pasajeros
                        if (empty($objPasajero->listar())) {
                            echo "Actualmente no hay pasajeros cargados en la BD. 🛑\n";
                        } else {
                            mostrarElementos($objPasajero, "");
                        }
                        break;
                    case 4:
                        //Eliminar un pasajero
                        eliminarPasajero($objPasajero);
                        break;
                }
                $opcionPasajero = menuPasajero();
            }
    }
    $opcionMenu = menuOpciones();
}

echo "PROGRAMA FINALIZADO. 🔴";
