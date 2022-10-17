<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Vía Appia Runners</title>

    
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="/">
    <link href="css/main.css?v?<?php echo rand() ?>" rel="stylesheet">
    <!-- IE -->
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
    <!-- other browsers -->
    <link rel="icon" type="image/x-icon" href="favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
    <link rel="manifest" href="site.webmanifest">
    <link rel="mask-icon" href="safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    <meta name="MobileOptimized" content="width">
    <meta name="HandheldFriendly" content="true">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="shortcut icon" type="image/png" href="./mstile-150x150.png">
    <link rel="apple-touch-icon" href="./mstile-150x150.png">
    <link rel="apple-touch-startup-image" href="./mstile-150x150.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Antonio:wght@100;300;400;600;700&family=Playfair+Display:wght@400;500&display=swap" rel="stylesheet">

    <script src="js/jquery-1.12.4.min.js"></script> 
</head>

<body>

    <header>
        <img src="img/logo.svg" alt="">
        <?php if($childView <> 'pages/login.php'){ ?>
            <ul>
                <li><a href="/">Lista de pedidos</a></li>
                <li><a href="logout">Salir</a></li>
            </ul>
        <?php } ?>
    </header>
    <main>
        <?php include($childView); ?>
    </main>    
    <div class="alert"><div>Prueba de alerta</div></div>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('./sw.js')
                .then(reg => console.log('Registro de SW exitoso', reg))
                .catch(err => console.warn('Error al tratar de registrar el sw', err))
        }
    </script>
    <!-- firebase -->

    <script type="module">
        import {initializeApp} from "https://www.gstatic.com/firebasejs/9.9.1/firebase-app.js";
        import {getMessaging, getToken, onMessage} from "https://www.gstatic.com/firebasejs/9.9.1/firebase-messaging.js";
        const firebaseConfig = {
            apiKey: "AIzaSyBFtSHXpJv7k_9-Y-PrIm32Z_SjXf0tX2s",
            authDomain: "viaappia-2ff1e.firebaseapp.com",
            projectId: "viaappia-2ff1e",
            storageBucket: "viaappia-2ff1e.appspot.com",
            messagingSenderId: "639035428084",
            appId: "1:639035428084:web:1d964e932f3173d7fa22a1",
            measurementId: "G-E78CLSNBNL"
        };
        const api_token='<?php echo (!empty($_SESSION['api_token'])) ? $_SESSION['api_token'] : '';?>';
        const device_token='<?php echo (!empty($_SESSION['device_token'])) ? $_SESSION['device_token'] : '';?>';

        const app = initializeApp(firebaseConfig);
        const messaging = getMessaging(app);
        getToken(messaging, {vapidKey: "BP_EjaKMvH3nH0GY6V3I9MzgehtQX-gtZEz97bYZEcyCZnnczVfmmiaoWwqLcfKFUpZF0-qjGCbNVZqG22qkcWA"}).then((currentToken) => {
            if (currentToken) {
                // Send the token to your server and update the UI if necessary
                if(api_token != '') if(device_token != currentToken){
                    document.location = '/asignar_device_token?device_token=' + currentToken;
                    //alert(currentToken);
                    //$('body').html(currentToken);
                }
            } else {
                console.log('No registration token available. Request permission to generate one.');
            }
        }).catch((err) => {
            console.log('An error occurred while retrieving token. ', err);
        });
        /*
        onMessage(messaging, (payload) => {
            alert('pp');
            console.log('Message received. ', payload.data.url);
            // ...
        });
        */
        navigator.serviceWorker.addEventListener('message', (event) => {
            const payload=event.data;
            if(payload.hasOwnProperty("url")){
                document.location=payload.url;
            }else{
                window.focus();
                if(confirm(payload.data.mensaje)){
                    document.location = payload.data.url;
                }
            }
        });
    </script>
</body>

</html>
