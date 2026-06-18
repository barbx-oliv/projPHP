# Documentação RetroMusic
**Baseado na ISO/IEEE 29148:2018**

## Registro iniciado: 26/05/2026

---

## 1. Como rodar o código 
Para rodar e testar localmente, siga o passo a passo abaixo.

### 1.2 Pré-requisitos 
PHP 8.x ou superior
​Um gerenciador de Banco de Dados (PostgreSQL 14+ ou MySQL/MariaDB)
​Um cliente Git instalado (ou o arquivo .zip do projeto)
​Servidor Web (Apache/Nginx) ou uso do servidor embutido do PHP

### 1.3 Passo a passo para execução 

Clone o repositório (ou extraia o arquivo ZIP) dentro da pasta do seu servidor web
git clone https://github.com/seu-usuario/retromusic.git
cd retromusic

Se estiver no Linux/Apache, mova para a pasta pública se necessário:
# cp -r retromusic/ /var/www/html/

Crie a pasta de uploads e ajuste as permissões de escrita
mkdir -p uploads && chmod 755 uploads


### 1.4 Configurando o Banco de Dados 

Se você estiver usando o **PostgreSQL**:
# Cria o banco de dados
createdb -U postgres retromusic

Restaura a estrutura e os dados iniciais do arquivo SQL
psql -U postgres -d retromusic -f sql/retromusic.sql

Agora se você estiver pelo **MySQL**
# Acesse o terminal do seu MySQL
mysql -u root -p

Dentro do prompt do MySQL, execute os comandos:
CREATE DATABASE retromusic;
USE retromusic;
SOURCE sql/retromusic.sql;
EXIT;



## 7. Como Rodar o Projeto

Ajuste as credenciais de conexão do banco de dados (Host, Usuário, Senha)
nano config/db.php

Inicie o servidor embutido do PHP (caso não esteja usando Apache/Nginx)
php -S localhost:8000


## 2. Introdução

O projeto **RetroMusic** é um marketplace online para compra e venda de discos de vinil, CDs e lotes. Qualquer usuário cadastrado pode anunciar seus produtos, e compradores podem navegar pelo catálogo, filtrar por gênero e visualizar detalhes de cada item.

O sistema utiliza **PHP** para a interface de usuário e lógica de controle, **MySQL** como banco de dados relacional e **CSS** puro para o estilo visual.

### 2.2 Escopo

O sistema permitirá:

* Cadastro e login de usuário (com hash de senha bcrypt)
* Anúncio de discos de vinil individualmente
* Anúncio de CDs individualmente
* Criação de lotes (pacotes com 2 ou mais itens)
* Listagem pública de produtos com filtros de gênero e ordenação por preço
* Painel do vendedor: visualizar, editar e pausar anúncios
* Sistema de desconto percentual por produto

O sistema utilizará:

* PHP 8.x
* HTML5 + CSS3
* JavaScript (vanilla, para filtros)
* PostgreSQL 14+
* Arquitetura sem framework (MVC manual simples)

---

### 2.3 Definições

| Termo | Definição |
| ----- | --------- |
| Vinil | Disco de vinil analógico de 7", 10" ou 12" |
| CD | Compact Disc digital |
| Lote | Pacote com dois ou mais itens (vinil, CD ou misto) vendidos juntos |
| Anúncio | Produto cadastrado por um vendedor e exibido no catálogo |
| Estado (Capa/Mídia) | Grau de conservação segundo escala Goldmine: Mint, Near Mint, VG+, VG, G, F |
| Ativo | Produto visível no catálogo para compradores |
| Vendido | Produto marcado como comercializado, removido do catálogo |

---

## 3. Descrição Geral do Sistema

### 3.1 Funções do Sistema

O sistema deve:

* Cadastrar e autenticar usuários
* Permitir que usuários cadastrem produtos (vinil, CD, lote)
* Exibir catálogo com produtos disponíveis (ativos, não vendidos)
* Exibir destaque de produtos com desconto na página inicial
* Filtrar produtos por gênero e ordenar por preço ou data
* Exibir painel do usuário com seus anúncios e status

### 3.2 Estrutura de Arquivos

```
retromusic/
├── config/
│   └── db.php            ← Conexão PDO com o banco
├── css/
│   └── style.css         ← Estilos completos
├── img/                  ← Imagens do sistema e uploads
├── includes/
│   └── form_produto.php  ← Formulário de anúncio 
├── sql/
│   └── retromusic.sql    ← Script de criação do banco
├── uploads/              ← Imagens enviadas pelos usuários
├── header.php
├── footer.php
├── index.php             ← Página inicial com ofertas
├── login.php
├── cadastro.php
├── disco.php             ← Listagem de discos de vinil
├── cd.php                ← Listagem de CDs
├── lotes.php             ← Listagem de lotes
├── perfil.php            ← Painel do usuário
├── produto.php           ← Detalhe de produto (a criar)
└── editar_produto.php    ← Edição de anúncio (a criar)
```

---

## 4. Requisitos do Sistema

### 4.1 Requisitos Funcionais

#### RF-001: Cadastro de Usuário

**Descrição:** Permitir que um visitante crie uma conta no sistema.
**Prioridade:** Alta — **Versão:** 1.0 — **Data:** 2026-05-28
**Rastreabilidade:** Necessidade do Stakeholder 01

