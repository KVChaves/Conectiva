-- Script para converter tabelas existentes de latin1 para UTF-8
-- Execute este script se você já tinha tabelas criadas com latin1

-- Alterar charset da tabela conectiva
ALTER TABLE `conectiva` 
  CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Alterar charset da tabela conectiva_helpdesk
ALTER TABLE `conectiva_helpdesk` 
  CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Alterar charset do banco de dados (opcional)
ALTER DATABASE `conectiva` 
  CHARACTER SET utf8 
  COLLATE utf8_general_ci;
