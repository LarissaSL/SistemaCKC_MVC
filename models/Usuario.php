<?php

require_once 'Config/Conexao.php';

class Usuario
{
    protected $conexao;

    public function __construct()
    {
        $bancoDeDados = new Conexao();
        $this->conexao = $bancoDeDados->getConexao();
        
    }

    public function inserirUsuario($tipo, $nome, $sobrenome, $cpf, $email, $senha, $peso, $dataNascimento, $genero, $telefone)
    {
        $tipoUsuario = ($tipo == 'Administrador') ? 'Administrador' : 'Comum';
        try {
            $inserir = $this->conexao->prepare("INSERT INTO usuario (Tipo, Nome, Sobrenome, Cpf, Email, Senha, Peso, Data_nascimento, Genero, Telefone) VALUES (:tipo, :nome, :sobrenome, :cpf, :email, :senha, :peso, :data_nascimento, :genero, :telefone)");
            $inserir->bindValue(':tipo', $tipoUsuario);
            $inserir->bindValue(':nome', $nome);
            $inserir->bindValue(':sobrenome', $sobrenome);
            $inserir->bindValue(':cpf', $cpf);
            $inserir->bindValue(':email', $email);
            $inserir->bindValue(':senha', $senha);
            $inserir->bindValue(':peso', $peso);
            $inserir->bindValue(':data_nascimento', $dataNascimento);
            $inserir->bindValue(':genero', $genero);
            $inserir->bindValue(':telefone', $telefone);
            $inserir->execute();
            return true; 
        } catch (PDOException $erro) {
            echo "Erro no cadastro: " . $erro->getMessage();
            return false; 
        }
    }

    public function atualizarUsuario($id, $nome, $sobrenome, $cpf, $email, $peso, $dataNascimento, $genero, $telefone)
    {
        try {
            $atualizar = $this->conexao->prepare("UPDATE usuario SET Nome = :nome, Sobrenome = :sobrenome, Cpf = :cpf, Email = :email, Peso = :peso, Data_nascimento = :data_nascimento, Genero = :genero, Telefone = :telefone WHERE id = :id");
            $atualizar->bindValue(':nome', $nome);
            $atualizar->bindValue(':sobrenome', $sobrenome);
            $atualizar->bindValue(':cpf', $cpf);
            $atualizar->bindValue(':email', $email);
            $atualizar->bindValue(':peso', $peso);
            $atualizar->bindValue(':data_nascimento', $dataNascimento);
            $atualizar->bindValue(':genero', $genero);
            $atualizar->bindValue(':telefone', $telefone);
            $atualizar->bindValue(':id', $id);
            $atualizar->execute();
            return "atualizado"; 
        } catch (PDOException $erro) {
            $codigoDoErro = $erro->getCode();

            switch($codigoDoErro)
            {
                case 23000:
                    return "Ocorreu um erro. CPF ou E-mail já registrados no Sistema. Por favor, tente novamente.";
                    break;
                default:
                    return $erro;
                    break;
            }     
        }
    }

    public function inserirFoto($foto, $id)
    {
        try {
            //Ver se o usuario já tem uma foto de perfil , se sim excluir ela no if
            $usuarioModel = new Usuario();
            $imagem = new Imagem();
            $usuarioAntigo = $usuarioModel->consultarUsuarioPorId($id);

            if ($usuarioAntigo && isset($usuarioAntigo['Foto'])) {
                $nomeArquivoAntigo = basename($usuarioAntigo['Foto']);
                $caminho = "./views/Img/ImgUsuario/" . $nomeArquivoAntigo;
                $imagem->excluirImagem($caminho);
            }
            
            $novoNomeDaImagem = basename($foto);

            // Salvar o nome da imagem no banco de dados
            $inserir = $this->conexao->prepare("UPDATE usuario SET Foto = :foto WHERE Id = :id");
            $inserir->bindValue(':foto', $novoNomeDaImagem);
            $inserir->bindValue(':id', $id);
            $inserir->execute();

            // Mover a imagem para a pasta de destino (Views, Img, ImgUsuario)
            $caminhoCompleto = "./views/Img/ImgUsuario/" . $novoNomeDaImagem;
            if (move_uploaded_file($foto, $caminhoCompleto)) {
                return true; 
            } else {
                return false; 
            }
        } catch (PDOException $erro) {
            echo "Erro na inserção: " . $erro->getMessage();
            return false; 
        }
    }

