<?php
require_once('../../helpers/database.php');
require_once('../../helpers/validator.php');
require_once('../../models/orden.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $plan = new Orden;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'message' => null, 'exception' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idadministrador'])) {
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            case 'readAll':
                if ($result['dataset'] = $plan->readAllOrden()) {
                    $result['status'] = 1;
                } else {
                    $result['exception'] = 'No hay órdenes registradas';
                }
                break;
            case 'readOne':
                if ( $plan->setIdCompra( $_POST[ 'idcompra' ] ) ) {
                    if ( $result[ 'dataset' ] = $plan->readOneOrden() ) {
                        $result['status'] = 1;
                    } else {
                        $result['exception'] = 'Orden no existente';
                    }
                } else {
                    $result['exception'] = 'Orden no válido';
                }
                break;
            case 'status':
                if ( $plan->setEstado( $_POST[ 'estado' ] ) && $plan->setIdCompra( $_POST[ 'id' ] ) ) {
                    if ( $result[ 'dataset' ] = $plan->disableOrden() ) {
                        $result['status'] = 1;
                        $result['message'] = 'Estado actualizado correctamente';
                    } else {
                        $result['exception'] = 'No se pudo actualizar el estado';
                    }
                } else {
                    $result['exception'] = 'Estado no válido';
                }
                break;
            
        }
        // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
        header('content-type: application/json; charset=utf-8');
        // Se imprime el resultado en formato JSON y se retorna al controlador.
        print(json_encode($result));
    } else {
        exit('Acceso no disponible');
    }
} else {
    exit('Recurso denegado');
}
?>