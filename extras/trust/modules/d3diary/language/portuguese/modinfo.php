<?php
if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'd3diary' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

define( $constpref.'_LOADED' , 1 ) ;

// Module Info

// The name of this module
define($constpref."_DIARY_NAME","D3diario");
define($constpref."_DIARY_DESC","D3diario");

define($constpref."_DIARYLIST","Novos diários");
define($constpref."_PHOTOLIST","Imagens novas");
define($constpref."_FRIENDSDIARY","Amigos do diário");
define($constpref."_EDIT","Escrever no diário");
define($constpref."_CATEGORY","Categoria");
define($constpref."_COMMENT","Lista de Comentários");
define($constpref."_CONFIG","Configuração do Diário");
define($constpref."_CONFIG_CATEGORY","Configuração da categoria");
define($constpref."_YES","Sim");
define($constpref."_NO","Não");

// Admin
define($constpref."_ADMENU_MYLANGADMIN","Adminstração das constantes da linguagem");
define($constpref."_ADMENU_MYTPLSADMIN","Administração dos modelos");
define($constpref."_ADMENU_MYBLOCKSADMIN","Administração das permissões e dos Blocos");
define($constpref."_ADMENU_IMPORT","Importação");
define($constpref."_ADMENU_PERMISSION","Administrar permissões");

// module config
define($constpref."_MENU_LAYOUT","Leiaute do Menu");
define($constpref."_MENU_LAYOUTDESC","Leiaute do menu dos calendários, etc.");
define($constpref."_MENU_LAYOUT_RIGHT","Mostrar o menu no lado direito");
define($constpref."_MENU_LAYOUT_LEFT","Mostrar o menu no lado esquerdo");
define($constpref."_MENU_LAYOUT_NONE","Não mostrar lado do menu (use blocos)");

define($constpref."_RIGHT_WEIDTH","Largura do lado do menu");
define($constpref."_RIGHT_WEIDTHDESC","Especificar a largura do lado do menu em pixcels.<br />O valor padrão é 140 pixcels");

define($constpref."_USENAME","Mostrar nome");
define($constpref."_USENAMEDESC","Qual nome de usuário mostrar 'codinome' ou 'nome'. <br /> O padrão xoops é 'codinome'");
define($constpref."_USENAME_UNAME","usar'codinome'");
define($constpref."_USENAME_NAME","usar'nome'");

define($constpref."_BREADCRUMBS","Mostrar breadcrumbs");
define($constpref."_BREADCRUMBSDESC","selecione SIM para mostrar breadcrumbs ou <br/>NÃO para usar xoops_breadcrumbs dos temas do xoops");

define($constpref."_PREV_CHARMAX","Máximo de caracteres para lista de conteúdo");
define($constpref."_PREV_CHARMAXDESC","Máximo de caracteres para blocos e lista de conteúdo.");

define($constpref."_BLK_DNUM","Número máximo de itens da lista de conteúdo");
define($constpref."_BLK_DNUMDESC","Configuração do número máximo de itens da lista de conteúdo");

define($constpref."_PHOTO_MAXSIZE","Tamanho máximo da imagem (KB)");
define($constpref."_PHOTO_MAXSIZEDESC","Tamnho máximo da imagem permitido, em (KB)");

define($constpref."_PHOTO_USERESIZE","Salvar imagem encolhida");
define($constpref."_PHOTO_USERESIZEDESC","Salvar imagem encolhida automaticamente, <br/>menos do que 640 pixels.");
define($constpref."_PHOTO_USERESIZE_Y","Encolher imagem");
define($constpref."_PHOTO_USERESIZE_N","Não encolher");

define($constpref."_PHOTO_THUMBSIZE","Tamanho da miniatura da imagem");
define($constpref."_PHOTO_THUMBSIZEDESC","Configuração do tamanho da miniatura da imagem em pixcels");

// define($constpref."_PHOTO_RESIZEMAX","Maximum shrink size");
// define($constpref."_PHOTO_RESIZEMAXDESC","Maximum shrink size setting by px (pixcel)");

define($constpref."_PHOTO_MAXPICS","Número máximo de imagens");
define($constpref."_PHOTO_MAXPICSDESC","Configuração do número máximo permitido para envio de imagem.");
define($constpref."_PHOTO_USEINFO","Colocar informações de cada imagem");
define($constpref."_PHOTO_USEINFODESC","Selecione 'SIM' para colocar informações de cada imagem");

