<?php 
include('conexion.php');

include('Route.php');


function horas_array(){
	return ['07:00 AM','07:30 AM','08:00 AM','08:30 AM','09:00 AM','09:30 AM','10:00 AM','10:30 AM','11:00 AM','11:30 AM','12:00 PM','12:30 PM','01:00 PM','01:30 PM','02:00 PM','02:30 PM','03:00 PM','03:30 PM','04:00 PM','04:30 PM','05:00 PM','05:30 PM','06:00 PM','06:30 PM','07:00 PM','07:30 PM','08:00 PM','08:30 PM','09:00 PM','09:30 PM'];
}

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
            // $_SESSION['api_id'] = codifica($usuario['id']);
            // $_SESSION['api_token'] = $usuario['api_token'];
            // $_SESSION['device_token'] = $usuario['device_token'];
            
            setcookie('api_id',codifica($usuario['id']));
            setcookie('api_token', $usuario['api_token']);
            setcookie('device_token', $usuario['device_token']);

            $data=[
                'logged' => 1
            ];
            $sql=crea_update('runners', $data, " where api_token = '" . $_COOKIE['api_token'] . "'");
            $GLOBALS['mysqli']->query($sql);

            header('Location: /');
            exit;
        }
    }
    //$q->close();
    header('Location: login?error=1');
    exit;
},'post');

Route::add('/asignar_device_token',function(){
    $data=[
        'device_token' => rqq('device_token')
    ];
    $sql=crea_update('runners', $data, " where api_token = '" . $_COOKIE['api_token'] . "'");
    $GLOBALS['mysqli']->query($sql);
    // $_SESSION['device_token'] = rqq('device_token');
    setcookie('device_token', rqq('device_token'));
    
    header('Location: /');
},'get','login');

Route::add('/logout',function(){
    $data=[
        'logged' => 0
    ];
    $sql=crea_update('runners', $data, " where api_token = '" . $_COOKIE['api_token'] . "'");
    $GLOBALS['mysqli']->query($sql);

    unset($_COOKIE['api_id']);
    unset($_COOKIE['api_token']);
    unset($_COOKIE['device_token']);
    
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
    $sql="Select id, user_id from orders where estatus=0 order by created_at limit 1";
    $r=lee1($sql);
    if($r){
        $runner_id=decodifica($_COOKIE['api_id']);
        $data=[
            'runner_id' => $runner_id,
            'fecha_runner' => date('Y-m-d H:i:s'),
            'estatus' => 1
        ];
        $sql=crea_update('orders', $data, " where id = '" . $r['id'] . "'");
        $GLOBALS['mysqli']->query($sql);

        $sql=crea_update('users', ['order_estatus' => 1, 'order_last_update' => date('Y/m/d  H:i:s')], " where id = '" . $r['user_id'] . "'");
        $GLOBALS['mysqli']->query($sql);

        header('Location: /pedido?id=' . $r['id']);
        exit;
    }
    header('Location: /?estado=0');
},'get','login');

