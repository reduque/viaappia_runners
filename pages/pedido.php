<h1>Pedido</h1>

<?php
$sql="Select o.id, o.created_at, o.tipo_entrega, o.hora_desde, o.hora_hasta, o.delivery_ref, o.dia_entrega, o.tienda, o.company_id, companies.nombre as compania from orders o left join companies on o.company_id=companies.id where o.id=" . $id;
$pedido=lee1($sql);

if($pedido){
    ?>
    <p>
        <b>Pedido: </b><?php echo str_pad($id , 5, "0", STR_PAD_LEFT) . ' - ' . $pedido['tienda']; ?><br>
        <b>Fecha: </b><?php echo date('d/m/Y H:i:s',strtotime($pedido['created_at'])); ?><br>
        <?php
        if($pedido['compania']) echo '<p class="alerta_fechas naranja">' . $pedido['compania'] . '</p>';
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
        ?>
        <span <?php echo $class_alert; ?>>
            <b>Tipo de entrega: </b><?php echo $pedido['tipo_entrega'] . ' ' . date('d/m/Y',strtotime($pedido['dia_entrega']));
            if($pedido['hora_desde']){
                echo ', ' . $pedido['hora_desde'] . ' - ' . $pedido['hora_hasta'];
            }
            ?>
        </span>
        <?php
        if($pedido['delivery_ref']<>''){
            ?><br><b>Referencia de la empresa de delivery: </b><?php echo $pedido['delivery_ref'];
        }
        if($pedido['tipo_entrega']=='Pick up'){ 
            echo '<br><b>Hora estimado de retiro:</b> ' . $pedido['hora_desde'] . ' - ' . $pedido['hora_hasta'];
        }
        ?>
    </p>
    <br>
    <table class="pedido">
    <?php
    $sql="Select * from order_products where order_id=" . $id;
    $items=leen($sql);
    foreach($items as $item){
        /*
        $sql="select * from products where id=" . $item['product_id'];
        $producto=lee1o($sql);
        */
        $cantidad=($item['cantidad2']) ?: $item['cantidad'];
        ?>
        <tr data-id="<?php echo $item['id']; ?>" data-cantidad="<?php echo $item['cantidad']; ?>" <?php if($cantidad <> $item['cantidad']){echo 'class="cambia"';} ?>>
            <td>
                <b><?php echo $item['alias']; ?></b>
                <ul>
                <?php
                if( $item['variante'] <> ''){
                    echo '<li>' . $item['variante'] . '</li>';
                }
                if( $item['container_id'] <> ''){
                    $sql="select nombre from containers where id=" . $item['container_id'];
                    $contenedor=lee1o($sql);
                    echo '<li>' . 'Peso: ' . $item['peso'] . '</li>';
                    echo '<li>' . 'Raciones: ' . $item['porciones'] . ' (' . $contenedor->nombre . ')' . '</li>';
                }
                $u='';
                $contornos='';
                if( $item['sidedish_alias1'] <> ''){
                    $contornos .= $u . $item['sidedish_alias1'];
                    $u=', ';
                }
                if( $item['sidedish_alias2'] <> ''){
                    $contornos .= $u . $item['sidedish_alias2'];
                    $u=', ';
                }
                if($contornos<>''){
                    echo '<li>' . 'Contornos: ' . $contornos . '</li>';
                }
                if( $item['horneado']){
                    echo '<li>' . 'Horneado' . '</li>';
                }
                if( $item['empacado']){
                    echo '<li>' . 'Empacado al vacio' . '</li>';
                }
                if( $item['cubierto']){
                    echo '<li>' . 'Con cubiertos' . '</li>';
                }
                ?>
                </ul>
            </td>
            <td class="cant">
                <select class="cantidad" style="font-size: 2rem;"><?php
                for($l=0; $l <= $item['cantidad']; $l++){
                    ?><option <?php if($cantidad == $l) echo 'selected'; ?>><?php echo $l ?></option><?php
                }?></select>
            </td>
        </tr>
    <?php } ?>
    </table>
    <p align="center">
        <a href="" class="botones" data-tienda="TOL">Aprobar / Notificar</a>
        <!-- <a href="" class="botones" data-tienda="TOL">Aprobar / Notificar (TOL)</a>
        <a href="" class="botones" data-tienda="TRAILER">Aprobar / Notificar (TRAILER)</a> -->
    </p>
    <?php
}
?>
<script>
    $(document).ready(function(){
        $('.cantidad').change(function(){
            c2=$(this).val();
            el=$(this).parents('tr');
            c1=el.data('cantidad');
            id=el.data('id');
            if(parseInt(c1)==parseInt(c2)){
                el.removeClass('cambia');
            }else{
                el.addClass('cambia');
            }
            $.ajax({
                type: "GET",
                data:{
                    id: id,
                    c2: c2
                },
                url: "cantidad2"
            })
        })
        $('.botones').click(function(e){
            e.preventDefault();
            if($(this).data('tienda') == 'TRAILER'){
                if(confirm('¿Está seguro de enviar este pedido y camniar la tienda?')){
                    document.location="procesar?id=<?php echo $id; ?>&tienda=" + $(this).data('tienda') + "&company_id=<?php echo ($pedido['company_id']) ?:0; ?>";
                }
            }else{
                if(confirm('¿Está seguro de enviar este pedido?')){
                    document.location="procesar?id=<?php echo $id; ?>&tienda=" + $(this).data('tienda')+ "&company_id=<?php echo ($pedido['company_id']) ?:0; ?>";
                }
            }
        })
    })
</script>