    public function consultarTodosOsUsuarios()
    {
        try {
            $consulta = $this->conexao->prepare("SELECT * FROM usuario");
            $consulta->execute();
            $usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);
            return $usuarios;
        } catch (PDOException $erro) {
            echo "Erro na consulta: " . $erro->getMessage();
            return null;
        }
    }

    public function consultarUsuarioPorId($id)
    {
        try {
            $consulta = $this->conexao->prepare("SELECT * FROM usuario WHERE Id = :id");
            $consulta->bindValue(':id', $id);
            $consulta->execute();
            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                return $resultado;
            } else {
                return false;
            }
        } catch (PDOException $erro) {
            echo "Erro na consulta: " . $erro->getMessage();
            return false;
        }
    }

    public function excluirUsuarioPorId($id)
    {
        try {
            $consulta = $this->conexao->prepare("DELETE FROM usuario WHERE Id = :id");
            $consulta->bindValue(':id', $id);
            $consulta->execute();
            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                return "Usuário excluido com sucesso!";
            } else {
                return null;
            }
        } catch (PDOException $erro) {
            echo "Erro na exclusão " . $erro->getMessage();
            return null;
        }
    }

    public function consultarUsuarioPorEmail($email)
    {
        try {
            $consulta = $this->conexao->prepare("SELECT * FROM usuario WHERE Email = :email");
            $consulta->bindValue(':email', $email);
            $consulta->execute();
            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                return $resultado;
            } else {
                return null;
            }
        } catch (PDOException $erro) {
            echo "Erro na consulta: " . $erro->getMessage();
            return null;
        }
    }

    public function validarCpf($cpf, $acao) 
    {
        $statusDaValidacao = "aceito";

        // Verifica se a formatação inserida do CPF é válida (tam 11 e apenas números)
        if (!ctype_digit($cpf)) 
        {
            return $statusDaValidacao = "Digite o CPF apenas com números<br>Ex.: 11122233344.";
        }

        if(strlen($cpf) !== 11 ) 
        {
            return $statusDaValidacao = "O CPF precisa ter 11 dígitos."; 
        }

        // Verifica se o CPF do usuário já foi cadastrado
        try 
        {
            $consulta = $this->conexao->prepare("SELECT COUNT(*) AS total FROM usuario WHERE Cpf = :cpf");
            $consulta->bindValue(':cpf', $cpf);
            $consulta->execute();
            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

            // Se a ação for de cadastro e o CPF já estiver cadastrado, retorna uma mensagem de erro
            if ($resultado['total'] > 0 && $acao === 'cadastrar') {
                return $statusDaValidacao = "CPF já cadastrado no Sistema";
            } 
        } 
        catch (PDOException $erro) {
            return $statusDaValidacao = "Erro na consulta: " . $erro->getMessage();
        }
        
        return $statusDaValidacao;
    }

    public function validarEmail($email, $confirmarEmail, $acao) {
        $statusDaValidacao = "aceito";

        // Verifica se a formatação inserida do Email tem o @
        if (strpos($email, "@") == false) {
            return $statusDaValidacao = "Digite um e-mail válido";
        }

        if ($email !== $confirmarEmail) {
            return $statusDaValidacao = "Os e-mails devem ser idênticos";
        }

        // Verifica se o E-mail do usuário já foi Cadastrado
        try {
            $consulta = $this->conexao->prepare("SELECT COUNT(*) AS total FROM usuario WHERE Email = :email");
            $consulta->bindValue(':email', $email);
            $consulta->execute();
            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
            
            if($resultado['total'] > 0 && $acao === 'cadastrar') {
                return $statusDaValidacao = "E-mail já cadastrado no Sistema";
            }
        } catch (PDOException $erro) {
            return $statusDaValidacao = "Erro na consulta: ".$erro->getMessage();
        }
        
        return $statusDaValidacao;
    }

    public function validarSenha($senha, $confirmarSenha) 
    {
        if ($senha !== $confirmarSenha) {
            return $statusDaValidacao = "As senhas devem ser idênticas";
        }
        return "aceito";
    }

    public function criptografarSenha($senha) 
    {
        return $senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);
    }

    function formatarTelefone($telefone) 
    {
        // Deixando apenas os números
        $telefone = preg_replace('/\D/', '', $telefone);
    
        if (strlen($telefone) === 11) {
            // Formata o número de telefone para (NN) NNNNN-NNNN
            return '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 5) . '-' . substr($telefone, 7);
        } else {
            // Pra caso o Usuário já tenha digitado nesse formato
            return $telefone;
        }
    }

    public function autentificar($email, $senha) 
    {
        $usuario = $this->consultarUsuarioPorEmail($email);

        if($usuario !== null) {
            if(password_verify($senha, $usuario['Senha'])){
                return "Sucesso";
            } else {
                return "Senha inválida";
            }
        } else {
            return "Email inválido";
        }
    }

    public function trocarSenha($id, $senha)
    {
        try {
            $senhaCriptografada = $this->criptografarSenha($senha);
            $atualizarSenha = $this->conexao->prepare("UPDATE usuario SET Senha = :senha WHERE id = :id");
            $atualizarSenha->bindValue(':senha', $senhaCriptografada);
            $atualizarSenha->bindValue(':id', $id);
            $atualizarSenha->execute();
            return "Senha atualizada com sucesso";
        } catch (PDOException $erro) {
            return "Erro ao atualizar a senha: " . $erro->getMessage();
        }
    }

    public function consultarUsuariosComFiltro($busca, $tipo)
    {
        try {
            $sql = "SELECT * FROM usuario WHERE 1";

            if (!empty($busca)) {
                $sql .= " AND (Nome LIKE :busca OR Sobrenome LIKE :busca)";
            }

            // Adicionado sobre o tipo do usuário (Comum ou ADM)
            if (!empty($tipo)) {
                $sql .= " AND Tipo = :tipo";
            }

            $consulta = $this->conexao->prepare($sql);

            if (!empty($busca)) {
                $buscaParam = "%{$busca}%"; 
                $consulta->bindValue(':busca', $buscaParam);
            }
            if (!empty($tipo)) {
                $consulta->bindValue(':tipo', $tipo);
            }

            $consulta->execute();

            return array(
                'usuarios' => $consulta->fetchAll(PDO::FETCH_ASSOC),
                'feedback' => 'Consulta realizada com sucesso.',
                'classe' => 'alert alert-success'
            );
        } 
        catch (PDOException $erro) 
        {
            return array(
                'usuarios' => array(),
                'feedback' => "Erro na consulta: " . $erro->getMessage(),
                'classe' => "alert alert-danger"
            );
        }
    }
}

?>
