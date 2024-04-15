<?php

require_once 'Config/Conexao.php';

class Campeonato
{
    protected $conexao;

    public function __construct()
    {
        $bancoDeDados = new Conexao();
        $this->conexao = $bancoDeDados->getConexao();
    }

    public function inserirCampeonato($nome, $dataInicio, $dataTermino)
    {
        try {
            $queryInserir = "INSERT INTO campeonato (Nome, Data_inicio, Data_termino) VALUES (:nome, :dataInicio, :dataTermino)";
            $inserir = $this->conexao->prepare($queryInserir);
            $inserir->bindParam(':nome', $nome);
            $inserir->bindParam(':dataInicio', $dataInicio);
            $inserir->bindParam(':dataTermino', $dataTermino);
            $inserir->execute();

            return "Sucesso";
        } catch (PDOException $erro) {
            $codigoDoErro = $erro->getCode();

            switch($codigoDoErro)
            {
                case 23000:
                    return "Ocorreu um erro. Este Campeonato já está registrado no sistema.";
                    break;
                default:
                    return "Ocorreu um erro ao cadastrar o Campeonato: " . $erro->getMessage();
                    break;
            }     
        }
    }

    public function alterarCampeonato($id, $nome, $dataInicio, $dataTermino)
    {
        try {
            $query = "UPDATE campeonato SET Nome = :nome, Data_inicio = :dataInicio, Data_termino = :dataTermino  WHERE Id = :id";
            $alterar = $this->conexao->prepare($query);
            $alterar->bindParam(':id', $id);
            $alterar->bindParam(':nome', $nome);
            $alterar->bindParam(':dataInicio', $dataInicio);
            $alterar->bindParam(':dataTermino', $dataTermino);

            $alterar->execute();

            return "Sucesso";
        } catch (PDOException $erro) {
            $codigoDoErro = $erro->getCode();

            switch($codigoDoErro)
            {
                case 23000:
                    return "Ocorreu um erro. Este Campeonato já está registrado no sistema.";
                    break;
                default:
                    return "Ocorreu um erro ao alterar o Campeonato: " . $erro->getMessage();
                    break;
            }     
        }
    }

    public function excluirCampeonato($id)
    {
        try {
            $query = "DELETE FROM campeonato WHERE Id = :id";
            $excluir = $this->conexao->prepare($query);
            $excluir->bindParam(':id', $id);
            $excluir->execute();

            return "Sucesso";
        } catch (PDOException $erro) {
            $codigoDoErro = $erro->getCode();

            switch($codigoDoErro)
            {
                case 42000:
                    return "Erro de sintaxe SQL, por favor verificar com o Desenvolvedor.";
                    break;
                case 1452:
                    return "Ocorreu um erro. Não é possivel excluir o Campeonato pois existem corridas dele no Sistema";
                    break;
                default:
                    return "Ocorreu um erro ao Excluir o Campeonato: " . $erro->getMessage();
                    break;
            }   
        }
    }

    public function selecionarCampeonatoPorId($id)
    {
        try {
            $query = "SELECT * FROM campeonato WHERE Id = :id";
            $selecionar = $this->conexao->prepare($query);
            $selecionar->bindParam(':id', $id);
            $selecionar->execute();

            return $selecionar->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $erro) {
            return "Erro ao selecionar kartodromo por ID: " . $erro->getMessage();
        }
    }

    public function selecionarTodosOsCampeonatos()
    {
        try {
            $query = "SELECT * FROM campeonato";
            $selecionar = $this->conexao->prepare($query);
            $selecionar->execute();

            return $selecionar->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $erro) {
            return "Erro ao selecionar todos os kartodromos: " . $erro->getMessage();
        }
    }

    public function validarDataTermino($dataInicio, $dataTermino)
    {
        if ($dataInicio <= $dataTermino) {
            return "aceito";
        } else {
            return "A data de término precisa ser igual ou posterior a data de início do Campeonato";
        }
    }

    public function consultarCampeonatoPorFiltro($filtroNome, $filtroDataInicio, $filtroDataTermino)
    {
        try {
            $sql = "SELECT * FROM campeonato WHERE 1";

            // Adiciona as condições de filtro apenas se os parâmetros não estiverem vazios
            if (!empty($filtroNome)) {
                $sql .= " AND Nome LIKE :filtroNome";
            }

            if (!empty($filtroDataInicio)) {
                $sql .= " AND Data_inicio >= :filtroDataInicio";
            }

            if (!empty($filtroDataTermino)) {
                $sql .= " AND Data_termino <= :filtroDataTermino";
            }

            $consulta = $this->conexao->prepare($sql);

            // Binda os parâmetros de filtro apenas se não estiverem vazios
            if (!empty($filtroNome)) {
                $filtroNome = "%{$filtroNome}%";
                $consulta->bindParam(':filtroNome', $filtroNome);
            }

            if (!empty($filtroDataInicio)) {
                $consulta->bindParam(':filtroDataInicio', $filtroDataInicio);
            }

            if (!empty($filtroDataTermino)) {
                $consulta->bindParam(':filtroDataTermino', $filtroDataTermino);
            }

            $consulta->execute();

            return array(
                'campeonatos' => $consulta->fetchAll(PDO::FETCH_ASSOC),
                'feedback' => "Sucesso",
                'classe' => "Sucesso"
            );
        } catch (PDOException $erro) {
            return array(
                'campeonatos' => array(),
                'feedback' => "Erro na consulta: " . $erro->getMessage(),
                'classe' => "erro"
            );
        }
    }
}

?>