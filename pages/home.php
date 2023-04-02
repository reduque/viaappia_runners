<h1>Pedidos</h1>
<section class="fichas ordenes"></section>
<script>
$(document).ready(function(){
    function cargar(){
        $.ajax({
            type: "GET",
            dataType: "html",
            url: "ordenes",
            success: function(data){
                $('.ordenes').html(data);
                setTimeout(function(){
                    cargar();
                }, 10000);
            }
        })
    }
    cargar();
})
const audio = new Audio('./sonido/alerta.wav');

</script>