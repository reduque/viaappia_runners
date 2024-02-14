
<?php
$sonaralarma=false;
$haypedidot=false;
$runner_id=decodifica($_COOKIE['api_id']);

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
$sql="Select orders.id, orders.tienda, users.name, users.telefonos, tipo_entrega, hora_desde, hora_hasta, es_thanksgiving, dia_entrega from orders join users on orders.user_id=users.id where estatus=1 and runner_id=" . $runner_id;
$r=leen($sql);
if($r->num_rows>0){
    $haypedidot=true;
    //ordenar
    $pedidos=[];
    foreach($r as $pedido){
        $temp=$pedido;
        $temp['freal'] = strtotime($pedido['dia_entrega'] . ' ' . $pedido['hora_desde']);
        $pedidos[]=$temp;
    }
    for($l=0; $l<count($pedidos) - 1; $l++){
        for($i=$l+1; $i<count($pedidos); $i++){
            if($pedidos[$l]['freal'] > $pedidos[$i]['freal']){
                $temp = $pedidos[$l];
                $pedidos[$l] = $pedidos[$i];
                $pedidos[$i]=$temp;
            }
        }
    }
    //fin ordenar    
    foreach($pedidos as $pedido){
        $id=strval($pedido['id']);
        ?>
        <article class="a">
            <div>
                <div>Pedido en proceso (<?php echo str_pad($id , 5, "0", STR_PAD_LEFT) . ' - ' . $pedido['tienda']; ?>) <br><?php echo $pedido['tipo_entrega'] . ' ' . ((!$pedido['es_thanksgiving']) ? date('d/m/Y',strtotime($pedido['dia_entrega'])) : ''); if($pedido['tipo_entrega']=='Pick up') { echo '<br>' . (($pedido['es_thanksgiving']) ? '24/11/2022<br>' : '') .  $pedido['hora_desde'] . ' - ' . $pedido['hora_hasta'];} ?><br><b><?php echo $pedido['name']; ?></b><br><?php echo $pedido['telefonos']; ?></div>
                <a href="pedido?id=<?php echo $id; ?>">Validar existencia</a>
            </div>
        </article>
    <?php
    }
}

$sql="Select orders.id, orders.tienda, estatus, fecha_confirmacion, orders.forma_pago, users.name, users.telefonos, tipo_entrega, hora_desde, hora_hasta, es_thanksgiving, dia_entrega from orders join users on orders.user_id=users.id where estatus in (2,3,4,5) and runner_id=" . $runner_id;
$r=leen($sql);
if($r->num_rows>0){
    $haypedidot=true;
    //ordenar
    $pedidos=[];
    foreach($r as $pedido){
        $temp=$pedido;
        $temp['freal'] = strtotime($pedido['dia_entrega'] . ' ' . $pedido['hora_desde']);
        $pedidos[]=$temp;
    }
    for($l=0; $l<count($pedidos) - 1; $l++){
        for($i=$l+1; $i<count($pedidos); $i++){
            if($pedidos[$l]['freal'] > $pedidos[$i]['freal']){
                $temp = $pedidos[$l];
                $pedidos[$l] = $pedidos[$i];
                $pedidos[$i]=$temp;
            }
        }
    }
    //fin ordenar
    foreach($pedidos as $pedido){
        $id=strval($pedido['id']);
        ?>
        <article class="b">
            <div>
                <div>Esperando respuesta del cliente / administrador (<?php echo str_pad($id , 5, "0", STR_PAD_LEFT) . ' - ' . $pedido['tienda']; ?>) <?php
                if($pedido['forma_pago'] == 'Zelle'){
                    echo '<br><span style="color: red">Pago con Zelle</span>';
                }
                ?><br><?php echo $pedido['tipo_entrega'] . ' ' . ((!$pedido['es_thanksgiving']) ? date('d/m/Y',strtotime($pedido['dia_entrega'])) : ''); if($pedido['tipo_entrega']=='Pick up') { echo '<br>' . (($pedido['es_thanksgiving']) ? '24/11/2022<br>' : '') .  $pedido['hora_desde'] . ' - ' . $pedido['hora_hasta'];} ?><br><b><?php echo $pedido['name']; ?></b><br><?php echo $pedido['telefonos']; ?></div>
                <?php
                if($pedido['estatus'] == 3){
                    $to_time = time();
                    $from_time = strtotime($pedido['fecha_confirmacion']);
                    $difencia = ($to_time - $from_time) / 60;
                    $clase='';
                    // echo $difencia;
                    if($difencia <= 12){
                        $clase=' class="verde"';
                    }else if($difencia <= 14){
                        $clase=' class="amarillo"';
                        $sonaralarma=true;
                    }else{
                        $sonaralarma=true;
                    }
                    $difencia = round(100 * $difencia / 15);
                    if($difencia>100) $difencia=100;
                    ?><div class="b_progreso"><div<?php echo $clase;?> style="width: <?php echo $difencia; ?>%;"></div></div><?php
                } ?>
            </div>
        </article>
    <?php
    }
    if($sonaralarma) echo "<script>audio.play();</script>";

}

