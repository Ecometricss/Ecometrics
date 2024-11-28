<?php
session_start(); 
if (!isset($_SESSION['email'])) {
    header("Location: ../login.html"); 
    exit();
}


include 'db.php';


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'monitoreo') {
    $temperatura = isset($_POST['temperatura']) ? $_POST['temperatura'] : NULL;


    $id_invernadero = $_POST['id_invernadero'];
    $temperatura = $_POST['temperatura'];
    $humedad = $_POST['humedad'];
    $temperatura_amb = $_POST['temperatura_amb'];
    $humedad_amb = $_POST['humedad_amb'];
    

    $sql = "INSERT INTO datos_monitoreo (ID_Invernadero, Temperatura, Humedad, Temperatura_amb, Humedad_amb) VALUES (:id_invernadero, :temperatura, :humedad, :temperatura_amb, :humedad_amb)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'id_invernadero' => $id_invernadero,
        'temperatura' => $temperatura,
        'humedad' => $humedad,
        'temperatura_amb' => $temperatura_amb,
        'humedad_amb' => $humedad_amb
    ]);


    header("Location: index.php");
    exit();
}


$sql = "SELECT * FROM invernaderos WHERE ID_Usuario = :id_usuario";
$stmt = $conn->prepare($sql);
$stmt->execute(['id_usuario' => $_SESSION['ID_Usuario']]);
$invernaderos = $stmt->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'crear_invernadero') {
    $nombre_invernadero = $_POST['nombre_invernadero'];
    $ubicacion = $_POST['ubicacion'];


    $sql = "INSERT INTO invernaderos (ID_Usuario, Nombre, Ubicacion) VALUES (:id_usuario, :nombre, :ubicacion)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'id_usuario' => $_SESSION['ID_Usuario'],
        'nombre' => $nombre_invernadero,
        'ubicacion' => $ubicacion
    ]);


    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'eliminar_invernadero') {
    $id_invernadero = $_POST['id_invernadero'];


    $sql = "DELETE FROM datos_monitoreo WHERE ID_Invernadero = :id_invernadero";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['id_invernadero' => $id_invernadero]);


    $sql = "DELETE FROM invernaderos WHERE ID_Invernadero = :id_invernadero";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['id_invernadero' => $id_invernadero]);


    header("Location: index.php");
    exit();
}
$sql = "SELECT * FROM datos_monitoreo";
$stmt = $conn->query($sql);
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'comparar_cultivo') {
    $id_invernadero = $_POST['id_invernadero'];
    $id_cultivo = $_POST['id_cultivo'];

    // Obtener datos de monitoreo más recientes del invernadero
    $sql = "SELECT Temperatura, Humedad, Temperatura_amb, Humedad_amb FROM datos_monitoreo 
            WHERE ID_Invernadero = :id_invernadero 
            ORDER BY Fecha_hora DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['id_invernadero' => $id_invernadero]);
    $datos_monitoreo = $stmt->fetch(PDO::FETCH_ASSOC);

    // Obtener datos del cultivo
    $sql = "SELECT * FROM cultivos 
            WHERE ID_Cultivo = :id_cultivo AND ID_Usuario = :id_usuario";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'id_cultivo' => $id_cultivo,
        'id_usuario' => $_SESSION['ID_Usuario']
    ]);
    $cultivo = $stmt->fetch(PDO::FETCH_ASSOC);

    // Preparar mensaje de comparación
    $mensaje_comparacion = "";
    $es_apto = true;

    /* if ($datos_monitoreo['Temperatura'] < $cultivo['Temperatura_minima']) {
        $mensaje_comparacion .= "La temperatura es demasiado baja. ";
        $es_apto = false;
    } elseif ($datos_monitoreo['Temperatura'] > $cultivo['Temperatura_maxima']) {
        $mensaje_comparacion .= "La temperatura es demasiado alta. ";
        $es_apto = false;
    } */

    if ($datos_monitoreo['Humedad'] < $cultivo['Humedad_minima']) {
        $mensaje_comparacion .= "La humedad es demasiado baja. ";
        $es_apto = false;
    } elseif ($datos_monitoreo['Humedad'] > $cultivo['Humedad_maxima']) {
        $mensaje_comparacion .= "La humedad es demasiado alta. ";
        $es_apto = false;
    }

    //AMBIENTE
    if ($datos_monitoreo['Temperatura_amb'] < $cultivo['Temperatura_ambiente']) {
        $mensaje_comparacion .= "La temperatura del ambiente es demasiado baja. ";
        $es_apto = false;
    } elseif ($datos_monitoreo['Temperatura_amb'] > $cultivo['Max_temp_amb']) {
        $mensaje_comparacion .= "La temperatura del ambiente es demasiado alta. ";
        $es_apto = false;
    }

    if ($datos_monitoreo['Humedad_amb'] < $cultivo['Humedad_ambiente']) {
        $mensaje_comparacion .= "La humedad del ambiente es demasiado baja. ";
        $es_apto = false;
    } elseif ($datos_monitoreo['Humedad_amb'] > $cultivo['Max_hum_amb']) {
        $mensaje_comparacion .= "La humedad del ambiente es demasiado alta. ";
        $es_apto = false;
    }

    if ($es_apto) {
        $mensaje_comparacion = "¡Las condiciones son aptas para el cultivo de " . $cultivo['Nombre'] . "!";
    } else {
        $mensaje_comparacion = "Condiciones no aptas para el cultivo de " . $cultivo['Nombre'] . ". " . $mensaje_comparacion;
    }

    // Almacenar mensaje en sesión para mostrarlo
    $_SESSION['mensaje_comparacion'] = $mensaje_comparacion;
    header("Location: index.php");
    exit();
}

