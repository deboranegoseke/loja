# Banco de dados `lojinha` — Dicionário de Dados

**SGBD:** MariaDB 10.4 (Engine InnoDB) • **Charset/Collation:** `utf8mb4 / utf8mb4_unicode_ci`  
Suporta acentos/emoji e ordenação adequada para PT-BR.

**Diagrama (PNG):** `Banco_de_dados_diagrama.png`

---

## Mini‑glossário (direto ao ponto)
- **PK (Primary Key / chave primária):** identificador único da linha (geralmente `id`).  
- **FK (Foreign Key / chave estrangeira):** campo que aponta para a PK de outra tabela.  
- **UNIQUE:** não pode repetir (ex.: e-mail).  
- **INDEX:** acelera buscas/ordenações nesse(s) campo(s).  
- **AI (AUTO_INCREMENT):** número que sobe sozinho (1, 2, 3…).  
- **ON DELETE CASCADE:** se apagar o “pai”, apaga automaticamente os “filhos” ligados a ele (evita órfãos).

## Tipos de dados usados aqui
- `VARCHAR(n)`: texto curto (até `n` caracteres), ex.: nome, e-mail, CEP.  
- `TEXT/LONGTEXT/MEDIUMTEXT`: textos grandes (descrições, payloads).  
- `INT` / `BIGINT UNSIGNED`: números inteiros (sem sinal). `BIGINT` guarda IDs com folga.  
- `DECIMAL(10,2)`: dinheiro (ex.: 12345678,90).  
- `TINYINT(1)`: “0 ou 1” (próximo de boolean).  
- `ENUM(...)`: lista fechada de valores (ex.: `role`).  
- `TIMESTAMP`: data/hora.  
- `CHAR(2)`: 2 letras fixas (ex.: UF “SP”, “RJ”).

---

## Mapa das relações (cardinalidade)
- **users 1:1 addresses** — um usuário tem **um** endereço principal (`UNIQUE(user_id)` em `addresses`).  
- **users 1:N orders** — um usuário pode ter **vários** pedidos.  
- **orders N:M products** via **order_items** — um pedido tem **vários** produtos e um produto pode estar em **vários** pedidos.  
- **users 1:N support_tickets** (e **orders 1:N support_tickets** opcional) — um usuário pode abrir vários tickets; um ticket pode (ou não) estar ligado a um pedido.  
- **support_tickets 1:N support_messages** — cada ticket tem várias mensagens (conversa).  

> Quase todas as FKs usam **ON DELETE CASCADE**: apagou o pai, apaga os filhos ligados.

---

## Tabelas (explicado, com tipos e ligações)

### `users` — pessoas que usam o site
**Campos**
- `id` — `BIGINT UNSIGNED` [PK][AI] — identificador.  
- `name` — `VARCHAR(255)` — nome.  
- `email` — `VARCHAR(255)` [UNIQUE] — login/contato.  
- `role` — `ENUM('cliente','adm','gerente')` default `cliente` [INDEX] — perfil.  
- `email_verified_at` — `TIMESTAMP NULL` — quando confirmou o e-mail.  
- `password` — `VARCHAR(255)` — hash da senha.  
- `remember_token` — `VARCHAR(100) NULL` — “lembrar login”.  
- `created_at` / `updated_at` — `TIMESTAMP NULL`.

**Liga com (saída)**
- `addresses.user_id` → `users.id` (**1:1**, CASCADE)  
- `orders.user_id` → `users.id` (**1:N**, CASCADE)  
- `support_tickets.user_id` → `users.id` (**1:N**, CASCADE)  
- `support_messages.user_id` → `users.id` (**1:N**, CASCADE)

---

### `addresses` — endereço principal do usuário
**Campos**
- `id` — `BIGINT UNSIGNED` [PK][AI]  
- `user_id` — `BIGINT UNSIGNED` [FK][UNIQUE] → `users.id`  
- `logradouro` — `VARCHAR(255)`  
- `numero` — `VARCHAR(50)` (texto: aceita “S/N”).  
- `complemento` — `VARCHAR(255) NULL`  
- `bairro` — `VARCHAR(120)`  
- `cidade` — `VARCHAR(120)`  
- `estado` — `CHAR(2)` (“SP”, “RJ”…)  
- `cep` — `VARCHAR(9)` (com hífen)  
- `created_at` / `updated_at` — `TIMESTAMP NULL`

**Índices/Restrições**
- `PRIMARY KEY (id)`  
- `UNIQUE (user_id)` → **garante 1 endereço por usuário**  
- `INDEX (estado, cidade)`

**FK**
- `user_id` → `users.id` **ON DELETE CASCADE**

---

