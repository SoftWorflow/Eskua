<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Eskua</title>
    <link rel="stylesheet" href="../output.css">
    <script src="../auth.js"></script>
</head>
<body>

    <script>
        window.addEventListener('DOMContentLoaded', async () => {
            // Verifys that the user is authenticated
            const isAuthenticated = await requireAuth();
            
            if (isAuthenticated) {
                // Gets the user data
                const user = authManager.getUser();
                console.log("Logged User:", user.display_name);
                console.log("Profiel Picture: ", user.profile_picture_url);

                console.log("Access expires at: ", localStorage.getItem('access_expires_at'));
                
                // Actualizar la UI con los datos del usuario
            }
        });
    </script>

    <?php include 'navbars/navbarNormalUser.html'; ?>
    <?php include 'normal_user_home/index.html'; ?>

</body>
</html>