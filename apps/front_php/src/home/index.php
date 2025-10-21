<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Eskua</title>
    <link rel="stylesheet" href="../output.css">
    <script src="../auth.js"></script>
    <script src="../navbar-loader.js"></script>
    <script src="content-loader.js"></script>
    <script>
        document.documentElement.style.display = 'none';

        (async () => {
            // Allow access even if not logged in
            await authManager.ensureValidToken();
            
            if (authManager.getAccessToken()) {
                authManager.startTokenMonitoring();
            }
            
            await initializeNavbar('#navbar-container');
            await initializeContent('#content-container');
            
            document.documentElement.style.display = '';
        })();
    </script>
</head>
<body>

    <div id="navbar-container"></div>

    <div id="content-container"></div>

</body>
</html>