//Eliminar Monitoreos
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'eliminar_monitoreo') {
    $id_monitoreo = $_POST['id_monitoreo'];

    $sql = "DELETE FROM datos_monitoreo WHERE ID_Monitoreo = :id_monitoreo";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['id_monitoreo' => $id_monitoreo]);

    header("Location: index.php");
    exit();
}


?>

<!--ACA COMIENZA HTML-->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoreo de Invernaderos</title>
    <link rel="stylesheet" href="../CSS/invernadero.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <nav class="top-nav">
        <div class="nav-brand">
            <i class="fas fa-leaf"></i>
            <span>GreenHouse Monitor</span>
        </div>
        <div class="nav-links">
            <a href="cultivos.php" class="nav-link">
                <i class="fas fa-seedling"></i>
                Gestionar Cultivos
            </a>
            <a href="../login.html" class="nav-link">
                <i class="fas fa-sign-out-alt"></i>
                Salir
            </a>
        </div>
    </nav>
    <main class="main-content">
    <div class="dashboard">
        <div class="welcome-section">
            <h1><i class="fas fa-home"></i> Panel de Control</h1>
            <p class="welcome-message">Bienvenido, <?php echo $_SESSION['nombre']; ?>!</p>
        </div>

        <div class="main-grid">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-plus-circle"></i>
                    <h2>Crear Nuevo Invernadero</h2>
                </div>
                <form method="POST" class="styled-form">
                    <input type="hidden" name="action" value="crear_invernadero">
                    
                    <div class="form-group">
                        <label for="nombre_invernadero">
                            <i class="fas fa-tag"></i> Nombre:
                        </label>
                        <input type="text" name="nombre_invernadero" required>
                    </div>

                    <div class="form-group">
                        <label for="ubicacion">
                            <i class="fas fa-map-marker-alt"></i> Ubicación:
                        </label>
                        <input type="text" name="ubicacion" required>
                    </div>

                    <button type="submit" class="btn-primary">
                        <i class="fas fa-plus"></i> Crear Invernadero
                    </button>
                </form>
                <div class="icon-container">
                    <i class="fas fa-warehouse animated-icon greenhouse-icon pulse-animation"></i>
                    <i class="fas fa-fan animated-icon greenhouse-icon spin-animation"></i>
                </div>  
            </div>

            <?php if (count($invernaderos) > 0): ?>
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-thermometer-half"></i>
                    <h2>Registro de Datos</h2>
                </div>
                <form method="POST" class="styled-form">
                    <input type="hidden" name="action" value="monitoreo">
                    
                    <div class="form-group">
                        <label for="invernadero">
                            <i class="fas fa-warehouse"></i> Selecciona un Invernadero:
                        </label>
                        <select name="id_invernadero" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($invernaderos as $invernadero): ?>
                                <option value="<?php echo $invernadero['ID_Invernadero']; ?>">
                                    <?php echo $invernadero['Nombre']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group-inline">
                        <div class="form-group">
                            <label for="temperatura">
                                <i class="fas fa-temperature-high"></i> Temperatura:
                            </label>
                            <input type="number" step="0.1" name="temperatura" disabled  placeholder="Esta opción estará disponible pronto" >
                        </div>

                        <div class="form-group">
                            <label for="humedad">
                                <i class="fas fa-tint"></i> Humedad:
                            </label>
                            <input type="number" step="0.1" name="humedad" required required placeholder="Ej: 60.0">
                        </div>

                        <div class="form-group">
                            <label for="temperatura_amb">
                                <i class="fas fa-temperature-low"></i> Temperatura_ambiente:
                            </label>
                            <input type="number" step="0.1" name="temperatura_amb" required required placeholder="Ej: 18.0">
                        </div>

                        <div class="form-group">
                            <label for="humedad_amb">
                                <i class="fas fa-tint"></i> Humedad_ambiente:
                            </label>
                            <input type="number" step="0.1" name="humedad_amb" required required placeholder="Ej: 60.0">
                        </div>
                    </div>

                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Guardar Datos
                    </button>
                </form>
            </div>

            <div class="card">
            <div class="card-header">
                <i class="fas fa-balance-scale"></i>
                <h2>Comparar Condiciones del Cultivo</h2>
            </div>
            <form method="POST" class="styled-form">
                <input type="hidden" name="action" value="comparar_cultivo">
                
                <div class="form-group">
                    <label for="id_invernadero">
                        <i class="fas fa-warehouse"></i> Selecciona un Invernadero:
                    </label>
                    <select name="id_invernadero" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($invernaderos as $invernadero): ?>
                            <option value="<?php echo $invernadero['ID_Invernadero']; ?>">
                                <?php echo $invernadero['Nombre']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_cultivo">
                        <i class="fas fa-seedling"></i> Selecciona un Cultivo:
                    </label>
                    <?php 
                    // Obtener lista de cultivos del usuario
                    $sql = "SELECT * FROM cultivos WHERE ID_Usuario = :id_usuario";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute(['id_usuario' => $_SESSION['ID_Usuario']]);
                    $cultivos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <select name="id_cultivo" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($cultivos as $cultivo): ?>
                            <option value="<?php echo $cultivo['ID_Cultivo']; ?>">
                                <?php echo $cultivo['Nombre']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn-primary">
                    <i class="fas fa-check"></i> Comparar Condiciones
                </button>
            </form>
            <div class="icon-container">
                <i class="fas fa-tractor animated-icon plant-icon tractor-animation"></i>
                <i class="fas fa-gauge-high animated-icon plant-icon pulse-animation"></i>
            </div>
        </div>
            <!-- Mensaje de comparación con estilo mejorado -->
            <?php if (isset($_SESSION['mensaje_comparacion'])): ?>
            <div class="comparison-alert <?php echo (strpos($_SESSION['mensaje_comparacion'], '¡') !== false) ? 'success' : 'warning'; ?>">
                <div class="alert-icon">
                    <i class="fas <?php echo (strpos($_SESSION['mensaje_comparacion'], '¡') !== false) ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?>"></i>
                </div>
                <div class="alert-content">
                    <?php 
                    echo $_SESSION['mensaje_comparacion']; 
                    unset($_SESSION['mensaje_comparacion']); 
                    ?>
                </div>
            </div>
            <?php endif; ?>                   
            
            <div class="card full-width">
                <div class="card-header">
                    <i class="fas fa-list"></i>
                    <h2>Tus Invernaderos</h2>
                </div>
                <div class="table-container">
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Ubicación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($invernaderos as $invernadero): ?>
                            <tr>
                                <td><?php echo $invernadero['ID_Invernadero']; ?></td>
                                <td>
                                    <i class="fas fa-warehouse"></i>
                                    <?php echo $invernadero['Nombre']; ?>
                                </td>
                                <td>
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo $invernadero['Ubicacion']; ?>
                                </td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="eliminar_invernadero">
                                        <input type="hidden" name="id_invernadero" value="<?php echo $invernadero['ID_Invernadero']; ?>">
                                        <button type="submit" class="btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este invernadero?');">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card full-width">
    <div class="card-header">
        <i class="fas fa-chart-line"></i>
        <h2>Historial de Monitoreo</h2>
    </div>
    <div class="table-container">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Invernadero</th>
                    <th>Humedad</th>
                    <th>Temperatura Ambiente</th>
                    <th>Humedad Ambiente</th>
                    <th>Fecha y Hora</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datos as $dato): ?>
                <tr>
                    <td><?php echo $dato['ID_Monitoreo']; ?></td>
                    <td>
                        <i class="fas fa-warehouse"></i>
                        <?php
                        $sql = "SELECT Nombre FROM invernaderos WHERE ID_Invernadero = :id";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute(['id' => $dato['ID_Invernadero']]);
                        $invernadero = $stmt->fetch(PDO::FETCH_ASSOC);
                        echo $invernadero['Nombre'];
                        ?>
                    </td>
                    <td>
                        <i class="fas fa-tint"></i>
                        <?php echo $dato['Humedad']; ?>%
                    </td>
                    <td>
                         <i class="fas fa-temperature-low"></i>
                        <?php echo $dato['Temperatura_amb']; ?>°C
                    </td>
                    <td>
                         <i class="fas fa-tint"></i>
                        <?php echo $dato['Humedad_amb']; ?>%
                    </td>
                    <td>
                        <i class="far fa-clock"></i>
                        <?php echo $dato['Fecha_hora']; ?>
                    </td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="eliminar_monitoreo">
                            <input type="hidden" name="id_monitoreo" value="<?php echo $dato['ID_Monitoreo']; ?>">
                            <button type="submit" class="btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este registro?');">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
            <?php else: ?>
            <div class="card">
                <div class="empty-state">
                    <i class="fas fa-seedling"></i>
                    <p>No tienes invernaderos creados. Crea uno usando el formulario de arriba.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>
<footer class="footer">
        <div class="footer-container">
            <p>&copy; 2024 Invernaderos EcoMetrics. Todos los derechos reservados.</p>
            <div class="footer-links">
                <a href="https://ecometricss.github.io/Ecometrics/">Manuales del Usuario</a>
                <a href="#">Términos y Condiciones</a>
                <a href="#">Contacto</a>
                <a href="../login.html">Cerrar Sesion</a>
            </div>
            <div class="footer-social">
                <a href="https://www.facebook.com" target="_blank"><i class="fab fa-facebook-f"></i></a>
                <a href="https://www.instagram.com" target="_blank"><i class="fab fa-instagram"></i></a>
                <a href="https://www.twitter.com" target="_blank"><i class="fab fa-twitter"></i></a>
            </div>
        </div>
</footer>
</body>
</html>