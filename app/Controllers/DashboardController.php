<?php

class DashboardController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../Config/database.php';
        $this->pdo = $pdo;
    }

    public function resumo(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $totalPessoas = $this->pdo->query('SELECT COUNT(*) FROM pessoas')->fetchColumn();
        $totalTipos = $this->pdo->query('SELECT COUNT(*) FROM tipos_atendimentos')->fetchColumn();
        $totalAtendimentos = $this->pdo->query('SELECT COUNT(*) FROM atendimentos')->fetchColumn();

        echo json_encode([
            'pessoas' => (int) $totalPessoas,
            'tipos' => (int) $totalTipos,
            'atendimentos' => (int) $totalAtendimentos,
        ], JSON_UNESCAPED_UNICODE);
    }

    public function index(): void
    {
        require __DIR__ . '/../Views/dashboard/index.php';
    }
}