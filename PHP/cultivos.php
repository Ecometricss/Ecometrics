<?php
session_start(); 

if (!isset($_SESSION['email'])) {
    header("Location: login.html");
    exit();
}


include 'db.php';


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'agregar_cultivo') {

    $temperatura_minima = isset($_POST['temperatura_minima']) ? $_POST['temperatura_minima'] : NULL;
    $temperatura_maxima = isset($_POST['temperatura_maxima']) ? $_POST['temperatura_maxima'] : NULL;


    $nombre_cultivo = $_POST['nombre_cultivo'];
    $temperatura_minima = $_POST['temperatura_minima'];
    $temperatura_maxima = $_POST['temperatura_maxima'];
    $humedad_minima = $_POST['humedad_minima'];
    $humedad_maxima = $_POST['humedad_maxima'];
    $Temperatura_ambiente = $_POST['Temperatura_ambiente'];
    $Humedad_ambiente = $_POST['Humedad_ambiente'];
    $Max_temp_amb = $_POST['Max_temp_amb'];
    $Max_hum_amb = $_POST['Max_hum_amb'];

   
    $sql = "INSERT INTO cultivos (Nombre, Temperatura_minima, Temperatura_maxima, Humedad_minima, Humedad_maxima, Temperatura_ambiente, Humedad_ambiente, Max_temp_amb, Max_hum_amb, ID_Usuario) VALUES (:nombre, :temp_min, :temp_max, :hum_min, :hum_max, :temp_amb, :hum_amb, :max_temp, :max_hum, :id_usuario)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'nombre' => $nombre_cultivo,
        'temp_min' => $temperatura_minima,
        'temp_max' => $temperatura_maxima,
        'hum_min' => $humedad_minima,
        'hum_max' => $humedad_maxima,
        'temp_amb' => $Temperatura_ambiente,
        'hum_amb' => $Humedad_ambiente,
        'max_temp' => $Max_temp_amb,
        'max_hum' => $Max_hum_amb,
        'id_usuario' => $_SESSION['ID_Usuario']
    ]);

    
    header("Location: cultivos.php");
    exit();
}




// Lógica para eliminar cultivo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'eliminar_cultivo') {
    $id_cultivo = $_POST['id_cultivo'];

    $sql = "DELETE FROM cultivos WHERE ID_Cultivo = :id_cultivo AND ID_Usuario = :id_usuario";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'id_cultivo' => $id_cultivo,
        'id_usuario' => $_SESSION['ID_Usuario']
    ]);

    header("Location: cultivos.php");
    exit();
}


$sql = "SELECT * FROM cultivos WHERE ID_Usuario = :id_usuario";
$stmt = $conn->prepare($sql);
$stmt->execute(['id_usuario' => $_SESSION['ID_Usuario']]);
$cultivos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Cultivos</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/monitoreo.css">
</head>
<body>
<div class="container">
        <div class="header">
            <h1><i class="fas fa-leaf icon"></i>Gestión de Cultivos</h1>
            <div class="welcome-message">
                <h2>Bienvenido, <?php echo $_SESSION['nombre']; ?>!</h2>
                <a href="index.php" class="nav-link">
                    <i class="fas fa-chart-line icon"></i>Gestionar Monitoreo
                </a>
            </div>
        </div>

        <div class="form-container">
            <h2><i class="fas fa-plus icon"></i>Agregar Nuevo Cultivo</h2>
            <form method="POST">
                <input type="hidden" name="action" value="agregar_cultivo">

                <div class="form-group">
                    <label for="nombre_cultivo">Nombre:</label>
                    <input type="text" name="nombre_cultivo" required placeholder="Ingrese el nombre del cultivo">
                </div>

                <div class="form-group">
                    <label for="temperatura_minima">Temperatura Mínima (°C):</label>
                    <input type="number" step="0.1" name="temperatura_minima" disabled placeholder="Esta opción estará disponible pronto">
                </div>

                <div class="form-group">
                    <label for="temperatura_maxima">Temperatura Máxima (°C):</label>
                    <input type="number" step="0.1" name="temperatura_maxima" disabled placeholder="Esta opción estará disponible pronto">
                </div>

                <div class="form-group">
                    <label for="humedad_minima">Humedad Mínima (%):</label>
                    <input type="number" step="0.1" name="humedad_minima" required placeholder="Ej: 60.0">
                </div>

                <div class="form-group">
                    <label for="humedad_maxima">Humedad Máxima (%):</label>
                    <input type="number" step="0.1" name="humedad_maxima" required placeholder="Ej: 80.0">
                </div>

                <div class="form-group">
                    <label for="temperatura_ambiente">Temperatura ambiente (°C):</label>
                    <input type="number" step="0.1" name="Temperatura_ambiente" required placeholder="Ej: 18.5">
                </div>

                <div class="form-group">
                    <label for="max_temp_amb">Temperatura ambiente_max (°C):</label>
                    <input type="number" step="0.1" name="Max_temp_amb" required placeholder="Ej: 30.0">
                </div>

                <div class="form-group">
                    <label for="humedad_ambiente">Humedad ambiente (%):</label>
                    <input type="number" step="0.1" name="Humedad_ambiente" required placeholder="Ej: 60.0">
                </div>

                <div class="form-group">
                    <label for="max_hum_amb">Humedad ambiente_max (%):</label>
                    <input type="number" step="0.1" name="Max_hum_amb" required placeholder="Ej: 80.0">
                </div>

                <button type="submit">
                    <i class="fas fa-plus icon"></i>Agregar Cultivo
                </button>
            </form>
        </div>


        <div class="table-container">
            <h2><i class="fas fa-seedling icon"></i>Tus Cultivos</h2>
            <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Humedad Min (%)</th>
            <th>Humedad Max (%)</th>
            <th>Temp. Ambiente (°C)</th>
            <th>Temp. Ambiente_max (°C)</th>
            <th>Humedad Ambiente (%)</th>
            <th>Humedad Ambiente_max (%)</th>
            <th>Acciones</th> <!-- Nueva columna para acciones -->
        </tr>
        <?php foreach ($cultivos as $cultivo): ?>
        <tr>
            <td><?php echo $cultivo['ID_Cultivo']; ?></td>
            <td><?php echo $cultivo['Nombre']; ?></td>
            <td><?php echo $cultivo['Humedad_minima']; ?></td>
            <td><?php echo $cultivo['Humedad_maxima']; ?></td>
            <td><?php echo $cultivo['Temperatura_ambiente']; ?></td>
            <td><?php echo $cultivo['Max_temp_amb']; ?></td>
            <td><?php echo $cultivo['Humedad_ambiente']; ?></td> 
            <td><?php echo $cultivo['Max_hum_amb']; ?></td> 
            <td>
                <!-- Botón para eliminar el cultivo -->
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="eliminar_cultivo">
                    <input type="hidden" name="id_cultivo" value="<?php echo $cultivo['ID_Cultivo']; ?>">
                    <button type="submit" onclick="return confirm('¿Estás seguro de que deseas eliminar este cultivo?');">
                        Eliminar
                    </button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
        </div>
    </div>
    


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











