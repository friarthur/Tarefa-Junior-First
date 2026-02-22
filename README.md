# 🚀 Sistema de Indicação MisterCheff  
## Indique e Ganhe

Sistema desenvolvido em **PHP puro** para a gestão de marketing de indicação entre unidades parceiras da plataforma MisterCheff.

Este projeto foi desenvolvido como **avaliação técnica Full Stack**, contemplando backend, banco de dados, regras de negócio, antifraude e integração SMTP.

---

# 📌 Objetivo

Permitir que lojas parceiras indiquem novos estabelecimentos para a plataforma.

Quando um indicado se torna cliente, a loja que realizou a indicação recebe **R$ 100,00 de bônus por conversão**, com cálculo acumulativo.

---

# 🧩 Funcionalidades Principais

## 🖥 Dashboard de Gestão
- Visualização consolidada de métricas
- Total de indicados
- Total de convertidos
- Saldo acumulado em bônus
- Status dos convites

---

## 🔗 Gerenciamento de Links
- Geração de identificador público único (`ref_code`)
- Não exposição de ID primário do banco
- Link personalizado por loja

Exemplo:
http://localhost/public/index.php?ref=ABC123XYZ

---

## 📲 Compartilhamento
- Botão para copiar link
- Integração com API do WhatsApp para compartilhamento rápido

---

## 🛡 Protocolo Antifraude

- Bloqueio de autoindicação (validação do e-mail da loja)
- Bloqueio de convites duplicados para o mesmo e-mail na mesma unidade
- Registro de cliques com:
  - Endereço IP
  - User Agent
  - Timestamp

---

## 📧 Comunicação SMTP

- Integração real com **PHPMailer**
- Envio de e-mail em HTML
- Registro de logs em `logs/email_log.txt`
- Estrutura preparada para ambiente de produção

---

## 🔄 Conversão de Leads

- Alteração manual do status para `virou_cliente`
- Conversão automatizada do indicado em nova loja
- Expansão em rede (modelo de crescimento escalável)

---

# ⚙️ Requisitos Técnicos

- PHP 8.1+
- MySQL 5.7+ ou MariaDB
- Composer (para PHPMailer)
- Extensão PDO MySQL habilitada

---

# 📁 Estrutura de Pastas
/
├── includes/
│ ├── conexao.php
│ └── funcoes.php
│
├── logs/
│ └── email_log.txt
│
├── portal/
│ ├── index.php
│ ├── cadastrar.php
│ └── marcar_cliente.php
│
├── public/
│ └── index.php
│
└── vendor/

---

# 🗄 Esquema do Banco de Dados

```sql
CREATE TABLE lojas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    ref_code VARCHAR(50) UNIQUE NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE indicados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_loja INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    status ENUM('convidado', 'virou_cliente') DEFAULT 'convidado',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_loja) REFERENCES lojas(id)
);

CREATE TABLE cliques (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_loja INT NOT NULL,
    ip VARCHAR(45) NOT NULL,
    user_agent TEXT,
    data_clique TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_loja) REFERENCES lojas(id)
);
