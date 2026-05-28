# Documentação 
Documento baseado na ISO/IEEE 29148:2018

## Registro iniciado dia 26/05

## 1. Introdução 

O projeto Discos tem como intuito registrar usuários que querem vender ou comprar discos de venil, podendo até mesmo leiloar. O sistema utiliza PHP para a interface de usuário e lógica de controle, enquanto o banco de dados SQLite serve para salvar os dados do usuário. 

### 1.2 Escopo

O sistema permitirá:
* cadastro e login de usuário 
* cadastro de discos
* cadastro de vitrolas
* cadastro de acessórios de vitrolas
* 

O sistema utilizará:
* HTML
* CSS
* JavaScript 
* Arquitetura MVC
* PostgresSQL
* PHP

---

### 1.3 Definições 

| Termo | Definições |
| ----- | ---------- |


## 2. Descrição Geral do Sistema 

### 2.1 Funções do Sistema 
O sistema deve:
* cadastrar produtos 
* cadastrar usuários 
* exibir produtos disponíveis
* exibir produtos esgotados
* exibir produtos com desconto

## 3. Requisitos do Sistema 

### 3.1 Requisitos Funcionais 

#### RF-001: Cadastro do Usuário

**Descrição:** Permitir cadastrar um usuário
**Prioridade:** Alta
**Versão:** 1.0

**Data:** 2026-05-28
**Rastreabilidade:** Necessidade do Stakeholder

**Critérios de Aceitação:**
- [ ] Entrada de Dados: Nome completo, email, senha
- [ ] Saída: Notificação ao Usuário 

---

#### RF-002: Cadastro de Produto

**Descrição:** Permitir cadastrar um produto
**Prioridade:** Alta
**Versão:** 1.0

**Data:** 2026-05-28
**Rastreabilidade:** Necessidade do Stakeholder 02

**Critérios de Aceitação:**
- [ ] Entrada de Dados: Nome da música, Categoria, Preço, Estoque, Estado do produto e Descrição
- [ ] Validação de Campos
- [ ] Verificação de Duplicidade
- [ ] Saída: Notificação ao Usuário 

---

#### RF-003: Atualizar Estoque

**Descrição:** Permitir atualizar de dados de items existentes
**Prioridade:** Alta
**Versão:** 1.0

**Data:** 2026-05-28
**Rastreabilidade:** Necessidade do Stakeholder 03

**Critérios de Aceitação:**
- [ ] Verificar se item está cadastrado
- [ ] Entrada de Dados: Nome da música, Categoria, Preço, Quantidade, Estado do produto 
- [ ] Validação de Campos
- [ ] Saída: Notificação ao Usuário 

---

#### RF-004: Registro de Vendas

**Descrição:** Permitir vender produtos

**Prioridade:** Alta

**Versão:** 1.0

**Data:** 2026-04-14

**Rastreabilidade:** Necessidade do Stakeholder 004 

**Critérios de Aceitação:**
- [ ] Venda de Produtos Cadastro
- [ ] Verificação de Quantidade
- [ ] Atualização do Estoque 
- [ ] Notificação de Venda Realizada

### 3.2 Requisitos Não Funcionais 

#### RNF-001: 

---

## 4. Regras do Negócio 

Tabela de Regras de Negócio 

| Regras de Negócio |                      Descrição                        |
| ----------------- | ----------------------------------------------------- |
|      RN-001       |
|      RN-002       |
|      RN-003       |
|      RN-004       |

