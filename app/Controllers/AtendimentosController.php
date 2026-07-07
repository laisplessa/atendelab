<?php

class AtendimentosController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../Config/database.php';
        $this->pdo = $pdo;
    }

    public function listar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $sql = 'SELECT
                    a.id,
                    a.usuario_id,
                    u.nome AS usuario,
                    a.pessoa_id,
                    p.nome AS pessoa,
                    a.tipo_atendimento_id,
                    t.nome AS tipo_atendimento,
                    a.data_atendimento,
                    a.horario_atendimento,
                    a.descricao,
                    a.observacao_final,
                    a.status,
                    a.criado_em
                FROM atendimentos a
                INNER JOIN usuarios u ON u.id = a.usuario_id
                INNER JOIN pessoas p ON p.id = a.pessoa_id
                INNER JOIN tipos_atendimentos t ON t.id = a.tipo_atendimento_id
                ORDER BY a.id DESC';

        $stmt = $this->pdo->query($sql);
        $atendimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($atendimentos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function buscarPorId(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }

        $sql = 'SELECT
                    a.id,
                    a.usuario_id,
                    u.nome AS usuario,
                    a.pessoa_id,
                    p.nome AS pessoa,
                    a.tipo_atendimento_id,
                    t.nome AS tipo_atendimento,
                    a.data_atendimento,
                    a.horario_atendimento,
                    a.descricao,
                    a.observacao_final,
                    a.status,
                    a.criado_em
                FROM atendimentos a
                INNER JOIN usuarios u ON u.id = a.usuario_id
                INNER JOIN pessoas p ON p.id = a.pessoa_id
                INNER JOIN tipos_atendimentos t ON t.id = a.tipo_atendimento_id
                WHERE a.id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $atendimento = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$atendimento) {
            http_response_code(404);
            echo json_encode(['erro' => 'Atendimento não encontrado.']);
            return;
        }

        echo json_encode($atendimento, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function criar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $usuarioId = $_SESSION['usuario']['id'] ?? null;
        $pessoaId = filter_input(INPUT_POST, 'pessoa_id', FILTER_VALIDATE_INT);
        $tipoAtendimentoId = filter_input(INPUT_POST, 'tipo_atendimento_id', FILTER_VALIDATE_INT);
        $dataAtendimento = $_POST['data_atendimento'] ?? '';
        $horaAtendimento = $_POST['horario_atendimento'] ?? '';
        $descricao = trim($_POST['descricao'] ?? '');
        $observacaoFinal = trim($_POST['observacao_final'] ?? '');
        $status = $_POST['status'] ?? 'aberto';

        if (!$usuarioId || !$pessoaId || !$tipoAtendimentoId || $dataAtendimento === '' || $horaAtendimento === '' || $descricao === '') {
            http_response_code(400);
            echo json_encode([
                'erro' => 'Usuário, pessoa, tipo de atendimento, data, hora e descrição são obrigatórios.'
            ]);
            return;
        }

        if (!in_array($status, ['aberto', 'em_andamento', 'concluido'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Status inválido.']);
            return;
        }

        try {
            $sql = 'INSERT INTO atendimentos
                    (usuario_id, pessoa_id, tipo_atendimento_id, data_atendimento, horario_atendimento, descricao, observacao_final, status)
                    VALUES
                    (:usuario_id, :pessoa_id, :tipo_atendimento_id, :data_atendimento, :horario_atendimento, :descricao, :observacao_final, :status)';

            $stmt = $this->pdo->prepare($sql);

            $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
            $stmt->bindValue(':pessoa_id', $pessoaId, PDO::PARAM_INT);
            $stmt->bindValue(':tipo_atendimento_id', $tipoAtendimentoId, PDO::PARAM_INT);
            $stmt->bindValue(':data_atendimento', $dataAtendimento);
            $stmt->bindValue(':horario_atendimento', $horaAtendimento);
            $stmt->bindValue(':descricao', $descricao);
            $stmt->bindValue(':observacao_final', $observacaoFinal);
            $stmt->bindValue(':status', $status);

            $stmt->execute();

            echo json_encode([
                'mensagem' => 'Atendimento criado com sucesso.',
                'id' => $this->pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao criar atendimento.']);
        }
    }

    public function atualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $usuarioId = filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT);
        $pessoaId = filter_input(INPUT_POST, 'pessoa_id', FILTER_VALIDATE_INT);
        $tipoAtendimentoId = filter_input(INPUT_POST, 'tipo_atendimento_id', FILTER_VALIDATE_INT);
        $dataAtendimento = $_POST['data_atendimento'] ?? '';
        $horaAtendimento = $_POST['horario_atendimento'] ?? '';
        $descricao = trim($_POST['descricao'] ?? '');
        $observacaoFinal = trim($_POST['observacao_final'] ?? '');
        $status = $_POST['status'] ?? 'aberto';

        if (!$id || !$usuarioId || !$pessoaId || !$tipoAtendimentoId || $dataAtendimento === '' || $horaAtendimento === '' || $descricao === '') {
            http_response_code(400);
            echo json_encode([
                'erro' => 'ID, usuário, pessoa, tipo de atendimento, data, hora e descrição são obrigatórios.'
            ]);
            return;
        }

        if (!in_array($status, ['aberto', 'em_andamento', 'concluido'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Status inválido.']);
            return;
        }

        try {
            $sql = 'UPDATE atendimentos
                    SET usuario_id = :usuario_id,
                        pessoa_id = :pessoa_id,
                        tipo_atendimento_id = :tipo_atendimento_id,
                        data_atendimento = :data_atendimento,
                        horario_atendimento = :horario_atendimento,
                        descricao = :descricao,
                        observacao_final = :observacao_final,
                        status = :status
                    WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);

            $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
            $stmt->bindValue(':pessoa_id', $pessoaId, PDO::PARAM_INT);
            $stmt->bindValue(':tipo_atendimento_id', $tipoAtendimentoId, PDO::PARAM_INT);
            $stmt->bindValue(':data_atendimento', $dataAtendimento);
            $stmt->bindValue(':horario_atendimento', $horaAtendimento);
            $stmt->bindValue(':descricao', $descricao);
            $stmt->bindValue(':observacao_final', $observacaoFinal);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            $stmt->execute();

            echo json_encode([
                'mensagem' => 'Atendimento atualizado com sucesso.'
            ], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao atualizar atendimento.']);
        }
    }

    public function atualizarStatus(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $status = $_POST['status'] ?? '';
        $observacaoFinal = trim($_POST['observacao_final'] ?? '');

        if (!$id || !in_array($status, ['aberto', 'em_andamento', 'concluido'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID e status válido são obrigatórios.']);
            return;
        }

        if ($status === 'concluido' && $observacaoFinal === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'Observação final é obrigatória ao concluir.']);
            return;
        }

        try {
            $sql = 'UPDATE atendimentos
                    SET status = :status,
                        observacao_final = :observacao_final,
                        atualizado_em = NOW()
                    WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':observacao_final', $observacaoFinal);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode([
                'mensagem' => 'Status atualizado com sucesso.'
            ], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao atualizar status.']);
        }
    }

    public function excluir(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }

        try {
            $sql = 'DELETE FROM atendimentos WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode([
                'mensagem' => 'Atendimento excluído com sucesso.'
            ], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao excluir atendimento.']);
        }
    }
}