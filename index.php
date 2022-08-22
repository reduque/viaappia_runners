<?php 
include('conexion.php');

include('Route.php');

Route::add('/login',function(){
    $childView = 'pages/login.php';
    include('pages/layout.php');
},'get');

Route::add('/ingresar',function(){
    $email=rqq('email');
    $password=rqq('password');
    if($q = $GLOBALS['mysqli']->query("select id,password, api_token, device_token from runners where email='" . $email . "'")){
        $usuario=mysqli_fetch_array($q, MYSQLI_ASSOC);
        $q->close();
        if (password_verify($password, $usuario['password'])) {
            $_SESSION['api_id'] = codifica($usuario['id']);
            $_SESSION['api_token'] = $usuario['api_token'];
            $_SESSION['device_token'] = $usuario['device_token'];
            
            $data=[
                'logged' => 1
            ];
            $sql=crea_update('runners', $data, " where api_token = '" . $_SESSION['api_token'] . "'");
            $GLOBALS['mysqli']->query($sql);

            header('Location: /');
            exit;
        }
    }
    $q->close();
    header('Location: login?error=1');
},'post');

Route::add('/asignar_device_token',function(){
    $data=[
        'device_token' => rqq('device_token')
    ];
    $sql=crea_update('runners', $data, " where api_token = '" . $_SESSION['api_token'] . "'");
    $GLOBALS['mysqli']->query($sql);
    $_SESSION['device_token'] = rqq('device_token');
    
    header('Location: /');
},'get','login');

Route::add('/logout',function(){
    $data=[
        'logged' => 0
    ];
    $sql=crea_update('runners', $data, " where api_token = '" . $_SESSION['api_token'] . "'");
    $GLOBALS['mysqli']->query($sql);

    unset($_SESSION['api_id']);
    unset($_SESSION['api_token']);
    unset($_SESSION['device_token']);
    
    header('Location: login');
},'get','login');


Route::add('/',function(){
    $childView = 'pages/home.php';
    include('pages/layout.php');
},'get','login');

Route::add('/ordenes',function(){
    include('pages/ordenes.php');
},'get',);

Route::add('/tomar_pedido',function(){
    $sql="Select id from orders where estatus=0 order by created_at limit 1";
    $r=lee1($sql);
    if($r){
        $runner_id=decodifica($_SESSION['api_id']);
        $data=[
            'runner_id' => $runner_id,
            'fecha_runner' => date('Y-m-d H:i:s'),
            'estatus' => 1
        ];
        $sql=crea_update('orders', $data, " where id = '" . $r['id'] . "'");
        $GLOBALS['mysqli']->query($sql);    

        header('Location: /pedido?id=' . $r['id']);
        exit;
    }
    header('Location: /?estado=0');
},'get','login');

Route::add('/tomar_yo',function(){
    $sql="Select id from orders where (estatus=1 or estatus=4) and id=" . rqq('id');
    $r=lee1($sql);
    if($r){
        $runner_id=decodifica($_SESSION['api_id']);
        $data=[
            'runner_id' => $runner_id,
            'fecha_runner' => date('Y-m-d H:i:s'),
            'estatus' => 1
        ];
        $sql=crea_update('orders', $data, " where id = '" . $r['id'] . "'");
        $GLOBALS['mysqli']->query($sql);    

        header('Location: /pedido?id=' . $r['id']);
        exit;
    }
    header('Location: /?estado=0');
},'get','login');

Route::add('/pedido',function(){
    $id=rqq('id');
    $childView = 'pages/pedido.php';
    include('pages/layout.php');
},'get','login');

Route::add('/cantidad2',function(){
    $data=[
        'cantidad2' => rqq('c2'),
        'cambiado' => 1
    ];
    $sql=crea_update('order_products', $data, " where id = '" . rqq('id') . "'");
    $GLOBALS['mysqli']->query($sql);
},'get');

Route::add('/procesar',function(){
    $id=rqq('id');
    $idc=codifica($id);
    $cambia=false;
    $sql="Select * from order_products where order_id=" . $id;
    $items=leen($sql);
    foreach($items as $item){
        if($item['cambiado']){
            if($item['cantidad'] <> $item['cantidad2']){
                $cambia=true;
            }
        }else{
            $data=[
                'cantidad2' => $item['cantidad']
            ];
            $sql=crea_update('order_products', $data, " where id = '" . $item['id'] . "'");
            $GLOBALS['mysqli']->query($sql);
        }
    }
    $sql="select device_token from orders a inner join users b on a.user_id=b.id where a.id=" . $id;
    $usuario=lee1o($sql);
    if($cambia){
        $data=[
            'estatus' => 2,
            'fecha_confirmacion' => date('Y-m-d H:i:s'),
        ];
        $sql=crea_update('orders', $data, " where id = " . $id);
        $GLOBALS['mysqli']->query($sql);

        if($usuario->device_token){
            enviar_push('/revisar_pedido/' . $idc,[$usuario->device_token],"Pedidos","Revisar el pedido",'Tienes una nueva notificación de su compra, ¿Quieres ir a la compra?');
        }
    }else{
        $data=[
            'estatus' => 3,
            'fecha_confirmacion' => date('Y-m-d H:i:s'),
        ];
        $sql=crea_update('orders', $data, " where id = " . $id);
        $GLOBALS['mysqli']->query($sql);
        if($usuario->device_token){
            enviar_push('/checkout/' . $idc,[$usuario->device_token],"Pedidos","Revisar el pedido",'Tienes una nueva notificación de su compra, ¿Quieres ir a la compra?');
        }
        /*
        echo '/checkout/' . $idc;
        exit;
        */
    }
    
    header('Location: /');
    exit;

},'get','login');

Route::add('/ver_despacho',function(){
    $id=rqq('id');
    $childView = 'pages/ver_despacho.php';
    include('pages/layout.php');
},'get','login');

Route::add('/despachar',function(){
    $id=rqq('id');
    $idc=codifica($id);
    
    $sql="select device_token from orders a inner join users b on a.user_id=b.id where a.id=" . $id;
    $usuario=lee1o($sql);

    $data=[
        'estatus' => 5,
        'fecha_despacho' => date('Y-m-d H:i:s'),
    ];
    $sql=crea_update('orders', $data, " where id = " . $id);
    $GLOBALS['mysqli']->query($sql);

    if($usuario->device_token){
        enviar_push('/pedido/' . $idc,[$usuario->device_token],"Pedidos","Revisar el pedido",'Tienes una nueva notificación de su compra, ¿Quieres ir a la compra?');
    }
    header('Location: /');
},'get','login');







Route::run('/');
