# Banco de dados `lojita` — Dicionário de Dados (formato compacto)

> SGBD: MariaDB 10.4.32 • Engine: InnoDB • Charset/Collation: utf8mb4 / utf8mb4_unicode_ci

### Diagrama (png): `Banco_de_dados_diagrama.png`.

---

## Convenções usadas
- `[PK]` = chave primária • `[FK → tabela.coluna]` • `[UNIQUE]` • `[INDEX]` • `[AI]` = AUTO_INCREMENT
- `NULL` só aparece quando **permitido**; caso contrário é `NOT NULL` por padrão.

---

## users
```
Engine=InnoDB  Charset=utf8mb4_unicode_ci

Colunas
- id: BIGINT(20) UNSIGNED [PK][AI]
- name: VARCHAR(255)
- email: VARCHAR(255) [UNIQUE]
- role: ENUM('cliente','adm','gerente') DEFAULT 'cliente' [INDEX]
- email_verified_at: TIMESTAMP NULL
- password: VARCHAR(255)
- remember_token: VARCHAR(100) NULL
- created_at: TIMESTAMP NULL
- updated_at: TIMESTAMP NULL

Índices
- PRIMARY KEY (id)
- UNIQUE    (email)
- INDEX     (role)

Relacionamentos (saída)
- addresses.user_id   [FK → users.id] ON DELETE CASCADE
- orders.user_id      [FK → users.id] ON DELETE CASCADE
- support_tickets.user_id [FK → users.id] ON DELETE CASCADE
- support_messages.user_id [FK → users.id] ON DELETE CASCADE
```

## addresses
```
Engine=InnoDB  Charset=utf8mb4_unicode_ci

Colunas
- id: BIGINT(20) UNSIGNED [PK][AI]
- user_id: BIGINT(20) UNSIGNED [FK → users.id] [UNIQUE]
- logradouro: VARCHAR(255)
- numero: VARCHAR(50)
- complemento: VARCHAR(255) NULL
- bairro: VARCHAR(120)
- cidade: VARCHAR(120)
- estado: CHAR(2)
- cep: VARCHAR(9)
- created_at: TIMESTAMP NULL
- updated_at: TIMESTAMP NULL

Índices
- PRIMARY KEY (id)
- UNIQUE      (user_id)
- INDEX       (estado, cidade)

Ações de FK
- user_id → users.id ON DELETE CASCADE
```

## products
```
Engine=InnoDB  Charset=utf8mb4_unicode_ci

Colunas
- id: BIGINT(20) UNSIGNED [PK][AI]
- sku: VARCHAR(255) NULL [UNIQUE]
- name: VARCHAR(255)
- slug: VARCHAR(255) [UNIQUE]
- description: TEXT NULL
- stock: INT(10) UNSIGNED DEFAULT 0
- price: DECIMAL(10,2)
- cost_price: DECIMAL(10,2) DEFAULT 0.00
- image_url: VARCHAR(255) NULL
- image_path: VARCHAR(255) NULL
- active: TINYINT(1) DEFAULT 1
- created_at: TIMESTAMP NULL
- updated_at: TIMESTAMP NULL

Índices
- PRIMARY KEY (id)
- UNIQUE      (slug)
- UNIQUE      (sku)

Relacionamentos (entrada/saída)
- order_items.product_id [FK → products.id] ON DELETE CASCADE
```

## orders
```
Engine=InnoDB  Charset=utf8mb4_unicode_ci

Colunas
- id: BIGINT(20) UNSIGNED [PK][AI]
- user_id: BIGINT(20) UNSIGNED [FK → users.id]
- total: DECIMAL(10,2) DEFAULT 0.00
- status: VARCHAR(255) DEFAULT 'novo'
- fulfillment_status: VARCHAR(30) DEFAULT 'aguardando'
- tracking_code: VARCHAR(255) NULL
- created_at: TIMESTAMP NULL
- updated_at: TIMESTAMP NULL
- customer_name: VARCHAR(255) NULL
- customer_email: VARCHAR(255) NULL
- pix_txid: VARCHAR(35) NULL
- pix_payload: TEXT NULL

Índices
- PRIMARY KEY (id)
- INDEX       (user_id)

Ações de FK
- user_id → users.id ON DELETE CASCADE

Relacionamentos (saída)
- order_items.order_id    [FK → orders.id] ON DELETE CASCADE
- support_tickets.order_id [FK → orders.id] ON DELETE CASCADE
```

## order_items
```
Engine=InnoDB  Charset=utf8mb4_unicode_ci

Colunas
- id: BIGINT(20) UNSIGNED [PK][AI]
- order_id: BIGINT(20) UNSIGNED [FK → orders.id]
- product_id: BIGINT(20) UNSIGNED [FK → products.id]
- quantity: INT(10) UNSIGNED
- unit_price: DECIMAL(10,2)
- total: DECIMAL(10,2)
- created_at: TIMESTAMP NULL
- updated_at: TIMESTAMP NULL

Índices
- PRIMARY KEY (id)
- INDEX       (order_id)
- INDEX       (product_id)

Ações de FK
- order_id  → orders.id   ON DELETE CASCADE
- product_id→ products.id ON DELETE CASCADE
```

