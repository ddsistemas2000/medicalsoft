<?php
// 1. CONFIGURACIÓN Y CONEXIÓN
require_once 'conexion_bd.php'; 
mysqli_select_db($conn, 'medicalsoft'); 

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// --- 2. CONSULTA DE DATOS ---
// Solo necesitamos obtener la lista, eliminamos todo el procesamiento de POST
$resultado = mysqli_query($conn, "SELECT * FROM sistema_alergias ORDER BY categoria ASC, sustancia ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo de Alergias - MedicalSoft</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .card { border-radius: 15px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .badge-criticidad { font-size: 0.7rem; padding: 4px 8px; border-radius: 12px; }
        .table-container { background: white; border-radius: 15px; padding: 20px; }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 mb-4">
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold m-0">
                        <i class="bi bi-list-check text-primary"></i> Catálogo de Alergias
                    </h4>
                    <div>
                        <a href="alergias_crud.php" class="btn btn-sm btn-success fw-bold">
                            <i class="bi bi-plus-lg"></i> Nuevo Registro
                        </a>
                        <a href="dashboard_master.php" class="btn btn-sm btn-outline-secondary fw-bold">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>               
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Sustancia</th>
                                <th>Categoría</th>
                                <th>Criticidad</th>
                                <th>Descripción de la Reacción</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                            <tr>
                                <td class="text-end">
                                    <a href="alergias_crud.php?edit=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square"></i> Editar
                                    </a>
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($resultado)): 
                                $nivel = $row['nivel_criticidad'];
                                switch ($nivel) {
                                    case 'Bajo': $color = 'bg-info text-dark'; break;
                                    case 'Moderado': $color = 'bg-warning text-dark'; break;
                                    case 'Alto': $color = 'bg-danger text-white'; break;
                                    case 'Vital/Anafilaxis': $color = 'bg-dark text-white'; break;
                                    default: $color = 'bg-secondary text-white';
                                }
                            ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-primary"><?= htmlspecialchars($row['sustancia']) ?></div>
                                </td>
                                <td><span class="text-muted small"><?= htmlspecialchars($row['categoria']) ?></span></td>
                                <td>
                                    <span class="badge badge-criticidad <?= $color ?>">
                                        <?= htmlspecialchars($row['nivel_criticidad']) ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($row['reaccion_descripcion'] ?: 'Sin descripción detallada') ?>
                                    </small>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if(mysqli_num_rows($resultado) == 0): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-info-circle fs-2"></i>
                        <p class="mt-2">No hay alergias registradas en el sistema.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>