<!DOCTYPE html>
<html lang='es'>

    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>404 - Página no encontrada</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
                background: linear-gradient(135deg, #D4A574 0%, #8B6F47 100%);
                color: white;
            }

            .error-container {
                text-align: center;
            }

            .error-code {
                font-size: 8rem;
                font-weight: bold;
                margin: 0;
            }

            .error-message {
                font-size: 1.5rem;
                margin: 1rem 0;
            }

            .error-link {
                display: inline-block;
                margin-top: 2rem;
                padding: 1rem 2rem;
                background: white;
                color: #8B6F47;
                text-decoration: none;
                border-radius: 8px;
                font-weight: bold;
            }
        </style>
    </head>

    <body>
        <div class='error-container'>
            <h1 class='error-code'>404</h1>
            <p class='error-message'>Página no encontrada</p>
            <p>La ruta solicitada no existe en el sistema.</p>
            <p>Ruta: {$path}</p>
            <a href='/dashboard' class='error-link'>Volver al Dashboard</a>
        </div>
    </body>

</html>
