
<?php
require_once __DIR__ . "/../config/conexion.php";

function escapar($valor) {
    return htmlspecialchars(
        (string)$valor,
        ENT_QUOTES,
        "UTF-8"
    );
}

$mensaje = "";
$tipoMensaje = "exito";

// Registrar una nueva estación
if (
    $_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST["guardar"])
) {
    $nombre = trim($_POST["nombre"] ?? "");
    $ubicacion = trim($_POST["ubicacion"] ?? "");

    $id_linea = filter_input(
        INPUT_POST,
        "id_linea",
        FILTER_VALIDATE_INT
    );

    if ($nombre !== "" && $id_linea) {
        try {
            $verificar = $conexion->prepare(
                "SELECT COUNT(*) FROM lineas WHERE id_linea = ?"
            );
            $verificar->execute([$id_linea]);

            if ($verificar->fetchColumn() > 0) {
                $sql = "INSERT INTO estaciones
                        (nombre, ubicacion, id_linea)
                        VALUES (?, ?, ?)";

                $stmt = $conexion->prepare($sql);
                $stmt->execute([
                    $nombre,
                    $ubicacion,
                    $id_linea
                ]);

                header("Location: estaciones.php?guardado=1");
                exit;
            } else {
                $mensaje = "La línea seleccionada no existe.";
                $tipoMensaje = "error";
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $mensaje = "No se pudo registrar la estación.";
            $tipoMensaje = "error";
        }
    } else {
        $mensaje = "Completa los campos obligatorios.";
        $tipoMensaje = "error";
    }
}

// Eliminar una estación
if (
    $_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST["eliminar"])
) {
    $id = filter_input(
        INPUT_POST,
        "id_estacion",
        FILTER_VALIDATE_INT
    );

    if ($id) {
        try {
            $stmt = $conexion->prepare(
                "DELETE FROM estaciones WHERE id_estacion = ?"
            );
            $stmt->execute([$id]);

            header("Location: estaciones.php?eliminado=1");
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $mensaje = "No se pudo eliminar la estación porque tiene registros relacionados o se produjo un error.";
            $tipoMensaje = "error";
        }
    } else {
        $mensaje = "El identificador de la estación no es válido.";
        $tipoMensaje = "error";
    }
}

// Obtener líneas para el selector
$lineas = $conexion->query(
    "SELECT id_linea, nombre
     FROM lineas
     ORDER BY nombre"
)->fetchAll(PDO::FETCH_ASSOC);

// Obtener estaciones y sus líneas
$estaciones = $conexion->query(
    "SELECT e.id_estacion,
            e.nombre,
            e.ubicacion,
            l.nombre AS nombre_linea
     FROM estaciones e
     INNER JOIN lineas l
         ON e.id_linea = l.id_linea
     ORDER BY e.id_estacion DESC"
)->fetchAll(PDO::FETCH_ASSOC);

// Mensajes de confirmación
if (isset($_GET["guardado"])) {
    $mensaje = "Estación registrada correctamente.";
}

