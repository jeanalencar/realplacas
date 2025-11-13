<?php
// config.php - Configurações do sistema
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