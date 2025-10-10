<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Eskua</title>
    <link rel="stylesheet" href="../../output.css">
    <script src="script.js" defer></script>
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-family: "Lexend", sans-serif;
            line-height: 1;
        }

        h1, .fs-h1 {
            font-size: clamp(3rem, 2.5459rem + 1.8164vw, 3.999rem);
            font-weight: 400;
        }

        h2, .fs-h2 {
            font-size: clamp(2.25rem, 1.9094rem + 1.3623vw, 2.9993rem);
            font-weight: 400;
        }

        h3, .fs-h3 {
            font-size: clamp(1.875rem, 1.5912rem + 1.1352vw, 2.4994rem);
            font-weight: 400;
        }

        h4, .fs-h4 {
            font-size: clamp(1.5rem, 1.273rem + 0.9082vw, 1.9995rem);
            font-weight: 400;
        }

        h5, .fs-h5 {
            font-size: clamp(1.25rem, 1.0608rem + 0.7568vw, 1.6663rem);
            font-weight: 400;
        }

        h6, .fs-h6 {
            font-size: clamp(1.125rem, 0.9547rem + 0.6811vw, 1.4996rem);
            font-weight: 400;
        }

        p, .fs-p {
            font-size: clamp(0.7502rem, 0.6366rem + 0.4542vw, 1rem);
            font-weight: 400;
        }

        a {
            color: #1b3b50;
            font-size: clamp(0.7502rem, 0.6366rem + 0.4542vw, 1rem);
        }

        .title {
            margin: 1rem 0 3rem 0;
            /* text-align: left; Mueve el title para la izquierda, pero no me termina de convencer */
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100vh;
            background-color: #BDBDBD;
            padding: 25px;
        }

        .main-container {
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            background-color: white;
            width: 100%;
            height: 100%;
            border-radius: 30px;
            padding: 30px;
            gap: 5rem;
        }

        .data-container, .data-container form {
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }

        .data-container {
            max-width: 30rem;
            flex: 1;
            gap: 1.5rem;
            overflow: hidden;
        }

        .data-container form {
            gap: 2.188rem;
        }

        .input-container {
            display: flex;
            align-items: center;
            position: relative;
            width: 100%;
            min-height: 3.125rem;
            max-height: 5rem;
            line-height: 50px;
        }

        .input-container input {
            position: absolute;
            width: 100%;
            outline: none;
            font-size: 24px;
            padding: 0 14px;
            line-height: 50px;
            border-radius: 10px;
            background: transparent;
            border: 2px solid #EEE7E7;
            transition: 0.1s ease;
            z-index: 10;
        }

        .input-password {
            padding: 0px 68px 0px 14px !important;
        }

        .input-container img {
            position: absolute;
            width: 34px;
            right: 20px;
            cursor: pointer;
            z-index: 70;
        }

        .input-label {
            position: absolute;
            font-size: 24px;
            padding: 0 15px;
            margin: 0 20px;
            transition: 0.2s ease;
            background-color: white;
            user-select: none;
        }

        .input-container input:focus + .input-label,
        .input-container input:valid + .input-label {
            height: 30px;
            line-height: 30px;
            z-index: 20;
            transform: translate(-15px, -23px) scale(0.88);
        }

        .invalid + .input-label {
            height: 30px;
            line-height: 30px;
            z-index: 20;
            transform: translate(-15px, -23px) scale(0.88);
            color: #CC4033;
        }

        .input-container input.invalid {
        color: #CC4033;
        border-color: #CC4033;
        }

        .valid + .input-label {
            height: 30px;
            line-height: 30px;
            z-index: 20;
            transform: translate(-15px, -23px) scale(0.88);
            color: #28C98E;
        }

        .input-container input.valid {
            color: #28C98E;
            border-color: #28C98E;
        }

        .bottom-form-content, .remeber-me-container {
            display: flex;
            flex-direction: row;
        }

        .bottom-form-content {
            justify-content: space-between;
        }

        .remeber-me-container {
            gap: 0.5rem;
        }

        .btn-submit, .google-btn {
            height: 50px;
            font-size: clamp(1.125rem, 0.9547rem + 0.6811vw, 1.4996rem);
            line-height: 24px;
            width: 100%;
            border: none;
            border-radius: 10px;
            background-color: #EEE7E7;
            cursor: pointer;
        }

        .btn-submit:hover, .google-btn:hover {
            background-color: #ccc6c6;
        }

        .btns-container {
            display: flex;
            flex-direction: row;
            justify-content: center;
            width: 100%;
            gap: 1.188rem;
        }

        .register-link {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            flex-direction: row;
            gap: 0.313rem;
        }

        .image-container {
            max-width: 50%;
            min-width: 50rem;
            height: 100%;
            background-color: #D9D9D9;
            border-radius: 30px;
        }

        .content-separator {
            display: flex;
            flex-direction: row;
            gap: 1.25rem;
            width: 100%;
            justify-content: center;
        }

        .content-separator::after, .content-separator::before {
            content: '───────────';
        }

        /* RESPONSIVE */
        @media (max-width: 1530px) {
            .main-container {
                justify-content: center;
                padding: 0px;
            }
            
            .data-container {
                margin-top: 0px;   
            }

            .image-container {
                display: none;
            }
        }

        @media (max-width: 1024px) {

            .data-container {
                margin-top: 0px;   
            }

            .image-container {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .data-container {
                min-width: 100%;
                max-width: 100%;
                padding: 15px;
            }

            .image-container {
                display: none;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            .data-container {
                margin-top: 0px;   
            }

            .btns-container {
                flex-direction: column;
                gap: 10px;
            }

            .btn-submit,
            .google-btn {
                width: 100%;
            }

            h1 {
                font-size: 2rem;
                margin-top: 20px;
                margin-bottom: 40px;
            }

            .input-container input,
            .input-label {
                font-size: 18px;
            }

            .input-label {
                transform: none;
            }
        }
  </style>
</head>
<body>
    <div class="main-container">
        <div class="data-container">
            <h6>Eskua</h6>

            <div class="title"><h2>Hola, Bienvenido Devuelta!</h2></div>

            <form id="login-form">
                <div class="input-container">
                    <div id="username-error-message" class="error-message"></div>
                    <input type="text" name="username" id="username" required>
                    <label class="input-label" for="username">Nombre Usuario</label>
                </div>

                <div class="input-container">
                    <div id="password-error-message" class="error-message"></div>
                    <input class="input-password" type="password" name="password" id="user-password" required>
                    <label class="input-label" for="user-password">Contraseña</label>
                    <img src="../images/hide.png" id="eye-icon" onclick="HandleShowingAndHidingPassword()">
                </div>

                <div class="bottom-form-content">
                    <div class="remeber-me-container">
                        <input type="checkbox" name="remeber-me" id="remeber-me-checkbox">
                        <p>Recuerdame</p>
                    </div>
                    <a href="">Olvidaste tu contraseña?</a>
                </div>

                <button type="submit" class="btn-submit">Login</button>
            </form>
        
            <div class="content-separator">
                <p>O Iniciar Sesión con</p>
            </div>

            <div class="btns-container">
                <button class="google-btn" id="google-btn" type="button">Google</button>
            </div>

            <div class="register-link">
                <p>No estas registrado?</p>
                <a href="../register/">Crear cuenta</a>
            </div>
        </div>

        <div class="image-container"></div>
    </div>
</body>
</html>