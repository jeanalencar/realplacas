<?php
// Configurações
$destinatario = "contato@realplacas.com"; // Substitua pelo e-mail real
$assunto = "Novo Orçamento - Site Real Placas";

// Proteção contra spam - honeypot
if (!empty($_POST['honeypot'])) {
    http_response_code(400);
    die("Spam detectado");
}

// Validar campos obrigatórios
if (empty($_POST['nome']) || empty($_POST['telefone'])) {
    http_response_code(400);
    die("Campos obrigatórios não preenchidos");
}

// Função para limpar dados de entrada
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Sanitizar dados
$nome = clean_input($_POST['nome']);
$telefone = clean_input($_POST['telefone']);
$email = isset($_POST['email']) ? clean_input($_POST['email']) : '';
$endereco = isset($_POST['endereco']) ? clean_input($_POST['endereco']) : '';
$mensagem = isset($_POST['mensagem']) ? clean_input($_POST['mensagem']) : '';

// Proteção contra Header Injection no email
if (!empty($email)) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        die("E-mail inválido");
    }
    // Verificar se há quebras de linha (tentativa de injeção de cabeçalho)
    if (preg_match( "/[\r\n]/", $email)) {
        http_response_code(400);
        die("Tentativa de injeção de cabeçalho detectada");
    }
}

// Processar serviços selecionados
$servicos = [];
if (isset($_POST['servicos']) && is_array($_POST['servicos'])) {
    $servicos = array_map('clean_input', $_POST['servicos']);
}

// Construir o corpo do e-mail
$corpoEmail = "
NOVO PEDIDO DE ORÇAMENTO - REAL PLACAS
==========================================

DADOS DO CLIENTE:
------------------
Nome: $nome
Telefone: $telefone
E-mail: " . ($email ?: "Não informado") . "
Endereço da Obra: " . ($endereco ?: "Não informado") . "

SERVIÇOS DE INTERESSE:
----------------------
" . (count($servicos) > 0 ? implode("\n", $servicos) : "Nenhum serviço selecionado") . "

MENSAGEM/DETALHES:
------------------
" . ($mensagem ?: "Nenhuma mensagem adicional") . "

INFORMAÇÕES DO PEDIDO:
----------------------
Data: " . date('d/m/Y H:i:s') . "
IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'Não disponível') . "
";

// Cabeçalhos do e-mail
$headers = "From: site@realplacas.com\r\n";
$headers .= "Reply-To: " . ($email ?: "contato@realplacas.com") . "\r\n";
$headers .= "Content-Type: text/plain; charset=utf-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Tentar enviar o e-mail
try {
    // Suppress warning for mail() if not configured locally
    $envioEmail = @mail($destinatario, $assunto, $corpoEmail, $headers);
    
    if ($envioEmail) {
        // Também enviar para WhatsApp (opcional - via webhook)
        enviarParaWhatsApp($nome, $telefone, $servicos);
        
        // Responder com sucesso
        http_response_code(200);
        echo "Mensagem enviada com sucesso!";
    } else {
        // Log do erro real (não mostrar ao usuário)
        error_log("Falha ao enviar e-mail via mail(). Verifique configurações do servidor.");
        throw new Exception("Falha no envio do e-mail. Tente contato via WhatsApp.");
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo "Erro ao enviar mensagem: " . $e->getMessage();
}

// Função para enviar notificação para WhatsApp (opcional)
function enviarParaWhatsApp($nome, $telefone, $servicos) {
    $numeroWhatsApp = "558798000202"; // Número da Real Placas
    
    // Formatar mensagem para WhatsApp
    $mensagemWhatsApp = urlencode(
        "🚨 *NOVO PEDIDO DE ORÇAMENTO* 🚨\n\n" .
        "*Cliente:* $nome\n" .
        "*Telefone:* $telefone\n" .
        "*Serviços:* " . implode(", ", $servicos) . "\n\n" .
        "_Enviado via Site Real Placas_"
    );
    
    // URL para API do WhatsApp (exemplo usando API própria)
    $urlWhatsApp = "https://api.whatsapp.com/send?phone=$numeroWhatsApp&text=$mensagemWhatsApp";
    
    // Aqui você pode integrar com uma API de WhatsApp real
    // Por enquanto, apenas registramos no log
    error_log("Notificação WhatsApp: $urlWhatsApp");
}
?>