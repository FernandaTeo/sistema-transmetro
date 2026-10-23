
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

// Registrar un nuevo piloto
if (
    $_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST["guardar"])
) {
    $nombre = trim($_POST["nombre"] ?? "");
    $licencia = trim($_POST["licencia"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");

    if ($nombre !== "" && $licencia !== "") {
        try {
            $stmt = $conexion->prepare(
                "INSERT INTO pilotos
                 (nombre, licencia, telefono, estado)
                 VALUES (?, ?, ?, 'Activo')"
            );

            $stmt->execute([
                $nombre,
                $licencia,
                $telefono
            ]);

            header("Location: pilotos.php?guardado=1");
            exit;

        } catch (PDOException $e) {
            error_log($e->getMessage());

            $mensaje = "No se pudo registrar el piloto. Verificá que el número de licencia no esté repetido.";
            $tipoMensaje = "error";
        }
    } else {
        $mensaje = "Completá los campos obligatorios.";
        $tipoMensaje = "error";
    }
}

// Eliminar un piloto
if (
    $_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST["eliminar"])
) {
    $id = filter_input(
        INPUT_POST,
        "id_piloto",
        FILTER_VALIDATE_INT
    );

    if ($id) {
        try {
            $stmt = $conexion->prepare(
                "DELETE FROM pilotos WHERE id_piloto = ?"
            );

            $stmt->execute([$id]);

            header("Location: pilotos.php?eliminado=1");
            exit;

        } catch (PDOException $e) {
            error_log($e->getMessage());

            $mensaje = "No se pudo eliminar el piloto porque tiene registros relacionados o se produjo un error.";
            $tipoMensaje = "error";
        }
    } else {
        $mensaje = "El identificador del piloto no es válido.";
        $tipoMensaje = "error";
    }
}

// Consultar pilotos registrados
$pilotos = $conexion->query(
    "SELECT *
     FROM pilotos
     ORDER BY id_piloto DESC"
)->fetchAll(PDO::FETCH_ASSOC);

// Mensajes de confirmación
if (isset($_GET["guardado"])) {
    $mensaje = "Piloto registrado correctamente.";
}

if (isset($_GET["eliminado"])) {
    $mensaje = "Piloto eliminado correctamente.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Transmetro | Gestión de Pilotos</title>

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
        input[type="tel"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccd5e0;
            border-radius: 7px;
            background: white;
            font-family: inherit;
            font-size: 14px;
        }

        input:focus {
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

        .estado {
            display: inline-block;
            padding: 6px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .estado-activo {
            background: #e5f4ea;
            color: #16803c;
        }

        .estado-otro {
            background: #fff1d6;
            color: #986300;
        }

        .sin-registros {
            text-align: center;
            color: #64748b;
            padding: 25px;
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
        <p>Gestión y control de pilotos</p>
    </header>

    <main>

        <nav class="navegacion">
            <a href="../index.php" class="boton-volver">
                ← Volver al inicio
            </a>
        </nav>

        <section class="encabezado-seccion">
            <h2>Gestión de Pilotos</h2>

            <p>
                En este apartado podés registrar pilotos,
                consultar sus datos personales, verificar
                su número de licencia y revisar su estado.
            </p>
        </section>

        <?php if ($mensaje !== ""): ?>
            <div class="mensaje <?= escapar($tipoMensaje) ?>">
                <?= escapar($mensaje) ?>
            </div>
        <?php endif; ?>

        <section class="tarjeta">

            <h2>Registrar nuevo piloto</h2>

            <form method="POST">

                <label for="nombre">
                    Nombre completo
                </label>

                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    maxlength="150"
                    placeholder="Ejemplo: Carlos López"
                    required
                >

                <label for="licencia">
                    Número de licencia
                </label>

                <input
                    type="text"
                    id="licencia"
                    name="licencia"
                    maxlength="50"
                    placeholder="Ejemplo: LIC-001"
                    required
                >

                <label for="telefono">
                    Teléfono
                </label>

                <input
                    type="tel"
                    id="telefono"
                    name="telefono"
                    maxlength="20"
                    placeholder="Ejemplo: 5555-1234"
                >

                <button
                    class="guardar"
                    type="submit"
                    name="guardar"
                    value="1">
                    Guardar piloto
                </button>

            </form>

        </section>

        <section class="tarjeta">

            <h2>
                Pilotos registrados (<?= count($pilotos) ?>)
            </h2>

            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Licencia</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($pilotos as $piloto): ?>
                            <tr>
                                <td>
                                    <?= escapar($piloto["id_piloto"]) ?>
                                </td>

                                <td>
                                    <?= escapar($piloto["nombre"]) ?>
                                </td>

                                <td>
                                    <?= escapar($piloto["licencia"]) ?>
                                </td>

                                <td>
                                    <?= escapar($piloto["telefono"]) ?>
                                </td>

                                <td>
                                    <?php
                                    $claseEstado =
                                        $piloto["estado"] === "Activo"
                                        ? "estado-activo"
                                        : "estado-otro";
                                    ?>

                                    <span class="estado <?= $claseEstado ?>">
                                        <?= escapar($piloto["estado"]) ?>
                                    </span>
                                </td>

                                <td>
                                    <form
                                        method="POST"
                                        onsubmit="return confirm('¿Estás seguro de eliminar este piloto?');"
                                    >
                                        <input
                                            type="hidden"
                                            name="id_piloto"
                                            value="<?= escapar($piloto["id_piloto"]) ?>"
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

                        <?php if (count($pilotos) === 0): ?>
                            <tr>
                                <td colspan="6" class="sin-registros">
                                    No hay pilotos registrados.
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
