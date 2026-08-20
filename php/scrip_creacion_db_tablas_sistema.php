<?php
// Script de reestructuración de base de datos
// Damian Diaz

// 1. IMPORTAR LA CONEXIÓN (Asegúrate de que conexion_bd.php ya NO pida el nombre de la BD al conectar)
require_once 'conexion_bd.php'; 

try {
    // --- PASO A: VALIDAR O CREAR LA BASE DE DATOS ---
    $target_db = 'medicalsoft';

    // Verificamos si la base de datos existe
    $db_check = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$target_db'");

    if ($db_check->num_rows == 0) {
        echo "🆕 La base de datos '$target_db' no existe. Creándola...";
        echo '<br>';
        $conn->query("CREATE DATABASE `$target_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
        echo "✅ Base de datos '$target_db' creada correctamente.";
        echo '<br>';
    }

    // --- PASO B: SELECCIONAR LA BASE DE DATOS PARA TRABAJAR ---
    $conn->select_db($target_db);
    echo "✅ CONEXIÓN AL SERVIDOR Y BD '$target_db' EXITOSA";
    echo '<br>';

    // $var_decimal = "DECIMAL(15,2) DEFAULT 0.00";

    // -- DATA GENERAL DEL SISTEMA --
    // --- sistema_paises ---
    $nombre_tabla_paises = 'sistema_paises';
    $result_paises = $conn->query("SHOW TABLES LIKE '$nombre_tabla_paises'");

    if ($result_paises->num_rows == 0) {
        echo "\n 🆕 Tabla '$nombre_tabla_paises' no existe. Creando...\n";
        echo '<br>';

        $create_paises_sql = "
            CREATE TABLE `$nombre_tabla_paises` (
                `id`            INT(11) NOT NULL AUTO_INCREMENT,
                `iso_alpha3`    CHAR(3) NOT NULL,
                `iso_numeric`   CHAR(3) NOT NULL,
                `nombre`        VARCHAR(100) NOT NULL,
                `codigo_area`   VARCHAR(10),
                `emoji_bandera` VARCHAR(10),
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_iso_alpha3` (`iso_alpha3`),
                UNIQUE KEY `uk_iso_numeric` (`iso_numeric`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";

        if ($conn->query($create_paises_sql)) {
            echo " ✅ Tabla '$nombre_tabla_paises' creada correctamente.\n";
            echo '<br>';
        }

    } else {
        echo "\n 🛠 La tabla '$nombre_tabla_paises' ya existe. Aplicando modificaciones...\n";
        echo '<br>';

        $alter_paises_sqls = [
            "MODIFY COLUMN `id`            INT(11) NOT NULL AUTO_INCREMENT",
            "MODIFY COLUMN `iso_alpha3`    CHAR(3) NOT NULL",
            "MODIFY COLUMN `iso_numeric`   CHAR(3) NOT NULL",
            "MODIFY COLUMN `nombre`        VARCHAR(100) NOT NULL",
            "MODIFY COLUMN `codigo_area`   VARCHAR(10)",
            "MODIFY COLUMN `emoji_bandera` VARCHAR(10)"
        ];

        foreach ($alter_paises_sqls as $sql) {
            $conn->query("ALTER TABLE `$nombre_tabla_paises` $sql");
        }
        echo " ✅ Modificaciones en '$nombre_tabla_paises' aplicadas correctamente.\n";
        echo '<br>';
    }
    
    // -- Medicamentos - Grupo - Subgrupo - Objetivo del Tratamiento  --
    // --- sistema_grupo_atc ---
    $nombre_tabla_atc = 'sistema_grupo_atc';
    $result_atc = $conn->query("SHOW TABLES LIKE '$nombre_tabla_atc'");

    if ($result_atc->num_rows == 0) {
        echo "\n 🆕 Tabla '$nombre_tabla_atc' no existe. Creando...\n";
        echo '<br>';

        $create_atc_sql = "
            CREATE TABLE `$nombre_tabla_atc` (
                `ID_Grupo`      VARCHAR(10) NOT NULL,
                `Nombre_Grupo`  VARCHAR(255) NOT NULL,
                PRIMARY KEY (`ID_Grupo`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";

        if ($conn->query($create_atc_sql)) {
            echo " ✅ Tabla '$nombre_tabla_atc' creada correctamente.\n";
            echo '<br>';
        }

    } else {
        echo "\n 🛠 La tabla '$nombre_tabla_atc' ya existe. Aplicando modificaciones...\n";
        echo '<br>';

        $alter_atc_sqls = [
            "MODIFY COLUMN `ID_Grupo`      VARCHAR(10) NOT NULL",
            "MODIFY COLUMN `Nombre_Grupo`  VARCHAR(255) NOT NULL"
        ];

        foreach ($alter_atc_sqls as $sql) {
            $conn->query("ALTER TABLE `$nombre_tabla_atc` $sql");
        }
        echo " ✅ Modificaciones en '$nombre_tabla_atc' aplicadas correctamente.\n";
        echo '<br>';
    }

    // --- sistema_subgrupo_terapeutico ---
    $nombre_tabla_subgrupo = 'sistema_subgrupo_terapeutico';
    $result_subgrupo = $conn->query("SHOW TABLES LIKE '$nombre_tabla_subgrupo'");

    if ($result_subgrupo->num_rows == 0) {
        echo "\n 🆕 Tabla '$nombre_tabla_subgrupo' no existe. Creando...\n";
        echo '<br>';

        $create_subgrupo_sql = "
            CREATE TABLE `$nombre_tabla_subgrupo` (
                `ID_Subgrupo`     INT(11) NOT NULL AUTO_INCREMENT,
                `Nombre_Subgrupo` VARCHAR(255) NOT NULL,
                `ID_Grupo`        VARCHAR(10) NOT NULL,
                PRIMARY KEY (`ID_Subgrupo`),
                INDEX `idx_grupo_relacion` (`ID_Grupo`),
                CONSTRAINT `fk_atc_grupo` 
                    FOREIGN KEY (`ID_Grupo`) 
                    REFERENCES `sistema_grupo_atc` (`ID_Grupo`) 
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";

        if ($conn->query($create_subgrupo_sql)) {
            echo " ✅ Tabla '$nombre_tabla_subgrupo' creada correctamente.\n";
            echo '<br>';
        } else {
            echo " ❌ Error al crear tabla: " . $conn->error . "\n";
            echo '<br>';
        }

    } else {
        echo "\n 🛠 La tabla '$nombre_tabla_subgrupo' ya existe. Aplicando modificaciones...\n";
        echo '<br>';

        $alter_subgrupo_sqls = [
            "MODIFY COLUMN `ID_Subgrupo`     INT(11) NOT NULL AUTO_INCREMENT",
            "MODIFY COLUMN `Nombre_Subgrupo` VARCHAR(255) NOT NULL",
            "MODIFY COLUMN `ID_Grupo`        VARCHAR(10) NOT NULL"
        ];

        foreach ($alter_subgrupo_sqls as $sql) {
            $conn->query("ALTER TABLE `$nombre_tabla_subgrupo` $sql");
        }
        echo " ✅ Modificaciones en '$nombre_tabla_subgrupo' aplicadas correctamente.\n";
        echo '<br>';
    }

    // --- sistema_patologia ---
    $nombre_tabla_patologia = 'sistema_patologia';
    $result_patologia = $conn->query("SHOW TABLES LIKE '$nombre_tabla_patologia'");

    if ($result_patologia->num_rows == 0) {
        echo "\n 🆕 Tabla '$nombre_tabla_patologia' no existe. Creando...\n";
        echo '<br>';

        $create_patologia_sql = "
            CREATE TABLE `$nombre_tabla_patologia` (
                `id_patologia`         INT(11) NOT NULL AUTO_INCREMENT,
                `nombre_enfermedad`    VARCHAR(150) NOT NULL,
                `objetivo_tratamiento` TEXT,
                PRIMARY KEY (`id_patologia`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";

        if ($conn->query($create_patologia_sql)) {
            echo " ✅ Tabla '$nombre_tabla_patologia' creada correctamente.\n";
            echo '<br>';
        }

    } else {
        echo "\n 🛠 La tabla '$nombre_tabla_patologia' ya existe. Aplicando modificaciones...\n";
        echo '<br>';

        $alter_patologia_sqls = [
            "MODIFY COLUMN `id_patologia`         INT(11) NOT NULL AUTO_INCREMENT",
            "MODIFY COLUMN `nombre_enfermedad`    VARCHAR(150) NOT NULL",
            "MODIFY COLUMN `objetivo_tratamiento` TEXT"
        ];

        foreach ($alter_patologia_sqls as $sql) {
            $conn->query("ALTER TABLE `$nombre_tabla_patologia` $sql");
        }
        echo " ✅ Modificaciones en '$nombre_tabla_patologia' aplicadas correctamente.\n";
        echo '<br>';
    }

    // --- sistema_medicamento ---
    $nombre_tabla_medicamento = 'sistema_medicamento';
    $result_medicamento = $conn->query("SHOW TABLES LIKE '$nombre_tabla_medicamento'");

    if ($result_medicamento->num_rows == 0) {
        echo "\n 🆕 Tabla '$nombre_tabla_medicamento' no existe. Creando...\n";
        echo '<br>';

        $create_medicamento_sql = "
            CREATE TABLE `$nombre_tabla_medicamento` (
                `id_medicamento`  INT(11) NOT NULL AUTO_INCREMENT,
                `nombre_generico` VARCHAR(150) NOT NULL,
                `id_subgrupo`     INT(11) NOT NULL,
                PRIMARY KEY (`id_medicamento`),
                CONSTRAINT `fk_medicamento_subgrupo` 
                    FOREIGN KEY (`id_subgrupo`) 
                    REFERENCES `sistema_subgrupo_terapeutico` (`ID_Subgrupo`) 
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";

        if ($conn->query($create_medicamento_sql)) {
            echo " ✅ Tabla '$nombre_tabla_medicamento' creada correctamente.\n";
            echo '<br>';
        }

    } else {
        echo "\n 🛠 La tabla '$nombre_tabla_medicamento' ya existe. Aplicando modificaciones...\n";
        echo '<br>';

        $alter_medicamento_sqls = [
            "MODIFY COLUMN `id_medicamento`  INT(11) NOT NULL AUTO_INCREMENT",
            "MODIFY COLUMN `nombre_generico` VARCHAR(150) NOT NULL",
            "MODIFY COLUMN `id_subgrupo`     INT(11) NOT NULL"
        ];

        foreach ($alter_medicamento_sqls as $sql) {
            $conn->query("ALTER TABLE `$nombre_tabla_medicamento` $sql");
        }
        echo " ✅ Modificaciones en '$nombre_tabla_medicamento' aplicadas correctamente.\n";
        echo '<br>';
    }

    // --- sistema_tratamiento_sugerido ---
    $nombre_tabla_tratamiento = 'sistema_tratamiento_sugerido';
    $result_tratamiento = $conn->query("SHOW TABLES LIKE '$nombre_tabla_tratamiento'");

    if ($result_tratamiento->num_rows == 0) {
        echo "\n 🆕 Tabla '$nombre_tabla_tratamiento' no existe. Creando...\n";
        echo '<br>';

        $create_tratamiento_sql = "
            CREATE TABLE `$nombre_tabla_tratamiento` (
                `id_patologia`   INT(11) NOT NULL,
                `id_medicamento` INT(11) NOT NULL,
                PRIMARY KEY (`id_patologia`, `id_medicamento`),
                CONSTRAINT `fk_tratamiento_patologia` 
                    FOREIGN KEY (`id_patologia`) 
                    REFERENCES `sistema_patologia` (`id_patologia`) 
                    ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk_tratamiento_medicamento` 
                    FOREIGN KEY (`id_medicamento`) 
                    REFERENCES `sistema_medicamento` (`id_medicamento`) 
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";

        if ($conn->query($create_tratamiento_sql)) {
            echo " ✅ Tabla '$nombre_tabla_tratamiento' creada correctamente.\n";
            echo '<br>';
        }

    } else {
        echo "\n 🛠 La tabla '$nombre_tabla_tratamiento' ya existe. Aplicando modificaciones...\n";
        echo '<br>';

        $alter_tratamiento_sqls = [
            "MODIFY COLUMN `id_patologia`   INT(11) NOT NULL",
            "MODIFY COLUMN `id_medicamento` INT(11) NOT NULL"
        ];

        foreach ($alter_tratamiento_sqls as $sql) {
            $conn->query("ALTER TABLE `$nombre_tabla_tratamiento` $sql");
        }
        echo " ✅ Modificaciones en '$nombre_tabla_tratamiento' aplicadas correctamente.\n";
        echo '<br>';
    }
    // -- FIN Medicamentos - Grupo - Subgrupo - Objetivo del Tratamiento  --

    // --- sistema_alergias ---
    $nombre_tabla_alergias = 'sistema_alergias';
    $result_alergias = $conn->query("SHOW TABLES LIKE '$nombre_tabla_alergias'");

    if ($result_alergias->num_rows == 0) {
        echo "\n 🆕 Tabla '$nombre_tabla_alergias' no existe. Creando...\n";

        $create_alergias_sql = "
            CREATE TABLE `$nombre_tabla_alergias` (
                `id`                    INT(11) NOT NULL AUTO_INCREMENT,
                `categoria`             ENUM('Alimentaria', 'Medicamentosa', 'Ambiental', 'Otra') NOT NULL,
                `sustancia`             VARCHAR(100) NOT NULL,
                `nivel_criticidad`      ENUM('Bajo', 'Moderado', 'Alto', 'Vital/Anafilaxis') DEFAULT 'Moderado',
                `reaccion_descripcion`  TEXT,
                PRIMARY KEY (`id`),
                INDEX `idx_sustancia` (`sustancia`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";

        if ($conn->query($create_alergias_sql)) {
            echo " ✅ Tabla '$nombre_tabla_alergias' creada correctamente.\n";
        }

    } else {
        echo "\n 🛠 La tabla '$nombre_tabla_alergias' ya existe. Aplicando modificaciones...\n";

        $alter_alergias_sqls = [
            "MODIFY COLUMN `id`                    INT(11) NOT NULL AUTO_INCREMENT",
            "MODIFY COLUMN `categoria`             ENUM('Alimentaria', 'Medicamentosa', 'Ambiental', 'Otra') NOT NULL",
            "MODIFY COLUMN `sustancia`             VARCHAR(100) NOT NULL",
            "MODIFY COLUMN `nivel_criticidad`      ENUM('Bajo', 'Moderado', 'Alto', 'Vital/Anafilaxis') DEFAULT 'Moderado'",
            "MODIFY COLUMN `reaccion_descripcion`  TEXT"
        ];

        foreach ($alter_alergias_sqls as $sql) {
            $conn->query("ALTER TABLE `$nombre_tabla_alergias` $sql");
        }
        echo " ✅ Modificaciones en '$nombre_tabla_alergias' aplicadas.\n";
    }

    // --- sistema_especialidades_medicas ---
    $nombre_tabla = 'sistema_especialidades_medicas';
    $result = $conn->query("SHOW TABLES LIKE '$nombre_tabla'");

    if ($result->num_rows == 0) {
        echo "🆕 Tabla '$nombre_tabla' no existe. Creando...";
        echo '<br>';

        $create_especialidades_sql = "
            CREATE TABLE `$nombre_tabla` (
                `id_especialidad`               INT(11) NOT NULL AUTO_INCREMENT,
                `nombre`                        VARCHAR(100) NOT NULL,
                `descripcion`                   TEXT,
                `activo`                        TINYINT(1) DEFAULT 1,
                `fecha_creacion`                TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id_especialidad`),
                UNIQUE KEY `uk_nombre_especialidad` (`nombre`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";

        $conn->query($create_especialidades_sql);
        echo "\n ✅ Tabla '$nombre_tabla' creada correctamente. \n";

    // --- sistema_especialidades_medicas ---
    $nombre_tabla = 'sistema_especialidades_medicas';
    $result = $conn->query("SHOW TABLES LIKE '$nombre_tabla'");

    if ($result->num_rows == 0) {
        echo "🆕 Tabla '$nombre_tabla' no existe. Creando...";
        echo '<br>';

        $create_especialidades_sql = "
            CREATE TABLE `$nombre_tabla` (
                `id_especialidad`               INT(11) NOT NULL AUTO_INCREMENT,
                `nombre`                        VARCHAR(100) NOT NULL,
                `descripcion`                   TEXT,
                `activo`                        TINYINT(1) DEFAULT 1,
                `fecha_creacion`                TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id_especialidad`),
                UNIQUE KEY `uk_nombre_especialidad` (`nombre`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";

        $conn->query($create_especialidades_sql);
        echo "\n ✅ Tabla '$nombre_tabla' creada correctamente. \n";

    } else {
        echo "\n 🛠 La tabla '$nombre_tabla' ya existe. Aplicando modificaciones...\n";

        $alter_especialidades_sqls = [
            "MODIFY COLUMN `id_especialidad`    INT(11) NOT NULL AUTO_INCREMENT",
            "MODIFY COLUMN `nombre`             VARCHAR(100) NOT NULL",
            "MODIFY COLUMN `descripcion`        TEXT",
            "MODIFY COLUMN `activo`             TINYINT(1) DEFAULT 1",
        ];

        foreach ($alter_especialidades_sqls as $sql) {
            $conn->query("ALTER TABLE `$nombre_tabla` $sql");
        }

        echo "\n ✅ Estructura de la tabla '$nombre_tabla' actualizada exitosamente. \n";
    }

    } else {
        echo "\n 🛠 La tabla '$nombre_tabla' ya existe. Aplicando modificaciones...\n";

        $alter_especialidades_sqls = [
            "MODIFY COLUMN `id_especialidad`    INT(11) NOT NULL AUTO_INCREMENT",
            "MODIFY COLUMN `nombre`             VARCHAR(100) NOT NULL",
            "MODIFY COLUMN `descripcion`        TEXT",
            "MODIFY COLUMN `activo`             TINYINT(1) DEFAULT 1",
        ];

        foreach ($alter_especialidades_sqls as $sql) {
            $conn->query("ALTER TABLE `$nombre_tabla` $sql");
        }

        echo "\n ✅ Estructura de la tabla '$nombre_tabla' actualizada exitosamente. \n";
    }

    // --- sistema_usuarios ---
    $nombre_tabla = 'sistema_usuarios';
    $result = $conn->query("SHOW TABLES LIKE '$nombre_tabla'");

    if ($result->num_rows == 0) {
        echo "🆕 Tabla '$nombre_tabla' no existe. Creando...";
        echo '<br>';

        $create_usuarios_sql = "
            CREATE TABLE `$nombre_tabla` (
                `id`                            INT(11) NOT NULL AUTO_INCREMENT,
                `alias`                         VARCHAR(50) NOT NULL,
                `correo_electronico`            VARCHAR(150) NOT NULL,
                `contrasena`                    VARCHAR(255) NOT NULL,
                `nombre_completo`               VARCHAR(100) DEFAULT '',
                `activo`                        TINYINT(1) DEFAULT 1,
                `token_recuperacion`            VARCHAR(255) DEFAULT '',
                `ip_estacion`                   VARCHAR(40) DEFAULT '',
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_usuario` (`alias`),
                UNIQUE KEY `uk_email` (`correo_electronico`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";

        $conn->query($create_usuarios_sql);
        echo "\n ✅ Tabla '$nombre_tabla' creada correctamente. \n";

    } else {
        echo "\n 🛠 La tabla '$nombre_tabla' ya existe. Aplicando modificaciones...\n";

        $alter_usuarios_sqls = [
            "MODIFY COLUMN `id`                 INT(11) NOT NULL AUTO_INCREMENT",
            "MODIFY COLUMN `alias`              VARCHAR(50) NOT NULL",
            "MODIFY COLUMN `correo_electronico` VARCHAR(150) NOT NULL",
            "MODIFY COLUMN `contrasena`         VARCHAR(255) NOT NULL",
            "MODIFY COLUMN `nombre_completo`    VARCHAR(100) DEFAULT ''",
            "MODIFY COLUMN `activo`             TINYINT(1) DEFAULT 1",
            "MODIFY COLUMN `token_recuperacion` VARCHAR(255) DEFAULT ''",
            "MODIFY COLUMN `ip_estacion`        VARCHAR(40) DEFAULT ''",
        ];

        foreach ($alter_usuarios_sqls as $sql) {
            $conn->query("ALTER TABLE `$nombre_tabla` $sql");
        }

        echo "\n ✅ Estructura de la tabla '$nombre_tabla' actualizada exitosamente. \n";
    }

    // --- pacientes_ficha ---
    $nombre_tabla = 'pacientes_ficha';
    $result = $conn->query("SHOW TABLES LIKE '$nombre_tabla'");

    if ($result->num_rows == 0) {
    echo "\n 🆕 Tabla '$nombre_tabla' no existe. Creando...\n";

    $create_pacientes_sql = "
        CREATE TABLE `$nombre_tabla` (
            `id`                                INT(11) NOT NULL AUTO_INCREMENT,
            `nombres`                           VARCHAR(100) NOT NULL,
            `apellidos`                         VARCHAR(100) NOT NULL,
            `no_identificacion`                 VARCHAR(20) NOT NULL,
            `fecha_nacimiento`                  DATE NOT NULL,
            `genero`                            VARCHAR(20) DEFAULT '',
            `telefono_principal`                VARCHAR(20) DEFAULT '',
            `correo_electronico`                VARCHAR(150) DEFAULT '',
            `direccion_residencia`              TEXT,
            `contacto_emergencia`               VARCHAR(150) DEFAULT '',
            `tel_emergencia`                    VARCHAR(20) DEFAULT '',
            `tipo_sangre`                       VARCHAR(5) DEFAULT '',
            `alergias`                          TEXT,
            `antecedentes_personales`           TEXT,
            `antecedentes_familiares`           TEXT,
            `fecha_registro`                    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_paciente_id` (`no_identificacion`),
            UNIQUE KEY `uk_paciente_email` (`correo_electronico`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ";

    $conn->query($create_pacientes_sql);
    echo "\n ✅ Tabla '$nombre_tabla' creada correctamente.\n";

} else {
    echo "\n 🛠 La tabla '$nombre_tabla' ya existe. Aplicando modificaciones...\n";

    $alter_pacientes_sqls = [
        "MODIFY COLUMN `id`                     INT(11) NOT NULL AUTO_INCREMENT",
        "MODIFY COLUMN `nombres`                VARCHAR(100) NOT NULL",
        "MODIFY COLUMN `apellidos`              VARCHAR(100) NOT NULL",
        "MODIFY COLUMN `no_identificacion`      VARCHAR(20) NOT NULL",
        "MODIFY COLUMN `fecha_nacimiento`       DATE NOT NULL",
        "MODIFY COLUMN `genero`                 VARCHAR(20) DEFAULT ''",
        "MODIFY COLUMN `telefono_principal`     VARCHAR(20) DEFAULT ''",
        "MODIFY COLUMN `correo_electronico`     VARCHAR(150) DEFAULT ''",
        "MODIFY COLUMN `direccion_residencia`   TEXT",
        "MODIFY COLUMN `contacto_emergencia`    VARCHAR(150) DEFAULT ''",
        "MODIFY COLUMN `tel_emergencia`         VARCHAR(20) DEFAULT ''",
        "MODIFY COLUMN `tipo_sangre`            VARCHAR(5) DEFAULT ''",
        "MODIFY COLUMN `alergias`               TEXT",
        "MODIFY COLUMN `antecedentes_personales`TEXT",
        "MODIFY COLUMN `antecedentes_familiares`TEXT"
    ];

    foreach ($alter_pacientes_sqls as $sql) {
        $conn->query("ALTER TABLE `$nombre_tabla` $sql");
    }

    echo "\n ✅ Estructura de la tabla '$nombre_tabla' actualizada exitosamente.\n";
}

    // --- medicos_ficha ---
    $nombre_tabla_medicos = 'medicos_ficha';
    $result_medicos = $conn->query("SHOW TABLES LIKE '$nombre_tabla_medicos'");

    if ($result_medicos->num_rows == 0) {
        echo "\n 🆕 Tabla '$nombre_tabla_medicos' no existe. Creando...\n";

        $create_medicos_sql = "
            CREATE TABLE `$nombre_tabla_medicos` (
                `id`                        INT(11) NOT NULL AUTO_INCREMENT,
                `nombre`                    VARCHAR(100) NOT NULL,
                `apellido`                  VARCHAR(100) NOT NULL,
                `matricula_ministerio`      VARCHAR(50) NOT NULL,
                `matricula_colegio`         VARCHAR(50) DEFAULT '',
                `email`                     VARCHAR(150) DEFAULT '',
                `telefono`                  VARCHAR(20) DEFAULT '',
                `id_especialidad`           INT(11) NOT NULL,
                `fecha_creacion`            TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_medico_ministerio` (`matricula_ministerio`),
                UNIQUE KEY `uk_medico_email` (`email`),
                CONSTRAINT `fk_medico_especialidad` 
                    FOREIGN KEY (`id_especialidad`) 
                    REFERENCES `sistema_especialidades_medicas` (`id_especialidad`) 
                    ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";

        $conn->query($create_medicos_sql);
        echo "\n ✅ Tabla '$nombre_tabla_medicos' creada correctamente.\n";

    } else {
        echo "\n 🛠 La tabla '$nombre_tabla_medicos' ya existe. Aplicando modificaciones...\n";

        $alter_medicos_sqls = [
            "MODIFY COLUMN `id`                   INT(11) NOT NULL AUTO_INCREMENT",
            "MODIFY COLUMN `nombre`               VARCHAR(100) NOT NULL",
            "MODIFY COLUMN `apellido`             VARCHAR(100) NOT NULL",
            "MODIFY COLUMN `matricula_ministerio` VARCHAR(50) NOT NULL",
            "MODIFY COLUMN `matricula_colegio`    VARCHAR(50) DEFAULT ''",
            "MODIFY COLUMN `email`                VARCHAR(150) DEFAULT ''",
            "MODIFY COLUMN `telefono`             VARCHAR(20) DEFAULT ''",
            "MODIFY COLUMN `id_especialidad`      INT(11) NOT NULL"
        ];

        foreach ($alter_medicos_sqls as $sql) {
            $conn->query("ALTER TABLE `$nombre_tabla_medicos` $sql");
        }

        echo "\n ✅ Estructura de la tabla '$nombre_tabla_medicos' actualizada exitosamente.\n";
    }


    echo "\n ✅ ✅ ESTRUCTURA BD PROCESADA CORRECTAMENTE ✅ ✅...";
    $conn->close();

} catch (mysqli_sql_exception $e) {
    die("❌ Error de ejecución SQL: " . $e->getMessage());
}
?>