<?php
// Constantes gerais da aplicação
define('APP_NAME', 'CONECTIVA - ADAB');
define('APP_DESCRIPTION', 'Sistema de Gerenciamento de Pontos de Internet');

// Configurações de paginação
define('ITEMS_POR_PAGINA', 10);

// Timezone
define('TIMEZONE', 'America/Bahia');
date_default_timezone_set(TIMEZONE);

// Tipos de problemas
define('TIPOS_PROBLEMA', [
    'Rompimento de Fibra',
    'Lentidão',
    'Blacklist',
    'Intermitência',
    'Falta de Internet',
    'Falta de Pagamento',
    'Problema no Equipamento de Internet'
]);

// Tipos de conexão
define('TIPOS_CONEXAO', [
    'Internet Cedida',
    'LUME',
    'Velox',
    'Modem Vivo',
    'Rede Governo'
]);

// Cores para cada tipo de conexão (para o mapa)
define('CORES_TIPO', [
    'Internet Cedida' => '#28a745',  // Verde
    'LUME' => '#007bff',             // Azul
    'Velox' => '#dc3545',            // Vermelho
    'Modem Vivo' => '#6f42c1',       // Roxo
    'Rede Governo' => '#17a2b8'      // Ciano
]);

// Velocidades padrão (em Mbps)
define('VELOCIDADES_PADRAO', [
    '1 Mbps',
    '5 Mbps',
    '10 Mbps',
    '20 Mbps',
    '50 Mbps',
    '60 Mbps',
    '100 Mbps',
    '200 Mbps'
]);

// Status de Chamados
define('STATUS_CHAMADO', [
    'Aberto' => 'Aberto',
    'Em Andamento' => 'Em Andamento',
    'Aguardando' => 'Aguardando Retorno',
    'Resolvido' => 'Resolvido',
    'Fechado' => 'Fechado',
    'Cancelado' => 'Cancelado'
]);

