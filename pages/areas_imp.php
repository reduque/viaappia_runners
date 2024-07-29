<?php
//42
$salida='';
function ancholinea($p){
    $le=strlen($p);
    if($le <= 42){
        $t=42 - $le;
    }else{
        $t=42 - ($le % 42);
    }

    $l=str_repeat(' ',$t);
    return $p . $l;
    //return str_pad($p , 42, " ", STR_PAD_RIGHT);
}
$sql="Select id, nombre from areas where id=" . $area_id;
$area=lee1($sql);

$sql="Select id from categories where area_id=" . $area_id;
$items=leen($sql);
$categorias=[];
foreach($items as $item){
    $categorias[] = $item['id'];
}



$sql="Select id, delivery_ref, tipo_entrega, user_id, dia_entrega, hora_desde, hora_hasta, tienda, created_at from orders where id=" . $id;
$pedido=lee1($sql);

$sql="Select name from users where id=" . $pedido['user_id'];
$usuario=lee1($sql);


if($pedido){
    $salida.=ancholinea('Área en la cocina: ' . $area['nombre']);
    $salida.=ancholinea('Pedido: ' . str_pad($id , 5, "0", STR_PAD_LEFT) . ' - ' . $pedido['tienda']);
    // $salida.=ancholinea('Fecha: ' . date('d/m/Y H:i:s',strtotime($pedido['created_at'])) );
    $salida.=ancholinea('Tipo de entrega: ' . $pedido['tipo_entrega'] );

    $salida.=ancholinea('Retiro / entrega: ');
    $salida.=ancholinea(date('d/m/Y',strtotime($pedido['dia_entrega'])) . ' entre ' . date('h:i A', strtotime('-1 hour', strtotime('2023/02/02 ' . $pedido['hora_desde']))) .  ' y ' . date('h:i A', strtotime('-1 hour', strtotime('2023/02/02 ' . $pedido['hora_hasta']))));
    $salida.=ancholinea('Cliente: ' . $usuario['name']);

    // if($pedido['delivery_ref']<>''){
    //     $salida.=ancholinea('Referencia de la empresa de delivery: ' .  $pedido['delivery_ref'] );
    // }
    $salida.=ancholinea(' ');
    $sql="Select * from order_products where order_id=" . $id;
    $items=leen($sql);

    foreach($items as $item){
        $sql="select category_id, presentacion from products where id=" . $item['product_id'];
        $producto=lee1o($sql);
        if(in_array($producto->category_id, $categorias)){
            $cantidad=$item['cantidad'];
            $salida.=ancholinea($item['alias']);
            if( $item['variante'] <> ''){
                $salida.=ancholinea($item['variante'] );
            }
            $salida.=ancholinea('Presentacion:' . $producto->presentacion);
            if( $item['container_id'] <> ''){
                $sql="select nombre from containers where id=" . $item['container_id'];
                $contenedor=lee1o($sql);
                $salida.=ancholinea('Peso: ' . $item['peso'] );
                $salida.=ancholinea('Envase: ' . $contenedor->nombre );
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
                $salida.=ancholinea('Contornos: ' . $contornos );
            }
            if( $item['horneado']){
                $salida.=ancholinea('Cocinado' );
            }
            if( $item['empacado']){
                $salida.=ancholinea('Empacado al vacio' );
            }
            if( $item['cubierto']){
                $salida.=ancholinea('Con cubiertos' );
            }
            $salida.=ancholinea('Cantidad: ' . $item['cantidad'] );
            $salida.=ancholinea(' ');
        }
    }
    $salida.=ancholinea(' ');
    $salida.=ancholinea(' ');
}


$params= '["' . $salida . '"]';
$params=[
    "c" => 1,
    "p" => rqq('p'),
    "t" => $salida
];

// echo json_encode($params);
// exit;
include('imp1.php');