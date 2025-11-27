<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Cuenta - Sistema HabilProf</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #F8F9FA;
            color: #222222;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 90vh;
        }
        .verify-container {
            max-width: 450px;
            width: 100%;
            margin: 20px auto;
            background-color: #FFFFFF;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid #CED4DA;
            overflow: hidden;
        }
        header {
            padding: 25px 30px;
            background-color: #FFFFFF;
            border-bottom: 1px solid #CED4DA;
            text-align: center;
        }
        header img {
            max-height: 50px;
            width: auto;
            margin-bottom: 15px;
        }
        header h1 {
            margin: 0;
            color: #222222;
            font-size: 1.8em;
        }
        .form-container {
            padding: 30px 40px;
        }
        .form-container h2 {
            text-align: center;
            color: #E60026;
            margin-top: 0;
            margin-bottom: 25px;
        }
        .message {
            background-color: #E7F3FF;
            border: 1px solid #B3D7FF;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
            color: #0056A8;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: 600;
            display: block;
            margin-bottom: 8px;
            font-size: 0.9em;
            color: #333333;
        }
        .form-group input[type="text"],
        .form-group input[type="email"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #CED4DA;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 1em;
            text-align: center;
            letter-spacing: 0.5em;
            font-weight: bold;
        }
        .button-container {
            text-align: center;
            margin-top: 25px;
        }
        button {
            width: 100%;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: 600;
            transition: background-color 0.2s ease;
            background-color: #E60026;
            color: white;
        }
        button:hover {
            background-color: #C00020;
        }
        .error-message {
            color: #E60026;
            font-size: 0.875em;
            font-weight: 500;
            margin-top: 5px;
            text-align: center;
        }
        .resend-link {
            text-align: center;
            margin-top: 20px;
        }
        .resend-link a {
            color: #0056A8;
            text-decoration: none;
            font-weight: 600;
        }
        .resend-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="verify-container">
        <header>
            <img src="{{ asset('imagenes/ucsc2.png') }}" alt="Logo UCSC">
            <h1>Verificación</h1>
            <h1>Sistema HabilProf</h1>
        </header>

        <main class="form-container">
            <h2>Verificar Cuenta</h2>

            <div class="message">
                Hemos enviado un código de verificación al correo ingresado. Ingréselo para activar su cuenta.
            </div>

            <form method="POST" action="{{ route('verification.verify') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" value="{{ $email }}" readonly
                           style="background-color: #f8f9fa;">
                </div>

                <div class="form-group">
                    <label for="verification_code">Código de Verificación</label>
                    <input type="text" id="verification_code" name="verification_code" required
                           placeholder="000000" maxlength="6" pattern="[0-9]{6}">
                    @error('verification_code')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="button-container">
                    <button type="submit">Confirmar Código</button>
                </div>

            </form>

            <div class="resend-link">
                <p>¿No recibió el código? <a href="{{ route('register') }}">Registrarse nuevamente</a></p>
            </div>
        </main>
    </div>

    <script>
        // Auto-focus en el campo de código
        document.getElementById('verification_code').focus();

        // Solo permitir números en el campo de código
        document.getElementById('verification_code').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>

</body>
</html>
