<?php include_once('../includes/bdd.php'); ?>
<!DOCTYPE html>
<html>
<?php head(); ?>
<body>
	<div class="d-flex min-h-100">
		<?php sidebar(); ?>
		<div class="body">
<?php
			topbar(array(
				'<li class="d-none d-lg-inline-flex"><a href="'.PATH.'modulos/agenda/"><i class="fa-solid fa-home"></i> INÍCIO</a></li>'
			));
?>
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="page">
							<h3 class="mb-4">Agenda!</h3>
							<div class="content">
								<div id="agenda"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php footer(); ?>
		</div>
	</div>
	<?php last(); ?>
</body>
</html>