### `products` — catálogo de produtos
**Campos**
- `id` — `BIGINT UNSIGNED` [PK][AI]  
- `sku` — `VARCHAR(255) NULL` [UNIQUE] — código opcional interno.  
- `name` — `VARCHAR(255)`  
- `slug` — `VARCHAR(255)` [UNIQUE] — URL amigável.  
- `description` — `TEXT NULL`  
- `stock` — `INT UNSIGNED` default `0` — quantidade no estoque.  
- `price` — `DECIMAL(10,2)` — preço de venda.  
- `cost_price` — `DECIMAL(10,2)` default `0.00` — custo (para margem).  
- `image_url` / `image_path` — `VARCHAR(255) NULL` — link/arquivo.  
- `active` — `TINYINT(1)` default `1` — ativo/inativo.  
- `created_at` / `updated_at` — `TIMESTAMP NULL`

**Índices**
- `PRIMARY KEY (id)`  
- `UNIQUE (slug)`  
- `UNIQUE (sku)`

**Usado por**
- `order_items.product_id` → `products.id` (**N:M** via `order_items`, CASCADE)

---

### `orders` — pedidos
**Campos**
- `id` — `BIGINT UNSIGNED` [PK][AI]  
- `user_id` — `BIGINT UNSIGNED` [FK] → `users.id`  
- `total` — `DECIMAL(10,2)` default `0.00`  
- `status` — `VARCHAR(255)` default `'novo'`  
- `fulfillment_status` — `VARCHAR(30)` default `'aguardando'`  
- `tracking_code` — `VARCHAR(255) NULL`  
- `customer_name` / `customer_email` — `VARCHAR(255) NULL`  
- `pix_txid` — `VARCHAR(35) NULL`  
- `pix_payload` — `TEXT NULL`  
- `created_at` / `updated_at` — `TIMESTAMP NULL`

**Índices**
- `PRIMARY KEY (id)`  
- `INDEX (user_id)`

**FK**
- `user_id` → `users.id` **ON DELETE CASCADE**

**Usado por**
- `order_items.order_id` → `orders.id` (**1:N**, CASCADE)  
- `support_tickets.order_id` → `orders.id` (**1:N opcional**, CASCADE)

---

### `order_items` — itens do pedido (faz a ponte N:M)
**Campos**
- `id` — `BIGINT UNSIGNED` [PK][AI]  
- `order_id` — `BIGINT UNSIGNED` [FK] → `orders.id`  
- `product_id` — `BIGINT UNSIGNED` [FK] → `products.id`  
- `quantity` — `INT UNSIGNED`  
- `unit_price` — `DECIMAL(10,2)`  
- `total` — `DECIMAL(10,2)` (geralmente `quantity * unit_price`)  
- `created_at` / `updated_at` — `TIMESTAMP NULL`

**Índices**
- `INDEX (order_id)`  
- `INDEX (product_id)`

**FK**
- `order_id` → `orders.id` **ON DELETE CASCADE**  
- `product_id` → `products.id` **ON DELETE CASCADE**

> Essas duas FKs criam a relação **orders N:M products**.

---

### `support_tickets` — chamados de suporte (SAC)
**Campos**
- `id` — `BIGINT UNSIGNED` [PK][AI]  
- `user_id` — `BIGINT UNSIGNED` [FK] → `users.id`  
- `order_id` — `BIGINT UNSIGNED NULL` [FK] → `orders.id` (opcional)  
- `status` — `VARCHAR(255)` default `'aberto'`  
- `subject` — `VARCHAR(255) NULL`  
- `closed_at` — `TIMESTAMP NULL`  
- `created_at` / `updated_at` — `TIMESTAMP NULL`

**Índices**
- `INDEX (user_id)`  
- `INDEX (order_id)`

**FK**
- `user_id` → `users.id` **ON DELETE CASCADE**  
- `order_id` → `orders.id` **ON DELETE CASCADE**

**Usado por**
- `support_messages.support_ticket_id` → `support_tickets.id` (**1:N**, CASCADE)

---

### `support_messages` — mensagens do ticket
**Campos**
- `id` — `BIGINT UNSIGNED` [PK][AI]  
- `support_ticket_id` — `BIGINT UNSIGNED` [FK] → `support_tickets.id`  
- `user_id` — `BIGINT UNSIGNED` [FK] → `users.id`  
- `sender_type` — `ENUM('customer','store')`  
- `body` — `TEXT`  
- `attachments` — `LONGTEXT` (collation binário) com `CHECK json_valid(attachments)` — JSON de anexos; pode ser `NULL`.  
- `created_at` / `updated_at` — `TIMESTAMP NULL`

**Índices**
- `INDEX (user_id)`  
- `INDEX (sender_type)`  
- `INDEX (support_ticket_id, created_at)` (listar cronológico)

