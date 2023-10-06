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
				'<li class="d-none d-lg-inline-flex"><a href="'.PATH.'modulos/clientes/"><i class="fa-solid fa-home"></i> INÍCIO</a></li>',
				'<li class="d-none d-lg-inline-flex"><a data-bs-toggle="modal" data-bs-target="#cadastrar_novo_cliente"><i class="fa-solid fa-circle-plus"></i> CADASTRAR NOVO CLIENTE</a></li>'
			));
?>
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="page">
							<h3 class="mb-4">Clientes!</h3>
							<div class="content">
								<table class="table text-center">
								    <thead>
								        <tr>
								            <th>Nome</th>
								            <th class="hide_mobile">Telefone</th>
											<th class="hide_mobile">E-mail</th>
											<th>Data de criação</th>
											<th class="hide_mobile">Ações</th>
								        </tr>
								    </thead>
								    <tbody>
<?php
										$selecao = "SELECT * from clientes WHERE id_usuario = :id_usuario ORDER BY id DESC";
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
											            <td><?php echo $conteudo->nome_razao_social; ?></td>
											            <td class="hide_mobile"><a href="#"><?php echo $conteudo->telefone; ?></a></td>
											            <td class="hide_mobile"><a href="#"><?php echo $conteudo->email; ?></a></td>
											            <td><?php echo formatar_data($conteudo->data_de_criacao); ?></td>
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
													                    <h5 class="modal-title">Detalhes do Cliente</h5>
													                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
													                </div>
													                <div class="modal-body">
													            		<p><strong>Nome / Razão Social:</strong></p>
													                	<p>'.$conteudo->nome_razao_social.'</p>
													                	<hr>
													                	<p><strong>CPF / CNPJ:</strong></p>
													                	<p>'.$conteudo->cpf_cnpj.'</p>
													                	<hr>
													                	<p><strong>Telefone:</strong></p>
													                	<p>'.$conteudo->telefone.'</p>
													                	<hr>
													                	<p><strong>E-mail:</strong></p>
													                	<p>'.$conteudo->email.'</p>
													                	<hr>
													                	<p><strong>CEP:</strong></p>
													                	<p>'.$conteudo->cep.'</p>
													                	<hr>
													                	<p><strong>Endereço:</strong></p>
													                	<p>'.$conteudo->endereco.'</p>
													                	<hr>
													                	<p><strong>Número:</strong></p>
													                	<p>'.$conteudo->numero.'</p>
													                	<hr>
													                	<p><strong>Complemento:</strong></p>
													                	<p>'.$conteudo->complemento.'</p>
													                	<hr>
													                	<p><strong>Bairro:</strong></p>
													                	<p>'.$conteudo->bairro.'</p>
													                	<hr>
													                	<p><strong>Estado:</strong></p>
													                	<p>'.$conteudo->estado.'</p>
													                	<hr>
													                	<p><strong>Cidade:</strong></p>
													                	<p>'.$conteudo->cidade.'</p>
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
													                    <h5 class="modal-title">Editar: '.$conteudo->nome_razao_social.'</h5>
													                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
													                </div>
													                <div class="modal-body">
													                	<form method="POST">
													                		<div class="mb-4">
														                		<strong>Dados básicos</strong>
														                		<hr class="mt-2">
														                		<div class="form-floating mb-3">
																				  	<input type="text" id="nome_razao_social" class="form-control" placeholder="Digite o nome ou razão social" name="nome_razao_social" value="'.$conteudo->nome_razao_social.'" required>
																				  	<label for="nome_razao_social">Nome / Razão Social</label>
																				</div>
																				<div class="form-floating mb-3">
																				  	<input type="text" id="cpf_cnpj" class="form-control cpf_cnpj" placeholder="Digite o cpf ou cnpj" name="cpf_cnpj" value="'.$conteudo->cpf_cnpj.'">
																				  	<label for="cpf_cnpj">CPF / CNPJ</label>
																				</div>
																				<div class="form-floating mb-3">
																				  	<input type="text" id="telefone" class="form-control sp_celphones" placeholder="Digite o telefone" name="telefone" value="'.$conteudo->telefone.'">
																				  	<label for="telefone">Telefone</label>
																				</div>
																				<div class="form-floating mb-3">
																				  	<input type="email" id="email" class="form-control" placeholder="Digite o e-mail" name="email" value="'.$conteudo->email.'">
																				  	<label for="email">E-mail</label>
																				</div>
																			</div>
																			<div>
																				<strong>Endereço</strong>
														                		<hr class="mt-2">
														                		<div class="form-floating mb-3">
																				  	<input type="text" id="cep" class="form-control cep" placeholder="Digite o cep" name="cep" value="'.$conteudo->cep.'">
																				  	<label for="cep">CEP</label>
																				</div>
																				<div class="form-floating mb-3">
																				  	<input type="text" id="endereco" class="form-control" placeholder="Digite o endereço" name="endereco" value="'.$conteudo->endereco.'">
																				  	<label for="endereco">Endereço</label>
																				</div>
																				<div class="form-floating mb-3">
																				  	<input type="number" id="numero" class="form-control" placeholder="Digite o número" name="numero" value="'.$conteudo->numero.'">
																				  	<label for="numero">Número</label>
																				</div>
																				<div class="form-floating mb-3">
																				  	<input type="text" id="complemento" class="form-control" placeholder="Digite o complemento" name="complemento" value="'.$conteudo->complemento.'">
																				  	<label for="complemento">Complemento</label>
																				</div>
																				<div class="form-floating mb-3">
																				  	<input type="text" id="bairro" class="form-control" placeholder="Digite o bairro" name="bairro" value="'.$conteudo->bairro.'">
																				  	<label for="bairro">Bairro</label>
																				</div>
																				<div class="form-floating mb-3">
																				  	<input type="text" id="estado" class="form-control" placeholder="Digite o estado" name="estado" value="'.$conteudo->estado.'">
																				  	<label for="estado">Estado</label>
																				</div>
																				<div class="form-floating mb-3">
																				  	<input type="text" id="cidade" class="form-control" placeholder="Digite a cidade" name="cidade" value="'.$conteudo->cidade.'">
																				  	<label for="cidade">Cidade</label>
																				</div>
														                	</div>
													                		<input type="hidden" name="atualizar_cliente" value="'.$conteudo->id.'">
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
													                    <h5 class="modal-title">Excluir: '.$conteudo->nome_razao_social.'</h5>
													                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
													                </div>
													                <div class="modal-body">
													                	<form method="POST">
													                		<input type="hidden" name="excluir_cliente" value="'.$conteudo->id.'">
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
	<div class="modal fade" id="cadastrar_novo_cliente">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            	<div class="modal-header">
                    <h5 class="modal-title">Novo Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                	<form method="POST">
                		<div class="mb-4">
	                		<strong>Dados básicos</strong>
	                		<hr class="mt-2">
	                		<div class="form-floating mb-3">
							  	<input type="text" id="nome_razao_social" class="form-control" placeholder="Digite o nome ou razão social" name="nome_razao_social" required>
							  	<label for="nome_razao_social">Nome / Razão Social</label>
							</div>
							<div class="form-floating mb-3">
							  	<input type="text" id="cpf_cnpj" class="form-control cpf_cnpj" placeholder="Digite o cpf ou cnpj" name="cpf_cnpj">
							  	<label for="cpf_cnpj">CPF / CNPJ</label>
							</div>
							<div class="form-floating mb-3">
							  	<input type="text" id="telefone" class="form-control sp_celphones" placeholder="Digite o telefone" name="telefone">
							  	<label for="telefone">Telefone</label>
							</div>
							<div class="form-floating mb-3">
							  	<input type="email" id="email" class="form-control" placeholder="Digite o e-mail" name="email">
							  	<label for="email">E-mail</label>
							</div>
						</div>
						<div>
							<strong>Endereço</strong>
	                		<hr class="mt-2">
	                		<div class="form-floating mb-3">
							  	<input type="text" id="cep" class="form-control cep" placeholder="Digite o cep" name="cep">
							  	<label for="cep">CEP</label>
							</div>
							<div class="form-floating mb-3">
							  	<input type="text" id="endereco" class="form-control" placeholder="Digite o endereço" name="endereco">
							  	<label for="endereco">Endereço</label>
							</div>
							<div class="form-floating mb-3">
							  	<input type="number" id="numero" class="form-control" placeholder="Digite o número" name="numero">
							  	<label for="numero">Número</label>
							</div>
							<div class="form-floating mb-3">
							  	<input type="text" id="complemento" class="form-control" placeholder="Digite o complemento" name="complemento">
							  	<label for="complemento">Complemento</label>
							</div>
							<div class="form-floating mb-3">
							  	<input type="text" id="bairro" class="form-control" placeholder="Digite o bairro" name="bairro">
							  	<label for="bairro">Bairro</label>
							</div>
							<div class="form-floating mb-3">
							  	<input type="text" id="estado" class="form-control" placeholder="Digite o estado" name="estado">
							  	<label for="estado">Estado</label>
							</div>
							<div class="form-floating mb-3">
							  	<input type="text" id="cidade" class="form-control" placeholder="Digite a cidade" name="cidade">
							  	<label for="cidade">Cidade</label>
							</div>
	                	</div>
                		<input type="hidden" name="cadastrar_novo_cliente">
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