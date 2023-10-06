<?php include_once('includes/bdd.php'); ?>
<!DOCTYPE html>
<html>
<?php head(); ?>
<body>
	<div class="d-flex min-h-100">
		<?php sidebar(); ?>
		<div class="body">
<?php
			topbar(array(
				'<li class="d-none d-lg-inline-flex"><a href="'.PATH.'minha-conta/"><i class="fa-solid fa-home"></i> INÍCIO</a></li>',
				'<li class="d-none d-lg-inline-flex"><a href="'.PATH.'atualizar-email/"><i class="fa-solid fa-envelope"></i> ATUALIZAR E-MAIL</a></li>',
				'<li class="d-none d-lg-inline-flex"><a href="'.PATH.'atualizar-senha/"><i class="fa-solid fa-key"></i> ATUALIZAR SENHA</a></li>'
			));
?>
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="page">
							<h3 class="mb-4">Atualização de E-mail!</h3>
							<div class="content">
								<form method="POST">
									<div class="form-floating mb-3">
									  	<input type="email" id="email" class="form-control" placeholder="name@example.com" name="email" required>
									  	<label for="email">E-mail Atual</label>
									</div>
									<div class="form-floating mb-3">
									  	<input type="email" id="novo_email" class="form-control" placeholder="name@example.com" name="novo_email" required>
									  	<label for="novo_email">Novo E-mail</label>
									</div>
									<div class="form-floating mb-3">
									  	<input type="email" id="confirmar_novo_email" class="form-control" placeholder="name@example.com" name="confirmar_novo_email" required>
									  	<label for="confirmar_novo_email">Confirme seu Novo E-mail</label>
									</div>
									<input type="hidden" name="atualizar_email">
									<button type="submit" class="w-100">SALVAR <i class="fa-solid fa-floppy-disk"></i></button>
								</form>
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