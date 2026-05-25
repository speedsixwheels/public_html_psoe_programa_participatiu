<?php


// Muestra cualquier variable formateada para depuracion rapida en pantalla.
function pre($var) {
	echo '<pre>';
	print_r($var);
	echo '</pre>';
}

?>