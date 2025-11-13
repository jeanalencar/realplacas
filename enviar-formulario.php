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

// Sanitizar dados
$nome = filter_var($_POST['nome'], FILTER_SANITIZE_STRING);
$telefone = filter_var($_POST['telefone'], FILTER_SANITIZE_STRING);
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$endereco = filter_var($_POST['endereco'], FILTER_SANITIZE_STRING);
$mensagem = filter_var($_POST['mensagem'], FILTER_SANITIZE_STRING);

// Processar serviços selecionados
$servicos = [];
if (isset($_POST['servicos']) && is_array($_POST['servicos'])) {
    $servicos = array_map(function($servico) {
        return filter_var($servico, FILTER_SANITIZE_STRING);
    }, $_POST['servicos']);
}

// Validar e-mail se fornecido
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    die("E-mail inválido");
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

// Tentar enviar o e-mail
try {
    $envioEmail = mail($destinatario, $assunto, $corpoEmail, $headers);
    
    if ($envioEmail) {
        // Também enviar para WhatsApp (opcional - via webhook)
        enviarParaWhatsApp($nome, $telefone, $servicos);
        
        // Responder com sucesso
        http_response_code(200);
        echo "Mensagem enviada com sucesso!";
    } else {
        throw new Exception("Falha no envio do e-mail");
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

// Função para salvar no banco de dados (opcional)
function salvarNoBanco($dados) {
    // Exemplo de conexão com MySQL
    /*
    $servername = "localhost";
    $username = "usuario";
    $password = "senha";
    $dbname = "real_placas";
    
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $conn->prepare("INSERT INTO orcamentos 
            (nome, telefone, email, endereco, servicos, mensagem, data_criacao) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())");
        
        $servicosJson = json_encode($dados['servicos']);
        $stmt->execute([
            $dados['nome'],
            $dados['telefone'],
            $dados['email'],
            $dados['endereco'],
            $servicosJson,
            $dados['mensagem']
        ]);
        
    } catch(PDOException $e) {
        error_log("Erro ao salvar no banco: " . $e->getMessage());
    }
    */
}
?>