$sql="Select orders.id, orders.tienda, users.name, users.telefonos, tipo_entrega, hora_desde, hora_hasta, es_thanksgiving, dia_entrega, delivery_ref from orders join users on orders.user_id=users.id where estatus=6 and runner_id=" . $runner_id;
$r=leen($sql);
if($r->num_rows>0){
    $haypedidot=true;
    //ordenar
    $pedidos=[];
    foreach($r as $pedido){
        $temp=$pedido;
        $temp['freal'] = strtotime($pedido['dia_entrega'] . ' ' . $pedido['hora_desde']);
        $pedidos[]=$temp;
    }
    for($l=0; $l<count($pedidos) - 1; $l++){
        for($i=$l+1; $i<count($pedidos); $i++){
            if($pedidos[$l]['freal'] > $pedidos[$i]['freal']){
                $temp = $pedidos[$l];
                $pedidos[$l] = $pedidos[$i];
                $pedidos[$i]=$temp;
            }
        }
    }
    //fin ordenar
    foreach($pedidos as $pedido){
        $id=strval($pedido['id']);
        ?>
        <article class="v">
            <div>
                <div>Listo para despacho (<?php echo str_pad($id , 5, "0", STR_PAD_LEFT) . ' - ' . $pedido['tienda']; ?>
                <?php if($pedido['delivery_ref'] <> '') echo ' - ' . $pedido['delivery_ref']; ?>
                )<br><?php 
                    /*
                    echo $pedido['tipo_entrega'] . ' ' . ((!$pedido['es_thanksgiving']) ? date('d/m/Y',strtotime($pedido['dia_entrega'])) : ''); if($pedido['tipo_entrega']=='Pick up') { echo '<br>' . (($pedido['es_thanksgiving']) ? '24/11/2022<br>' : '') .  $pedido['hora_desde'] . ' - ' . $pedido['hora_hasta'];} ?><br><b><?php echo $pedido['name']; ?></b><br><?php echo $pedido['telefonos'];
                    */
                    $class_alert = '';
                    if(strtotime(date('Y-m-d',strtotime($pedido['dia_entrega']))) > strtotime(date('Y-m-d'))){
                        $class_alert = 'class="alerta_fechas rojo"';
                    }else{
                        if($pedido['hora_desde']) if(date('Y-m-d',strtotime($pedido['dia_entrega'])) == date('Y-m-d')){
                            $hour = date('H');
                            $minute = (date('i')>30)?'30':'00';
                            $hora=strtotime($hour . ':' . $minute . ':00');
                            $hora=strtotime("+30 MINUTE",$hora);
                            $hora=date('h:i A',$hora);
                            if(array_search($pedido['hora_desde'], horas_array()) - 4 >= array_search($hora, horas_array())){
                                $class_alert = 'class="alerta_fechas naranja"';
                            }
                        }
                    }

                    echo '<span '. $class_alert . '>';
                    echo $pedido['tipo_entrega'] . ' ' . date('d/m/Y',strtotime($pedido['dia_entrega']));
                    if($pedido['hora_desde']) {
                        echo '<br>' . $pedido['hora_desde'] . ' - ' . $pedido['hora_hasta'];
                    }
                    echo '</span>';
                    ?><br><b><?php echo $pedido['name']; 
                    ?></b><br><?php echo $pedido['telefonos'];
                ?></div>
                <a href="ver_despacho?id=<?php echo $id; ?>">Preparar pedido</a>
            </div>
        </article>
    <?php
    }
}

