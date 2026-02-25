# Sistema de Gestão de Pontos de Internet - Conectiva Bahia

Sistema web simples em PHP para gerenciar pontos de internet distribuídos pela Bahia.

## 📋 Funcionalidades

- **Mapa Interativo**: Visualização de todos os pontos em um mapa com marcadores coloridos por tipo
- **Listagem de Pontos**: Gerenciar pontos com opções de adicionar, editar, detalhar e excluir
- **Relatórios e Dashboards**: Visualização de estatísticas e exportação de dados em Excel
- **Gerenciamento de Chamados**: Sistema de helpdesk para abertura e acompanhamento de problemas

## 🚀 Instalação

### Requisitos
- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor Apache ou Nginx
- phpMyAdmin (opcional, para gerenciar o banco)

### Passo a Passo

1. **Clone ou extraia os arquivos** para a pasta do seu servidor web (ex: `htdocs`, `www`, `public_html`)

2. **Configure o banco de dados**:
   - Acesse o phpMyAdmin
   - Crie um banco de dados chamado `conectiva`
   - Importe o arquivo `database/schema.sql` para criar as tabelas
   - Ou execute o SQL manualmente no phpMyAdmin

3. **Configure a conexão com o banco**:
   - Abra o arquivo `config/database.php`
   - Ajuste as credenciais se necessário:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_NAME', 'conectiva');
     ```

4. **Acesse o sistema**:
   - Abra seu navegador
   - Acesse: `http://localhost/conectiva` (ajuste conforme sua configuração)

## 📁 Estrutura do Projeto

```
conectiva/
├── config/
│   └── database.php          # Configuração do banco de dados
├── templates/
│   └── layout.php            # Template base com sidebar
├── database/
│   └── schema.sql            # Script SQL para criar tabelas
├── index.php                 # Página do mapa de pontos
├── pontos.php                # Listagem de pontos
├── adicionar_ponto.php       # Adicionar novo ponto
├── editar_ponto.php          # Editar ponto existente
├── relatorios.php            # Página de relatórios
├── chamados.php              # Gerenciamento de chamados
├── exportar.php              # Exportação de dados em CSV/Excel
└── README.md                 # Este arquivo
```

## 🗺️ Como Usar

### Mapa de Pontos
- Visualize todos os pontos no mapa da Bahia
- Cada cor representa um tipo de conexão:
  - 🟢 Verde: Fibra
  - 🔵 Azul: Rádio
  - 🟠 Laranja: Satélite
  - 🟣 Roxo: Móvel
  - ⚫ Cinza: Outros
- Clique nos marcadores para ver detalhes e ações

### Gerenciar Pontos
1. Clique em "Listar Pontos" no menu lateral
2. Use o botão "Adicionar Ponto" para cadastrar novos
3. Clique no mapa para definir coordenadas automaticamente
4. Use os botões de ação para editar ou excluir

### Relatórios
- Visualize dashboards com estatísticas
- Exporte dados em formato Excel/CSV
- Três tipos de relatório:
  - Pontos: todos os dados dos pontos
  - Chamados: histórico de chamados
  - Completo: dados combinados

### Chamados
1. Clique em "Chamados" no menu
2. Use "Abrir Novo Chamado" para registrar problemas
3. Selecione o ponto afetado e o tipo de problema
4. Acompanhe o status (Aberto/Fechado)
5. Use filtros para encontrar chamados específicos

## 🔧 Personalização

### Alterar Cores dos Tipos
Edite o array `$coresTipo` em `index.php` e `relatorios.php`:
```php
$coresTipo = [
    'Fibra' => '#28a745',
    'Radio' => '#007bff',
    // ...
];
```

### Adicionar Novos Tipos de Conexão
1. Adicione a opção nos selects de `adicionar_ponto.php` e `editar_ponto.php`
2. Adicione a cor correspondente no array de cores

### Adicionar Novos Campos
1. Adicione a coluna na tabela do banco de dados
2. Atualize os formulários de cadastro/edição
3. Atualize as queries SQL correspondentes

## 🛠️ Tecnologias Utilizadas

- **Backend**: PHP 7.4+
- **Banco de Dados**: MySQL
- **Frontend**: 
  - Bootstrap 5.3
  - Bootstrap Icons
  - Leaflet.js (mapas)
  - Chart.js (gráficos)
  - jQuery

## 📝 Banco de Dados

### Tabela `conectiva`
Armazena os dados dos pontos de internet:
- id, localidade, território, cidade, endereço
- latitude, longitude (coordenadas GPS)
- velocidade, tipo de conexão
- data_instalacao, observacao
- data_criacao, data_atualizacao (automáticos)

### Tabela `conectiva_helpdesk`
Armazena os chamados de suporte:
- id, ponto_id (FK para conectiva)
- tipo_problema, status
- data_abertura, data_fechamento

## 🐛 Solução de Problemas

### Erro de conexão com banco
- Verifique as credenciais em `config/database.php`
- Confirme que o MySQL está rodando
- Verifique se o banco `conectiva` existe

### Mapa não carrega
- Verifique sua conexão com a internet (Leaflet requer internet)
- Abra o console do navegador (F12) para ver erros

### Erro ao exportar
- Verifique permissões de escrita
- Confirme que há dados para exportar

## 📞 Suporte

Para dúvidas ou problemas:
1. Verifique este README
2. Revise os logs de erro do PHP
3. Consulte a documentação das bibliotecas usadas

## 📄 Licença

Sistema desenvolvido para uso interno da instituição.

---

**Desenvolvido para Conectiva Bahia** 🌐
Sistema de Gestão de Pontos de Internet