## support_tickets
```
Engine=InnoDB  Charset=utf8mb4_unicode_ci

Colunas
- id: BIGINT(20) UNSIGNED [PK][AI]
- user_id: BIGINT(20) UNSIGNED [FK → users.id]
- order_id: BIGINT(20) UNSIGNED NULL [FK → orders.id]
- status: VARCHAR(255) DEFAULT 'aberto'
- subject: VARCHAR(255) NULL
- closed_at: TIMESTAMP NULL
- created_at: TIMESTAMP NULL
- updated_at: TIMESTAMP NULL

Índices
- PRIMARY KEY (id)
- INDEX       (user_id)
- INDEX       (order_id)

Ações de FK
- user_id  → users.id  ON DELETE CASCADE
- order_id → orders.id ON DELETE CASCADE
```

## support_messages
```
Engine=InnoDB  Charset=utf8mb4_unicode_ci

Colunas
- id: BIGINT(20) UNSIGNED [PK][AI]
- support_ticket_id: BIGINT(20) UNSIGNED [FK → support_tickets.id]
- user_id: BIGINT(20) UNSIGNED [FK → users.id]
- sender_type: ENUM('customer','store')
- body: TEXT
- attachments: LONGTEXT COLLATE utf8mb4_bin NULL  [CHECK json_valid(attachments)]
- created_at: TIMESTAMP NULL
- updated_at: TIMESTAMP NULL

Índices
- PRIMARY KEY (id)
- INDEX       (user_id)
- INDEX       (sender_type)
- INDEX       (support_ticket_id, created_at)

Ações de FK
- support_ticket_id → support_tickets.id ON DELETE CASCADE
- user_id          → users.id            ON DELETE CASCADE
```

## sessions
```
Engine=InnoDB  Charset=utf8mb4_unicode_ci

Colunas
- id: VARCHAR(255) [PK]
- user_id: BIGINT(20) UNSIGNED NULL
- ip_address: VARCHAR(45) NULL
- user_agent: TEXT NULL
- payload: LONGTEXT
- last_activity: INT(11)

Índices
- PRIMARY KEY (id)
- INDEX       (user_id)
- INDEX       (last_activity)

Observação: não há FK formal para users.user_id (padrão do Laravel).
```

## password_reset_tokens
```
Engine=InnoDB  Charset=utf8mb4_unicode_ci

Colunas
- email: VARCHAR(255) [PK]
- token: VARCHAR(255)
- created_at: TIMESTAMP NULL
```

## cache
```
Engine=InnoDB  Charset=utf8mb4_unicode_ci

Colunas
- key: VARCHAR(255) [PK]
- value: MEDIUMTEXT
- expiration: INT(11)
```

## cache_locks
```
Engine=InnoDB  Charset=utf8mb4_unicode_ci

Colunas
- key: VARCHAR(255) [PK]
- owner: VARCHAR(255)
- expiration: INT(11)
```

## jobs
```
Engine=InnoDB  Charset=utf8mb4_unicode_ci

Colunas
- id: BIGINT(20) UNSIGNED [PK][AI]
- queue: VARCHAR(255)
- payload: LONGTEXT
- attempts: TINYINT(3) UNSIGNED
- reserved_at: INT(10) UNSIGNED NULL
- available_at: INT(10) UNSIGNED
- created_at: INT(10) UNSIGNED

Índices
- PRIMARY KEY (id)
- INDEX       (queue)
```

## job_batches
```
Engine=InnoDB  Charset=utf8mb4_unicode_ci

Colunas
- id: VARCHAR(255) [PK]
- name: VARCHAR(255)
- total_jobs: INT(11)
- pending_jobs: INT(11)
- failed_jobs: INT(11)
- failed_job_ids: LONGTEXT
- options: MEDIUMTEXT NULL
- cancelled_at: INT(11) NULL
- created_at: INT(11)
- finished_at: INT(11) NULL
```

## failed_jobs
```
Engine=InnoDB  Charset=utf8mb4_unicode_ci

Colunas
- id: BIGINT(20) UNSIGNED [PK][AI]
- uuid: VARCHAR(255) [UNIQUE]
- connection: TEXT
- queue: TEXT
- payload: LONGTEXT
- exception: LONGTEXT
- failed_at: TIMESTAMP DEFAULT CURRENT_TIMESTAMP()

Índices
- PRIMARY KEY (id)
- UNIQUE      (uuid)
```

## migrations
```
Engine=InnoDB  Charset=utf8mb4_unicode_ci

Colunas
- id: INT(10) UNSIGNED [PK][AI]
- migration: VARCHAR(255)
- batch: INT(11)
```

---

## Resumo de Foreign Keys (todas)
```
addresses.user_id          → users.id             ON DELETE CASCADE
orders.user_id             → users.id             ON DELETE CASCADE
order_items.order_id       → orders.id            ON DELETE CASCADE
order_items.product_id     → products.id          ON DELETE CASCADE
support_tickets.user_id    → users.id             ON DELETE CASCADE
support_tickets.order_id   → orders.id            ON DELETE CASCADE
support_messages.user_id   → users.id             ON DELETE CASCADE
support_messages.support_ticket_id → support_tickets.id ON DELETE CASCADE
```
