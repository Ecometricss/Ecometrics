<?php
include 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = htmlspecialchars($_POST['correo']); 

    $sql = "DELETE FROM usuarios WHERE Correo = :correo";
    $stmt = $conn->prepare($sql);


    if ($stmt->execute(['correo' => $correo])) {
        echo "Usuario eliminado con éxito.";
    } else {
        echo "Error al eliminar el usuario.";
    }
}
?>


<form method="POST" action="">
    <label for="correo">Correo del usuario a eliminar:</label>
    <input type="email" name="correo" required>
    <input type="submit" value="Eliminar Usuario">
</form>
