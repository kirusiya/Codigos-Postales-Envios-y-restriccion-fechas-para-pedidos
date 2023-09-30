<?php
/*
  Plugin Name: Códigos Postales y Envios
  Plugin URI: http://ajamba.org 
  Description: En el Checkout de Woocommerce muestra los códigos postales en un select cargados y cambia el precio segun el codigo Postal. Tambien añade campos extra al Checkout para recibir pedidos con 24 horas de anticipación. Restringe dias especiales en el Checkout
  Version: 1.0
  Author: Ajamba | Ing. Edward Avalos
  Author URI: https://www.linkedin.com/in/edward-avalos-severiche/

 */ 


global $wpdb;


/*tablas*/
$charset_collate = $wpdb->get_charset_collate();

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

$sql = "CREATE TABLE IF NOT EXISTS reservas (

  	`cod_reserva` int(11) NOT NULL AUTO_INCREMENT,
	`fecha` text NOT NULL,

	`hora` text NOT NULL,

  PRIMARY KEY  (cod_reserva)

) $charset_collate;";
dbDelta( $sql );
/*tablas*/


function prefix_append_support_and_faq_links( $links_array, $plugin_file_name, $plugin_data, $status ) {
	if ( strpos( $plugin_file_name, basename(__FILE__) ) ) {

		// You can still use `array_unshift()` to add links at the beginning.
		$links_array[] = '<a href="https://wa.me/59161781119" target="_blank"><span class="dashicons dashicons-whatsapp"></span> Enviame un Mensaje</a>';
		$links_array[] = '<a href="https://www.facebook.com/ajamba.web.1" target="_blank"><span class="dashicons dashicons-facebook"></span> Visita mi Facebook</a>';
	}
 
	return $links_array;
}

add_filter( 'plugin_row_meta', 'prefix_append_support_and_faq_links', 10, 4 );

/*agrear css y js al admin del plugin*/
add_action('admin_head', 'css_ajamba_admin_logo');
function css_ajamba_admin_logo() {
    ?>
<style>
li#toplevel_page_fechas-calendario .wp-menu-image::before {
    content: ' ';
    background-image: url(<?php echo plugins_url( basename( __DIR__ ) . '/img/ajamba.jpg' ); ?>);
    background-clip: content-box;
    background-repeat: no-repeat;
    background-position: center center;
    background-size: 25px;
    width: 25px;
    height: 25px;
    margin-top: 5px;
    padding: 0;
    border-radius: 50%;
}        
</style>
    <?php
}

/*agrear css y js al admin del plugin*/

function register_session(){
    if( !session_id() )
        session_start();
}
add_action('init','register_session');


/*tabla temporadas borradas*/
$charset_collate = $wpdb->get_charset_collate();

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

$sql = "CREATE TABLE IF NOT EXISTS a1_fechas (

  	`cod_fecha` int(11) NOT NULL AUTO_INCREMENT,
	`fecha` text NOT NULL,

  PRIMARY KEY  (cod_fecha)

) $charset_collate;";
dbDelta( $sql );
/*tabla temporadas borradas*/


/*******coloca un menu al admin*********/ 
add_action('admin_menu', 'config_fechas');

function config_fechas() {
    add_menu_page('config_fechas', //page title
            'Desactivar Fechas', //menu title
            'manage_options', //capabilities
            'fechas-calendario', //menu slug
            'configuracion' //function
    );
}
/*******coloca un menu al admin*********/

/*******rutas de archivos*********/ 
define('ROOTDIR_DP_AJA', plugin_dir_path(__FILE__)); 
require_once(ROOTDIR_DP_AJA . 'configuracion.php');
/*******rutas de archivos*********/





function css_ajamba() {

?>
<style>

input#fecha_entrega.hasDatepicker,
input#hora_inicio.hasDatepicker,
input#hora_fin.hasDatepicker{
    background: #fff !important;
}
	
#custom_select2_field .select2.select2-container {
    width: 100% !important;
}	
	
	
#select2-billing_postcode-container .select2-selection__clear {
    cursor: pointer;
    float: right;
    font-weight: 700;
    font-size: 25px;
    margin-top: -5px;
    margin-right: 5px;
    color: red;
}	
	
