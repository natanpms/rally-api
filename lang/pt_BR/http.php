<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mensagens de Erro HTTP
    |--------------------------------------------------------------------------
    |
    | Mensagens usadas pelo handler de exceções em `bootstrap/app.php` para
    | padronizar as respostas de erro da API em português.
    |
    */

    'unauthenticated' => 'Não autenticado. Envie um token de acesso válido.',
    'forbidden' => 'Você não tem permissão para executar esta ação.',
    'not_found' => 'Recurso não encontrado.',
    'method_not_allowed' => 'Método HTTP não permitido para esta rota.',
    'too_many_requests' => 'Muitas requisições. Tente novamente em :seconds segundos.',
    'server_error' => 'Erro interno no servidor.',

];
