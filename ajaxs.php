<?php
	//Json
    header('Content-Type: application/json');
    //-----------------------------------------------------------------------------------
    
	//Incluir arquivo bdd.php
	include_once('includes/bdd.php');
	//-----------------------------------------------------------------------------------

	//Entrar
	if(isset($_POST['entrar'])) {
		$selecao = "SELECT * from usuarios WHERE email = :email";
        try {
            $resultado = $bdd->prepare($selecao);
            $resultado->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
            $resultado->execute();
            $entcontar = $resultado->rowCount();
            if($entcontar > 0) {
                $contador = $resultado->fetchAll();
                foreach($contador as $conteudo) {
                    $entsenha = $conteudo['senha'];
                }
                if(password_verify($_POST['senha'], $entsenha)) {
                    if(empty($_POST['lembre_de_mim'])) {
                        $_SESSION['email_'.PATH] = $_POST['email'];
                        $_SESSION['senha_'.PATH] = $entsenha;
                    }
                    else {
                        setcookie('email_'.PATH, $_POST['email'], 2147483647, PATH);
                        setcookie('senha_'.PATH, $entsenha, 2147483647, PATH);
                    }
                    echo json_encode([
                        'sucesso' => 'Estamos entrando..',
                        'redirecionamento' => PATH.'modulos/dashboard/',
                        'atualizar' => ''
                    ]);
                }
                else {
                    echo json_encode(['erro' => 'A senha digitada não confere.']);
                }
            }
            else {
                echo json_encode(['erro' => 'O e-mail digitado não foi encontrado.']);
            }
        }
        catch(PDOException $error) {
            echo $error;
        }
	}
	//-----------------------------------------------------------------------------------

    //Sair da conta
    if(isset($_POST['sair'])) {
        foreach($_COOKIE as $name => $value) {
            setcookie($name, '', -1, PATH);
        }
        session_destroy();
        echo json_encode(['sucesso' => '']);
    }
    //-----------------------------------------------------------------------------------

    //Atualizar informações básicas
    if(isset($_POST['atualizar_informacoes_basicas'])) {
        $resultado = $bdd->prepare("UPDATE usuarios set nome = ? WHERE id = ?");
        if($resultado->execute(
            array(
                strip_tags($_POST['nome']),
                $usuario_logado_id
            ))) {
            echo json_encode([
                'sucesso' => '',
                'atualizar' => 'direto'
            ]);
        }
    }
    //-----------------------------------------------------------------------------------

    //Atualizar e-mail
    if(isset($_POST['atualizar_email'])) {
        if($_POST['email'] == $usuario_logado_email) {
            if($_POST['novo_email'] == $_POST['confirmar_novo_email']) {
                $resultado = $bdd->prepare("UPDATE usuarios set email = ? WHERE id = ?");
                if($resultado->execute(
                    array(
                        strip_tags($_POST['novo_email']),
                        $usuario_logado_id
                    ))) {
                    if(isset($_SESSION['email_'.PATH])) {
                        $_SESSION['email_'.PATH] = $_POST['novo_email'];
                    }
                    else {
                        setcookie('email_'.PATH, $_POST['novo_email'], 2147483647, PATH);
                    }
                    echo json_encode(['sucesso' => 'E-mail alterado com sucesso.']);
                }
            }
            else {
                echo json_encode(['erro' => 'E-mails digitados não são iguais.']);
            }
        }
        else {
            echo json_encode(['erro' => 'E-mail digitado não confere.']);
        }
    }
    //-----------------------------------------------------------------------------------

    //Atualizar senha
    if(isset($_POST['atualizar_senha'])) {
        if(password_verify($_POST['senha'], $usuario_logado_senha)) {
            if($_POST['nova_senha'] == $_POST['confirmar_nova_senha']) {
                $nova_senha = password_hash($_POST['nova_senha'], PASSWORD_DEFAULT);
                $resultado = $bdd->prepare("UPDATE usuarios set senha = ? WHERE id = ?");
                if($resultado->execute(
                    array(
                        $nova_senha,
                        $usuario_logado_id
                    ))) {
                    if(isset($_SESSION['senha_'.PATH])) {
                        $_SESSION['senha_'.PATH] = $nova_senha;
                    }
                    else {
                        setcookie('senha_'.PATH, $nova_senha, 2147483647, PATH);
                    }
                    echo json_encode(['sucesso' => 'Senha alterada com sucesso.']);
                }
            }
            else {
                echo json_encode(['erro' => 'Senhas digitados não são iguais.']);
            }
        }
        else {
            echo json_encode(['erro' => 'Senha digitado não confere.']);
        }
    }
    //-----------------------------------------------------------------------------------

    //Salvar ordenação bloco de notas
    if(isset($_POST['id']) AND $_POST['id'] == 'blocos_de_notas') {
        foreach($_POST['ordem'] as $posicao => $id) {
            $resultado = $bdd->prepare("UPDATE blocos_de_notas set ordem = ? WHERE id = ? AND id_usuario = ?");
            $resultado->execute(
                array(
                    $posicao,
                    $id,
                    $usuario_logado_id
                )
            );
        }
    }
    //-----------------------------------------------------------------------------------

    //Criar novo bloco de notas
    if(isset($_POST['criar_novo_bloco_de_notas'])) {
        $insercao = "INSERT into blocos_de_notas (titulo, conteudo, data_de_criacao, id_usuario) VALUES (:titulo, :conteudo, :data_de_criacao, :id_usuario)";
        try {
            $resultado = $bdd->prepare($insercao);
            $resultado->bindParam(':titulo' , strip_tags($_POST['titulo']), PDO::PARAM_STR);
            $resultado->bindParam(':conteudo' , strip_tags($_POST['conteudo']), PDO::PARAM_STR);
            $resultado->bindParam(':data_de_criacao' , date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $resultado->bindParam(':id_usuario' , $usuario_logado_id, PDO::PARAM_STR);
            $resultado->execute();
            $contador = $resultado->rowCount();
            if($contador > 0) {
                echo json_encode([
                    'sucesso' => '',
                    'atualizar' => 'direto'
                ]);
            }
        }
        catch(PDOException $e) {
            echo $e;
        }
    }
    //-----------------------------------------------------------------------------------

    //Atualizar bloco de notas
    if(isset($_POST['atualizar_bloco_de_notas'])) {
        $resultado = $bdd->prepare("UPDATE blocos_de_notas set titulo = ?, conteudo = ? WHERE id = ? AND id_usuario = ?");
        if($resultado->execute(
            array(
                strip_tags($_POST['titulo']),
                strip_tags($_POST['conteudo']),
                $_POST['atualizar_bloco_de_notas'],
                $usuario_logado_id
            ))) {
            echo json_encode(['sucesso' => '']);
        }
    }
    //-----------------------------------------------------------------------------------

    //Excluir bloco de notas
    if(isset($_POST['excluir_bloco_de_notas'])) {
        $resultado = $bdd->prepare('DELETE FROM blocos_de_notas WHERE id = :id AND id_usuario = :id_usuario');
        $resultado->bindParam(':id', $_POST['excluir_bloco_de_notas']);
        $resultado->bindParam(':id_usuario', $usuario_logado_id);
        if($resultado->execute()) {
            echo json_encode([
                'sucesso' => '',
                'atualizar' => 'direto'
            ]);
        }
    }
    //-----------------------------------------------------------------------------------

    //Puxar eventos da agenda
    if(isset($_POST['agenda'])) {
        $eventos = array();
        $selecao = "SELECT * from agenda WHERE id_usuario = :id_usuario";
        try {
            $resultado = $bdd->prepare($selecao);
            $resultado->bindParam(':id_usuario', $usuario_logado_id, PDO::PARAM_STR);
            $resultado->execute();
            $contador = $resultado->rowCount();
            if($contador > 0) {
                $eventos = array();
                while($conteudo = $resultado->FETCH(PDO::FETCH_OBJ)) {
                    $data_inicial = explode('-', $conteudo->data_inicial);
                    $data_inicial = $data_inicial[2].'/'.$data_inicial[1].'/'.$data_inicial[0];
                    $data_final = explode('-', $conteudo->data_final);
                    $data_final = $data_final[2].'/'.$data_final[1].'/'.$data_final[0];
                    $evento = array(
                        'id' => $conteudo->id,
                        'title' => $conteudo->titulo,
                        'description' => $conteudo->descricao,
                        'data_inicial' => $data_inicial,
                        'horario_inicial' => $conteudo->horario_inicial,
                        'data_final' => $data_final,
                        'horario_final' => $conteudo->horario_final,
                        'start' => $conteudo->data_inicial.' '.$conteudo->horario_inicial,
                        'end' => $conteudo->data_final.' '.$conteudo->horario_final
                    );
                    $eventos[] = $evento;
                }
                echo json_encode($eventos);
            }
        }
        catch(PDOException $e) {
            echo $e;
        }
    }
    //-----------------------------------------------------------------------------------

    //Agendar
    if(isset($_POST['agendar'])) {
        $data_inicial = explode('/', $_POST['data_inicial']);
        $data_inicial = $data_inicial[2].'-'.$data_inicial[1].'-'.$data_inicial[0];
        $data_final = explode('/', $_POST['data_final']);
        $data_final = $data_final[2].'-'.$data_final[1].'-'.$data_final[0];
        $insercao = "INSERT into agenda (titulo, descricao, data_inicial, horario_inicial, data_final, horario_final, data_de_criacao, id_usuario) VALUES (:titulo, :descricao, :data_inicial, :horario_inicial, :data_final, :horario_final, :data_de_criacao, :id_usuario)";
        try {
            $resultado = $bdd->prepare($insercao);
            $resultado->bindParam(':titulo' , strip_tags($_POST['titulo']), PDO::PARAM_STR);
            $resultado->bindParam(':descricao' , strip_tags($_POST['descricao']), PDO::PARAM_STR);
            $resultado->bindParam(':data_inicial' , $data_inicial, PDO::PARAM_STR);
            $resultado->bindParam(':horario_inicial' , strip_tags($_POST['horario_inicial']), PDO::PARAM_STR);
            $resultado->bindParam(':data_final' , $data_final, PDO::PARAM_STR);
            $resultado->bindParam(':horario_final' , strip_tags($_POST['horario_final']), PDO::PARAM_STR);
            $resultado->bindParam(':data_de_criacao' , date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $resultado->bindParam(':id_usuario' , $usuario_logado_id, PDO::PARAM_STR);
            $resultado->execute();
            $contador = $resultado->rowCount();
            if($contador > 0) {
                echo json_encode([
                    'sucesso' => '',
                    'atualizar' => 'direto'
                ]);
            }
        }
        catch(PDOException $e) {
            echo $e;
        }
    }
    //-----------------------------------------------------------------------------------

    //Atualizar agenda
    if(isset($_POST['atualizar_agenda'])) {
        $data_inicial = explode('/', $_POST['data_inicial']);
        $data_inicial = $data_inicial[2].'-'.$data_inicial[1].'-'.$data_inicial[0];
        $data_final = explode('/', $_POST['data_final']);
        $data_final = $data_final[2].'-'.$data_final[1].'-'.$data_final[0];
        $resultado = $bdd->prepare("UPDATE agenda set titulo = ?, descricao = ?, data_inicial = ?, horario_inicial = ?, data_final = ?, horario_final = ? WHERE id = ? AND id_usuario = ?");
        if($resultado->execute(
            array(
                strip_tags($_POST['titulo']),
                strip_tags($_POST['descricao']),
                $data_inicial,
                strip_tags($_POST['horario_inicial']),
                $data_final,
                strip_tags($_POST['horario_final']),
                $_POST['atualizar_agenda'],
                $usuario_logado_id
            ))) {
            echo json_encode([
                'sucesso' => '',
                'atualizar' => 'direto'
            ]);
        }
    }
    //-----------------------------------------------------------------------------------

    //Remover agenda
    if(isset($_POST['excluir_agenda'])) {
        $resultado = $bdd->prepare('DELETE FROM agenda WHERE id = :id AND id_usuario = :id_usuario');
        $resultado->bindParam(':id', $_POST['excluir_agenda']);
        $resultado->bindParam(':id_usuario', $usuario_logado_id);
        if($resultado->execute()) {
            echo json_encode([
                'sucesso' => '',
                'atualizar' => 'direto'
            ]);
        }
    }
    //-----------------------------------------------------------------------------------

    //Cadastrar novo cliente
    if(isset($_POST['cadastrar_novo_cliente'])) {
        $insercao = "INSERT into clientes (nome_razao_social, cpf_cnpj, telefone, email, cep, endereco, numero, complemento, bairro, estado, cidade, data_de_criacao, id_usuario) VALUES (:nome_razao_social, :cpf_cnpj, :telefone, :email, :cep, :endereco, :numero, :complemento, :bairro, :estado, :cidade, :data_de_criacao, :id_usuario)";
        try {
            $resultado = $bdd->prepare($insercao);
            $resultado->bindParam(':nome_razao_social' , strip_tags($_POST['nome_razao_social']), PDO::PARAM_STR);
            $resultado->bindParam(':cpf_cnpj' , strip_tags($_POST['cpf_cnpj']), PDO::PARAM_STR);
            $resultado->bindParam(':telefone' , strip_tags($_POST['telefone']), PDO::PARAM_STR);
            $resultado->bindParam(':email' , strip_tags($_POST['email']), PDO::PARAM_STR);
            $resultado->bindParam(':cep' , strip_tags($_POST['cep']), PDO::PARAM_STR);
            $resultado->bindParam(':endereco' , strip_tags($_POST['endereco']), PDO::PARAM_STR);
            $resultado->bindParam(':numero' , strip_tags($_POST['numero']), PDO::PARAM_STR);
            $resultado->bindParam(':complemento' , strip_tags($_POST['complemento']), PDO::PARAM_STR);
            $resultado->bindParam(':bairro' , strip_tags($_POST['bairro']), PDO::PARAM_STR);
            $resultado->bindParam(':estado' , strip_tags($_POST['estado']), PDO::PARAM_STR);
            $resultado->bindParam(':cidade' , strip_tags($_POST['cidade']), PDO::PARAM_STR);
            $resultado->bindParam(':data_de_criacao' , date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $resultado->bindParam(':id_usuario' , $usuario_logado_id, PDO::PARAM_STR);
            $resultado->execute();
            $contador = $resultado->rowCount();
            if($contador > 0) {
                echo json_encode([
                    'sucesso' => '',
                    'atualizar' => 'direto'
                ]);
            }
        }
        catch(PDOException $e) {
            echo $e;
        }
    }
    //-----------------------------------------------------------------------------------

    //Atualizar cliente
    if(isset($_POST['atualizar_cliente'])) {
        $resultado = $bdd->prepare("UPDATE clientes set nome_razao_social = ?, cpf_cnpj = ?, telefone = ?, email = ?, cep = ?, endereco = ?, numero = ?, complemento = ?, bairro = ?, estado = ?, cidade = ? WHERE id = ? AND id_usuario = ?");
        if($resultado->execute(
            array(
                strip_tags($_POST['nome_razao_social']),
                strip_tags($_POST['cpf_cnpj']),
                strip_tags($_POST['telefone']),
                strip_tags($_POST['email']),
                strip_tags($_POST['cep']),
                strip_tags($_POST['endereco']),
                strip_tags($_POST['numero']),
                strip_tags($_POST['complemento']),
                strip_tags($_POST['bairro']),
                strip_tags($_POST['estado']),
                strip_tags($_POST['cidade']),
                $_POST['atualizar_cliente'],
                $usuario_logado_id
            ))) {
            echo json_encode([
                'sucesso' => '',
                'atualizar' => 'direto'
            ]);
        }
    }
    //-----------------------------------------------------------------------------------

    //Remover cliente
    if(isset($_POST['excluir_cliente'])) {
        $resultado = $bdd->prepare('DELETE FROM clientes WHERE id = :id AND id_usuario = :id_usuario');
        $resultado->bindParam(':id', $_POST['excluir_cliente']);
        $resultado->bindParam(':id_usuario', $usuario_logado_id);
        if($resultado->execute()) {
            echo json_encode([
                'sucesso' => '',
                'atualizar' => 'direto'
            ]);
        }
    }
    //-----------------------------------------------------------------------------------

    //Salvar ordenação pasta
    if(isset($_POST['id']) AND $_POST['id'] == 'pastas') {
        foreach($_POST['ordem'] as $posicao => $id) {
            $resultado = $bdd->prepare("UPDATE pastas set ordem = ? WHERE id = ? AND id_usuario = ?");
            $resultado->execute(
                array(
                    $posicao,
                    $id,
                    $usuario_logado_id
                )
            );
        }
    }
    //-----------------------------------------------------------------------------------

    //Criar nova pasta
    if(isset($_POST['criar_nova_pasta'])) {
        $insercao = "INSERT into pastas (nome, data_de_criacao, id_usuario) VALUES (:nome, :data_de_criacao, :id_usuario)";
        try {
            $resultado = $bdd->prepare($insercao);
            $resultado->bindParam(':nome' , strip_tags($_POST['nome']), PDO::PARAM_STR);
            $resultado->bindParam(':data_de_criacao' , date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $resultado->bindParam(':id_usuario' , $usuario_logado_id, PDO::PARAM_STR);
            $resultado->execute();
            $contador = $resultado->rowCount();
            if($contador > 0) {
                echo json_encode([
                    'sucesso' => '',
                    'atualizar' => 'direto'
                ]);
            }
        }
        catch(PDOException $e) {
            echo $e;
        }
    }
    //-----------------------------------------------------------------------------------

    //Atualizar pasta
    if(isset($_POST['atualizar_pasta'])) {
        $resultado = $bdd->prepare("UPDATE pastas set nome = ? WHERE id = ? AND id_usuario = ?");
        if($resultado->execute(
            array(
                strip_tags($_POST['nome']),
                $_POST['atualizar_pasta'],
                $usuario_logado_id
            ))) {
            echo json_encode([
                'sucesso' => '',
                'atualizar' => 'direto'
            ]);
        }
    }
    //-----------------------------------------------------------------------------------

    //Remover pasta
    if(isset($_POST['excluir_pasta'])) {
        $selecao = "SELECT * from arquivos WHERE id_pasta = :id_pasta AND id_usuario = :id_usuario";
        try {
            $resultado = $bdd->prepare($selecao);
            $resultado->bindParam(':id_pasta', $_POST['excluir_pasta'], PDO::PARAM_STR);
            $resultado->bindParam(':id_usuario', $usuario_logado_id, PDO::PARAM_STR);
            $resultado->execute();
            $contador = $resultado->rowCount();
            if($contador > 0) {
                while($conteudo = $resultado->FETCH(PDO::FETCH_OBJ)) {
                    if(unlink(ROOT.'assets/files/'.$conteudo->file)) {
                        $resultado = $bdd->prepare('DELETE FROM arquivos WHERE id = :id AND id_usuario = :id_usuario');
                        $resultado->bindParam(':id', $conteudo->id);
                        $resultado->bindParam(':id_usuario', $usuario_logado_id);
                        $resultado->execute();
                    }
                }
            }
        }
        catch(PDOException $e) {
            echo $e;
        }
        $resultado = $bdd->prepare('DELETE FROM pastas WHERE id = :id AND id_usuario = :id_usuario');
        $resultado->bindParam(':id', $_POST['excluir_pasta']);
        $resultado->bindParam(':id_usuario', $usuario_logado_id);
        if($resultado->execute()) {
            echo json_encode([
                'sucesso' => '',
                'atualizar' => 'direto'
            ]);
        }
    }
    //-----------------------------------------------------------------------------------

    //Salvar ordenação arquivo
    if(isset($_POST['id']) AND $_POST['id'] == 'arquivos') {
        foreach($_POST['ordem'] as $posicao => $id) {
            $resultado = $bdd->prepare("UPDATE arquivos set ordem = ? WHERE id = ? AND id_usuario = ?");
            $resultado->execute(
                array(
                    $posicao,
                    $id,
                    $usuario_logado_id
                )
            );
        }
    }
    //-----------------------------------------------------------------------------------

    //Anexar novo arquivo
    if(isset($_POST['anexar_novo_arquivo'])) {
        $total_files = count($_FILES['file']['tmp_name']);
        foreach($_FILES['file']['tmp_name'] as $index => $tmp_name) {
            $file_nome = $_FILES['file']['name'][$index];
            $file_nome_sem_extensao = explode('.', $file_nome);
            $file_nome_sem_extensao = $file_nome_sem_extensao[0];
            $file_tmp = $_FILES['file']['tmp_name'][$index];
            if($_FILES['file']['error'][$index] == UPLOAD_ERR_OK) {
                $file_extensao = strtolower(pathinfo($file_nome, PATHINFO_EXTENSION));
                $file_novo_nome = uniqid().'.'.$file_extensao;
                if(move_uploaded_file($file_tmp, ROOT.'assets/files/'.$file_novo_nome)) {
                    if($file_extensao == 'docx') {
                        $texto = '';
                        \PhpOffice\PhpWord\Settings::setTempDir(ROOT.'assets/files/');
                        $phpWord = \PhpOffice\PhpWord\IOFactory::load(ROOT.'assets/files/'.$file_novo_nome);
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
                        if($matches[1] > 0) {
                            $form = 'on';
                        }
                        else {
                            $form = null;
                        }
                    }
                    else {
                        $form = null;
                    }
                    $insercao = "INSERT into arquivos (nome, file, form, id_pasta, data_de_criacao, id_usuario) VALUES (:nome, :file, :form, :id_pasta, :data_de_criacao, :id_usuario)";
                    try {
                        $resultado = $bdd->prepare($insercao);
                        $resultado->bindParam(':nome' , $file_nome_sem_extensao, PDO::PARAM_STR);
                        $resultado->bindParam(':file' , $file_novo_nome, PDO::PARAM_STR);
                        $resultado->bindParam(':form' , $form, PDO::PARAM_STR);
                        $resultado->bindParam(':id_pasta' , strip_tags($_POST['anexar_novo_arquivo']), PDO::PARAM_STR);
                        $resultado->bindParam(':data_de_criacao' , date('Y-m-d H:i:s'), PDO::PARAM_STR);
                        $resultado->bindParam(':id_usuario' , $usuario_logado_id, PDO::PARAM_STR);
                        $resultado->execute();
                        $contador = $resultado->rowCount();
                        if($contador > 0 && $index == ($total_files - 1)) {
                            echo json_encode([
                                'sucesso' => '',
                                'atualizar' => 'direto'
                            ]);
                        }
                    }
                    catch(PDOException $e) {
                        echo $e;
                    }
                }
            }
        }
    }
    //-----------------------------------------------------------------------------------

    //Atualizar arquivo
    if(isset($_POST['atualizar_arquivo'])) {
        $resultado = $bdd->prepare("UPDATE arquivos set nome = ? WHERE id = ? AND id_usuario = ?");
        if($resultado->execute(
            array(
                strip_tags($_POST['nome']),
                $_POST['atualizar_arquivo'],
                $usuario_logado_id
            ))) {
            echo json_encode([
                'sucesso' => '',
                'atualizar' => 'direto'
            ]);
        }
    }
    //-----------------------------------------------------------------------------------

    //Remover arquivo
    if(isset($_POST['excluir_arquivo'])) {
        $selecao = "SELECT * from arquivos WHERE id = :id AND id_usuario = :id_usuario";
        try {
            $resultado = $bdd->prepare($selecao);
            $resultado->bindParam(':id', $_POST['excluir_arquivo'], PDO::PARAM_STR);
            $resultado->bindParam(':id_usuario', $usuario_logado_id, PDO::PARAM_STR);
            $resultado->execute();
            $contador = $resultado->rowCount();
            if($contador > 0) {
                $loop = $resultado->fetchAll();
                foreach($loop as $conteudo) {
                    $file = $conteudo['file'];
                }
                if(unlink(ROOT.'assets/files/'.$file)) {
                    $resultado = $bdd->prepare('DELETE FROM arquivos WHERE id = :id AND id_usuario = :id_usuario');
                    $resultado->bindParam(':id', $_POST['excluir_arquivo']);
                    $resultado->bindParam(':id_usuario', $usuario_logado_id);
                    if($resultado->execute()) {
                        echo json_encode([
                            'sucesso' => '',
                            'atualizar' => 'direto'
                        ]);
                    }
                }
            }
        }
        catch(PDOException $e) {
            echo $e;
        }
    }
    //-----------------------------------------------------------------------------------

    //Cadastrar finança
    if(isset($_POST['cadastrar_financa'])) {
        $valor = str_replace('.', '', $_POST['valor']);
        $valor = str_replace(',', '.', $valor);
        if(!empty($_POST['data_de_vencimento'])) {
            $data_de_vencimento = str_replace('/', '-', $_POST['data_de_vencimento']);
            $data_de_vencimento = date('Y-m-d', strtotime($data_de_vencimento));
        }
        $insercao = "INSERT into financeiro (titulo, valor, data_de_vencimento, situacao, data_de_criacao, id_usuario) VALUES (:titulo, :valor, :data_de_vencimento, :situacao, :data_de_criacao, :id_usuario)";
        try {
            $resultado = $bdd->prepare($insercao);
            $resultado->bindParam(':titulo' , strip_tags($_POST['titulo']), PDO::PARAM_STR);
            $resultado->bindParam(':valor' , $valor, PDO::PARAM_STR);
            $resultado->bindParam(':data_de_vencimento' , $data_de_vencimento, PDO::PARAM_STR);
            $resultado->bindParam(':situacao' , strip_tags($_POST['situacao']), PDO::PARAM_STR);
            $resultado->bindParam(':data_de_criacao' , date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $resultado->bindParam(':id_usuario' , $usuario_logado_id, PDO::PARAM_STR);
            $resultado->execute();
            $contador = $resultado->rowCount();
            if($contador > 0) {
                echo json_encode([
                    'sucesso' => '',
                    'atualizar' => 'direto'
                ]);
            }
        }
        catch(PDOException $e) {
            echo $e;
        }
    }
    //-----------------------------------------------------------------------------------

    //Atualizar finança
    if(isset($_POST['atualizar_financa'])) {
        $valor = str_replace('.', '', $_POST['valor']);
        $valor = str_replace(',', '.', $valor);
        if(!empty($_POST['data_de_vencimento'])) {
            $data_de_vencimento = str_replace('/', '-', $_POST['data_de_vencimento']);
            $data_de_vencimento = date('Y-m-d', strtotime($data_de_vencimento));
        }
        $resultado = $bdd->prepare("UPDATE financeiro set titulo = ?, valor = ?, data_de_vencimento = ?, situacao = ? WHERE id = ? AND id_usuario = ?");
        if($resultado->execute(
            array(
                strip_tags($_POST['titulo']),
                $valor,
                $data_de_vencimento,
                strip_tags($_POST['situacao']),
                $_POST['atualizar_financa'],
                $usuario_logado_id
            ))) {
            echo json_encode([
                'sucesso' => '',
                'atualizar' => 'direto'
            ]);
        }
    }
    //-----------------------------------------------------------------------------------

    //Remover finança
    if(isset($_POST['excluir_financa'])) {
        $resultado = $bdd->prepare('DELETE FROM financeiro WHERE id = :id AND id_usuario = :id_usuario');
        $resultado->bindParam(':id', $_POST['excluir_financa']);
        $resultado->bindParam(':id_usuario', $usuario_logado_id);
        if($resultado->execute()) {
            echo json_encode([
                'sucesso' => '',
                'atualizar' => 'direto'
            ]);
        }
    }
    //-----------------------------------------------------------------------------------
?>