**Critérios de Aceitação:**
- [x] Campos: Nome completo, e-mail, senha, confirmação de senha
- [x] Validação de e-mail (formato e duplicidade)
- [x] Senha com mínimo de 6 caracteres
- [x] Senha armazenada com hash bcrypt
- [x] Notificação de sucesso ou erro ao usuário

---

#### RF-002: Login de Usuário

**Descrição:** Permitir autenticação de usuário cadastrado.
**Prioridade:** Alta — **Versão:** 1.0 — **Data:** 2026-05-28
**Rastreabilidade:** Necessidade do Stakeholder 01

**Critérios de Aceitação:**
- [x] Campos: E-mail e senha
- [x] Verificação com password_verify (bcrypt)
- [x] Sessão PHP iniciada após login
- [x] Redirecionamento para index após login
- [x] Mensagem de erro para credenciais inválidas

---

#### RF-003: Cadastro de Produto

**Descrição:** Permitir que um usuário logado anuncie um produto.
**Prioridade:** Alta — **Versão:** 1.0 — **Data:** 2026-05-28
**Rastreabilidade:** Necessidade do Stakeholder 02

**Critérios de Aceitação:**
- [x] Campos: Nome do álbum, artista, tipo (vinil/CD), gênero, ano, preço, desconto, estado da capa, estado da mídia, descrição, imagem
- [x] Validação de todos os campos obrigatórios
- [x] Upload de imagem com validação de extensão e tamanho (máx. 3 MB)
- [x] Verificação de autenticação antes de exibir formulário
- [x] Notificação de sucesso ao usuário

---

#### RF-004: Listagem de Produtos

**Descrição:** Exibir catálogo de produtos disponíveis para compra.
**Prioridade:** Alta — **Versão:** 1.0 — **Data:** 2026-05-28
**Rastreabilidade:** Necessidade do Stakeholder 02

**Critérios de Aceitação:**
- [x] Exibe apenas produtos ativos e não vendidos
- [x] Filtro por gênero musical
- [x] Ordenação por data, menor preço e maior preço
- [x] Cards com imagem, nome, estado, preço e desconto

---

#### RF-005: Cadastro de Lote

**Descrição:** Permitir que um usuário crie um lote com 2 ou mais produtos.
**Prioridade:** Média — **Versão:** 1.0 — **Data:** 2026-05-28
**Rastreabilidade:** Necessidade do Stakeholder 02

**Critérios de Aceitação:**
- [ ] Seleção de produtos existentes do vendedor para compor o lote
- [ ] Campos: Título, descrição, tipo de mídia, preço do lote
- [ ] Mínimo de 2 itens por lote
- [ ] Notificação de sucesso ao usuário

---

#### RF-006: Painel do Vendedor

**Descrição:** Área do usuário para gerenciar seus anúncios.
**Prioridade:** Alta — **Versão:** 1.0 — **Data:** 2026-05-28

**Critérios de Aceitação:**
- [x] Listagem de todos os anúncios do usuário com status
- [x] Link para edição de cada produto
- [ ] Ação para pausar/reativar anúncio
- [ ] Ação para marcar como vendido

---

### 3.2 Requisitos Não Funcionais

#### RNF-001: Segurança

* Senhas armazenadas com bcrypt (PASSWORD_DEFAULT)
* Proteção contra SQL Injection via PDO com prepared statements
* Sessões PHP para controle de autenticação
* Páginas restritas redirecionam para login.php se não autenticado

#### RNF-002: Usabilidade

* Interface responsiva para mobile e desktop
* Paleta de cores consistente em todas as páginas
* Feedback visual em todos os formulários (mensagens de erro e sucesso)
* Navegação fixa no topo para acesso rápido às categorias

#### RNF-003: Desempenho

* Banco de dados indexado nos campos mais consultados (tipo, ativo, vendido)
* Imagens de upload limitadas a 3 MB
* Queries limitadas a evitar N+1 (lotes buscam imagens com prepared statement)

---

## 5. Regras de Negócio

| Regra | Descrição |
| ----- | --------- |
| RN-001 | Um produto só aparece no catálogo se `ativo = 1` e `vendido = 0` |
| RN-002 | O desconto é percentual (0–90%) e calculado no PHP: `preco * (1 - desconto/100)` |
| RN-003 | Um lote deve conter no mínimo 2 produtos |
| RN-004 | Somente o dono do produto pode editá-lo ou excluí-lo |
| RN-005 | O estado de capa e de mídia seguem a escala Goldmine padrão do mercado |
| RN-006 | Um produto marcado como vendido é removido automaticamente do catálogo |
| RN-007 | O e-mail é único por usuário — não é possível cadastrar dois usuários com o mesmo e-mail |

---

## 6. Banco de Dados

### Diagrama de tabelas

```
usuarios (id, nome, email, senha, created_at, updated_at)
    │
    ├── produtos (id, usuario_id→, nome, artista, tipo, genero, ano,
    │             preco, desconto_percent, estado_capa, estado_midia,
    │             descricao, imagem, ativo, vendido, created_at, updated_at)
    │
    └── lotes (id, usuario_id→, titulo, descricao, tipo_midia,
               preco, ativo, vendido, created_at, updated_at)
                   │
                   └── lote_itens (id, lote_id→, produto_id→)
```

