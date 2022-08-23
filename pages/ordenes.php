
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
$sql="Select id from orders where estatus=1 and runner_id=" . $runner_id;
$r=leen($sql);
if($r->num_rows>0){
    $haypedidot=true;
    foreach($r as $pedido){
        $id=strval($pedido['id']);
        ?>
        <article class="a">
            <div>
                <div>Pedido en proceso (<?php echo str_pad($id , 5, "0", STR_PAD_LEFT); ?>)</div>
                <a href="pedido?id=<?php echo $id; ?>">Validar existencia</a>
            </div>
        </article>
    <?php
    }
}

$sql="Select id from orders where estatus=2 and runner_id=" . $runner_id;
$r=leen($sql);
if($r->num_rows>0){
    $haypedidot=true;
    foreach($r as $pedido){
        $id=strval($pedido['id']);
        ?>
        <article class="b">
            <div>
                <div>Esperando revisión del cliente (<?php echo str_pad($id , 5, "0", STR_PAD_LEFT); ?>)</div>
            </div>
        </article>
    <?php
    }
}
$sql="Select id from orders where estatus=3 and runner_id=" . $runner_id;
$r=leen($sql);
if($r->num_rows>0){
    $haypedidot=true;
    foreach($r as $pedido){
        $id=strval($pedido['id']);
        ?>
        <article class="b">
            <div>
                <div>Esperando pago del cliente (<?php echo str_pad($id , 5, "0", STR_PAD_LEFT); ?>)</div>
            </div>
        </article>
    <?php
    }
}

$sql="Select id from orders where estatus=4 and runner_id=" . $runner_id;
$r=leen($sql);
if($r->num_rows>0){
    $haypedidot=true;
    foreach($r as $pedido){
        $id=strval($pedido['id']);
        ?>
        <article class="v">
            <div>
                <div>Listo para despacho (<?php echo str_pad($id , 5, "0", STR_PAD_LEFT); ?>)</div>
                <a href="ver_despacho?id=<?php echo $id; ?>">Preparar pedido</a>
            </div>
        </article>
    <?php
    }
}

$sql="Select a.id, b.name from orders a inner join runners b on a.runner_id=b.id where (estatus=1 or estatus=4) and runner_id<>" . decodifica($_SESSION['api_id']);
$r=leen($sql);
if($r->num_rows>0){
    $haypedidot=true;
    foreach($r as $pedido){
        $id=strval($pedido['id']);
        ?>
        <article class="n">
            <div>
                <div>Tomados por otro runner (<?php echo str_pad($id , 5, "0", STR_PAD_LEFT); ?>)<br><?php echo $pedido['name']; ?></div>
                <a href="tomar_yo?id=<?php echo $pedido['id']; ?>">Tomar yo</a>
            </div>
        </article>
    <?php
    }
}
if(!$haypedidot){ ?><p>No hay pedidos pendientes</p><?php }

