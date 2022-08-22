<section class="login">
    <div class="formulario">
        <h2>Ingreso</h2>
        <form action="ingresar" method="POST">
            <?php
                if(rqq('error')<>''){
                    ?><p class="error">Usuario o clave incorrctos</p><?php
                }
            ?>
            <p>Email</p>
            <input type="email" name="email" required>
            <p>Clave</p>
            <input type="password" name="password" required>
            <p align="center">
                <button>Ingresar</button>
            </p>
        </form>
    </div>
</section>