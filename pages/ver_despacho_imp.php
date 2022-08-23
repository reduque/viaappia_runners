<html>
    <head>
        <style>                
            table.pedido{
                width: 100%;
                border-collapse: collapse;
            }
            th, td{
                border: 1px solid #e8e8e8;
                padding: 0.5rem 0.8rem;
                vertical-align: top;
            }
            td.cant{
                width: 5rem;
            }
            ul{
                padding-left: 1rem;
            }
            tr.cambia b{
                color: red;
            }
        </style>
    </head>
    <body onload="window.print()">
    <h1>Pedido</h1>

    <?php
    $sql="Select id, delivery_ref, tipo_entrega, created_at from orders where id=" . $id;
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
            ?>
        </p>
        <br>
            <table class="pedido">
            <?php
            $sql="Select * from order_products where order_id=" . $id;
            $items=leen($sql);
            foreach($items as $item){
                $sql="select * from products where id=" . $item['product_id'];
                $producto=lee1o($sql);
                $cantidad=($item['cantidad2']) ?: $item['cantidad'];
                ?>
                <tr data-id="<?php echo $item['id']; ?>" data-cantidad="<?php echo $item['cantidad']; ?>" <?php if($cantidad <> $item['cantidad']){echo 'class="cambia"';} ?>>
                    <td>
                        <b><?php echo $producto->nombre; ?></b>
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
                        if( $item['sidedish1'] <> ''){
                            $sql="select * from sidedishes where id=" . $item['sidedish1'];
                            $acompanante=lee1o($sql);
                            $contornos .= $u . $acompanante->nombre;
                            $u=', ';
                        }
                        if( $item['sidedish2'] <> ''){
                            $sql="select * from sidedishes where id=" . $item['sidedish2'];
                            $acompanante=lee1o($sql);
                            $contornos .= $u . $acompanante->nombre;
                            $u=', ';
                        }
                        if($contornos<>''){
                            echo '<li>' . 'Contornos: ' . $contornos . '</li>';
                        }

                        ?>
                        </ul>
                    </td>
                    <td class="cant" style="text-align: center;"><?php echo $item['cantidad']; ?></td>
                </tr>
            <?php } ?>
            </table>
        </div>
        <?php
    }
    ?>
    </body>
</html>