$sql="Select orders.id, orders.tienda, users.name, users.telefonos, tipo_entrega, hora_desde, hora_hasta, es_thanksgiving, dia_entrega, delivery_ref from orders join users on orders.user_id=users.id where estatus=7 and runner_id=" . $runner_id;
$r=leen($sql);
if($r->num_rows>0){
    $haypedidot=true;
    //ordenar
    $pedidos=[];
    foreach($r as $pedido){
        $temp=$pedido;
        $temp['freal'] = strtotime($pedido['dia_entrega'] . ' ' . $pedido['hora_desde']);
        $pedidos[]=$temp;
    }
    for($l=0; $l<count($pedidos) - 1; $l++){
        for($i=$l+1; $i<count($pedidos); $i++){
            if($pedidos[$l]['freal'] > $pedidos[$i]['freal']){
                $temp = $pedidos[$l];
                $pedidos[$l] = $pedidos[$i];
                $pedidos[$i]=$temp;
            }
        }
    }
    //fin ordenar
    foreach($pedidos as $pedido){
        $id=strval($pedido['id']);
        ?>
        <article class="v">
            <div>
                <div>Esperando entrega (<?php echo str_pad($id , 5, "0", STR_PAD_LEFT) . ' - ' . $pedido['tienda']; ?>
                <?php if($pedido['delivery_ref'] <> '') echo ' - ' . $pedido['delivery_ref']; ?>
                )<br><?php echo $pedido['tipo_entrega'] . ' ' . ((!$pedido['es_thanksgiving']) ? date('d/m/Y',strtotime($pedido['dia_entrega'])) : ''); if($pedido['tipo_entrega']=='Pick up') { echo '<br>' . (($pedido['es_thanksgiving']) ? '24/11/2022<br>' : '') .  $pedido['hora_desde'] . ' - ' . $pedido['hora_hasta'];} ?><br><b><?php echo $pedido['name']; ?></b><br><?php echo $pedido['telefonos']; ?></div>
                <a href="ver_despacho?id=<?php echo $id; ?>">Entregado por delivery</a>
            </div>
        </article>
    <?php
    }
}

$sql="Select orders.id, orders.tienda, b.name as runner_name, users.name, users.telefonos, tipo_entrega, hora_desde, hora_hasta, es_thanksgiving, dia_entrega from orders join runners b on orders.runner_id=b.id join users on orders.user_id=users.id where (estatus=1 or estatus=6 or estatus=7) and runner_id<>" . decodifica($_COOKIE['api_id']);
$r=leen($sql);
if($r->num_rows>0){
    $haypedidot=true;
    //ordenar
    $pedidos=[];
    foreach($r as $pedido){
        $temp=$pedido;
        $temp['freal'] = strtotime($pedido['dia_entrega'] . ' ' . $pedido['hora_desde']);
        $pedidos[]=$temp;
    }
    for($l=0; $l<count($pedidos) - 1; $l++){
        for($i=$l+1; $i<count($pedidos); $i++){
            if($pedidos[$l]['freal'] > $pedidos[$i]['freal']){
                $temp = $pedidos[$l];
                $pedidos[$l] = $pedidos[$i];
                $pedidos[$i]=$temp;
            }
        }
    }
    //fin ordenar    
    foreach($pedidos as $pedido){
        $id=strval($pedido['id']);
        ?>
        <article class="n">
            <div>
                <div>Tomados por otro runner (<?php echo str_pad($id , 5, "0", STR_PAD_LEFT) . ' - ' . $pedido['tienda']; ?>) <br><?php echo $pedido['runner_name']; ?><br>
                <?php 
                
                $class_alert = '';
                if(strtotime(date('Y-m-d',strtotime($pedido['dia_entrega']))) > strtotime(date('Y-m-d'))){
                    $class_alert = 'class="alerta_fechas rojo"';
                }else{
                    if($pedido['hora_desde']) if(date('Y-m-d',strtotime($pedido['dia_entrega'])) == date('Y-m-d')){
                        $hour = date('H');
                        $minute = (date('i')>30)?'30':'00';
                        $hora=strtotime($hour . ':' . $minute . ':00');
                        $hora=strtotime("+30 MINUTE",$hora);
                        $hora=date('h:i A',$hora);
                        if(array_search($pedido['hora_desde'], horas_array()) - 4 >= array_search($hora, horas_array())){
                            $class_alert = 'class="alerta_fechas naranja"';
                        }
                    }
                }
                echo '<span '. $class_alert . '>';
                echo $pedido['tipo_entrega'] . ' ' . date('d/m/Y',strtotime($pedido['dia_entrega']));
                if($pedido['hora_desde']) {
                    echo '<br>' . $pedido['hora_desde'] . ' - ' . $pedido['hora_hasta'];
                }
                echo '</span>';
                
                
                ?>
                
                <br><b><?php echo $pedido['name']; ?></b><br><?php echo $pedido['telefonos']; ?></div>
                <a href="tomar_yo?id=<?php echo $pedido['id']; ?>">Tomar yo</a>
            </div>
        </article>
    <?php
    }
}

if(!$haypedidot){ ?><p>No hay pedidos pendientes</p><?php }
