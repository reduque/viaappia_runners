<h1>Vencidos</h1>
<section class="fichas ordenes">
<?php   
$sql="Select 
    orders.id, orders.tienda, users.name, users.telefonos, tipo_entrega, hora_desde, hora_hasta, es_thanksgiving, dia_entrega, delivery_ref, companies.nombre as compania 
    from (orders join users on orders.user_id=users.id) left join companies on orders.company_id=companies.id 
    where estatus>=21 and orders.created_at > date_sub(now(), interval 24 hour)
    order by orders.created_at desc";
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
                    if($pedido['compania']) echo '<br><b>' . $pedido['compania'] . '</b>';
                    ?></div>
                <a href="reactivar_compra?id=<?php echo $id; ?>">Reactivar compra</a>
            </div>
        </article>
    <?php
    }
} ?>
</section>
