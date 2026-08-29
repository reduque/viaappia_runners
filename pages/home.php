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
const audioNuevo = new Audio('./sonido/nuevo.wav');

function reproducirAlarmas(nuevo, alerta) {
    audio.pause();
    audio.currentTime = 0;
    audio.onended = null;

    audioNuevo.pause();
    audioNuevo.currentTime = 0;
    audioNuevo.onended = null;

    if (nuevo && alerta) {
        audioNuevo.onended = function() {
            audio.play();
            audioNuevo.onended = null;
        };
        audioNuevo.play();
    } else if (nuevo) {
        audioNuevo.play();
    } else if (alerta) {
        audio.play();
    }
}
</script>