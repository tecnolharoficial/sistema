<?php include_once('includes/bdd.php'); ?>
<!DOCTYPE html>
<html>
<?php head(); ?>
<body>
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-5">
				<div class="box-page text-center">
					<div class="content">
						<h3 class="mb-4">Entre na sua conta!</h3>
						<form method="POST">
							<div class="form-floating mb-3">
							  	<input type="email" id="email" class="form-control" placeholder="name@example.com" name="email" required>
							  	<label for="email">Endereço de e-mail</label>
							</div>
							<div class="form-floating mb-3">
							  	<input type="password" id="senha" class="form-control" placeholder="********" name="senha" required>
							  	<label for="senha">Senha</label>
							</div>
							<div class="form-check mb-3">
								<label for="lembre_de_mim" class="form-check-label">
									<input type="checkbox" id="lembre_de_mim" class="form-check-input" name="lembre_de_mim">
									Lembrar de mim
								</label>
							</div>
							<input type="hidden" name="entrar">
							<button type="submit" class="w-100">ENTRAR <i class="fa-solid fa-right-to-bracket"></i></button>
						</form>
					</div>
					<div class="footer">
						<p>Ao inscrever-se você concorda com <a href="#"><strong>Termos e Condições</strong></a> e <a href="#"><strong>Política de Privacidade</strong></a></p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php last(); ?>
</body>
</html>