<h1>Pedido</h1>

<?php
$sql="Select id, user_id, estatus, delivery_sku, delivery_ref, created_at, tipo_entrega, dia_entrega, nombre, ci, telefono, direccion, municipio, ubicacion, forma_pago, monto_efectivo, seriales_billetes, hora_desde, hora_hasta, facturado, mesa from orders where id=" . $id;
$pedido=lee1($sql);

if($pedido){
    ?>
    <p>
        <b>Pedido: </b><?php echo str_pad($id , 5, "0", STR_PAD_LEFT); ?><br>
        <b>Fecha: </b><?php echo date('d/m/Y H:i:s',strtotime($pedido['created_at'])); ?><br>
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
        $es_carro = ($pedido['delivery_sku'] == '4246') ? ' Carro' : '';
        ?>
        <span <?php echo $class_alert; ?>>
            <b>Tipo de entrega: </b><?php echo $pedido['tipo_entrega'] . $es_carro . ' ' . date('d/m/Y',strtotime($pedido['dia_entrega']));
            if($pedido['hora_desde']){
                echo ', ' . $pedido['hora_desde'] . ' - ' . $pedido['hora_hasta'];
            }
            ?>
        </span>
        <?php
        if($pedido['delivery_ref']<>''){
            ?><br><b>Referencia de la empresa de delivery: </b><?php echo $pedido['delivery_ref'];
        }
        /*
        if($pedido['tipo_entrega']=='Pick up'){ 
            echo '<br><b>Hora estimado de retiro:</b> ' . $pedido['hora_desde'] . ' - ' . $pedido['hora_hasta'];
        }
        */
        $sql="select name, telefonos from users where id=" . $pedido['user_id'];
        $usuario=lee1o($sql);
        echo '<br><b>Cliente: </b>' . $usuario->name . ' ' . $usuario->telefonos;
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
            <label><b>Referencia de la empresa de delivery</b></label>
            <table width="100%">
                <tr>
                    <td><input name="delivery_ref" type="text" maxlength="50" value="<?php echo $pedido['delivery_ref']; ?>" require></td>
                    <td width="1" style="white-space: nowrap;">&nbsp;<button>Actualizar</button></td>
                </tr>
            </table>
        </form>
    </p>
    <?php }
    if($pedido['estatus'] == 6){ ?>
    <p>
        <table width="100%">
            <tr>
                <td colspan="3">
                    Impresora a usar: &nbsp;
                    <label style="display: inline-block;"><input class="impresora imp28" name="impresora" type="radio" value="28">1</label> 
                    <label style="display: inline-block;"><input class="impresora imp29" name="impresora" type="radio" value="29">2</label>
                </td>
            </tr>
            <tr>
                <td><a href="javascript:" class="botones" onclick="printDiv()">Imprimir pedido</a></td>
                <td><a href="javascript:" class="botones" onclick="printDiv2()">Imprimir pedido cliente</a></td>
                <td>
                <?php if( $pedido['forma_pago']=='PagoMovil' ){ ?>
                    <b>Forma de pago: </b><?php echo $pedido['forma_pago']; ?>
                    <?php if($pedido['facturado']==1){ ?>
                        <br>Mesa: <?php echo $pedido['mesa']; ?>
                    <?php }else{
                        ?>&nbsp; &nbsp;<a href="https://www.viaappia.com.ve/orden_facturar/<?php echo ($idc); ?>?origen=runners" class="botones facturar">Facturar</a><?php
                    }
                }?>
                </td>
                <?php if($pedido['tipo_entrega']=='Delivery'){ 
                if($pedido['forma_pago']=='Efectivo' or $pedido['forma_pago']=='Mixto'){
                    $m_efectivo='Monto en efectivo: $' . $pedido['monto_efectivo'] . '
Seriales billetes:
' . $pedido['seriales_billetes'];
                }else{
                    $m_efectivo='No recibe efectivo.';
                }
                ?>
                <td align="right"><a href="https://wa.me/584242973067?text=<?php
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
        <a href="" class="botones procesarpedido">Marcar como <?php echo ($pedido['tipo_entrega']=='Delivery') ? 'enviado' : 'entregado' ;   ?></a>
    </p>
    <?php }else{ ?>
    <p align="center">
        <a href="" class="botones procesarpedido2">Marcar como entregado</a>
    </p>
    <?php }
}
?>
<script>
    $(document).ready(function(){
        if (typeof localStorage.impresora === 'undefined') {
            localStorage.impresora=28;
        }
        $('.imp' + localStorage.impresora).prop("checked",true);
        $('.impresora').click(function(){
            localStorage.impresora=($(this).val());
        })
        //alert(localStorage.impresora);
        $('.procesarpedido').click(function(e){
            e.preventDefault();
            if(confirm('¿Está seguro de despachar este pedido?')){
                document.location="despachar?id=<?php echo $id; ?>";
            }
        })
        $('.procesarpedido2').click(function(e){
            e.preventDefault();
            if(confirm('¿Está seguro de marcar como entregado este pedido?')){
                document.location="entregar?id=<?php echo $id; ?>";
            }
        })
        $('.facturar').click(function(event){
            event.preventDefault();
            url=$(this).attr("href");
            if(confirm("¿Está seguro de facturar este pedido?")){
                $.ajax({
                    type: "GET",
                    url: url,
                    success: function(){
                        alert('Pedido facturado');
                        window.location.reload();
                    }
                })
            }
        })
    })
    function printDiv(){
        $.ajax({
            type: "GET",
            url: "ver_despacho_imp?id=<?php echo $pedido['id']; ?>&p=" + localStorage.impresora,
            success: function(data){
                /*        
                $.each(data, function(i,linea) {
                    var url = "https://runners.viaappia.com.ve/imp1.php?p=" + localStorage.impresora + "&t=" + linea;
                    
                    var xhr = new XMLHttpRequest();
                    xhr.open("GET", url);
                    xhr.onreadystatechange = function () {
                    };
                    xhr.send();
                    delete xhr; 
                });
                */
                alert('Pedido enviado');
                /*
                var newWin=window.open('','Print-Window');
                newWin.document.open();
                newWin.document.write(data);
                newWin.document.close();
                setTimeout(function(){newWin.close();},10);
                */

            }
        })
    }
    function printDiv2(){
        $.ajax({
            type: "GET",
            url: "ver_despacho_imp2?id=<?php echo $pedido['id']; ?>&p=" + localStorage.impresora,
            success: function(data){
                /*      
                $.each(data, function(i,linea) {
                    var url = "https://runners.viaappia.com.ve/imp1.php?p=28&t=" + linea;
                    
                    var xhr = new XMLHttpRequest();
                    xhr.open("GET", url);
                    xhr.onreadystatechange = function () {
                    };
                    xhr.send();
                    delete xhr; 
                });
                */

                alert('Pedido enviado');
                /*
                var newWin=window.open('','Print-Window');
                newWin.document.open();
                newWin.document.write(data);
                newWin.document.close();
                setTimeout(function(){newWin.close();},10);
                */

            }
        })
    }
</script>
