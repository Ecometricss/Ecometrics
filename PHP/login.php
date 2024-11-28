<?php
session_start(); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST['email']);
    $contraseña = $_POST['contraseña'];

    include 'db.php'; 

   
    $sql = "SELECT * FROM usuarios WHERE Correo = :email"; 
    $stmt = $conn->prepare($sql);
    $stmt->execute(['email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
     
        if (password_verify($contraseña, $usuario['Contraseña'])) {
        
            $_SESSION['email'] = $usuario['Correo'];
            $_SESSION['nombre'] = $usuario['Nombre'];
            $_SESSION['ID_Usuario'] = $usuario['ID_Usuario'];
            header("Location: index.php");
            exit();
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "Usuario no encontrado.";
    }
}
?>