.select2-container--default .select2-selection--single button.select2-selection__clear {
    cursor: pointer;
    float: right;
    font-weight: 700;
    font-size: 25px;
    margin-top: -16px;
    margin-right: 25px;
    color: red;
}
	
.select2-container--default .select2-selection--single .select2-selection__clear span:hover {
    color: black !important;
}	

</style>
<?php

}
 
add_action( 'wp_head', 'css_ajamba' );


function enqueue_datepicker_scripts() {
    // Asegurarse de cargar jQuery antes de estos scripts
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_script('jquery-ui-slider');
    wp_enqueue_script('jquery-ui-timepicker-addon', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js', array('jquery', 'jquery-ui-datepicker'), '', true);
    wp_enqueue_style('jquery-ui-datepicker-style', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css');
}

add_action('wp_enqueue_scripts', 'enqueue_datepicker_scripts');

// Resto del código para añadir los campos de fecha y hora como antes...
function is_monday($date) {
    return (date('N', strtotime($date)) == 1);
}

function add_time_picker_field() {
	global $woocommerce, $wp_roles, $post, $wpdb,$wp_query;
	
    ?>
    <script>
		
		
        jQuery(document).ready(function($) {
			
			$.timepicker.regional['es'] = {
                timeOnlyTitle: 'Elegir una hora',
                timeText: 'Hora',
                hourText: 'Horas',
                minuteText: 'Minutos',
                secondText: 'Segundos',
                millisecText: 'Milisegundos',
                timezoneText: 'Zona horaria',
                currentText: 'Ahora',
                closeText: 'Cerrar',
                timeFormat: 'HH:mm',
                amNames: ['AM', 'A'],
                pmNames: ['PM', 'P'],
            };
			
			$.timepicker.setDefaults($.timepicker.regional['es']);
			

            // Configurar el selector de fecha para permitir solo fechas futuras
            /******************************************************/
			
			<?php
			/*fecha*/
			$fecha = date('Y-m-d');
			$hora = "9_10";
			$hora_9_10 = $wpdb->get_results("SELECT cod_reserva, fecha, hora 
										FROM reservas 
										WHERE fecha = '$fecha' and hora = '$hora' ");
			$con_9_10 = 0;
			$pedidos_9_10 = 0;
			if($hora_9_10){
				foreach ($hora_9_10 as $dia_9_10){					
					$con_9_10++;
					
					if($con_9_10>=3){
						$pedidos_9_10 = 1; 
					}
				}

			}
			/*fecha*/
	
			/*fecha*/
			$fecha = date('Y-m-d');
			$hora = "10_11";
			$hora_10_11 = $wpdb->get_results("SELECT cod_reserva, fecha, hora 
										FROM reservas 
										WHERE fecha = '$fecha' and hora = '$hora' ");
			$con_10_11 = 0;
			$pedidos_10_11 = 0;
			if($hora_10_11){
				foreach ($hora_10_11 as $dia_10_11){					
					$con_10_11++;
					
					if($con_10_11>=3){
						$pedidos_10_11 = 1; 
					}
				}

			}
			/*fecha*/
	
	


	
			?>
			
			console.log('pedidos 9_10 = <?php echo $con_9_10;?>');
			console.log('pedidos 10_1 = <?php echo $con_10_11;?>')
			
			<?php
			if($pedidos_9_10==1 and $pedidos_10_11 == 1){
			?>
			
				var currentDate = new Date();
				currentDate.setDate(currentDate.getDate() + 2);
			<?php
			}else{
				
			?>
				var currentDate = new Date();
				currentDate.setDate(currentDate.getDate() + 1);
			<?php
				
			}
			?>

			var currentHour = new Date().getHours();

			// Si es después de las 14:00 am, el pedido se moverá 2 días hacia adelante
			if (currentHour >= 14) {
				<?php
				if($pedidos_9_10==1 and $pedidos_10_11 == 1){
				?>
					currentDate.setDate(currentDate.getDate() + 2);
				<?php
				}else{
				?>
					currentDate.setDate(currentDate.getDate() + 1);
				<?php
					
				}
				?>
			}
			
			<?php
	
// Tu arreglo de fechas desactivadas en formato 'Y-m-d'
$fechas_desactivadas = array('2023-09-30', '2023-10-04', '2023-10-03', '2023-10-05'); // Ejemplo de fechas desactivadas

// Convierte las fechas desactivadas a un formato adecuado para JavaScript
$fechas_desactivadas_js = array_map(function($fecha) {
    return date('Y-m-d', strtotime($fecha));
}, $fechas_desactivadas);
	
	
$all_fechas = $wpdb->get_results("SELECT cod_fecha, fecha 
										FROM a1_fechas  ");
$fechas_desactivadas = array();	
if($all_fechas){

	foreach ($all_fechas as $date){
		
		$fechas_desactivadas[] = $date->fecha;
									
	}
								
}
		
	
	?>

			// Configurar el selector de fecha para permitir solo fechas futuras y deshabilitar según las reglas
			$("#fecha_entrega").datepicker({
				minDate: currentDate, // Fecha mínima = fecha actual + 1 día
				dateFormat: 'yy-mm-dd', // Formato de fecha
				beforeShowDay: function(date) {
					var day = date.getDay();
					
					var newDay = day + 1;
					
        			var hour = new Date().getHours();
					
					console.log (day+':'+hour+' '+ currentDate+ ' nuevo dia: '+newDay)		
					var selectedDate = $.datepicker.formatDate('yy-mm-dd', date);
					
					if (day === 1) { // Si es lunes
						// Devolver [false] para deshabilitar la fecha
						return [false];
					}

					
					if ($.inArray(selectedDate, <?php echo json_encode($fechas_desactivadas); ?>) != -1) {
						return [false];
					}
					// Calcular la diferencia de días entre el día seleccionado y el día actual
					var dayDifference = date - currentDate;

					console.log(dayDifference)
					
					<?php
					if($pedidos_9_10==1 and $pedidos_10_11 == 1){
					?>
					
						if (dayDifference >= 1 && dayDifference <= 2) { 
							// Si la condición se cumple y es el día siguiente o el segundo día siguiente
							console.log('funciona')
							return [false];
						}else{
							return [true];
						}
					
					<?php
					}else{
						
					?>
						if (dayDifference === 1) { // Si es el día siguiente del calendario
							// Devolver [false] para deshabilitar la fecha
							
							console.log('entra aqui')
							return [false];
						} else {
							console.log('entra aqui error')
							// Devolver [true] para habilitar otras fechas
							return [true];
						}
					<?php
						
					}
					?>
				},
				onSelect: function(dateText) {
					// Aquí puedes realizar acciones adicionales cuando se selecciona una fecha
				}
			});

			$("#hora_entrega").select2();
			
			
			/********************************************************/
			

            // Configurar el selector de tiempo para hora de inicio
            
			
        });
    </script>

	
    
    <p class="form-row">
        <label for="fecha_entrega">Fecha de Entrega: <abbr class="required" title="obligatorio">*</abbr></label>
		
        <span class="woocommerce-input-wrapper">
            <input type="text" id="fecha_entrega" name="fecha_entrega" placeholder="Selecciona Fecha">
        </span>
    </p>

	

    <p class="form-row">
        <label for="hora_entrega">Hora de Entrega: <abbr class="required" title="obligatorio">*</abbr></label>
        <span class="woocommerce-input-wrapper">
            <select id="hora_entrega" class="select2 tex-center" name="hora_entrega">
				
				<option value="9:00am a 10:00am">9:00am a 10:00am</option>
				<option value="10:00am a 11:00am">10:00am a 11:00am</option>
				
			</select>	
        </span>
    </p>

    
    <?php
}

add_action('woocommerce_after_checkout_billing_form', 'add_time_picker_field');

// Validación y manejo de errores para los campos de fecha y hora
function validate_custom_checkout_fields() {
    if (empty($_POST['fecha_entrega'])) {
        wc_add_notice('Por favor, selecciona una fecha de entrega.', 'error');
    }

    if (empty($_POST['hora_entrega'])) {
        wc_add_notice('Por favor, selecciona una hora de Entrega.', 'error');
    }

}

add_action('woocommerce_checkout_process', 'validate_custom_checkout_fields');

// Guardar campos personalizados en los metadatos de la orden
function save_custom_checkout_fields($order_id) {
	global $woocommerce, $wp_roles, $post, $wpdb,$wp_query; 
    if (!empty($_POST['fecha_entrega'])) {
        update_post_meta($order_id, 'fecha_entrega', sanitize_text_field($_POST['fecha_entrega']));
    }

    if (!empty($_POST['hora_entrega'])) {
        update_post_meta($order_id, 'hora_entrega', sanitize_text_field($_POST['hora_entrega']));
    }
	
	if (!empty($_POST['fecha_entrega']) and !empty($_POST['hora_entrega']) ) {
		
		$fecha= date('Y-m-d');
		
		$hora = "";
		if($_POST['hora_entrega']=="9:00am a 10:00am"){
			$hora = "9_10";
		}elseif($_POST['hora_entrega']=="10:00am a 11:00am"){
			$hora = "10_11";
		}
		
		
		$sql = "
				INSERT INTO reservas (fecha, hora)
				VALUES ('$fecha', '$hora')
				";

		$wpdb->query($sql);
		
	}

}

add_action('woocommerce_checkout_update_order_meta', 'save_custom_checkout_fields');

// Mostrar campos personalizados en la sección de Envío en la administración de pedidos
function display_custom_order_data_in_admin($order){
    echo '<p><strong>'.__('Fecha de Entrega').':</strong> ' . get_post_meta($order->id, 'fecha_entrega', true) . '</p>';
    echo '<p><strong>'.__('Hora de Entrega').':</strong> ' . get_post_meta($order->id, 'hora_entrega', true). '</p>';
    
}

add_action('woocommerce_admin_order_data_after_shipping_address', 'display_custom_order_data_in_admin', 10, 1);

// Agregar los metadatos personalizados a los correos electrónicos de confirmación de pedidos
function custom_add_order_meta_to_emails($order, $sent_to_admin, $plain_text, $email) {
    $fecha_entrega = $order->get_meta('fecha_entrega');
    $hora_entrega = $order->get_meta('hora_entrega');

    if ($fecha_entrega) {
        echo '<p><strong>' . __('Fecha de Entrega') . ':</strong> ' . esc_html($fecha_entrega) . '</p>';
    }

    if ($hora_entrega) {
        echo '<p><strong>' . __('Hora de Entrega') . ':</strong> ' . esc_html($hora_entrega) . '</p>';
    }
}
add_action('woocommerce_email_order_details', 'custom_add_order_meta_to_emails', 10, 4);



/***********CARRITO DE COMPRAS**************/
function enqueue_select2_scripts() {
    // Asegurarse de cargar jQuery antes de estos scripts
    wp_enqueue_script('jquery');
    wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js', array('jquery'), '', true);
    wp_enqueue_style('select2-style', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css');
}

add_action('wp_enqueue_scripts', 'enqueue_select2_scripts');

function add_select2_field_in_cart() {
	global $woocommerce, $wp_roles, $post, $wpdb,$wp_query;
	session_start()
    

?>
    <p class="form-row form-row-wide" id="custom_select2_field" style="display: none">
        <label for="custom_select2">Selecciona Código Postal:</label>
        <select id="custom_select2" class="select2" name="custom_select2">
            <option value="">-- Elije un Código Postal --</option>
			
			<?php
			
			$all_zip = $wpdb->get_results("SELECT location_code , location_type
                                    FROM {$wpdb->prefix}woocommerce_shipping_zone_locations 
                                    WHERE location_type = 'postcode'
									ORDER BY location_code ASC
									");
		
			if ($all_zip) {
				foreach ($all_zip as $zip) {
					$zip_code = $zip->location_code;
					echo '<option value="' . $zip_code . '">' . $zip_code . '</option>';
				}
			} else {
				echo '<option value="">No se encontraron códigos postales</option>';
			}	
	
			?>
			
			
           
        </select>
    </p>
    <script>
        jQuery(document).ready(function($) {
            // Inicializar Select2
            $("#custom_select2").select2();

            // Mover el campo Select2 encima del campo de código postal
            $("#custom_select2_field").insertBefore("#calc_shipping_postcode_field");
			$("#custom_select2_field").show();
			
			
			
        });
    </script>
    <?php
}

add_action('woocommerce_before_cart_totals', 'add_select2_field_in_cart');

// JavaScript para manejar el proceso de cálculo de envío
function custom_shipping_calculation_js() {
	if (is_cart()) {
		
		
    ?>

<style>
#calc_shipping_postcode {
    position: absolute;
    z-index: 0;
    width: 0;
    height: 0;
    visibility: hidden;
}
*/
</style>
    <script>
        jQuery(document).ready(function($) {
			
			function updatePostalCodeSession() {
                var selectedValue = $('#calc_shipping_postcode').val();
                $.ajax({
                    type: "POST",
                    url: "<?php echo admin_url( 'admin-ajax.php' );?>",
                    data: {
                        action: "update_postal_code_session",
                        postal_code: selectedValue
                    },
                    success: function(response) {
                        sessionStorage.setItem('postal_code_choose', selectedValue);
                    }
                });
            }
			
            // Agregar evento al botón de cálculo de envío
            $('.shipping-calculator-form button[name="calc_shipping"]').on('click', function() {
                // Ocultar el campo select2 si cart_totals está en proceso
                 interval = setInterval(function() {
                    if ($('.cart_totals.calculated_shipping').hasClass('processing')) {
                        //$('#custom_select2_field').hide();
						
                    } else {
						
                        // Mover el campo select2 antes del calc_shipping_postcode_field
						if ($('#custom_select2_field').index() < $('#calc_shipping_city_field').index()) {
						
							console.log('esta dentro');
							$('#custom_select2_field').insertBefore('#calc_shipping_postcode_field');
							$('#custom_select2_field').show();
							$("#custom_select2").select2();

							$('#custom_select2').on('change', function() {
									var selectedValue = $(this).val();
									$('#calc_shipping_postcode').val(selectedValue);
									//$('button[name="calc_shipping"]').trigger('click'); 
									updatePostalCodeSession();
							});
							
							var selectedValue = $('#calc_shipping_postcode').val();
                    		$('#custom_select2').val(selectedValue).trigger('change');
							
							
						}else{
							console.log('esta fuera');
							
						}
						
						
						
                    }
                }, 500); // Verificar cada 0.5 segundos
            });
			
			$('#custom_select2').on('change', function() {
                    var selectedValue = $(this).val();
                    $('#calc_shipping_postcode').val(selectedValue);
                    //$('button[name="calc_shipping"]').trigger('click'); // Simular clic en el botón de cálculo de envío
					updatePostalCodeSession();
            });
			
			var selectedValue = $('#calc_shipping_postcode').val();
            $('#custom_select2').val(selectedValue).trigger('change');
			
        });
    </script>
    <?php
	}
}

add_action('wp_footer', 'custom_shipping_calculation_js');

add_action('wp_ajax_update_postal_code_session', 'update_postal_code_session');
add_action('wp_ajax_nopriv_update_postal_code_session', 'update_postal_code_session');

function update_postal_code_session() {
	session_start();
    if (isset($_POST['postal_code'])) {
        $_SESSION['postal_code_choose'] = $_POST['postal_code'];
    }
    wp_die();
}


add_filter('woocommerce_checkout_fields', 'replace_billing_postcode_with_select');
function replace_billing_postcode_with_select($fields) {
    global $wpdb;

    $all_zip = $wpdb->get_results("SELECT location_code, location_type
                                    FROM {$wpdb->prefix}woocommerce_shipping_zone_locations 
                                    WHERE location_type = 'postcode' ORDER BY location_code ASC");
    $postcode_options = array();

    if ($all_zip) {
        foreach ($all_zip as $zip) {
            $zip_code = $zip->location_code;
            $postcode_options[$zip_code] = $zip_code;
        }
    }
	
	//$postcode_options = array_merge(array('sin_codigo_postal' => 'Sin Código Postal'), $postcode_options);

    // Reemplazar el campo "billing_postcode" con un campo de selección personalizado
    $fields['billing']['billing_postcode'] = array(
        'label'         => __('Código Postal', 'woocommerce'),
        'required'      => true,
        'type'          => 'select',
        'options'       => $postcode_options,
        'class'         => array('form-row-wide'),
        'clear'         => true,
        'default'       => !empty($_SESSION['postal_code_choose']) ? $_SESSION['postal_code_choose'] : ''
    );

    return $fields;
}


 








