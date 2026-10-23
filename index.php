
<?php
$nombreSistema = "Sistema de Administración del Transmetro";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transmetro | Panel de Administración</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f6fa;
            color: #243247;
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
        }

        main {
            max-width: 1050px;
            margin: 45px auto;
            padding: 0 20px;
        }

        .bienvenida {
            background: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 15px #00000010;
            margin-bottom: 30px;
        }

        h2 {
            color: #17365d;
            margin-top: 0;
        }

        .bienvenida p {
            color: #64748b;
            line-height: 1.6;
        }

        .estado {
            display: inline-block;
            background: #e7f6ec;
            color: #16803c;
            padding: 9px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }

        .titulo-modulos {
            margin: 0 0 20px;
            font-size: 21px;
            color: #17365d;
        }

        .modulos {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .modulo {
            background: white;
            border: 1px solid #e4eaf1;
            border-radius: 12px;
            padding: 28px;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 4px 15px #00000008;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .modulo:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 22px #00000016;
        }

        .icono {
            font-size: 32px;
            margin-bottom: 15px;
        }

        .modulo h3 {
            margin: 0 0 10px;
            color: #17365d;
            font-size: 20px;
        }

        .modulo p {
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
            min-height: 44px;
        }

        .ingresar {
            display: inline-block;
            margin-top: 12px;
            background: #17365d;
            color: white;
            padding: 11px 20px;
            border-radius: 7px;
            font-weight: bold;
            font-size: 14px;
        }

        footer {
            text-align: center;
            color: #718096;
            font-size: 13px;
            padding: 30px 15px;
        }

        @media (max-width: 650px) {
            .modulos {
                grid-template-columns: 1fr;
            }

            header h1 {
                font-size: 22px;
            }

            main {
                margin-top: 25px;
            }
        }
    </style>
</head>

<body>

    <header>
        <h1><?php echo htmlspecialchars($nombreSistema); ?></h1>
        <p>Panel principal de administración</p>
    </header>

    <main>
        <section class="bienvenida">
            <h2>Bienvenido al sistema</h2>

            <p>
                Administrá la información de las líneas,
                estaciones, buses y pilotos del Transmetro
                desde un solo lugar.
            </p>

            <span class="estado">
                Panel principal disponible
            </span>
        </section>

        <h2 class="titulo-modulos">
            Módulos del sistema
        </h2>

        <section class="modulos">

            <a class="modulo" href="modulos/lineas.php">
                <div class="icono"></div>
                <h3>Gestión de Líneas</h3>
                <p>
                    Consultar y administrar las líneas
                    de transporte registradas.
                </p>
                <span class="ingresar">Ingresar →</span>
            </a>

            <a class="modulo" href="modulos/estaciones.php">
                <div class="icono"></div>
                <h3>Gestión de Estaciones</h3>
                <p>
                    Registrar y consultar estaciones
                    asociadas a las líneas.
                </p>
                <span class="ingresar">Ingresar →</span>
            </a>

            <a class="modulo" href="modulos/buses.php">
                <div class="icono"></div>
                <h3>Gestión de Buses</h3>
                <p>
                    Administrar los buses, sus capacidades
                    y estados operativos.
                </p>
                <span class="ingresar">Ingresar →</span>
            </a>

            <a class="modulo" href="modulos/pilotos.php">
                <div class="icono"></div>
                <h3>Gestión de Pilotos</h3>
                <p>
                    Consultar y administrar la información
                    de los pilotos registrados.
                </p>
                <span class="ingresar">Ingresar →</span>
            </a>

        </section>
    </main>

    <footer>
        Sistema de Administración del Transmetro |
        Proyecto Académico 2026
    </footer>

</body>
</html>
