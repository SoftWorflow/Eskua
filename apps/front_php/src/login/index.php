<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Eskua</title>
    <link rel="stylesheet" href="../../output.css">
    <script src="script.js" defer></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" rel="stylesheet">
    <!-- Google Login -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <style>
        .invalid {
            color: #CC4033;
        }
    </style>
</head>
<body class="flex justify-center items-center w-full h-screen bg-neutral-400 md:p-6">
    
    <div class="flex flex-row justify-center items-center bg-white w-full h-full min-h-[calc(100vh-48px)] md:rounded-3xl p-8 gap-20">
        
        <!-- Contenedor de datos -->
        <div class="max-w-[30rem] flex-1 flex flex-col justify-center text-center gap-6 overflow-hidden">
            <h6 class="text-lg md:text-xl">Eskua</h6>

            <div class="my-4 mb-12">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-normal">Hola, Bienvenido Devuelta!</h2>
            </div>

            <form id="login-form" class="flex flex-col gap-9">
                <!-- Username -->
                <div id="input-container" class="flex items-center relative w-full min-h-[50px] max-h-20 leading-[50px]">
                    <div id="error-message" class="absolute text-red-500 text-sm -translate-y-11 z-50 whitespace-nowrap select-none"></div>
                    <label 
                        for="username" 
                        class="text-xl md:text-2xl px-3.5 mx-5 transition-all duration-200 bg-white select-none pointer-events-none"
                    >
                        Nombre Usuario
                    </label>
                    <input
                        type="text"
                        name="username"
                        id="username"
                        required
                        class="absolute w-full outline-none text-xl md:text-2xl px-3.5 leading-[50px] h-[50px] rounded-xl bg-transparent border-2 border-gray-200 transition-all duration-100 z-10"
                    />
                </div>

                <!-- Password -->
                <div id="input-container" class="flex items-center relative w-full min-h-[50px] max-h-20 leading-[50px]">
                    <div id="error-message" class="absolute text-red-500 text-sm -translate-y-11 z-50 whitespace-nowrap select-none"></div>
                    <input
                        type="password"
                        name="password"
                        id="user-password"
                        required
                        class="absolute w-full outline-none text-xl md:text-2xl px-3.5 pr-16 leading-[50px] h-[50px] rounded-xl bg-transparent border-2 border-gray-200 transition-all duration-100 z-10"
                    />
                    <label 
                        for="user-password" 
                        class="absolute text-xl md:text-2xl px-3.5 mx-5 transition-all duration-200 bg-white select-none pointer-events-none"
                    >
                        Contraseña
                    </label>
                    <img src="../images/hide.png" id="eye-icon" class="absolute right-5 w-8 cursor-pointer z-[70]" onclick="HandleShowingAndHidingPassword()">
                </div>

                <!-- Bottom form content -->
                <div class="flex flex-row justify-between items-center">
                    <div class="flex flex-row gap-2 items-center">
                        <input type="checkbox" name="remember-me" id="remember-me-checkbox" class="w-4 h-4 cursor-pointer">
                        <p class="text-sm md:text-base">Recuerdame</p>
                    </div>
                    <a href="" class="text-blue-900 text-sm md:text-base hover:underline">Olvidaste tu contraseña?</a>
                </div>

                <button 
                    type="submit"
                    id="login-btn"
                    class="h-12 text-lg md:text-xl leading-6 w-full border-none rounded-xl bg-gray-200 cursor-pointer hover:bg-gray-300 transition-colors"
                >
                    Login
                </button>
            </form>

            <div class="flex flex-row gap-5 w-full justify-center items-center">
                <span class="text-gray-400">───────────</span>
                <p class="text-sm md:text-base">O Iniciar Sesión con</p>
                <span class="text-gray-400">───────────</span>
            </div>

            <div class="flex flex-row justify-center w-full gap-5">
                <button 
                    type="button"
                    id="google-btn"
                    class="h-12 text-lg md:text-xl leading-6 w-full border-none rounded-xl bg-gray-200 cursor-pointer hover:bg-gray-300 transition-colors"
                >
                    Google
                </button>
            </div>

            <div class="flex justify-center flex-row items-end gap-1.5">
                <p class="text-sm md:text-base">No estas registrado?</p>
                <a href="../register/" class="text-blue-900 text-sm md:text-base hover:underline">
                    Crear cuenta
                </a>
            </div>
        </div>

        <div class="hidden xl:block w-1/2 min-w-[50rem] h-full bg-gray-300 rounded-3xl"></div>
    </div>

</body>
</html>