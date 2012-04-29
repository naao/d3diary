<?php

// D3DIARY IMPORT
define('_MD_D3DIARY_H2_IMPORTFROM','Importar');
define('_MD_D3DIARY_H3_IMPORTDATABASE','Copiar dados para o módulo d3diario');
define('_MD_D3DIARY_BTN_DOIMPORT','Executar a importação');
define('_MD_D3DIARY_LABEL_SELECTMODULE','Selecionar um módulo');
define('_MD_D3DIARY_CONFIRM_DOIMPORT','Você realmente deseja executar a importação?');

define('_MD_D3DIARY_HELP_IMPORTFROM','Você pode importar somente do módulo d3diary, minidiário, weblogD3 ou d3blog. Para weblogD3, não será importada mesa de trackback. Para d3blog, será importada também mesa de trackback mas nenhuma função em d3diary, e em ambos caso categoria deveriam ser comprimidos upto 2 camadas antes de execução de importar. <br /> <font color="#FF0000"> Tem cuidado que a importação de d3diary é tudo escritos elaboradamente. </font>');
define('_MD_D3DIARY_IMPORTDONE','A importação está concluida.');
define('_MD_D3DIARY_ERR_INVALIDMID','Não foi possível executar a importação do módulo que você selecionou.');
define('_MD_D3DIARY_SQLONIMPORT','A importação falhou.<br />Possívelmente existe diferenças na estrutura das tabelas do banco de dados, entre as tabelas de importação e as tabelas de destino.<br />Por favor, verifique se os dois módulos já foram atualizados para suas versões mais recentes.');

define('_MD_D3DIARY_H3_IMPORTCOM','mover os dados dos comentários do xoops');
define('_MD_D3DIARY_HELP_COMIMPORT','Importação do comentário movidos dos comentários. <br />Você não pode desfazer depois da execução.');
define('_MD_D3DIARY_CONFIRM_DOCOMIMPORT','Você realmente deseja mover os comentários?');

define('_MD_D3DIARY_H3_IMPORTNOTIF','Mover os dados de notificação do xoops');
define('_MD_D3DIARY_HELP_NOTIFIMPORT','Importação de notificação movidas de notificações. <br />Você não pode desfazer depois da execução.');
define('_MD_D3DIARY_CONFIRM_DONOTIFIMPORT','Você realmente deseja mover as notificações?');

// D3DIARY PERMISSION
define('_MD_D3DIARY_LANG_PERMISSION_MANAGER','Permissões');
define('_MD_D3DIARY_LANG_CATEGORY_NAME','Categoria');
define('_MD_D3DIARY_LANG_CATEGORY_GLOBAL','Geral');
define('_MD_D3DIARY_LANG_GROUP_NAME','Nome do grupo');
define('_MD_D3DIARY_PERMDESC_ALLOW_EDIT','Permitir postar no diário');
define('_MD_D3DIARY_PERMDESC_ALLOW_HTML','Permitir post em HTML');
define('_MD_D3DIARY_PERMDESC_ALLOW_REGDATE','Permitir especificar data do envio');
define('_MD_D3DIARY_PERMDESC_ALLOW_GPERM','Permitir especificar permissão do grupo');
define('_MD_D3DIARY_PERMDESC_ALLOW_PPERM','Permitir especificar permissão do usuário');
define('_MD_D3DIARY_PERMDESC_ALLOW_MAILPOST','Permita importação de e-mail');
define('_MD_D3DIARY_MESSAGE_DBUPDATE_FAILED','Mudança da configuração das permissões falharam');
define('_MD_D3DIARY_MESSAGE_DBUPDATE_SUCCESS','Configuração das permissões foram mudadas');

// For D3pipes Options
define('_MD_D3DIARY_MAXTEXT','Comprimento máximo do texto:');
define('_MD_D3DIARY_TOPICSPOSTS','Usar bloco:');
define('_MD_D3DIARY_USEAGGRE','Usar agragação com outros fóruns:');
define('_MD_D3DIARY_CATLIMIT','ID da categoria');
define('_MD_D3DIARY_FORUMLIMIT','ID do Fórum');
define('_MD_D3DIARY_DISPLAY_PERSONAL','Número máximo mostrado por pessoa:');

?>
