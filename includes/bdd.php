<?php
	//Raiz
	define('PATH', '/sistema/');
	define('ROOT', $_SERVER['DOCUMENT_ROOT'].PATH);
	//-----------------------------------------------------------------------------------

	//Imprimir erros
	ini_set('display_errors', 1);
	//-----------------------------------------------------------------------------------

	//Configs
    ob_start();
    session_start();
    setlocale( LC_ALL, 'pt_BR.utf-8', 'pt_BR', 'Portuguese_Brazil');
    date_default_timezone_set('America/Sao_Paulo');
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
    //-----------------------------------------------------------------------------------

    //Uses
    use PhpOffice\PhpWord\IOFactory;
    //-----------------------------------------------------------------------------------

    //Conexão com o banco de dados
    try {
        $bdd = new PDO('mysql:host=localhost;dbname=look9131_tecnolhar_sistema;charset=utf8', 'look9131_tecnolhar_sistema', 'xX40028922');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $error) {
        echo $error->getMessage();
    }
    //-----------------------------------------------------------------------------------

	//Phpoffice
	include_once(ROOT.'addons/phpoffice/vendor/autoload.php');
	//-----------------------------------------------------------------------------------

	//Criar variável para o usuário logado e puxar informações do usuário
    if(isset($_SESSION['email_'.PATH]) OR isset($_COOKIE['email_'.PATH]) AND isset($_SESSION['senha_'.PATH]) OR isset($_COOKIE['senha_'.PATH])) {
        if(isset($_SESSION['email_'.PATH])) {
            $usuario_logado = '';
            $usuario_logado_email = $_SESSION['email_'.PATH];
            $usuario_logado_senha = $_SESSION['senha_'.PATH];
        }
        else {
            $usuario_logado = '';
            $usuario_logado_email = $_COOKIE['email_'.PATH];
            $usuario_logado_senha = $_COOKIE['senha_'.PATH];
        }
        $selecao = "SELECT * from usuarios WHERE email = :email AND senha = :senha";
        try {
            $resultado = $bdd->prepare($selecao);
            $resultado->bindParam(':email', $usuario_logado_email, PDO::PARAM_STR);
            $resultado->bindParam(':senha', $usuario_logado_senha, PDO::PARAM_STR);
            $resultado->execute();
            $contador = $resultado->rowCount();
            if($contador > 0) {
                $loop = $resultado->fetchAll();
                foreach($loop as $conteudo) {
                    $usuario_logado_id = $conteudo['id'];
                    $usuario_logado_nome = $conteudo['nome'];
                }
            }
            else {
            	foreach($_COOKIE as $name => $value) {
		            setcookie($name, '', -1, PATH);
		        }
		        session_destroy();
		        header('Location: '.PATH.'entrar/');
            }
        }
        catch(PDOException $e) {
            echo $e;
        }
    }
	//-----------------------------------------------------------------------------------

	//Controle de acessos dos arquivos
	if(basename($_SERVER['PHP_SELF'],'.php') == 'atualizar-email') {
        if(!isset($usuario_logado)) {
            header('Location: '.PATH);
        }
    }
    if(basename($_SERVER['PHP_SELF'],'.php') == 'atualizar-senha') {
        if(!isset($usuario_logado)) {
            header('Location: '.PATH);
        }
    }
    if(basename($_SERVER['PHP_SELF'],'.php') == 'index') {
    	if(isset($usuario_logado)) {
            header('Location: '.PATH.'modulos/dashboard/');
        }
    }
    if(basename($_SERVER['PHP_SELF'],'.php') == 'minha-conta') {
        if(!isset($usuario_logado)) {
            header('Location: '.PATH);
        }
    }
    if(basename($_SERVER['PHP_SELF'],'.php') == 'bloco-de-notas') {
        if(!isset($usuario_logado)) {
            header('Location: '.PATH);
        }
    }
    if(basename($_SERVER['PHP_SELF'],'.php') == 'dashboard') {
        if(!isset($usuario_logado)) {
            header('Location: '.PATH);
        }
    }
    //-----------------------------------------------------------------------------------

    //Formatar data
    function formatar_data($data = '') {
    	if(!empty($data)) {
        	$data = strtotime($data);
        	return $data = date('d/m/Y', $data);
        }
    }
    //-----------------------------------------------------------------------------------

    //Formatar valor em reais
    function formatar_reais($valor) {
        return 'R$'.number_format($valor, 2, ',', '.');
    }
    //-----------------------------------------------------------------------------------

    //Valor financeiro
    function valor_financeiro($valor, $situacao) {
    	$valor = formatar_reais($valor);
    	if($situacao == 1) {
    		return '<span class="text-green"><i class="fa-solid fa-circle-plus"></i> '.$valor.'</span>';
    	}
    	elseif($situacao == 2) {
    		return '<span class="text-green"><i class="fa-solid fa-money-bill-transfer"></i> '.$valor.'</span>';
    	}
    	elseif($situacao == 3) {
    		return '<span class="text-red"><i class="fa-solid fa-circle-minus"></i> '.$valor.'</span>';
    	}
    	elseif($situacao == 4) {
    		return '<span class="text-red"><i class="fa-solid fa-money-bill-transfer"></i> '.$valor.'</span>';
    	}
    }
    //-----------------------------------------------------------------------------------

    //Situação financeiro
    function situacao_financeiro($option) {
    	if($option == 1) {
    		return '<span class="situacao text-green bg-green">Á Receber</span>';
    	}
    	elseif($option == 2) {
    		return '<span class="situacao text-green bg-green">Recebido</span>';
    	}
    	elseif($option == 3) {
    		return '<span class="situacao text-red bg-red">Á Pagar</span>';
    	}
    	elseif($option == 4) {
    		return '<span class="situacao text-red bg-red">Pago</span>';
    	}
    }
    //-----------------------------------------------------------------------------------

    //Select situação financeiro
    function select_situacao_financeiro($option = '') {
    	ob_start();
?>
		<select id="situacao" class="form-select" name="situacao" required>
			<option <?php if(empty($option)) { echo 'selected'; } ?> value disabled>Selecione uma opção</option>
			<option value="1" <?php if(!empty($option) AND $option == 1) { echo 'selected'; } ?>>Á Receber</option>
			<option value="2" <?php if(!empty($option) AND $option == 2) { echo 'selected'; } ?>>Recebido</option>
			<option value="3" <?php if(!empty($option) AND $option == 3) { echo 'selected'; } ?>>Á Pagar</option>
			<option value="4" <?php if(!empty($option) AND $option == 4) { echo 'selected'; } ?>>Pago</option>
		</select>
		<label for="situacao">Situação</label>
<?php
    	$html = ob_get_clean();
    	return $html;
    }
    //-----------------------------------------------------------------------------------

    //Somar valores financeiro
    function somar_valores_financeiro($situacao) {
    	$selecao = "SELECT SUM(valor) AS total FROM financeiro WHERE situacao = :situacao AND id_usuario = :usuario_logado_id";
		$resultado = $GLOBALS['bdd']->prepare($selecao);
		$resultado->bindParam(':situacao', $situacao, PDO::PARAM_STR);
		$resultado->bindParam(':usuario_logado_id', $GLOBALS['usuario_logado_id'], PDO::PARAM_INT);
		$resultado->execute();
		$soma = $resultado->fetchColumn();
		if($soma == null) {
			$soma = 0;
		}
		return $soma;
    }
    //-----------------------------------------------------------------------------------

	//Head
	function head() {
?>
		<head>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<title>Tecnolhar Sistema</title>
			<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-proxima-nova@1.0.1/style.min.css">
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
			<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
			<link rel="stylesheet" type="text/css" href="<?php echo PATH.'assets/css/style.css?'.rand(); ?>">
		</head>
<?php
	}
	//-----------------------------------------------------------------------------------

	//Sidebar
	function sidebar() {
?>
		<div class="sidebar">
			<div class="text-center">
				<img src="https://www.tecnolhar.com.br/sistema/assets/img/geral/logo.png" class="logo">
			</div>
			<hr>
			<ul>
				<li><a href="<?php echo PATH.'modulos/dashboard/'; ?>"><i class="fa-solid fa-home"></i> DASHBOARD</a></li>
				<li><a href="<?php echo PATH.'modulos/bloco-de-notas/'; ?>"><i class="fa-solid fa-calendar"></i> BLOCO DE NOTAS</a></li>
				<li><a href="<?php echo PATH.'modulos/agenda/'; ?>"><i class="fa-solid fa-calendar-days"></i> AGENDA</a></li>
				<li><a href="<?php echo PATH.'modulos/clientes/'; ?>"><i class="fa-solid fa-users"></i> CLIENTES</a></li>
				<li><a href="<?php echo PATH.'modulos/documentos/'; ?>"><i class="fa-solid fa-file"></i> DOCUMENTOS</a></li>
				<li><a href="https://meet.google.com/new" target="_Blank"><i class="fa-solid fa-video"></i> VÍDEO CONFÊRENCIA</a></li>
				<li><a href="<?php echo PATH.'modulos/financeiro/'; ?>"><i class="fa-solid fa-money-bill-trend-up"></i> FINANCEIRO</a></li>
			</ul>
		</div>
<?php
	}
	//-----------------------------------------------------------------------------------

	//Topbar
	function topbar($paginas = '') {
?>
		<div class="topbar">
			<div class="container">
				<div class="row">
					<div class="col-lg-12 d-flex align-items-center justify-content-between">
						<ul>
							<li class="d-lg-none"><a class="sidebar_toggle"><i class="fa-solid fa-circle-plus"></i> MENU</a></li>
<?php
							if(!empty($paginas)) {
								foreach($paginas as $pagina) {
									echo $pagina;
								}
							}
?>
						</ul>
						<ul>
							<li>
								<div class="dropdown">
				                    <a class="dropdown-toggle" data-bs-toggle="dropdown">
										<span class="me-2"><?php echo 'Olá '.$GLOBALS['usuario_logado_nome']; ?></span>
										<img src="https://www.tecnolhar.com.br/sistema/assets/img/geral/no-photo.png" width="25" height="25" class="rounded-circle">
									</a>
									<ul class="dropdown-menu">
										<li><a class="dropdown-item" href="<?php echo PATH.'minha-conta/'; ?>"><i class="fa-solid fa-user"></i> Minha Conta</a></li>
										<li><a class="dropdown-item sair"><i class="fa-solid fa-right-from-bracket"></i> Sair da Conta</a></li>
									</ul>
								</div>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
<?php
	}
	//-----------------------------------------------------------------------------------

	//Footer
	function footer() {
?>
		<div class="footer">
			<div class="container">
				<div class="row">
					<div class="col-lg-12 text-center">
						<p>Copyright © <?php date('Y'); ?> Tecnolhar. Todos os direitos reservados.</p>
					</div>
				</div>
			</div>
		</div>
<?php
	}
	//-----------------------------------------------------------------------------------

	//End
	function last() {
?>
		<script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js" integrity="sha512-pHVGpX7F/27yZ0ISY+VVjyULApbDlD0/X0rgGbTqCE7WFW5MezNTWG/dnhtbBuICzsd0WQPgpE4REBLv+UqChw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
		<script type="text/javascript" src="<?php echo PATH.'assets/js/custom.js?'.rand(); ?>"></script>
<?php
	}
	//-----------------------------------------------------------------------------------

	//Gerar documento
	if(isset($_GET['gerar_documento'])) {
		$selecao = "SELECT * from arquivos WHERE id = :id AND id_usuario = :id_usuario";
        try {
            $resultado = $bdd->prepare($selecao);
            $resultado->bindParam(':id', $_GET['gerar_documento'], PDO::PARAM_STR);
            $resultado->bindParam(':id_usuario', $usuario_logado_id, PDO::PARAM_STR);
            $resultado->execute();
            $contador = $resultado->rowCount();
            if($contador > 0) {
                $loop = $resultado->fetchAll();
                foreach($loop as $conteudo) {
                    $arquivo_nome = $conteudo['nome'];
                    $arquivo_file = $conteudo['file'];
                }
			    $diretorioTemporario = realpath(__DIR__);
			    $arquivoTemporario = tempnam($diretorioTemporario, 'temp-docx-');
			    \PhpOffice\PhpWord\Settings::setTempDir($diretorioTemporario);
			    copy(ROOT.'assets/files/'.$arquivo_file, $arquivoTemporario);
			    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($arquivoTemporario);
			    foreach($_GET as $variavel => $valor) {
			    	$templateProcessor->setValue(str_replace('_', ' ', $variavel), $valor);
			    }
			    $templateProcessor->saveAs($arquivoTemporario);
			    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
			    header('Content-Disposition: attachment; filename='.$arquivo_nome.'.docx');
			    readfile($arquivoTemporario);
			    unlink($arquivoTemporario);
			    exit;
            }
        }
        catch(PDOException $e) {
            echo $e;
        }
	}
	//-----------------------------------------------------------------------------------
?>