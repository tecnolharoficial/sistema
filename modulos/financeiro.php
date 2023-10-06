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
				'<li class="d-none d-lg-inline-flex"><a href="'.PATH.'modulos/financeiro/"><i class="fa-solid fa-home"></i> INÍCIO</a></li>',
				'<li class="d-none d-lg-inline-flex"><a data-bs-toggle="modal" data-bs-target="#cadastrar_financa"><i class="fa-solid fa-circle-plus"></i> CADASTRAR FINANCEIRO</a></li>'
			));
?>
			<div class="container">
				<div class="page mb-0">
					<div class="row">
						<div class="col-lg-2 mb-3">
							<div class="box bg-success">
								<span>Á Receber</span>
								<h5 class="m-0"><?php echo formatar_reais(somar_valores_financeiro(1)); ?></h5>
								<i class="fa-solid fa-money-bill-trend-up"></i>
							</div>
						</div>
						<div class="col-lg-2 mb-3">
							<div class="box bg-success">
								<span>Recebido</span>
								<h5 class="m-0"><?php echo formatar_reais(somar_valores_financeiro(2)); ?></h5>
								<i class="fa-solid fa-money-bill-trend-up"></i>
							</div>
						</div>
						<div class="col-lg-2 mb-3">
							<div class="box bg-danger">
								<span>Á Pagar</span>
								<h5 class="m-0"><?php echo formatar_reais(somar_valores_financeiro(3)); ?></h5>
								<i class="fa-solid fa-money-bill-trend-up"></i>
							</div>
						</div>
						<div class="col-lg-2 mb-3">
							<div class="box bg-danger">
								<span>Pago</span>
								<h5 class="m-0"><?php echo formatar_reais(somar_valores_financeiro(4)); ?></h5>
								<i class="fa-solid fa-money-bill-trend-up"></i>
							</div>
						</div>
						<div class="col-lg-2 mb-3">
							<div class="box bg-primary">
								<span>Saldo Atual</span>
								<h5 class="m-0"><?php echo formatar_reais((somar_valores_financeiro(2) - somar_valores_financeiro(4))); ?></h5>
								<i class="fa-solid fa-money-bill-trend-up"></i>
							</div>
						</div>
						<div class="col-lg-2 mb-3">
							<div class="box text-dark bg-warning">
								<span>Previsão</span>
								<h5 class="m-0 text-dark"><?php echo formatar_reais((somar_valores_financeiro(1) + somar_valores_financeiro(2)) - (somar_valores_financeiro(3) + somar_valores_financeiro(4))); ?></h5>
								<i class="fa-solid fa-money-bill-trend-up"></i>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<div class="page mt-3">
							<div class="content">
								<table class="table text-center">
								    <thead>
								        <tr>
								            <th>Título</th>
								            <th>Valor</th>
											<th>Data de Vencimento</th>
											<th class="hide_mobile">Situação</th>
											<th class="hide_mobile">Data de criação</th>
											<th class="hide_mobile">Ações</th>
								        </tr>
								    </thead>
								    <tbody>
