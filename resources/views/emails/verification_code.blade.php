<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Cuenta HabilProf</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 150px;
        }
        .content {
            margin-bottom: 20px;
        }
        .code {
            font-size: 24px;
            font-weight: bold;
            color: #E60026;
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Verificación de Cuenta HabilProf</h1>
        </div>
        <div class="content">
            <p>Hola,</p>
            <p>Hemos recibido una solicitud para crear una cuenta en HabilProf. Para completar el proceso de registro, por favor utiliza el siguiente código de verificación:</p>
            <div class="code">{{ $code }}</div>
            <p>Ingresa este código en la página de verificación para activar tu cuenta.</p>
            <p>Si no solicitaste esta verificación, puedes ignorar este correo.</p>
        </div>
        <div class="footer">
            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
            <p>Universidad Católica de la Santísima Concepción - HabilProf</p>
        </div>
    </div>
</body>
</html>
