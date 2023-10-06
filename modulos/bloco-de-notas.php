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
				'<li class="d-none d-lg-inline-flex"><a href="'.PATH.'modulos/bloco-de-notas/"><i class="fa-solid fa-home"></i> INÍCIO</a></li>',
				'<li class="d-none d-lg-inline-flex"><a data-bs-toggle="modal" data-bs-target="#criar_novo_bloco_de_notas"><i class="fa-solid fa-circle-plus"></i> CRIAR NOVO BLOCO DE NOTAS</a></li>'
			));
?>
			<div class="container">
				<div class="page">
					<h3 class="mb-4">Bloco de Notas!</h3>
					<div id="blocos_de_notas" class="row sortable">
<?php
						$selecao = "SELECT * from blocos_de_notas WHERE id_usuario = :id_usuario ORDER BY ordem ASC";
                        try {
                            $resultado = $bdd->prepare($selecao);
                            $resultado->bindParam(':id_usuario', $usuario_logado_id, PDO::PARAM_STR);
                            $resultado->execute();
                            $contador = $resultado->rowCount();
                            if($contador > 0) {
                            	$modal_exclusao = array();
                                while($conteudo = $resultado->FETCH(PDO::FETCH_OBJ)) {
?>
									<div id="<?php echo $conteudo->id; ?>" class="col-lg-3 m-lg-3 mt-0 mb-5">
										<div class="notas">
											<form method="POST">
												<div class="float-buttons">
													<button type="submit" class="text-white bg-success"><i class="fa-solid fa-floppy-disk"></i></button>
													<button type="button" class="text-white bg-danger" data-bs-toggle="modal" data-bs-target="<?php echo '#excluir_'.$conteudo->id; ?>"><i class="fa-solid fa-trash"></i></button>
												</div>
												<textarea name="titulo" placeholder="Digite um título" required><?php echo $conteudo->titulo; ?></textarea>
												<hr>
												<textarea name="conteudo" placeholder="Digite o conteúdo"><?php echo $conteudo->conteudo; ?></textarea>
												<input type="hidden" name="atualizar_bloco_de_notas" value="<?php echo $conteudo->id; ?>">
											</form>
										</div>
									</div>
<?php
									$modal_exclusao[] = '
										<div class="modal fade" id="excluir_'.$conteudo->id.'">
									        <div class="modal-dialog modal-dialog-centered">
									            <div class="modal-content">
									            	<div class="modal-header">
									                    <h5 class="modal-title">Excluir: '.$conteudo->titulo.'</h5>
									                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
									                </div>
									                <div class="modal-body">
									                	<form method="POST">
									                		<input type="hidden" name="excluir_bloco_de_notas" value="'.$conteudo->id.'">
															<button type="submit" class="w-100 bg-danger border-danger">CONFIRMAR EXCLUSÃO <i class="fa-solid fa-trash"></i></button>
									                	</form>
									                </div>
									            </div>
									        </div>
									    </div>
									';
								}
							}
							else {
								echo '<div class="alert alert-warning"><strong>Oops! </strong>Você ainda não criou nenhum bloco de notas.</div>';
							}
						}
						catch(PDOException $e) {
			                echo $e;
			            }
?>
					</div>
				</div>
			</div>
			<?php footer(); ?>
		</div>
	</div>
	<div class="modal fade" id="criar_novo_bloco_de_notas">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            	<div class="modal-header">
                    <h5 class="modal-title">Novo Bloco de Notas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                	<form method="POST">
                		<div class="form-floating mb-3">
						  	<input type="text" id="titulo" class="form-control" placeholder="Digite um título" name="titulo" required>
						  	<label for="titulo">Título</label>
						</div>
						<div class="form-floating mb-3">
						  	<textarea id="conteudo" class="form-control" placeholder="Digite o seu conteúdo" name="conteudo" style="height: 100px;"></textarea>
						  	<label for="conteudo">Conteúdo</label>
						</div>
                		<input type="hidden" name="criar_novo_bloco_de_notas">
						<button type="submit" class="w-100">CRIAR <i class="fa-solid fa-circle-plus"></i></button>
                	</form>
                </div>
            </div>
        </div>
    </div>
<?php
	foreach($modal_exclusao as $modal) {
		echo $modal;
	}
	last();
?>
</body>
</html>