// Territórios e cidades
$GLOBALS['territorios'] = [

    "ALAGOINHAS - LITORAL NORTE E AGRESTE BAIANO" => [
        "ACAJUTIBA", "ALAGOINHAS", "APORÁ", "ARAÇAS", "ARAMARI", "CARDEAL DA SILVA",
        "CATU", "CONDE", "CRISÓPOLIS", "ENTRE RIOS", "ESPLANADA", "INHAMBUPE", 
        "ITANAGRA", "ITAPICURU", "JANDAÍRA", "OLINDINA", "OURIÇANGAS", "PEDRÃO", 
        "RIO REAL", "SÁTIRO DIAS"
    ],

    "BARREIRAS - BACIA DO RIO GRANDE" => [
        "ANGICAL", "BAIANÓPOLIS", "BARREIRAS", "BURITIRAMA", "CATOLÂNDIA", "COTEGIPE",
        "CRISTÓPOLIS", "FORMOSA DO RIO PRETO", "LUÍS EDUARDO MAGALHÃES", "MANSIDÃO",
        "RIACHÃO DAS NEVES", "SANTA RITA DE CÁSSIA", "SÃO DESIDÉRIO", "WANDERLEY"
    ],

    "BOM JESUS DA LAPA - VELHO CHICO" => [
        "BARRA", "BOM JESUS DA LAPA", "BROTAS DE NACAÚBAS", "CARINHANHA", "FEIRA DA MATA",
        "IBOTIRAMA", "IGAPORÃ", "MALHADA", "MATINA", "MORPARÁ", "MUGUÉM DO SÃO FRANCISCO",
        "OLIVEIRA DOS BREJINHOS", "PARATINGA", "RIACHO DE SANTANA", "SERRA DO RAMALHO",
        "SÍTIO DO MATO"
    ],

    "CRUZ DAS ALMAS - RECÔNCAVO" => [
        "CABECEIRA DO PARAGUAÇU", "CACHOEIRA", "CASTRO ALVES", "CONCEIÇÃO DO ALMEIDA",
        "CRUZ DAS ALMAS", "DOM MACEDO COSTA", "GOVERNADOR MANGABEIRA", "MARAGOGIPE",
        "MUNIZ FERREIRA", "MURITIBA", "NAZARÉ", "SALINAS DA MARGARIDAS", "SANTO AMARO",
        "SANTO ANTÔNIO DE JESUS", "SÃO FELIPE", "SÃO FÉLIX", "SAPEAÇU", "SAUBARA", "VARZEDO"
    ],

    "EUNÁPOLIS - COSTA DO DESCOBRIMENTO" => [
        "BELMONTE", "EUNÁPOLIS", "GUARATINGA", "ITABELA", "ITAGIMIRIM", "ITAPEBI",
        "PORTO SEGURO", "SANTA CRUZ CABRÁLIA"
    ],

    "FEIRA DE SANTANA - PORTAL DO SERTÃO" => [
        "ÁGUA FRIA", "AMÉLIA RODRIGUES", "ANGUERA", "ANTÔNIO CARDOSO", "CONCEIÇÃO DA FEIRA",
        "CONCEIÇÃO DO JACUÍPE", "CORAÇÃO DE MARIA", "FEIRA DE SANTANA", "IPECAETÁ", "IRARÁ",
        "SANTA BÁRBARA", "SANTANÓPOLIS", "SANTO ESTÊVÃO", "SÃO GONÇALO DOS CAMPOS",
        "TANQUINHO", "TEODORO SAMPAIO", "TERRA NOVA"
    ],

    "GANDU - BAIXO SUL" => [
        "ARATUÍPE", "CAIRU", "CAMAMU", "GANDU", "IBIRAPITANGA", "IGRAPIÚNA", "ITUBERÁ",
        "JAGUARIPE", "NILO PEÇANHA", "PIRAÍ DO NORTE", "PRESIDENTE TANCREDO NEVES",
        "TAPEROÁ", "TEOLÂNDIA", "VALENÇA", "WENCESLAU GUIMARÃES"
    ],

    "GUANAMBI - SERTÃO PRODUTIVO" => [
        "BRUMADO", "CACULÉ", "CAETITÉ", "CANDIBA", "CONTENDAS DO SINCORÁ", "DOM BASÍLIO",
        "GUANAMBI", "IBIASSUCÊ", "ITUAÇU", "IUIÚ", "LAGOA REAL", "LIVRAMENTO DE NOSSA SENHORA",
        "MALHADA DE PEDRAS", "PALMAS DE MONTE ALTO", "PINDAÍ", "RIO DO ANTÔNIO",
        "SEBASTIÃO LARANJEIRAS", "TANHAÇU", "TANQUE NOVO", "URANDI"
    ],

    "IPIRÁ - BACIA DO JACUÍPE" => [
        "BAIXA GRANDE", "CAPELA DO ALTO ALEGRE", "CAPIM GROSSO", "GAVIÃO", "IPIRÁ",
        "MAIRI", "NOVA FÁTIMA", "PÉ DE SERRA", "PINTADAS", "QUIXABEIRAS", "RIACHÃO DO JACUÍPE",
        "SÃO JOSÉ DO JACUÍPE", "SERRA PRETA", "VÁRZEA DO ROÇA", "VÁRZEA DO POÇO"
    ],

    "IRECÊ - IRECÊ" => [
        "AMÉRICA DOURADA", "BARRA DO MENDES", "BARRO ALTO", "CAFARNAUM", "CANARANA",
        "CENTRAL", "GENTIO DO OURO", "IBIPEBA", "IBITITÁ", "IPUPIARA", "IRECÊ", "ITAGUAÇU DA BAHIA",
        "JOÃO DOURADO", "JUSSARA", "LAPÃO", "MULUNGU DO MORRO", "PRESIDENTE DUTRA",
        "SÃO GABRIEL", "UIBAÍ", "XIQUE-XIQUE"
    ],

    "ITABERABA - PIEMONTE DO PARAGUAÇU" => [
        "BOA VISTA DO TUPIM", "IAÇU", "IBIQUERA", "ITABERABA", "ITATIM", "LAJEDINHO",
        "MACAJUBA", "MUNDO NOVO", "PIRITIBA", "RAFAEL JAMBEIRO", "RUY BARBOSA",
        "SANTA TERESINHA", "TAPIRAMUTÁ"
    ],

    "ITABUNA - LITORAL SUL" => [
        "ALMADINA", "ARATACA", "AURELINO LEAL", "BARRO PRETO", "BUERAREMA",
        "CAMACAN", "CANAVIEIRAS", "COARACI", "FLORESTA AZUL", "IBICARAI",
        "ILHÉUS", "ITABUNA", "ITACARÉ", "ITAJU DO COLÔNIA", "ITAJUÍPE",
        "ITAPÉ", "ITAPITANGA", "JUSSARI", "MARAÚ", "MASCOTE",
        "PAU BRASIL", "SANTA LUZIA", "SÃO JOSÉ DA VITÓRIA", "UBAITABA",
        "UNA", "URUÇUCA"
    ],
    
    "ITAPETINGA - MÉDIO SUDOESTE DA BAHIA" => [
        "CAATIBA", "FIRMINO ALVES", "IBICUÍ", "IGUAÍ", "ITAMBÉ", "ITAPETINGA",
        "ITARANTIM", "ITORORÓ", "MACARANI", "MAIQUINIQUE", "NOVA CANAÃ",
        "POTIRAGUÁ", "SANTA CRUZ DA VITÓRIA"
    ],
    
    "JAGUAQUARA - VALE DO JIQUIRIÇÁ" => [
        "AMARGOSA", "BREJÕES", "CRAVOLÂNDIA", "ELÍSIO MEDRADO", "IRAJUBA",
        "ITAQUARA", "ITIRUÇU", "JAGUAQUARA", "JIQUIRIÇÁ", "LAFAIETE COUTINHO",
        "LAJE", "LAJEDO DO TABOCAL", "MARACÁS", "MILAGRES", "MUTUÍPE",
        "NOVA ITARANA", "PLANALTINO", "SANTA INÊS", "SÃO MIGUEL DAS MATAS", "UBAÍRA"
    ],
    
    "JEQUIÉ - MÉDIO RIO DE CONTAS" => [
        "AIQUARA", "APUAREMA", "BARRA DO ROCHA", "BOA NOVA", "DÁRIO MEIRA",
        "GONGOGI", "IBIRATAIA", "IPIAÚ", "ITAGI", "ITAGIBÁ", "ITAMARI",
        "JEQUIÉ", "JITAÚNA", "MANOEL VITORINO", "NOVA IBIÁ", "UBATÃ"
    ],
    
    "JUAZEIRO - SERTÃO DO SÃO FRANCISCO" => [
        "CAMPO ALEGRE DE LOURDES", "CANUDOS", "CASA NOVA", "CURAÇÁ", "JUAZEIRO",
        "PILÃO ARCADO", "REMANSO", "SENTO SÉ", "SOBRADINHO", "UAUÁ"
    ],
    
    "MACAÚBAS -  BACIA DO PARAMIRIM" => [
        "BOQUIRA", "BOTUPORÃ", "CATURAMA", "ÉRICO CARDOSO", "IBIPITANGA",
        "MACAÚBAS", "PARAMIRIM", "RIO DE PIRES"
    ],
    
    "METROPOLITANO DE SALVADOR - INTERIOR" => [
        "ABRANTES", "CAMAÇARI", "CANDEIAS", "DIAS D'ÁVILA", "ITAPARICA",
        "LAURO DE FREITAS", "MADRE DE DEUS", "MATA DE SÃO JOÃO", "POJUCA",
        "SÃO FRANCISCO DO CONDE", "SÃO SEBASTIÃO DO PASSÉ", "SIMÕES FILHO", "VERA CRUZ"
    ],
    
    "MIGUEL CALMON - PIEMONTE DA DIAMANTINA" => [
        "CAÉM", "JACOBINA", "MIGUEL CALMON", "MIRANGABA", "OUROLÂNDIA",
        "SAÚDE", "SERROLÂNDIA", "UMBURANAS", "VÁRZEA NOVA"
    ],
    
    "PAULO AFONSO -  ITAPARICA" => [
        "ABARÉ", "CHORROCHÓ", "GLÓRIA", "MACURURÉ", "PAULO AFONSO", "RODELAS"
    ],
    
    "RIBEIRA DO POMBAL - SEMIÁRIDO NORDESTE II" => [
        "ADUSTINA", "ANTAS", "BANZAÊ", "CÍCERO DANTAS", "CIPÓ", "CORONEL JOÃO SÁ",
        "EUCLIDES DA CUNHA", "FÁTIMA", "HELIÓPOLIS", "JEREMOABO", "NOVA SOURE",
        "NOVO TRIUNFO", "PARIPIRANGA", "PEDRO ALEXANDRE", "RIBEIRA DO AMPARO",
        "RIBEIRA DO POMBAL", "SANTA BRÍGIDA", "SÍTIO DO QUINTO"
    ],
    
    "SANTA MARIA DA VITÓRIA - BACIA DO RIO CORRENTE" => [
        "BREJOLÂNDIA", "CANÁPOLIS", "COCOS", "CORIBE", "CORRENTINA", "JABORANDI",
        "SANTA MARIA DA VITÓRIA", "SANTANA", "SÃO FÉLIX DO CORIBE", "SERRA DOURADA",
        "TABOCAS DO BREJO VELHO"
    ],
    
    "SEABRA - CHAPADA DIAMANTINA" => [
        "ABAÍRA", "ANDARAÍ", "BARRA DA ESTIVA", "BONIDAL", "BONITO", "IBICOARA",
        "IBITIARA", "IRAMAIA", "IRAQUARA", "ITAETÉ", "JUSSIAPE", "LENÇÓIS",
        "MARCIONÍLIO SOUZA", "MORRO DO CHAPÉU", "MUCUGÊ", "NOVA REDENÇÃO",
        "NOVO HORIZONTE", "PALMEIRAS", "PIATÃ", "RIO DE CONTAS", "SEABRA",
        "SOUTO SOARES", "UTINGA", "WAGNER"
    ],
    
    "SENHOR DO BONFIM - PIEMONTE NORTE DO ITAPICURU" => [
        "ANDORINHA", "ANTÔNIO GONÇALVES", "CALDEIRÃO GRANDE", "CAMPO FORMOSO",
        "FILADÉLFIA", "JAGUARARI", "PINDOBAÇU", "PONTO NOVO", "SENHOR DO BONFIM"
    ],
    
    "SERRINHA - SISAL" => [
        "ARACI", "BARROCAS", "BIRITINGA", "CANDEAL", "CANSANÇÃO", "CONCEIÇÃO DO COITÉ",
        "ICHU", "ITIÚBA", "LAMARÃO", "MONTE SANTO", "NORDESTINA", "QUEIMADAS",
        "QUIJINGUE", "RETIROLÂNDIA", "SANTA LUZ", "SÃO DOMINGOS", "SERRINHA",
        "TEOFILÂNDIA", "TUCANO", "VALENTE"
    ],
    
    "TEIXEIRA DE FREITAS - EXTREMO SUL" => [
        "ALCOBAÇA", "CARAVELAS", "IBIRAPUÃ", "ITAMARAJU", "ITANHÉM", "JUCURUÇU",
        "LAJEDÃO", "MEDEIROS NETO", "MUCURI", "NOVA VIÇOSA", "PRADO",
        "TEIXEIRA DE FREITAS", "VEREDA"
    ],
    
    "VITÓRIA DA CONQUISTA - SUDOESTE BAIANO" => [
        "ANAGÉ", "ARACATU", "BARRA DO CHOÇA", "BELO CAMPO", "BOM JESUS DA SERRA",
        "CAETANOS", "CÂNDIDO SALES", "CARAÍBAS", "CONDEÚBA", "CORDEIROS",
        "ENCRUZILHADA", "GUAJERU", "JACARACI", "LICÍNIO DE ALMEIDA", "MAETINGA",
        "MIRANTE", "MORTUGABA", "PIRIPÁ", "PLANALTO", "POÇÕES",
        "PRESIDENTE JÂNIO QUANDROS", "RIBEIRÃO DO LARGO", "TREMENDAL", "VITÓRIA DA CONQUISTA"
    ],
    
    "SALVADOR" => [
        "SALVADOR"
    ]

];

// Função auxiliar para obter cor do tipo
function getCorTipo($tipo) {
    $cores = CORES_TIPO;
    return $cores[$tipo] ?? '#6c757d';
}

// Função para obter badge class do status
function getBadgeClassStatus($status) {
    $classes = [
        'Aberto' => 'warning',
        'Em Andamento' => 'info',
        'Aguardando' => 'secondary',
        'Resolvido' => 'success',
        'Fechado' => 'success',
        'Cancelado' => 'danger'
    ];
    return $classes[$status] ?? 'secondary';
}

// Função para obter lista de territórios
function getTerritorios() {
    return array_keys($GLOBALS['territorios']);
}

// Função para obter cidades de um território
function getCidades($territorio) {
    return $GLOBALS['territorios'][$territorio] ?? [];
}
?>