define($constpref."_USE_AVATAR","Mostrar o avatar do usuário");
define($constpref."_USE_AVATARDESC","Selecionar Mostrar o avatar do usuário na barra lateral da lista de páginas do Diário.");

define($constpref."_USE_OPEN_CAT","Configuração da permissão para cada categoria");
define($constpref."_USE_OPEN_CATDESC","Selecionar Habilitar para utilizar as configurações de permissão e configuração do blog externo de cada categoria.");
define($constpref."_USE_OPEN_CAT_Y","Permitir a configuração de um blog externo para cada categoria: ON");
define($constpref."_USE_OPEN_CAT_N","Permitir a configuração de um blog externo para cada categoria: OFF");
define($constpref."_USE_OPEN_CAT_G","Permissão de cada categoria: upto configuração do grupo & configuração do blog externo: ON");
define($constpref."_USE_OPEN_CAT_P","Permissão de cada categoria: upto configuração do grupo e membro & configuração do blog externo<b></b>: ON");

define($constpref."_USE_OPEN_ENTRY","Permitir a configuração de cada post");
define($constpref."_USE_OPEN_ENTRYDESC","Selecionar Habilitar para utilizar a configuração das permissões de cada post.");
define($constpref."_USE_OPEN_ENTRY_Y","Configuração das permissões de cada post: ON");
define($constpref."_USE_OPEN_ENTRY_N","Configuração das permissões de cada post: OFF");
define($constpref."_USE_OPEN_ENTRY_G","Configuração das permissões de cada entrada: upto configuração do grupo");
define($constpref."_USE_OPEN_ENTRY_P","Configuração das permissões de cada entrada: upto configuração do grupo e membro");

define($constpref."_USE_FRIEND","Configuração da cooperação com módulo Myfriend");
define($constpref."_USE_FRIENDDESC","Habilitar configuração das permissões incluindo ou não a funcão Cooperação co o módulo Myfriend. <br/><br/>Deixe OFF caso você não tenha instalado o XSNS ou o módulo Myfriend.");
define($constpref."_USE_FRIEND_N","Cooperação com o módulo Myfriend: OFF");
define($constpref."_USE_XSNS_Y","Cooperação com o módulo XSNS: ON");
define($constpref."_USE_MYFRIENDS_Y","Cooperação com o módulo Myfriend: ON");

define($constpref."_FRIEND_DIRNAME","Nome do diretório do módulo Myfriend cooperação");
define($constpref."_FRIEND_DIRNAMEDESC","Informe o nome do diretório, se você usa a funçao cooperação com o módulo Myfriend.");

define($constpref."_USE_TAG","Função etiqueta ON/OFF");
define($constpref."_USE_TAGDESC","Selecione a área para mostrar a nuvem de etiquetas, caso você esteja usando a função etiqueta.");
define($constpref."_USE_TAG_N","Funçao etiqueta: OFF");
define($constpref."_USE_TAG_INDEXONLY","Mostrar a nuvem de etiquetas na Página Index");
define($constpref."_USE_TAG_ALSODIARYLIST","Mostrar a nuvem de etiquetas em todas as páginas index e misturar com a lista da página.");
define($constpref."_USE_TAG_BLOCK","Não mostrar a nuvem de tags. (Use blocks)");

define($constpref."_BODY_EDITOR","Qual editor do corpo");
define($constpref."_BODY_EDITORDSC","simple não mostrar ajuda do BBcode. Selecionando 'xoopsdhml' é útil para o uso do BBcode.");
define($constpref."_BODY_HTMLEDITOR","Mostrar botão do editor HTML");
define($constpref."_BODY_HTMLEDITORDSC","Permitir HTML para grupos específicos em 'Administração das Permissões' tab screen, then select 'common/FCKeditor' to show FCKeditor button.");
define($constpref."_HTMLPR_EXCEPT","Grupos que podem permitir purificação por HTMLPurifier");
define($constpref."_HTMLPR_EXCEPTDSC","Post de usuários que não pertencem aos grupos que forçarão a purificação com sanitized HTML by HTMLPurifier in Protector>=3.14. Esta pirificação não pode trabalhar com PHP4");