**FK**
- `support_ticket_id` → `support_tickets.id` **ON DELETE CASCADE**  
- `user_id` → `users.id` **ON DELETE CASCADE**

---

### `sessions` — sessões web do Laravel
**Campos**
- `id` — `VARCHAR(255)` [PK]  
- `user_id` — `BIGINT UNSIGNED NULL` (sem FK formal)  
- `ip_address` — `VARCHAR(45) NULL`  
- `user_agent` — `TEXT NULL`  
- `payload` — `LONGTEXT`  
- `last_activity` — `INT(11)`

**Índices**
- `INDEX (user_id)`  
- `INDEX (last_activity)`

> Observação: por padrão do Laravel, **não há FK** para `users.id`.

---

### `password_reset_tokens` — redefinição de senha
**Campos**
- `email` — `VARCHAR(255)` [PK]  
- `token` — `VARCHAR(255)`  
- `created_at` — `TIMESTAMP NULL`

---

### `cache` / `cache_locks` — cache do Laravel
**cache**
- `key` — `VARCHAR(255)` [PK]  
- `value` — `MEDIUMTEXT`  
- `expiration` — `INT(11)`

**cache_locks**
- `key` — `VARCHAR(255)` [PK]  
- `owner` — `VARCHAR(255)`  
- `expiration` — `INT(11)`

---

### `jobs` / `job_batches` / `failed_jobs` — filas/execuções
**jobs**
- `id` — `BIGINT UNSIGNED` [PK][AI]  
- `queue` — `VARCHAR(255)` [INDEX]  
- `payload` — `LONGTEXT`  
- `attempts` — `TINYINT UNSIGNED`  
- `reserved_at` — `INT UNSIGNED NULL`  
- `available_at` — `INT UNSIGNED`  
- `created_at` — `INT UNSIGNED`

**job_batches**
- `id` — `VARCHAR(255)` [PK]  
- `name` — `VARCHAR(255)`  
- `total_jobs / pending_jobs / failed_jobs` — `INT(11)`  
- `failed_job_ids` — `LONGTEXT`  
- `options` — `MEDIUMTEXT NULL`  
- `cancelled_at / created_at / finished_at` — `INT(11)` (timestamps inteiros)

**failed_jobs**
- `id` — `BIGINT UNSIGNED` [PK][AI]  
- `uuid` — `VARCHAR(255)` [UNIQUE]  
- `connection` — `TEXT`  
- `queue` — `TEXT`  
- `payload` — `LONGTEXT`  
- `exception` — `LONGTEXT`  
- `failed_at` — `TIMESTAMP DEFAULT CURRENT_TIMESTAMP()`

---

### `migrations` — controle de versão do schema
- `id` — `INT UNSIGNED` [PK][AI]  
- `migration` — `VARCHAR(255)`  
- `batch` — `INT(11)`

---

## Resumo das Foreign Keys (com tipo e efeito)
- `addresses.user_id (BIGINT)`  → `users.id` — **1:1**, **CASCADE**  
- `orders.user_id (BIGINT)`     → `users.id` — **1:N**, **CASCADE**  
- `order_items.order_id (BIGINT)`   → `orders.id` — **1:N**, **CASCADE**  
- `order_items.product_id (BIGINT)` → `products.id` — **1:N**, **CASCADE**  
  > Juntas, essas duas FKs fazem **orders N:M products**.

- `support_tickets.user_id (BIGINT)`  → `users.id` — **1:N**, **CASCADE**  
- `support_tickets.order_id (BIGINT)` → `orders.id` — **1:N opcional**, **CASCADE**  
- `support_messages.user_id (BIGINT)` → `users.id` — **1:N**, **CASCADE**  
- `support_messages.support_ticket_id (BIGINT)` → `support_tickets.id` — **1:N**, **CASCADE**

---

## Dicas práticas
- **ID = `BIGINT UNSIGNED`**: padrão moderno para crescer sem medo.  
- **Dinheiro = `DECIMAL(10,2)`**, nunca `FLOAT/DOUBLE`.  
- **Booleano** simples: `TINYINT(1)` (0/1).  
- **Textos curtos**: `VARCHAR(255)`; **UF**: `CHAR(2)`; **CEP**: `VARCHAR(9)` com hífen.  
- **UNIQUE** no que não pode repetir (e-mail, slug, às vezes SKU).  
- **INDEX** nos campos que você filtra/ordena muito (`role`, `estado,cidade`, etc.).  
- **NULL** só quando faz sentido (ex.: `order_id` opcional no ticket).

> Quer que eu gere os `CREATE TABLE` completos, em ordem, prontos para colar? Posso incluir aqui também.
