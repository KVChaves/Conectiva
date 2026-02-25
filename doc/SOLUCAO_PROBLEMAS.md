# Guia de Correção de Problemas - Sistema Conectiva

## 🔧 Problemas Comuns e Soluções

### ❌ Problema 1: Caracteres com encoding errado (ABARÃ‰ ao invés de ABARÉ)

**Causa:** Incompatibilidade entre o charset do banco de dados (latin1) e do PHP (UTF-8).

**Solução:**

1. **Se as tabelas ainda não foram criadas:**
   - Use o arquivo `database/schema.sql` atualizado (já está em UTF-8)

2. **Se as tabelas já existem com dados:**
   
   **Opção A - Converter as tabelas:**
   ```sql
   -- Execute no phpMyAdmin:
   ALTER TABLE `conectiva` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
   ALTER TABLE `conectiva_helpdesk` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
   ALTER DATABASE `conectiva` CHARACTER SET utf8 COLLATE utf8_general_ci;
   ```
   
   **Opção B - Recriar as tabelas:**
   ```sql
   -- 1. Faça backup dos dados
   -- 2. Delete as tabelas antigas
   DROP TABLE IF EXISTS `conectiva_helpdesk`;
   DROP TABLE IF EXISTS `conectiva`;
   
   -- 3. Execute o schema.sql novamente
   -- 4. Reimporte os dados
   ```

3. **Verificar a conexão PHP:**
   - O arquivo `config/database.php` já foi atualizado com:
   ```php
   $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
   $conn->exec("SET NAMES utf8");
   $conn->exec("SET CHARACTER SET utf8");
   ```

### ❌ Problema 2: Mapa não carrega (tela branca ou sem marcadores)

**Possíveis Causas e Soluções:**

1. **Erro de JavaScript:**
   - Abra o Console do navegador (F12 → Console)
   - Veja se há erros em vermelho
   - Os erros mais comuns são:
     - `Leaflet is not defined` → Problema ao carregar a biblioteca
     - `Cannot read property 'LatLng' of undefined` → Leaflet não carregou

2. **Problema de rede/CDN:**
   - Verifique se você tem internet funcionando
   - Teste abrindo: `teste_mapa.html` no navegador
   - Se o teste_mapa.html funcionar, o problema está na página principal

3. **Dados com coordenadas inválidas:**
   - Verifique no banco se latitude e longitude estão preenchidas:
   ```sql
   SELECT id, localidade, latitude, longitude FROM conectiva;
   ```
   - Latitude deve estar entre -90 e 90
   - Longitude deve estar entre -180 e 180
   - Para Bahia: latitude entre -18 e -8, longitude entre -47 e -37

4. **Conflito de CSS:**
   - Verifique se o CSS do Leaflet está carregando
   - Inspecione o elemento do mapa e veja se tem altura definida

5. **Problema no PHP:**
   - Verifique se há erros PHP:
   ```php
   // Adicione no topo do index.php temporariamente:
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```

### ❌ Problema 3: Exportação Excel com acentos errados

**Solução:** Já corrigido! O arquivo `exportar.php` foi atualizado para:
- Usar charset UTF-8
- Remover caracteres especiais dos headers
- Headers sem acentos: "Territorio" ao invés de "Território"

**Se ainda tiver problemas:**
1. Abra o CSV no Excel
2. Vá em Dados → De Texto/CSV
3. Selecione o arquivo
4. Em "Origem do Arquivo", escolha "65001: Unicode (UTF-8)"
5. Clique em "Carregar"

### 🔍 Como Testar se Está Tudo OK

1. **Teste o encoding:**
   ```sql
   -- Execute no phpMyAdmin:
   SELECT localidade FROM conectiva WHERE localidade LIKE '%é%' OR localidade LIKE '%ç%' OR localidade LIKE '%ã%';
   ```
   - Deve mostrar os caracteres corretamente

2. **Teste o mapa:**
   - Acesse: `http://localhost/conectiva/teste_mapa.html`
   - Deve mostrar um mapa da Bahia com 3 marcadores

3. **Teste a listagem:**
   - Acesse: `http://localhost/conectiva/pontos.php`
   - Os nomes devem aparecer corretamente

### 📋 Checklist de Instalação Correta

- [ ] Banco de dados criado com charset UTF-8
- [ ] Tabelas criadas com o schema.sql atualizado
- [ ] Arquivo config/database.php com as credenciais corretas
- [ ] PHP versão 7.4 ou superior instalado
- [ ] Extensão PDO do PHP habilitada
- [ ] teste_mapa.html carrega corretamente
- [ ] Dados inseridos aparecem com acentuação correta

### 🆘 Solução Rápida: Começar do Zero

Se nada funcionar, siga estes passos:

1. **Backup (se houver dados importantes):**
   ```sql
   SELECT * FROM conectiva INTO OUTFILE '/tmp/backup_conectiva.csv';
   ```

2. **Limpar tudo:**
   ```sql
   DROP DATABASE IF EXISTS conectiva;
   CREATE DATABASE conectiva CHARACTER SET utf8 COLLATE utf8_general_ci;
   USE conectiva;
   ```

3. **Importar schema.sql atualizado:**
   - No phpMyAdmin, selecione o banco `conectiva`
   - Vá em "Importar"
   - Selecione o arquivo `database/schema.sql`
   - Clique em "Executar"

4. **Testar:**
   - Acesse `index.php` → deve abrir sem erros
   - Acesse `pontos.php` → deve listar os dados de exemplo
   - Verifique se "Vitória da Conquista" aparece corretamente

### 📞 Ainda com problemas?

Verifique os logs de erro:
- **Apache:** `/var/log/apache2/error.log`
- **PHP:** Configure em `php.ini` ou use `error_log()`
- **MySQL:** `/var/log/mysql/error.log`

Ou entre em contato com o suporte técnico enviando:
1. Screenshot do erro
2. Mensagens do Console do navegador (F12)
3. Versão do PHP: `php -v`
4. Versão do MySQL: `mysql --version`