if (isset($_GET["eliminado"])) {
    $mensaje = "Estación eliminada correctamente.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Transmetro | Gestión de Estaciones</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f6fa;
            color: #253247;
        }

        header {
            background: #17365d;
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        header h1 {
            margin: 0 0 10px;
            font-size: 28px;
        }

        header p {
            margin: 0;
            color: #dce7f5;
            font-size: 15px;
        }

        main {
            max-width: 1050px;
            margin: 35px auto;
            padding: 0 20px;
        }

        .navegacion {
            margin-bottom: 25px;
        }

        .boton-volver {
            display: inline-block;
            background: #17365d;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            transition: background 0.2s;
        }

        .boton-volver:hover {
            background: #27578d;
        }

        .encabezado-seccion {
            margin-bottom: 25px;
        }

        .encabezado-seccion h2 {
            color: #17365d;
            margin-bottom: 8px;
        }

        .encabezado-seccion p {
            color: #64748b;
            line-height: 1.6;
            margin-top: 0;
        }

        .tarjeta {
            background: white;
            padding: 28px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 4px 14px #00000012;
            border: 1px solid #e5eaf0;
        }

        .tarjeta h2 {
            color: #17365d;
            font-size: 21px;
            margin: 0 0 20px;
        }

        label {
            display: block;
            margin: 16px 0 7px;
            font-weight: bold;
            font-size: 14px;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccd5e0;
            border-radius: 7px;
            background: white;
            font-family: inherit;
            font-size: 14px;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #27578d;
            box-shadow: 0 0 0 3px #27578d20;
        }

        button {
            padding: 11px 18px;
            background: #17365d;
            color: white;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            font-family: inherit;
        }

        button:hover {
            background: #27578d;
        }

        .guardar {
            margin-top: 20px;
        }

        .eliminar {
            background: #b42332;
            padding: 9px 14px;
        }

        .eliminar:hover {
            background: #8f1c28;
        }

        .mensaje {
            padding: 15px 18px;
            background: #e5f4ea;
            color: #176b37;
            border-left: 4px solid #16803c;
            border-radius: 7px;
            margin-bottom: 25px;
            font-weight: bold;
        }

        .mensaje.error {
            background: #fdebec;
            color: #a12632;
            border-left-color: #b42332;
        }

        .tabla-contenedor {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid #e1e5eb;
            font-size: 14px;
        }

        th {
            background: #eaf0f7;
            color: #17365d;
            font-weight: bold;
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        .sin-registros {
            text-align: center;
            color: #64748b;
            padding: 25px;
        }

        .aviso {
            background: #fff7e6;
            border-left: 4px solid #e2a329;
            color: #765519;
            padding: 15px;
            border-radius: 7px;
            line-height: 1.6;
        }

        .enlace-lineas {
            display: inline-block;
            margin-top: 12px;
            color: #17365d;
            font-weight: bold;
            text-decoration: none;
        }

        .enlace-lineas:hover {
            text-decoration: underline;
        }

        footer {
            text-align: center;
            color: #718096;
            font-size: 13px;
            padding: 20px 15px 35px;
        }

        @media (max-width: 650px) {
            header h1 {
                font-size: 22px;
            }

            main {
                margin-top: 25px;
            }

            .tarjeta {
                padding: 20px;
            }
        }
    </style>
</head>

<body>

    <header>
        <h1>Administración del Transmetro</h1>
        <p>Gestión y control de estaciones</p>
    </header>

    <main>

        <nav class="navegacion">
            <a href="../index.php" class="boton-volver">
                ← Volver al inicio
            </a>
        </nav>

        <section class="encabezado-seccion">
            <h2>Gestión de Estaciones</h2>
            <p>
                En este apartado podés registrar estaciones,
                consultar su ubicación y verificar a qué
                línea del Transmetro pertenece cada una.
            </p>
        </section>

        <?php if ($mensaje !== ""): ?>
            <div class="mensaje <?= escapar($tipoMensaje) ?>">
                <?= escapar($mensaje) ?>
            </div>
        <?php endif; ?>

        <section class="tarjeta">

            <h2>Registrar nueva estación</h2>

            <?php if (count($lineas) === 0): ?>

                <div class="aviso">
                    Primero debés registrar una línea
                    para poder agregar estaciones.
                </div>

                <a href="lineas.php" class="enlace-lineas">
                    Ir a Gestión de Líneas →
                </a>

            <?php else: ?>

                <form method="POST">

                    <label for="nombre">
                        Nombre de la estación
                    </label>

                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        maxlength="100"
                        placeholder="Ejemplo: Estación Central"
                        required
                    >

                    <label for="ubicacion">
                        Ubicación
                    </label>

                    <input
                        type="text"
                        id="ubicacion"
                        name="ubicacion"
                        maxlength="200"
                        placeholder="Ejemplo: Zona 1, Ciudad de Guatemala"
                    >

                    <label for="id_linea">
                        Línea del Transmetro
                    </label>

                    <select
                        id="id_linea"
                        name="id_linea"
                        required
                    >
                        <option value="">
                            Seleccioná una línea
                        </option>

                        <?php foreach ($lineas as $linea): ?>
                            <option value="<?= escapar($linea["id_linea"]) ?>">
                                <?= escapar($linea["nombre"]) ?>
                            </option>
                        <?php endforeach; ?>

                    </select>

                    <button
                        class="guardar"
                        type="submit"
                        name="guardar"
                        value="1">
                        Guardar estación
                    </button>

                </form>

            <?php endif; ?>

        </section>

        <section class="tarjeta">

            <h2>
                Estaciones registradas (<?= count($estaciones) ?>)
            </h2>

            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Estación</th>
                            <th>Ubicación</th>
                            <th>Línea</th>
                            <th>Acción</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($estaciones as $estacion): ?>
                            <tr>
                                <td>
                                    <?= escapar($estacion["id_estacion"]) ?>
                                </td>

                                <td>
                                    <?= escapar($estacion["nombre"]) ?>
                                </td>

                                <td>
                                    <?= escapar($estacion["ubicacion"]) ?>
                                </td>

                                <td>
                                    <?= escapar($estacion["nombre_linea"]) ?>
                                </td>

                                <td>
                                    <form
                                        method="POST"
                                        onsubmit="return confirm('¿Estás seguro de eliminar esta estación?');"
                                    >
                                        <input
                                            type="hidden"
                                            name="id_estacion"
                                            value="<?= escapar($estacion["id_estacion"]) ?>"
                                        >

                                        <button
                                            class="eliminar"
                                            name="eliminar"
                                            value="1"
                                            type="submit">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (count($estaciones) === 0): ?>
                            <tr>
                                <td colspan="5" class="sin-registros">
                                    No hay estaciones registradas.
                                </td>
                            </tr>
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>

        </section>

    </main>

    <footer>
        Sistema de Administración del Transmetro |
        Proyecto Académico 2026
    </footer>

</body>
</html>