<?php
										$selecao = "SELECT * from financeiro WHERE id_usuario = :id_usuario ORDER BY CASE WHEN data_de_vencimento IS NULL THEN 1 ELSE 0 END, data_de_vencimento ASC";
				                        try {
				                            $resultado = $bdd->prepare($selecao);
				                            $resultado->bindParam(':id_usuario', $usuario_logado_id, PDO::PARAM_STR);
				                            $resultado->execute();
				                            $contador = $resultado->rowCount();
				                            if($contador > 0) {
				                            	$modal_detalhes = array();
				                            	$modal_edicao = array();
				                            	$modal_exclusao = array();
				                                while($conteudo = $resultado->FETCH(PDO::FETCH_OBJ)) {
?>
													<tr id="<?php echo $conteudo->id; ?>">
											            <td><?php echo $conteudo->titulo; ?></td>
											            <td><?php echo valor_financeiro($conteudo->valor, $conteudo->situacao); ?></td>
											            <td><?php echo formatar_data($conteudo->data_de_vencimento); ?></td>
											            <td class="hide_mobile"><?php echo situacao_financeiro($conteudo->situacao); ?></td>
											            <td class="hide_mobile"><?php echo formatar_data($conteudo->data_de_criacao); ?></td>
											            <td class="hide_mobile">
											            	<div class="dropdown">
											                    <a class="dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></a>
																<ul class="dropdown-menu">
																	<li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="<?php echo '#detalhes_'.$conteudo->id; ?>"><i class="fa-solid fa-circle-info"></i> Detalhar</a></li>
																	<li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="<?php echo '#editar_'.$conteudo->id; ?>"><i class="fa-solid fa-edit"></i> Editar</a></li>
																	<li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="<?php echo '#excluir_'.$conteudo->id; ?>"><i class="fa-solid fa-trash"></i> Remover</a></li>
																</ul>
															</div>
											            </td>
											        </tr>
<?php
													$modal_detalhes[] = '
														<div class="modal fade" id="detalhes_'.$conteudo->id.'">
													        <div class="modal-dialog modal-dialog-centered">
													            <div class="modal-content">
													            	<div class="modal-header">
													                    <h5 class="modal-title">Detalhes da Finança</h5>
													                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
													                </div>
													                <div class="modal-body">
													            		<p><strong>Título:</strong></p>
													                	<p>'.$conteudo->titulo.'</p>
													                	<hr>
													                	<p><strong>Valor:</strong></p>
													                	<p>'.valor_financeiro($conteudo->valor, $conteudo->situacao).'</p>
													                	<hr>
													                	<p><strong>Data de Vencimento:</strong></p>
													                	<p>'.formatar_data($conteudo->data_de_vencimento).'</p>
													                	<hr>
													                	<p><strong>Situação:</strong></p>
													                	<p>'.situacao_financeiro($conteudo->situacao).'</p>
													                	<hr>
													                	<div class="d-flex gap-2">
													                        <a data-bs-toggle="modal" data-bs-target="#editar_'.$conteudo->id.'"><i class="fa-solid fa-edit"></i> Editar</a>
													                        <a data-bs-toggle="modal" data-bs-target="#excluir_'.$conteudo->id.'"><i class="fa-solid fa-trash"></i> Excluir</a>
													                    </div>
													                </div>
													            </div>
													        </div>
													    </div>
													';
													$modal_edicao[] = '
														<div class="modal fade" id="editar_'.$conteudo->id.'">
													        <div class="modal-dialog modal-dialog-centered">
													            <div class="modal-content">
													            	<div class="modal-header">
													                    <h5 class="modal-title">Editar: '.$conteudo->titulo.'</h5>
													                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
													                </div>
													                <div class="modal-body">
													                	<form method="POST">
													                		<div class="form-floating mb-3">
																			  	<input type="text" id="titulo" class="form-control" placeholder="Digite um título" name="titulo" value="'.$conteudo->titulo.'" required>
																			  	<label for="titulo">Título</label>
																			</div>
																			<div class="form-floating mb-3">
																			  	<input type="text" id="valor" class="form-control money" placeholder="Digite um valor" name="valor" value="'.$conteudo->valor.'" required>
																			  	<label for="valor">Valor</label>
																			</div>
																			<div class="form-floating mb-3">
																			  	<input type="text" id="data_de_vencimento" class="form-control date" placeholder="Digite uma data de vencimento" name="data_de_vencimento" value="'.formatar_data($conteudo->data_de_vencimento).'">
																			  	<label for="data_de_vencimento">Data de Vencimento</label>
																			</div>
																			<div class="form-floating mb-3">
																				'.select_situacao_financeiro($conteudo->situacao).'
																			</div>
													                		<input type="hidden" name="atualizar_financa" value="'.$conteudo->id.'">
																			<button type="submit" class="w-100">SALVAR <i class="fa-solid fa-floppy-disk"></i></button>
													                	</form>
													                </div>
													            </div>
													        </div>
													    </div>
													';
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
													                		<input type="hidden" name="excluir_financa" value="'.$conteudo->id.'">
																			<button type="submit" class="w-100 bg-danger border-danger">CONFIRMAR EXCLUSÃO <i class="fa-solid fa-trash"></i></button>
													                	</form>
													                </div>
													            </div>
													        </div>
													    </div>
													';
												}
											}
										}
										catch(PDOException $e) {
							                echo $e;
							            }
?>
								    </tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php footer(); ?>
		</div>
	</div>
	<div class="modal fade" id="cadastrar_financa">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            	<div class="modal-header">
                    <h5 class="modal-title">Nova Finança</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                	<form method="POST">
                		<div class="form-floating mb-3">
						  	<input type="text" id="titulo" class="form-control" placeholder="Digite um título" name="titulo" required>
						  	<label for="titulo">Título</label>
						</div>
						<div class="form-floating mb-3">
						  	<input type="text" id="valor" class="form-control money" placeholder="Digite um valor" name="valor" required>
						  	<label for="valor">Valor</label>
						</div>
						<div class="form-floating mb-3">
						  	<input type="text" id="data_de_vencimento" class="form-control date" placeholder="Digite uma data de vencimento" name="data_de_vencimento">
						  	<label for="data_de_vencimento">Data de Vencimento</label>
						</div>
						<div class="form-floating mb-3">
							<?php echo select_situacao_financeiro(); ?>
						</div>
                		<input type="hidden" name="cadastrar_financa">
						<button type="submit" class="w-100">CADASTRAR <i class="fa-solid fa-circle-plus"></i></button>
                	</form>
                </div>
            </div>
        </div>
    </div>
<?php
	foreach($modal_detalhes as $modal) {
		echo $modal;
	}
	foreach($modal_edicao as $modal) {
		echo $modal;
	}
	foreach($modal_exclusao as $modal) {
		echo $modal;
	}
	last();
?>
</body>
</html>