<?php

include '../../includes/auth.php';
validarAcceso('estudiante');

require '../../includes/db.php';
include '../../includes/header.php';


if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../login/indexLogin.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario']; 
$mensaje = "";

// Guardar datos
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $contenido = trim($_POST['contenido']);

    if (!empty($contenido)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO diario (id_usuario, contenido, fecha) VALUES (:id_usuario, :contenido, CURRENT_DATE)");
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->bindParam(':contenido', $contenido, PDO::PARAM_STR);
            $stmt->execute();

            $mensaje = "<div class='alert alert-success text-center mt-3'>Tu entrada ha sido guardada correctamente 🌿</div>";
        } catch (PDOException $e) {
            $mensaje = "<div class='alert alert-danger text-center mt-3'>Error al guardar: " . $e->getMessage() . "</div>";
        }
    } else {
        $mensaje = "<div class='alert alert-warning text-center mt-3'>Por favor escribe algo antes de guardar.</div>";
    }
}
?>

<style>
    body {
        background-image: url('../../assets/images/indexDiario.jpg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        font-family: 'Segoe UI', sans-serif;
    }

    .diario-card {
        background: rgba(255, 255, 255, 0.75); 
        backdrop-filter: blur(6px);
        padding: 35px;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        position: relative;
        z-index: 5;
    }

    textarea {
        border-radius: 10px !important;
    }

    h2 {
        font-weight: 700;
        color: #2c3e50;
        text-shadow: 1px 1px 2px white;
    }

    /* ICONOS FLOTANTES JUSTO DEBAJO DEL TÍTULO */
    .floating-icons-mini {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-bottom: 20px;
        font-size: 1.8rem;
        opacity: 0.9;
    }

    .floating-icons-mini span {
        animation: floatMini 3s ease-in-out infinite;
        display: inline-block;
    }

    @keyframes floatMini {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }
</style>

<br>
<div class="container mt-5" style="max-width: 750px;">

    <div class="diario-card">

        <h2 class="text-center mb-3">
            <i class="fas fa-book-open me-2"></i>Mi Diario Emocional
        </h2>

        <!-- ICONOS ANIMADOS DEBAJO DEL TÍTULO -->
        <div class="floating-icons-mini">
            <span>🌿</span>
            <span>✨</span>
            <span>💚</span>
            <span>😊</span>
            <span>⭐</span>
        </div>

        <?= $mensaje ?>

        <form method="POST">
            <div class="mb-3">
                <label for="contenido" class="form-label fw-bold">¿Cómo te sientes hoy?</label>
                <textarea id="contenido" name="contenido" rows="6" class="form-control" placeholder="Escribe aquí tus pensamientos o emociones del día..." required></textarea>
            </div>

            <div class="d-flex justify-content-center mt-4">
                <button type="submit" class="btn btn-success px-4 me-3">Guardar entrada</button>
                <a href="verDiario.php" class="btn btn-outline-secondary px-4">
                    Ver mis entradas 🗓️
                </a>            
            </div>

        </form>
    </div>

</div>

<br/>

<?php include ('../../includes/footer.php'); ?>