define($constpref."_USE_UPDATEPING","Habilitar ping atualidado" );
define($constpref."_USE_UPDATEPING_DSC","Selecione SIM para usar ping atualizado" );
define($constpref."_UPDATEPING","Atualizar URL dos servidores do ping" );
define($constpref."_UPDATEPING_DSC","dividir cada URL por break" );
define($constpref."_UPDATEPING_SERVERS","http://ping.rss.drecom.jp/\nhttp://blog.goo.ne.jp/XMLRPC" );
define($constpref."_ENC_FROM","Tradução do código interno para a distribuição de RSS");
define($constpref."_ENC_FROMDSC","Normalmente 'padrão' é aplicável. Se a distribuição RSS for alterada, 'xoops_chrset' ou 'auto' pode ser melhor.");
define($constpref."_PERM_CLASS","Nome da classe para as permissão de vizualização");
define($constpref."_PERM_CLASSDSC","Informe o nome da classe para subscrever as permissões de vizualização. O padrão é d3diaryPermission");

define($constpref."_USE_MAILPOST" , 'Use poste através de e-mail');
define($constpref."_USE_MAILPOSTDSC" , 'Selecione "SIM" para habilitar poste por e-mail, e inspecione o grupo colocação de permissão.');
define($constpref."_POP3_SERVER","Servidor de correio POP");
define($constpref."_POP3_SERVER_DESC","Nome de servidor de correio PO");
define($constpref."_POP3_PORT","Porto de servidor PO");
define($constpref."_POP3_PORT_DESC","Por favor contate a administrador de servidor. Normalmente servidor POPULAR usa porto 110.");
define($constpref."_POP3_APOP","Uso APOP codificam autorização");
define($constpref."_POP3_APOP_DESC","Por favor contate a administrador de servidor se usar APOP codificam autorização");
define($constpref."_POST_EMAIL_ADDRESS","Conta de correio ID para inporting");
define($constpref."_POST_EMAIL_ADDRESS_DESC","Introduza o POP3 respondem ID por inporting");
define($constpref."_POST_EMAIL_PASSWORD","Contra-senha de POP3 para inporting");
define($constpref."_POST_EMAIL_PASSWORD_DESC","Introduza a contra-senha de POP3 para inportingting");
define($constpref."_POST_EMAIL_FULLADD","Correio endereço cheio");
define($constpref."_POST_EMAIL_FULLADDDSC","Endereço cheio para poste para qual não é para controle mas só para espetáculo.");
define($constpref."_POST_DETECT_ORDER","Remeta texto codificando descubra ordem");
define($constpref."_POST_DETECT_ORDERDSC","Fixe correio texto codificando descubra ordem. <br /> meios Desocupados 'auto.' Se alguns falsificam é occured, séries de texto de contribuição de 'ISO-2022-JP, UTF-8, UTF-7, ASCII, EUC-JP, JIS, SJIS, eucJP-win, SJIS-win'<br />Ex: 'ISO-2022-JP, UTF-8, JIS, EUC-JP, eucJP-win, SJIS'");

define($constpref."_USE_SIMPLECOMMENT","Modo comentário");
define($constpref."_USE_SIMPLECOMMENTDESC","Você pode usar o formulário de comentários fácil ao invés do formulário de comentários original do Xoops. <br/><br/> Convidado não pode escrever usando o o formulário de comentário fácil. ");
define($constpref."_USE_SIMPLECOMMENT_Y","Usar o Formulário de comentário fácil");
define($constpref."_USE_SIMPLECOMMENT_N","Usar o formulário de comentários original do Xoops");

//d3comment integration
define($constpref."_COM_DIRNAME","Nome do diretório para integração de comentários com o d3forum");
define($constpref."_COM_DIRNAMEDSC","Quando usar o sistema de integração de comentários d3. <br/>escreva seu diretório d3forum (html) <br/>Deixe isso vazio, se você não usar comentários ou usar o sistema de comentários do xoops.");
define($constpref."_COM_FORUMID","Número do forum para a integração de comentário com o d3forum");
define($constpref."_COM_FORUMIDDSC","Escreva o ID do forum, quando você configurar o diretório acima.");
define($constpref."_COM_ORDER","Classificação da integração de comentários");
define($constpref."_COM_ORDERDSC","Quando você configurar a integração de comentários, selecione mostrar a ordem dos posts de comentário");
define($constpref."_COM_VIEW","Vizualização da integração de comentários");
define($constpref."_COM_VIEWDSC","Selecione encolhido ou expandido");
define($constpref."_COM_POSTSNUM","Número máximo de posts mostrados na integração de cometários");
define($constpref."_COM_ANCHOR","Entrada âncora da integração de cometários");
define($constpref."_COM_ANCHORDSC","A entrada âncora padrão é 'post_path'.<br />Se você usar 'post_id' para múltiplos tópicos de uma entrada, <br /> você deve editar o modelo do d3forum.");
define($constpref."_USE_COM_ANCHOR_UNIQUEPATH","Usar o padrão d3forum 'post_path'");
define($constpref."_USE_COM_ANCHOR_POSTNUM","Usar 'post_id'");

