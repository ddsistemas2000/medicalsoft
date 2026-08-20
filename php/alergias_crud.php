<?php
require_once 'conexion_bd.php'; 
mysqli_select_db($conn, 'medicalsoft'); 
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$mensaje = "";
$tipo_alerta = "alert-info";

// --- 1. PROCESAMIENTO DE GUARDADO / ELIMINACIÓN ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (!empty($_POST['id'])) ? mysqli_real_escape_string($conn, $_POST['id']) : null;
    $categoria = mysqli_real_escape_string($conn, $_POST['categoria']);
    $sustancia = mysqli_real_escape_string($conn, $_POST['sustancia']);
    $nivel_criticidad = mysqli_real_escape_string($conn, $_POST['nivel_criticidad']);
    $reaccion_descripcion = mysqli_real_escape_string($conn, $_POST['reaccion_descripcion']);
    
    try {
        if (isset($_POST['accion_guardar'])) {
            if (empty($id)) {
                $sql = "INSERT INTO sistema_alergias (categoria, sustancia, nivel_criticidad, reaccion_descripcion) 
                        VALUES ('$categoria', '$sustancia', '$nivel_criticidad', '$reaccion_descripcion')";
                mysqli_query($conn, $sql);
                header("Location: alergias_lista.php?res=success"); exit();
            } else {
                $sql = "UPDATE sistema_alergias SET categoria='$categoria', sustancia='$sustancia', 
                        nivel_criticidad='$nivel_criticidad', reaccion_descripcion='$reaccion_descripcion' 
                        WHERE id=$id";
                mysqli_query($conn, $sql);
                header("Location: alergias_lista.php?res=updated"); exit();
            }
        }
        if (isset($_POST['accion_eliminar']) && !empty($id)) {
            mysqli_query($conn, "DELETE FROM sistema_alergias WHERE id = $id");
            header("Location: alergias_lista.php?res=deleted"); exit();
        }
    } catch (mysqli_sql_exception $e) { $mensaje = "❌ Error: " . $e->getMessage(); $tipo_alerta = "alert-danger"; }
}

// --- 2. CARGAR DATOS PARA EDICIÓN ---
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_get = mysqli_real_escape_string($conn, $_GET['edit']);
    $res_edit = mysqli_query($conn, "SELECT * FROM sistema_alergias WHERE id = '$id_get'");
    $edit_data = mysqli_fetch_assoc($res_edit);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Alergias - MedicalSoft</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .main-container { max-width: 600px; margin: 40px auto; }
        .card { border-radius: 15px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .form-label { font-weight: 600; font-size: 0.8rem; color: #666; text-transform: uppercase; }
    </style>
</head>
<body>

<div class="container main-container">
    <div class="card p-4">
        <h3 class="text-center fw-bold mb-4"><?= $edit_data ? 'Editar Alergia' : 'Nueva Alergia' ?></h3>
        
        <form method="POST" id="formAlergia">
            <input type="hidden" name="id" value="<?= $edit_data['id'] ?? '' ?>">
            
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Categoría</label>
                    <select name="categoria" class="form-select mb-3" required>
                        <?php 
                        $cats = ['Alimentaria', 'Medicamentosa', 'Ambiental', 'Otra'];
                        foreach($cats as $c) {
                            $sel = ($edit_data['categoria'] ?? '') == $c ? 'selected' : '';
                            echo "<option value='$c' $sel>$c</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Sustancia / Agente</label>
                    <input type="text" name="sustancia" class="form-control mb-3" value="<?= htmlspecialchars($edit_data['sustancia'] ?? '') ?>" required>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Nivel de Criticidad</label>
                    <select name="nivel_criticidad" class="form-select mb-3">
                        <?php 
                        $niveles = ['Bajo', 'Moderado', 'Alto', 'Vital/Anafilaxis'];
                        foreach($niveles as $n) {
                            $sel = ($edit_data['nivel_criticidad'] ?? 'Moderado') == $n ? 'selected' : '';
                            echo "<option value='$n' $sel>$n</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Descripción de la Reacción</label>
                    <textarea name="reaccion_descripcion" class="form-control mb-3" rows="3"><?= htmlspecialchars($edit_data['reaccion_descripcion'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" name="accion_guardar" class="btn btn-primary flex-grow-1">
                    <?= $edit_data ? 'Actualizar Registro' : 'Guardar Registro' ?>
                </button>
                <?php if($edit_data): ?>
                    <button type="submit" name="accion_eliminar" class="btn btn-outline-danger" onclick="return confirm('¿Eliminar esta alergia?')">Eliminar</button>
                <?php endif; ?>
                <a href="alergias_crud.php" class="btn btn-light">Limpiar</a>
            </div>
        </form>

        <div class="text-center mt-4">
            <a href="alergias_lista.php" class="btn btn-link text-decoration-none text-muted small">← Volver al Catálogo</a>
        </div>
    </div>
</div>
</body>
</html>