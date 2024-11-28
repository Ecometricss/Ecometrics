<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = htmlspecialchars($_POST['nombre']);
    $correo = htmlspecialchars($_POST['correo']);
    $contraseña = password_hash($_POST['contraseña'], PASSWORD_BCRYPT);

    // Verificar si el correo ya existe en la base de datos
    $sql_check = "SELECT COUNT(*) FROM usuarios WHERE Correo = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->execute([$correo]);
    $correoExiste = $stmt_check->fetchColumn() > 0;

    if ($correoExiste) {
        // Si el correo ya está registrado, mostrar mensaje de error
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Error en el Registro</title>
            <style>
                .error-container {
                    max-width: 500px;
                    margin: 50px auto;
                    padding: 20px;
                    background: #fff;
                    border-radius: 10px;
                    box-shadow: 0 0 10px rgba(0,0,0,0.1);
                }
                .error-message {
                    background: #ffe6e6;
                    padding: 15px;
                    border-radius: 5px;
                    border-left: 4px solid #ff4444;
                    color: #d63031;
                    margin-bottom: 20px;
                    text-align: center;
                }
                .back-button {
                    display: inline-block;
                    padding: 10px 20px;
                    background: #87cf3e;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                    transition: background 0.3s ease;
                    text-align: center;
                }
                .back-button:hover {
                    background: #226d0f;
                }
            </style>
        </head>
        <body style="background: linear-gradient(135deg, #87cf3e, #226d0f); min-height: 100vh; margin: 0; display: flex; align-items: center;">
            <div class="error-container">
                <p class="error-message">Este correo ya está registrado. Por favor, usa otro correo.</p>
                <a href="javascript:history.back()" class="back-button">Volver al formulario</a>
            </div>
        </body>
        </html>
        <?php
        exit();
    }

    // Si el correo no existe, proceder con el registro
    $sql = "INSERT INTO usuarios (Nombre, Correo, Contraseña) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute([$nombre, $correo, $contraseña])) {
        // Mensaje de registro exitoso con enlace a iniciar sesión
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Registro Exitoso</title>
            <style>
                .success-container {
                    max-width: 500px;
                    margin: 50px auto;
                    padding: 20px;
                    background: #fff;
                    border-radius: 10px;
                    box-shadow: 0 0 10px rgba(0,0,0,0.1);
                    text-align: center;
                }
                .success-message {
                    color: #226d0f;
                    font-size: 18px;
                    margin-bottom: 20px;
                }
                .login-link {
                    display: inline-block;
                    padding: 10px 20px;
                    background: #87cf3e;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                    transition: background 0.3s ease;
                }
                .login-link:hover {
                    background: #226d0f;
                }
            </style>
        </head>
        <body style="background: linear-gradient(135deg, #87cf3e, #226d0f); min-height: 100vh; margin: 0; display: flex; align-items: center;">
            <div class="success-container">
                <p class="success-message">¡Registro exitoso!</p>
                <a href="../login.html" class="login-link">Iniciar sesión</a>
            </div>
        </body>
        </html>
        <?php
    } else {
        // Mensaje de error en caso de fallo en la inserción
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Error en el Registro</title>
            <style>
                .error-container {
                    max-width: 500px;
                    margin: 50px auto;
                    padding: 20px;
                    background: #fff;
                    border-radius: 10px;
                    box-shadow: 0 0 10px rgba(0,0,0,0.1);
                }
                .error-message {
                    background: #ffe6e6;
                    padding: 15px;
                    border-radius: 5px;
                    border-left: 4px solid #ff4444;
                    color: #d63031;
                    margin-bottom: 20px;
                    text-align: center;
                }
                .back-button {
                    display: inline-block;
                    padding: 10px 20px;
                    background: #87cf3e;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                    transition: background 0.3s ease;
                    text-align: center;
                }
                .back-button:hover {
                    background: #226d0f;
                }
            </style>
        </head>
        <body style="background: linear-gradient(135deg, #87cf3e, #226d0f); min-height: 100vh; margin: 0; display: flex; align-items: center;">
            <div class="error-container">
                <p class="error-message">Error al registrar. Por favor, inténtelo de nuevo.</p>
                <a href="javascript:history.back()" class="back-button">Volver al formulario</a>
            </div>
        </body>
        </html>
        <?php
    }
}
?>