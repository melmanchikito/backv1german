<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login Administración | Ristorante Italini</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Mulish:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/login.css">
</head>

<body class="login-body">

    <div class="login-container">
        <div class="login-card">

            <h2 class="login-logo">
                Ristorante <span class="verde">Ita</span><span>li</span><span class="rojo">ni</span>
            </h2>

            <p class="login-subtitulo">Acceso Administración</p>

            <?php if (!empty($_SESSION['error'])): ?>
                <div class="login-error">
                    <?= $_SESSION['error'];
                    unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php?controller=auth&action=autenticar">

                <input type="text" name="usuario" placeholder="Usuario" required>
                <input type="password" name="password" placeholder="Contraseña" required>

                <button type="submit" class="btn btn-rojo btn-full">
                    Ingresar
                </button>
            </form>

            <a href="index.php" class="login-volver">← Volver al sitio</a>

        </div>
    </div>

</body>

</html>