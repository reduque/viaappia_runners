<h1>Pedido</h1>

<?php
$sql="Select id, delivery_ref, created_at, tipo_entrega, nombre, ci, telefono, direccion, municipio, ubicacion, forma_pago, monto_efectivo, seriales_billetes, hora_desde, hora_hasta from orders where id=" . $id;
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
            <td class="cant" style="text-align: center;"><?php echo $item['cantidad']; ?></td>
        </tr>
    <?php } ?>
    </table>
    <?php if($pedido['tipo_entrega']=='Delivery'){ ?>
    <p align="center">
        <form method="POST" action="actualizar_delivery_ref">
            <input type="hidden" name="pedido_id" value="<?php echo $pedido['id']; ?>">
            <label><b>Refelencia de la empresa de delivery</b></label>
            <table width="100%">
                <tr>
                    <td><input name="delivery_ref" type="text" maxlength="50" value="<?php echo $pedido['delivery_ref']; ?>" require></td>
                    <td width="1" style="white-space: nowrap;">&nbsp;<button>Actualizar</button></td>
                </tr>
            </table>
        </form>
    </p>
    <?php } ?>
    <p>
        <table width="100%">
            <tr>
                <td><a href="javascript:" class="botones" onclick="printDiv()">Imprimir pedido</a></td>
                <?php if($pedido['tipo_entrega']=='Delivery'){ 
                if($pedido['forma_pago']=='Efectivo' or $pedido['forma_pago']=='Mixto'){
                    $m_efectivo='Monto en efectivo: $' . $pedido['monto_efectivo'] . '
Seriales billetes:
' . $pedido['seriales_billetes'];
                }else{
                    $m_efectivo='No recibe efectivo.';
                }
                ?>
                <td align="right"><a href="https://wa.me/584149067303?text=<?php
echo urlencode('*INFORMACIÓN DE ENTREGA*
Referencia: ' . str_pad($id , 5, "0", STR_PAD_LEFT) . '
Nombre y apellido: ' . $pedido['nombre'] . '
C.I: ' . $pedido['ci'] . '
Número de teléfono: ' . $pedido['telefono'] . '
Dirección de entrega: ' . $pedido['direccion'] . '
Municipio: ' . $pedido['municipio'] . '
' . $m_efectivo . ' 

https://www.google.com/maps/@') . $pedido['ubicacion']; ?>,18z" class="boton_wa" target="_blank">Enviar WhatsApp al delivery</a></td>
                <?php } ?>
            </tr>
        </table>
    </p>
    <p align="center">
        <a href="" class="botones">Marcar como entregado</a>
    </p>

    <?php
}
?>
<script>
    $(document).ready(function(){
        $('.botones').click(function(e){
            e.preventDefault();
            if(confirm('¿Está seguro de despachar este pedido?')){
                document.location="despachar?id=<?php echo $id; ?>";
            }
        })
    })
    function printDiv(){
        $.ajax({
            type: "GET",
            dataType: "html",
            url: "ver_despacho_imp?id=<?php echo $pedido['id']; ?>",
            success: function(data){
                
                var newWin=window.open('','Print-Window');
                newWin.document.open();
                newWin.document.write(data);
                newWin.document.close();
                setTimeout(function(){newWin.close();},10);


            }
        })
    }
</script>
