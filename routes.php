<?php

require_once __DIR__ . '/app/Controllers/AuthController.php';
require_once __DIR__ . '/app/Controllers/UsuariosController.php';
require_once __DIR__ . '/app/Middleware/auth.php';

$controller = $_GET['controller'] ?? 'auth';
$action = $_GET['action'] ?? 'login';

switch ($controller) {

    case 'auth':

        $authController = new AuthController();

        switch ($action) {

            case 'login':
                $authController->exibirLogin();
                break;

            case 'entrar':
                $authController->entrar();
                break;

            case 'dashboard':
                $authController->dashboard();
                break;

            case 'logout':
                $authController->logout();
                break;

            default:
                http_response_code(404);
                echo 'Acao de autenticacao nao encontrada.';
        }

        break;

    case 'frontend':

        exigirAutenticacao();

        switch ($action) {

            case 'dashboard':
                require __DIR__ . '/app/Views/dashboard/index.php';
                break;

            case 'pessoas':
                require __DIR__ . '/app/Views/pessoas/index.php';
                break;

            case 'tipos':
                require __DIR__ . '/app/Views/tipos-atendimentos/index.php';
                break;

            case 'atendimentos':
                require __DIR__ . '/app/Views/atendimento/index.php';
                break;

            default:
                http_response_code(404);
                echo 'Pagina nao encontrada.';
        }

        break;

    case 'usuarios':

        exigirAutenticacao();

        $usuariosController = new UsuariosController();

        switch ($action) {

            case 'listar':
                $usuariosController->listar();
                break;

            case 'buscarPorId':
                $usuariosController->buscarPorId();
                break;

            case 'criar':
                $usuariosController->criar();
                break;

            case 'atualizar':
                $usuariosController->atualizar();
                break;

            case 'excluir':
                $usuariosController->excluir();
                break;

            default:
                http_response_code(404);
                echo 'Acao de usuarios nao encontrada.';
        }

        break;

    case 'pessoas':

        exigirAutenticacao();

        require_once __DIR__ . '/app/Controllers/PessoasController.php';

        $pessoasController = new PessoasController();

        switch ($action) {

            case 'listar':
                $pessoasController->listar();
                break;

            case 'buscarPorId':
            case 'buscar':
                $pessoasController->buscar();
                break;

            case 'criar':
                $pessoasController->criar();
                break;

            case 'atualizar':
                $pessoasController->atualizar();
                break;

            case 'inativar':
            case 'excluir':
                $pessoasController->inativar();
                break;

            default:
                http_response_code(404);
                echo 'Acao de pessoas nao encontrada.';
        }

        break;

    case 'tipos':

        exigirAutenticacao();

        require_once __DIR__ . '/app/Controllers/TiposAtendimentosController.php';

        $tiposController = new TiposAtendimentosController();

        switch ($action) {

            case 'listar':
                $tiposController->listar();
                break;

            case 'buscarPorId':
            case 'buscar':
                $tiposController->buscar();
                break;

            case 'criar':
                $tiposController->criar();
                break;

            case 'atualizar':
                $tiposController->atualizar();
                break;

            case 'inativar':
                $tiposController->inativar();
                break;

            default:
                http_response_code(404);
                echo 'Acao de tipos de atendimento nao encontrada.';
        }

        break;

    case 'dashboard':

        exigirAutenticacao();

        require_once __DIR__ . '/app/Controllers/DashboardController.php';

        $dashboardController = new DashboardController();

        switch ($action) {

            case 'index':
                $dashboardController->index();
                break;

            case 'resumo':
                $dashboardController->resumo();
                break;

            default:
                http_response_code(404);
                echo 'Acao de dashboard nao encontrada.';
        }

        break;

    case 'atendimentos':

        exigirAutenticacao();

        require_once __DIR__ . '/app/Controllers/AtendimentosController.php';

        $atendimentosController = new AtendimentosController();

        switch ($action) {

            case 'listar':
                $atendimentosController->listar();
                break;

            case 'visualizar':
                $atendimentosController->visualizar();
                break;

            case 'criar':
                $atendimentosController->criar();
                break;

            case 'alterarStatus':
            case 'atualizarStatus':
                $atendimentosController->atualizarStatus();
                break;

            case 'opcoesFormulario':
                $atendimentosController->opcoesFormulario();
                break;

            default:
                http_response_code(404);
                echo 'Acao de atendimentos nao encontrada.';
        }

        break;

    default:
        http_response_code(404);
        echo 'Controller nao encontrado.';
}