<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


function configuracion() {
	//require_once(ABSPATH . 'wp-config.php');
	global $woocommerce, $wp_roles, $post, $wpdb,$wp_query; 
	
	/*crear fechas*/
	$errorCrear = 3;
	if(isset($_POST['fecha']) and $_POST['fecha']!=""){
		
		$fecha = $_POST['fecha'];

		$sql = "
			INSERT INTO  a1_fechas  
			(fecha)

			VALUES  
			('$fecha');	
			";

		$wpdb->query($sql);
		
		$errorCrear = 0;
		
	}
	/*crear fechas*/
	
	
	/*crear fechas*/
	$errorBorrar = 3;
	if(isset($_GET['cod_fecha']) and $_GET['cod_fecha']!=""){
		
		$cod_fecha = $_GET['cod_fecha'];
		
		$sql = "
			DELETE FROM a1_fechas WHERE  cod_fecha = '$cod_fecha'	
			";

		$wpdb->query($sql);
		$errorBorrar = 0;
		
	}
	/*crear fechas*/
	
	
	
	
?>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.9/dist/flatpickr.min.css">
<style>
	
.notice,
.fs-notice,
div.fs-notice.updated, 
div.fs-notice.success, 
div.fs-notice.promotion{
    display: none !important;
}		
	
.flatpickr-input[readonly] {
    background: #fff;
	width: 100%;
}	
	
/* Estilo para la tabla con encabezado oscuro */


/* Estilo para las celdas del encabezado */
.dark-header th {
    background-color: #222; /* Fondo oscuro más oscuro */
    color: #fff; /* Texto blanco */
    border-color: #444; /* Borde más oscuro */
}
	
table.table tbody th, table.table tbody td {
    vertical-align: middle;
}	
	
	
</style>        
        

<div class="wrap">
	
	
	<div class="container mt-5">
		
		
		
		<div class="row">
			
			
			
			
			
			<!-- configurar temporadas -->
			
			<div class="col-md-6">
				
				<?php 
				if($errorCrear==0){
				?>
				<div class="alert alert-success mb-3">Fechas Agregada Correctamente</div>
				<?php 
				}
				?>
				
				<?php $url = admin_url('admin.php?page=fechas-calendario');?>
				<h1 class="mb-4"><strong>Agregar Fechas Especiales</strong></h1>
				
				<form action="<?php echo $url;?>" method="post" enctype="multipart/form-data">
					
				<div class="row">
					
					
					
					<div class="col-md-6 mb-3">
						
						<div class="date-picker">
							<label for="datepicker1"><strong>Fecha Especial</strong></label>
							<input type="text" id="datepicker1" name="fecha" required  
								   placeholder="Elije Fecha" class="primer_inicio date-input">
						</div>
						
					</div>	
					
					<div class="col-md-12 mb-3">
					
						<input type="hidden" name="crearTemporadas" value="ok">
						<button type="submit" id="btnTemporadas" class="button button-primary">
							Agregar Fecha
						</button>
					
					</div>
						
						
					
				</div>	
					
				</form>	
			
			
			</div>
			
			
			<!-- configurar temporadas -->
			
			<!-- Tabla de Temporadas -->
			
			<div class="col-md-6">
				
				<?php 
				if($errorBorrar==0){
				?>
				<div class="alert alert-success mb-3">Fecha Eliminada Correctamente</div>
				<?php 
				}
				?>
				
				<h1 class="mb-4"><strong>Todas las Fechas Especiales</strong></h1>
			
				<div class="detalle w-100 position-relative table-responsive">
					<table class="table table-striped">
						<thead  class="thead-dark">
							<tr>
								<th class="text-center">#</th>
								<th class="text-center">Fecha</th>
								<th class="text-center">Acciones</th>
							</tr>
						</thead>

						<tbody>
							
							<?php
							$all_fechas = $wpdb->get_results("SELECT cod_fecha, fecha 
										FROM a1_fechas  ");
							$cont=0;
							$url = admin_url('admin.php?page=fechas-calendario');	
							if($all_fechas){

								foreach ($all_fechas as $date){
									$fecha = $date->fecha;
									$cod_fecha = $date->cod_fecha;
									
									$cont++;
									
									?>
									<tr>
										<td class="text-center"><?php echo $cont;?></td>
										<td class="text-center"><?php echo $fecha;?></td>
										<td class="text-center">
										
											<a href="<?php echo $url;?>&cod_fecha=<?php echo $cod_fecha;?>" class="btn btn-danger">
												<i class="fas fa-trash-alt"></i> Borrar
											</a>
											
										</td>
									</tr>	
							
									<?php

								}

							}
	
							?>
							
						 
						</tbody>	

					</table>		

				</div>
				
			</div>	
			
			<!-- Tabla de Temporadas -->
			
			
			
			
				
		</div>	
		
		
		
		
	</div>	
	
	
	
	
</div>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
 

<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.9/dist/flatpickr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.9/dist/l10n/es.js"></script>
 </script>

<script>
	
document.addEventListener('DOMContentLoaded', function() {
    const dateInputs = document.querySelectorAll('.date-input');

    dateInputs.forEach(input => {
        flatpickr(input, {
            dateFormat: 'Y-m-d', // Formato deseado
			locale: 'es',
        });
    });
});
	
	


	

</script>

<?php





 

//fin config 
}