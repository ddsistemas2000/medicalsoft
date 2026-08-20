<?php
// 1. CONFIGURACIÓN Y CONEXIÓN
require_once 'conexion_bd.php'; 
mysqli_select_db($conn, 'medicalsoft'); 

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// --- 2. CONSULTA DE DATOS ---
// Obtenemos todos los usuarios ordenados por nombre
$resultado = mysqli_query($conn, "SELECT id, alias, correo_electronico, nombre_completo, ip_estacion, activo FROM sistema_usuarios ORDER BY nombre_completo ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Usuarios - MedicalSoft</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .card { border-radius: 15px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .user-avatar { width: 35px; height: 35px; background: #e9ecef; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #0061ff; }
        .status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 5px; }
        .bg-active { background-color: #28a745; }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 mb-4">
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold m-0">
                        <i class="bi bi-people-fill text-primary"></i> Gestión de Usuarios
                    </h4>
                    <div>
                        <a href="usuarios_crud.php" class="btn btn-sm btn-success fw-bold me-2">
                            <i class="bi bi-person-plus"></i> Nuevo Usuario
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
                                <th>Usuario</th>
                                <th>Alias</th>
                                <th>Correo Electrónico</th>
                                <th>Última IP</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($u = mysqli_fetch_assoc($resultado)): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-2">
                                            <?= strtoupper(substr($u['nombre_completo'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($u['nombre_completo'] ?: 'Sin nombre') ?></div>
                                            <small class="text-muted">
                                                <span class="status-dot bg-active"></span> Activo
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td><code class="text-primary fw-bold">@<?= htmlspecialchars($u['alias']) ?></code></td>
                                <td><span class="text-muted"><?= htmlspecialchars($u['correo_electronico']) ?></span></td>
                                <td><small class="badge bg-light text-dark border"><?= $u['ip_estacion'] ?></small></td>
                                <td class="text-end">
                                    <a href="usuarios_crud.php" class="btn btn-sm btn-outline-primary shadow-sm px-3">
                                        <i class="bi bi-pencil-square"></i> Editar
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if(mysqli_num_rows($resultado) == 0): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-person-exclamation fs-2"></i>
                        <p class="mt-2">No hay usuarios registrados en la base de datos.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>