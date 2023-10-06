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
							<h3 class="mb-4">Informações Básicas!</h3>
							<div class="content">
								<form method="POST">
									<div class="form-floating mb-3">
									  	<input type="text" id="nome" class="form-control" placeholder="Digite seu nome" name="nome" value="<?php echo $usuario_logado_nome; ?>" required>
									  	<label for="nome">Nome</label>
									</div>
									<input type="hidden" name="atualizar_informacoes_basicas">
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