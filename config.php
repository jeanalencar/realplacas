<?php
// config.php - Configurações do sistema

// Prevent direct access
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header('HTTP/1.0 403 Forbidden');
    exit('Forbidden');
}

return [
    'email' => [
        'destinatario' => 'contato@realplacas.com',
        'assunto' => 'Novo Orçamento - Site Real Placas',
        'from' => 'site@realplacas.com'
    ],
    'whatsapp' => [
        'numero' => '558798000202',
        'ativo' => true
    ],
    'database' => [
        'ativo' => false,
        'host' => 'localhost',
        'usuario' => 'usuario',
        'senha' => 'senha',
        'banco' => 'real_placas'
    ]
];
?>