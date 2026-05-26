<?php

// Muestra cualquier variable formateada para depuracion rapida en pantalla.
if( !function_exists( 'pre' ) ):
function pre($var) {
	echo '<pre>';
	print_r($var);
	echo '</pre>';
}
endif;

?>