Route::add('/tomar_yo',function(){
    $sql="Select id, user_id from orders where (estatus=1 or estatus=6 or estatus=7) and id=" . rqq('id');
    $r=lee1($sql);
    if($r){
        $runner_id=decodifica($_COOKIE['api_id']);
        $data=[
            'runner_id' => $runner_id,
            'fecha_runner' => date('Y-m-d H:i:s'),
            //'estatus' => 1
        ];
        $sql=crea_update('orders', $data, " where id = '" . $r['id'] . "'");
        $GLOBALS['mysqli']->query($sql);    
        /*
        $sql=crea_update('users', ['order_estatus' => 1, 'order_last_update' => date('Y/m/d  H:i:s')], " where id = '" . $r['user_id'] . "'");
        $GLOBALS['mysqli']->query($sql);
        */
        header('Location: /');
        //header('Location: /pedido?id=' . $r['id']);
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
    $sql="select device_token, a.user_id from orders a inner join users b on a.user_id=b.id where a.id=" . $id;
    $usuario=lee1o($sql);
    if($cambia){
        $data=[
            'estatus' => 2,
            'tienda' => rqq('tienda'),
            'fecha_confirmacion' => date('Y-m-d H:i:s'),
        ];
        $sql=crea_update('orders', $data, " where id = " . $id);
        $GLOBALS['mysqli']->query($sql);

        $sql=crea_update('users', ['order_estatus' => 2, 'order_last_update' => date('Y/m/d  H:i:s')], " where id = '" . $usuario->user_id . "'");
        $GLOBALS['mysqli']->query($sql);

        if($usuario->device_token){
            enviar_push('/revisar_pedido/' . $idc,[$usuario->device_token],"Pedidos","Revisar el pedido",'Tienes una nueva notificación de su compra, ¿Quieres ir a la compra?');
        }
    }else{
        $data=[
            'estatus' => 3,
            'tienda' => rqq('tienda'),
            'fecha_confirmacion' => date('Y-m-d H:i:s'),
        ];
        $sql=crea_update('orders', $data, " where id = " . $id);
        $GLOBALS['mysqli']->query($sql);
        
        $sql=crea_update('users', ['order_estatus' => 3, 'order_last_update' => date('Y/m/d  H:i:s')], " where id = '" . $usuario->user_id . "'");
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
    $idc=codifica($id);
    $childView = 'pages/ver_despacho.php';
    include('pages/layout.php');
},'get','login');

Route::add('/actualizar_delivery_ref',function(){
    $pedido_id=rqq('pedido_id');
    $data=[
        'delivery_ref' => rqq(('delivery_ref'))
    ];
    $sql=crea_update('orders', $data, " where id = '" . $pedido_id . "'");
    $GLOBALS['mysqli']->query($sql);

    header('Location: /ver_despacho?id=' . $pedido_id);
    exit;
},'post');

Route::add('/ver_despacho_imp',function(){
    $id=rqq('id');
    include('pages/ver_despacho_imp.php');
},'get');
Route::add('/ver_despacho_imp2',function(){
    $id=rqq('id');
    include('pages/ver_despacho_imp2.php');
},'get');
Route::add('/areas_imp',function(){
    $id=rqq('id');
    $area_id=rqq('area_id');
    include('pages/areas_imp.php');
},'get');

Route::add('/despachar',function(){
    $id=rqq('id');
    $idc=codifica($id);

    $sql="select a.tipo_entrega, device_token, a.user_id from orders a inner join users b on a.user_id=b.id where a.id=" . $id;
    $usuario=lee1o($sql);
    $estatus = ($usuario->tipo_entrega == 'Delivery') ? 7 : 8;
    $data=[
        'estatus' => $estatus,
        'fecha_despacho' => date('Y-m-d H:i:s'),
    ];
    $sql=crea_update('orders', $data, " where id = " . $id);
    $GLOBALS['mysqli']->query($sql);

    $sql=crea_update('users', ['order_estatus' => $estatus, 'order_last_update' => date('Y/m/d  H:i:s')], " where id = '" . $usuario->user_id . "'");
    $GLOBALS['mysqli']->query($sql);

    if($usuario->device_token){
        //if($usuario->tipo_entrega == 'Delivery')
        enviar_push('/compra/' . $idc,[$usuario->device_token],"Pedidos","Revisar el pedido",'Tienes una nueva notificación de su compra, ¿Quieres ir a la compra?');
    }
    if($estatus == 8){
        header('Location: /entregar?id=' . $id);
        exit;
    }
    header('Location: /');
},'get','login');

Route::add('/entregar',function(){
    $id=rqq('id');
    $estatus = 8;
    $data=[
        'estatus' => $estatus,
    ];
    $sql=crea_update('orders', $data, " where id = " . $id);
    $GLOBALS['mysqli']->query($sql);

    $sql="Select id, saldo_wallet, user_id from orders where id=" . $id;
    $pedido=lee1o($sql);

    if($pedido->saldo_wallet > 0){
        $sql="Select id, saldo from users where id=" . $pedido->user_id;
        $usuario=lee1o($sql);
        $saldo=$usuario->saldo + $pedido->saldo_wallet;

        $data=[
            'saldo' => $saldo
        ];
        $sql=crea_update('users', $data, " where id = " . $pedido->user_id);
        $GLOBALS['mysqli']->query($sql);

        $data=[
            'user_id' => $pedido->user_id,
            'order_id' => $pedido->id,
            'tipo' => 'Débito',
            'saldo' => $saldo,
            'monto' => $pedido->saldo_wallet

        ];
        $sql=crea_insert('cashflows', $data);
        $GLOBALS['mysqli']->query($sql);
    
        // Cashflow::create([
        // ]);
    }




    header('Location: /');
},'get','login');

Route::add('/imp1',function(){
    $botToken='5627721283:AAFeh4fz6vYCGiqeuCjnlA7X2t6_T2bAapA';

/*

    $website="https://api.telegram.org/bot".$botToken;
    $chatId=5627721283;  //Receiver Chat Id
    $params=[
        'chat_id'=>$chatId,
        'text'=>'This is my message !!!',
    ];
    $ch = curl_init($website . '/sendMessage');
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);


    /*
  */


    /*
    $id=rqq('id');
    
    echo 'prueba: ' . date('H:i');

    $linea="\n";
    $texto='Hola desde viaappia' . $linea . 'prueba'. $linea . $linea . $linea;
    $pp= 'https://viaappia.ddns.net/imprime.php?p=28&t=' . $texto;
    echo "<br>Resp file_get".file_get_contents($pp);

    /*

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://viaappia.ddns.net/imprime.php?p=28&t=' . $texto); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    //curl_setopt($ch, CURLOPT_HEADER, 0); 
    $data = curl_exec($ch); 
    echo $data;
    curl_close($ch);
    */

},'get');





Route::run('/');