//notifications
define($constpref."_GLOBAL_NOTIFY","Todo o Diário persoal");
define($constpref."_GLOBAL_NOTIFYDSC","Todo o Diário persoal");
define($constpref."_BLOGGER_NOTIFY","Especificado como Diário de Pessoal");
define($constpref."_BLOGGER_NOTIFYDSC","Especificado como Diário de Pessoal");

define($constpref."_GLOBAL_NEWENTRY_NOTIFY","Novo Diário");
define($constpref."_GLOBAL_NEWENTRY_NOTIFYCAP","Notifique-me de todos os novos diários pessoais");
define($constpref."_GLOBAL_NEWENTRY_NOTIFYDSC","Notifique-me de todos os novos diários pessoais");
define($constpref."_GLOBAL_NEWENTRY_NOTIFYSBJ","[{X_SITENAME}] {X_MODULE} novo diário");

define($constpref."_BLOGGER_NEWENTRY_NOTIFY","Especificado como Diário Pessoal");
define($constpref."_BLOGGER_NEWENTRY_NOTIFYCAP","Notifique-me deste novo diário pessoal");
define($constpref."_BLOGGER_NEWENTRY_NOTIFYDSC","Notifique-me deste novo diário pessoal");
define($constpref."_BLOGGER_NEWENTRY_NOTIFYSBJ","[{X_SITENAME}] {X_MODULE} novo diário");

define($constpref."_BLOGGER_COMMENT_NOTIFY","Comentário da pessoa especificada");
define($constpref."_BLOGGER_COMMENT_NOTIFYCAP","Notifique-me de comentário desta pessoa");
define($constpref."_BLOGGER_COMMENT_NOTIFYDSC","Notifique-me de comentário desta pessoa");
define($constpref."_BLOGGER_COMMENT_NOTIFYSBJ","[{X_SITENAME}] {X_MODULE} novo comentário");

define($constpref."_ENTRY_NOTIFY","Comentário deste post");
define($constpref."_ENTRY_NOTIFYDSC","Notifique-me deste post");

// Block
define($constpref."_BLOCK_NEWENTRY","Novo Diário ou Blog");
define($constpref."_BLOCK_NEWENTRYDSC","Novos posts - Novo Diário");
define($constpref."_BLOCK_BLOGGER","Nova lista de Bloguers");
define($constpref."_BLOCK_BLOGGERDSC","Nova lista de Bloguers");
define($constpref."_BLOCK_D3COMPOSTS","Nova lista de comentários de post");
define($constpref."_BLOCK_D3COMPOSTSDSC","Validar para integração de comentários");
define($constpref."_BLOCK_D3COMTOPICS","Nova lista de comentários de tópicos");
define($constpref."_BLOCK_D3COMTOPICSDSC","Validar para integração de comentário");
define($constpref."_BLOCK_PERSON","Autor");
define($constpref."_BLOCK_PERSONDSC","Bloco do autor do diário");
define($constpref."_BLOCK_CALENDAR","Calendário");
define($constpref."_BLOCK_CALENDARDSC","Bloco do calendário do diário");
define($constpref."_BLOCK_CATEGORY","Categoria");
define($constpref."_BLOCK_CATEGORYDSC","Bloco da categoria do diário");
define($constpref."_BLOCK_ENTRY","Novas entradas");
define($constpref."_BLOCK_ENTRYDSC","Bloco das novas entradas");
define($constpref."_BLOCK_COMMENT","Novos comentários");
define($constpref."_BLOCK_COMMENTDSC","Bloco dos novos comentários");
define($constpref."_BLOCK_MLIST","Mês");
define($constpref."_BLOCK_MLISTDSC","Bloco mostrar mensalmente");
define($constpref."_BLOCK_FRIENDS","Amigos");
define($constpref."_BLOCK_FRIENDSDSC","Bloco da lista de amigos");
define($constpref."_BLOCK_TAGCROUD","Nuvem de etiquetas");
define($constpref."_BLOCK_TAGCROUDDSC","Bloco da nuvem de etiquetas");
define($constpref."_BLOCK_PHOTOS","Fotografias");
define($constpref."_BLOCK_PHOTOSDSC","Fotografias bloqueiam");

//others
define($constpref."_BLOGGER","'s diario");

}
?>
