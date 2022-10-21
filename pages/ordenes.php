
<?php

$haypedidot=false;
$runner_id=decodifica($_SESSION['api_id']);

$sql="Select count(*) as n from orders where estatus=0";
$r=lee1($sql);
if($r['n']>0){
    $haypedidot=true;
    ?>
    <article class="r">
        <div>
            <div>Sin atender (<?php echo $r['n']; ?>)</div>
            <a href="tomar_pedido">Tomar pedido</a>
        </div>
    </article>
    <?php
}
$sql="Select orders.id, users.name from orders join users on orders.user_id=users.id where estatus=1 and runner_id=" . $runner_id;
$r=leen($sql);
if($r->num_rows>0){
    $haypedidot=true;
    foreach($r as $pedido){
        $id=strval($pedido['id']);
        ?>
        <article class="a">
            <div>
                <div>Pedido en proceso (<?php echo str_pad($id , 5, "0", STR_PAD_LEFT); ?>)<br><b><?php echo $pedido['name']; ?></b></div>
                <a href="pedido?id=<?php echo $id; ?>">Validar existencia</a>
            </div>
        </article>
    <?php
    }
}

$sql="Select orders.id, orders.forma_pago, users.name from orders join users on orders.user_id=users.id where estatus in (2,3,4,5) and runner_id=" . $runner_id;
$r=leen($sql);
if($r->num_rows>0){
    $haypedidot=true;
    foreach($r as $pedido){
        $id=strval($pedido['id']);
        ?>
        <article class="b">
            <div>
                <div>Esperando respuesta del cliente / administrador (<?php echo str_pad($id , 5, "0", STR_PAD_LEFT); ?>)<?php
                if($pedido['forma_pago'] == 'Zelle'){
                    echo '<br><span style="color: red">Pago con Zelle</span>';
                }
                ?><br><b><?php echo $pedido['name']; ?></b></div>
            </div>
        </article>
    <?php
    }
}

$sql="Select orders.id, users.name, delivery_ref from orders join users on orders.user_id=users.id where estatus=6 and runner_id=" . $runner_id;
$r=leen($sql);
if($r->num_rows>0){
    $haypedidot=true;
    foreach($r as $pedido){
        $id=strval($pedido['id']);
        ?>
        <article class="v">
            <div>
                <div>Listo para despacho (<?php echo str_pad($id , 5, "0", STR_PAD_LEFT); ?>
                <?php if($pedido['delivery_ref'] <> '') echo ' - ' . $pedido['delivery_ref']; ?>
                )<br><b><?php echo $pedido['name']; ?></b></div>
                <a href="ver_despacho?id=<?php echo $id; ?>">Preparar pedido</a>
            </div>
        </article>
    <?php
    }
}

$sql="Select orders.id, users.name, delivery_ref from orders join users on orders.user_id=users.id where estatus=7 and runner_id=" . $runner_id;
$r=leen($sql);
if($r->num_rows>0){
    $haypedidot=true;
    foreach($r as $pedido){
        $id=strval($pedido['id']);
        ?>
        <article class="v">
            <div>
                <div>Esperando entrega (<?php echo str_pad($id , 5, "0", STR_PAD_LEFT); ?>
                <?php if($pedido['delivery_ref'] <> '') echo ' - ' . $pedido['delivery_ref']; ?>
                )<br><b><?php echo $pedido['name']; ?></b></div>
                <a href="ver_despacho?id=<?php echo $id; ?>">Entregado por delivery</a>
            </div>
        </article>
    <?php
    }
}

$sql="Select orders.id, b.name as runner_name, users.name from orders join runners b on orders.runner_id=b.id join users on orders.user_id=users.id where (estatus=1 or estatus=6 or estatus=7) and runner_id<>" . decodifica($_SESSION['api_id']);
$r=leen($sql);
if($r->num_rows>0){
    $haypedidot=true;
    foreach($r as $pedido){
        $id=strval($pedido['id']);
        ?>
        <article class="n">
            <div>
                <div>Tomados por otro runner (<?php echo str_pad($id , 5, "0", STR_PAD_LEFT); ?>)<br><?php echo $pedido['runner_name']; ?><br><b><?php echo $pedido['name']; ?></b></div>
                <a href="tomar_yo?id=<?php echo $pedido['id']; ?>">Tomar yo</a>
            </div>
        </article>
    <?php
    }
}
if(!$haypedidot){ ?><p>No hay pedidos pendientes</p><?php }

