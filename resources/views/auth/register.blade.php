<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema HabilProf</title>
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
        .register-container {
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
        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #CED4DA;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 1em;
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
        .login-link {
            text-align: center;
            margin-top: 25px;
            font-size: 0.9em;
        }
        .login-link p {
            margin: 0;
            color: #555;
        }
        .login-link a {
            color: #0056A8;
            text-decoration: none;
            font-weight: 600;
        }
        .login-link a:hover {
            text-decoration: underline;
        }

        /*mostrar mensajes de error de validación */
        .error-message {
            color: #E60026; 
            font-size: 0.875em;
            font-weight: 500;
            margin-top: 5px;
        }
    </style>
</head>
<body>

    <div class="register-container">
        <header>
            <img src="{{ asset('imagenes/ucsc2.png') }}" alt="Logo UCSC">
            <h1>Registro</h1>
            <h1>Sistema HabilProf</h1>
        </header>

        <main class="form-container">
            <h2>Crear Nueva Cuenta</h2>
 
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group">
                    <label for="name">Nombre de Usuario</label>
                    <input type="text" id="name" name="name" required 
                           placeholder="Ingrese un nombre de usuario">
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" required 
                           placeholder="ejemplo@ucsc.cl">
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required 
                           placeholder="Mínimo 8 caracteres">
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirmar Contraseña</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required 
                           placeholder="Vuelva a escribir la contraseña">
                    @error('password_confirmation')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="button-container">
                    <button type="submit">Registrarse</button>
                </div>

            </form>
            
            <div class="login-link">
                <p>¿Ya tienes una cuenta? <a href="{{ route('login') }}">Ingresa aquí</a></p>
            </div>
        </main>
    </div>

</body>
</html>