<?php include_once('../includes/bdd.php'); ?>
<!DOCTYPE html>
<html>
<?php head(); ?>
<body>
<?php
	if(isset($_GET['file'])) {
		$selecao = "SELECT * from arquivos WHERE id = :id AND id_usuario = :id_usuario";
        try {
            $resultado = $bdd->prepare($selecao);
            $resultado->bindParam(':id', $_GET['file'], PDO::PARAM_STR);
            $resultado->bindParam(':id_usuario', $usuario_logado_id, PDO::PARAM_STR);
            $resultado->execute();
            $contador = $resultado->rowCount();
            if($contador > 0) {
                $loop = $resultado->fetchAll();
                foreach($loop as $conteudo) {
                    $arquivo_nome = $conteudo['nome'];
                    $arquivo_file = $conteudo['file'];
                }
            }
            else {
            	header("Location: ".PATH.'modulos/documentos/');
            }
        }
        catch(PDOException $e) {
            echo $e;
        }
?>
		<div class="d-flex min-h-100">
			<?php sidebar(); ?>
			<div class="body">
<?php
				topbar(array(
					'<li class="d-none d-lg-inline-flex"><a href="'.PATH.'modulos/documentos/"><i class="fa-solid fa-home"></i> INÍCIO</a></li>'
				));
?>
				<div class="container">
					<div class="row">
						<div class="col-lg-12">
							<div class="page">
								<h3 class="mb-4"><?php echo $arquivo_nome; ?></h3>
								<div class="content">
									<form method="GET">
<?php
										$texto = '';
								        $customTempDir = realpath(__DIR__);
										\PhpOffice\PhpWord\Settings::setTempDir($customTempDir);
										$phpWord = \PhpOffice\PhpWord\IOFactory::load(ROOT.'assets/files/'.$arquivo_file);
								        $sections = $phpWord->getSections();
								        foreach($sections as $section) {
								            $elements = $section->getElements();
								            foreach($elements as $element) {
								                if($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
								                    $texts = $element->getElements();
								                    foreach($texts as $text) {
								                        if($text instanceof \PhpOffice\PhpWord\Element\Text) {
								                            $texto .= $text->getText();
								                        }
								                    }
								                }
								            }
								        }
										preg_match_all('/\${([^}]+)\}/', $texto, $matches);
										$variaveis = array();
										foreach($matches[1] as $variavel) {
											if(!in_array($variavel, $variaveis)) {
												if($variavel == 'data') {
?>
													<input type="hidden" name="data" value="<?php echo date('d/m/Y'); ?>">
<?php
												}
												else {
?>
													<div class="form-floating mb-3">
													  	<input type="text" id="<?php echo $variavel; ?>" class="form-control" placeholder="<?php echo $variavel; ?>" name="<?php echo $variavel; ?>">
													  	<label for="<?php echo $variavel; ?>"><?php echo $variavel; ?></label>
													</div>
<?php
										    	}
										    }
										    $variaveis[] = $variavel;
										}
?>
								        <input type="hidden" name="gerar_documento" value="<?php echo $_GET['file']; ?>">
										<button type="submit" class="w-100">GERAR DOCUMENTO <i class="fa-solid fa-file"></i></button>
								    </form>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php footer(); ?>
			</div>
		</div>
<?php
	}
	elseif(isset($_GET['pasta'])) {
		$selecao = "SELECT * from pastas WHERE id = :id AND id_usuario = :id_usuario";
        try {
            $resultado = $bdd->prepare($selecao);
            $resultado->bindParam(':id', $_GET['pasta'], PDO::PARAM_STR);
            $resultado->bindParam(':id_usuario', $usuario_logado_id, PDO::PARAM_STR);
            $resultado->execute();
            $contador = $resultado->rowCount();
            if($contador > 0) {
                $loop = $resultado->fetchAll();
                foreach($loop as $conteudo) {
                    $pasta_nome = $conteudo['nome'];
                }
            }
            else {
            	header("Location: ".PATH.'modulos/documentos/');
            }
        }
        catch(PDOException $e) {
            echo $e;
        }
?>
		<div class="d-flex min-h-100">
			<?php sidebar(); ?>
			<div class="body">
<?php
				topbar(array(
					'<li class="d-none d-lg-inline-flex"><a href="'.PATH.'modulos/documentos/"><i class="fa-solid fa-home"></i> INÍCIO</a></li>',
					'<li class="d-none d-lg-inline-flex"><a data-bs-toggle="modal" data-bs-target="#anexar_novo_arquivo"><i class="fa-solid fa-circle-plus"></i> ANEXAR NOVO ARQUIVO</a></li>'
				));
?>
				<div class="container">
					<div class="row">
						<div class="col-lg-12">
							<div class="page">
								<h3 class="mb-4"><?php echo $pasta_nome; ?></h3>
								<div id="arquivos" class="folders sortable">
<?php
									$selecao = "SELECT * from arquivos WHERE id_pasta = :id_pasta AND id_usuario = :id_usuario ORDER BY ordem ASC";
			                        try {
			                            $resultado = $bdd->prepare($selecao);
			                            $resultado->bindParam(':id_pasta', $_GET['pasta'], PDO::PARAM_STR);
			                            $resultado->bindParam(':id_usuario', $usuario_logado_id, PDO::PARAM_STR);
			                            $resultado->execute();
			                            $contador = $resultado->rowCount();
			                            if($contador > 0) {
			                            	$modal_edicao = array();
			                            	$modal_exclusao = array();
			                                while($conteudo = $resultado->FETCH(PDO::FETCH_OBJ)) {
			                                	$file_nome = explode('.', $conteudo->file);
			                                	$file_nome = $conteudo->nome.'.'.$file_nome[1];
?>
												<div id="<?php echo $conteudo->id; ?>" class="folder">
													<div class="float-buttons">
														<div class="dropdown">
										                    <a class="dropdown-toggle text-white bg-secondary" data-bs-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></a>
															<ul class="dropdown-menu">
																<li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="<?php echo '#editar_'.$conteudo->id; ?>"><i class="fa-solid fa-edit"></i> Editar</a></li>
																<li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="<?php echo '#excluir_'.$conteudo->id; ?>"><i class="fa-solid fa-trash"></i> Remover</a></li>
															</ul>
														</div>
													</div>
													<a <?php if($conteudo->form == 'on') { echo 'href="'.PATH.'modulos/documentos/?file='.$conteudo->id.'"'; } else { echo 'href="'.PATH.'assets/files/'.$conteudo->file.'" download="'.$file_nome.'"'; } ?>>
														<img src="https://images.freeimages.com/fic/images/icons/1011/shined/250/file.png">
														<span><?php echo $conteudo->nome; ?></span>
													</a>
												</div>
<?php
												$modal_edicao[] = '
													<div class="modal fade" id="editar_'.$conteudo->id.'">
												        <div class="modal-dialog modal-dialog-centered">
												            <div class="modal-content">
												            	<div class="modal-header">
												                    <h5 class="modal-title">Editar: '.$conteudo->nome.'</h5>
												                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
												                </div>
												                <div class="modal-body">
												                	<form method="POST">
												                		<div class="form-floating mb-3">
																		  	<input type="text" id="nome" class="form-control" placeholder="Digite um nome" name="nome" value="'.$conteudo->nome.'" required>
																		  	<label for="nome">Nome</label>
																		</div>
												                		<input type="hidden" name="atualizar_arquivo" value="'.$conteudo->id.'">
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
												                    <h5 class="modal-title">Excluir: '.$conteudo->nome.'</h5>
												                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
												                </div>
												                <div class="modal-body">
												                	<form method="POST">
												                		<input type="hidden" name="excluir_arquivo" value="'.$conteudo->id.'">
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
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php footer(); ?>
			</div>
		</div>
		<div class="modal fade" id="anexar_novo_arquivo">
	        <div class="modal-dialog modal-dialog-centered">
	            <div class="modal-content">
	            	<div class="modal-header">
	                    <h5 class="modal-title">Novo Arquivo</h5>
	                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
	                </div>
	                <div class="modal-body">
	                	<form method="POST" enctype="multipart/form-data">
							<div class="form-floating mb-3">
								<input type="file" class="form-control" name="file[]" multiple required>
							</div>
	                		<input type="hidden" name="anexar_novo_arquivo" value="<?php echo $_GET['pasta']; ?>">
							<button type="submit" class="w-100">ANEXAR <i class="fa-solid fa-circle-plus"></i></button>
	                	</form>
	                </div>
	            </div>
	        </div>
	    </div>
<?php
		foreach($modal_edicao as $modal) {
			echo $modal;
		}
		foreach($modal_exclusao as $modal) {
			echo $modal;
		}
	}
	else {
?>
		<div class="d-flex min-h-100">
			<?php sidebar(); ?>
			<div class="body">
<?php
				topbar(array(
					'<li class="d-none d-lg-inline-flex"><a href="'.PATH.'modulos/documentos/"><i class="fa-solid fa-home"></i> INÍCIO</a></li>',
					'<li class="d-none d-lg-inline-flex"><a data-bs-toggle="modal" data-bs-target="#criar_nova_pasta"><i class="fa-solid fa-circle-plus"></i> CRIAR NOVA PASTA</a></li>'
				));
?>
				<div class="container">
					<div class="row">
						<div class="col-lg-12">
							<div class="page">
								<h3 class="mb-4">Pastas!</h3>
								<div id="pastas" class="folders sortable">
<?php
									$selecao = "SELECT * from pastas WHERE id_usuario = :id_usuario ORDER BY ordem ASC";
			                        try {
			                            $resultado = $bdd->prepare($selecao);
			                            $resultado->bindParam(':id_usuario', $usuario_logado_id, PDO::PARAM_STR);
			                            $resultado->execute();
			                            $contador = $resultado->rowCount();
			                            if($contador > 0) {
			                            	$modal_edicao = array();
			                            	$modal_exclusao = array();
			                                while($conteudo = $resultado->FETCH(PDO::FETCH_OBJ)) {
?>
												<div id="<?php echo $conteudo->id; ?>" class="folder">
													<div class="float-buttons">
														<div class="dropdown">
										                    <a class="dropdown-toggle text-white bg-secondary" data-bs-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></a>
															<ul class="dropdown-menu">
																<li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="<?php echo '#editar_'.$conteudo->id; ?>"><i class="fa-solid fa-edit"></i> Editar</a></li>
																<li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="<?php echo '#excluir_'.$conteudo->id; ?>"><i class="fa-solid fa-trash"></i> Remover</a></li>
															</ul>
														</div>
													</div>
													<a href="<?php echo PATH.'modulos/documentos/?pasta='.$conteudo->id; ?>">
														<img src="https://png.pngtree.com/png-vector/20220823/ourmid/pngtree-folder-icon-sign-data-vector-png-image_33404338.png">
														<span><?php echo $conteudo->nome; ?></span>
													</a>
												</div>
<?php
												$modal_edicao[] = '
													<div class="modal fade" id="editar_'.$conteudo->id.'">
												        <div class="modal-dialog modal-dialog-centered">
												            <div class="modal-content">
												            	<div class="modal-header">
												                    <h5 class="modal-title">Editar: '.$conteudo->nome.'</h5>
												                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
												                </div>
												                <div class="modal-body">
												                	<form method="POST">
												                		<div class="form-floating mb-3">
																		  	<input type="text" id="nome" class="form-control" placeholder="Digite um nome" name="nome" value="'.$conteudo->nome.'" required>
																		  	<label for="nome">Nome</label>
																		</div>
												                		<input type="hidden" name="atualizar_pasta" value="'.$conteudo->id.'">
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
												                    <h5 class="modal-title">Excluir: '.$conteudo->nome.'</h5>
												                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
												                </div>
												                <div class="modal-body">
												                	<form method="POST">
												                		<input type="hidden" name="excluir_pasta" value="'.$conteudo->id.'">
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
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php footer(); ?>
			</div>
		</div>
		<div class="modal fade" id="criar_nova_pasta">
	        <div class="modal-dialog modal-dialog-centered">
	            <div class="modal-content">
	            	<div class="modal-header">
	                    <h5 class="modal-title">Nova Pasta</h5>
	                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
	                </div>
	                <div class="modal-body">
	                	<form method="POST">
	                		<div class="form-floating mb-3">
							  	<input type="text" id="nome" class="form-control" placeholder="Digite um nome" name="nome" required>
							  	<label for="nome">Nome</label>
							</div>
	                		<input type="hidden" name="criar_nova_pasta">
							<button type="submit" class="w-100">CRIAR <i class="fa-solid fa-circle-plus"></i></button>
	                	</form>
	                </div>
	            </div>
	        </div>
	    </div>
<?php
		foreach($modal_edicao as $modal) {
			echo $modal;
		}
		foreach($modal_exclusao as $modal) {
			echo $modal;
		}
	}
	last();
?>
</body>
</html>