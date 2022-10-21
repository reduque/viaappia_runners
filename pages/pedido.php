<h1>Pedido</h1>

<?php
$sql="Select id, created_at, tipo_entrega, hora_desde, hora_hasta, delivery_ref from orders where id=" . $id;
$pedido=lee1($sql);

if($pedido){
    ?>
    <p>
        <b>Pedido: </b><?php echo str_pad($id , 5, "0", STR_PAD_LEFT); ?><br>
        <b>Fecha: </b><?php echo date('d/m/Y H:i:s',strtotime($pedido['created_at'])); ?><br>
        <b>Tipo de entrega: </b><?php echo $pedido['tipo_entrega']; ?>
        <?php
        if($pedido['delivery_ref']<>''){
            ?><br><b>Refelencia de la empresa de delivery: </b><?php echo $pedido['delivery_ref'];
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
        <a href="" class="botones">Aprobar / Notificar</a>
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
            if(confirm('¿Está seguro de enviar este pedido?')){
                document.location="procesar?id=<?php echo $id; ?>";
            }
        })
    })
</script>