-- tabela usuarios 

-- Cria uam tabela apenas se ela ainda não existir
CREATE TABLE if NOT EXISTS usuarios (
    id          SERIAL          PRIMARY KEY,
    nome        VARCHAR(120)    NOT NULL, -- NOT NULL -> campo obrigatório para preencher
    email       VARCHAR(180)    NOT NULL,
    senha       VARCHAR(180)    NOT NULL,
    created_at  TIMESTAMP       NOT NULL DEFAULT NOW(), -- O TIMESTAMP serve para armazenar dia e horário que o usuário foi criado
    updated_at  TIMESTAMP       NOT NULL DEFAULT NOW()
);

-- Triggers -> 

CREATE OR REPLACE FUNCTION fn_set_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE TRIGGER trg_usuarios_updated_at
    BEFORE UPDATE ON usuarios
    FOR EACH ROW EXECUTE FUNCTION fn_set_updated_at();

-- Tabela de discos
-- Faz a mesma coisa que a primeira, cria somente se ele não existir  
CREATE TABLE if NOT EXISTS discos (
    id                  SERIAL          PRIMARY KEY,
    usuario_id          INTEGER         NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
    -- chava estrangeira, vincula essa coluna com a tabela usuarios pra pegar o valor do id.  
    -- Para evitar erros, a exclusão em cascata serve para que, quando o ID do usuário for apagado, qualquer valor relacionado a ele seja apagado junto.
    nome                VARCHAR(120)    NOT NULL,
    genero              VARCHAR(120)    NOT NULL,
    ano                 VARCHAR(4)      NOT NULL,
    preco               NUMERIC(10,2)   NOT NULL, -- Serve para armazenar números decimais. Aceita até 10 digitos e somente 2 casas decimais.
    estado_capa         VARCHAR(30)     NOT NULL,
    estado_disco        VARCHAR(30)     NOT NULL,
    descricao           TEXT,
    imagem              VARCHAR(255)    NOT NULL DEFAULT 'img/placeholder.jpg',
    ativo               BOOLEAN         NOT NULL DEFAULT TRUE,
    vendido             BOOLEAN         NOT NULL DEFAULT FALSE,
    desconto_percent    INT             NOT NULL DEFAULT 0,
    created_at          TIMESTAMP       NOT NULL DEFAULT NOW(), -- O TIMESTAMP serve para armazenar dia e horário que o disco foi criado
    updated_at          TIMESTAMP       NOT NULL DEFAULT NOW()
);

-- Criando três indices para as colunas tipo, ativo e vendido
CREATE INDEX if NOT EXISTS idx_discos_ativo   ON discos(ativo);
CREATE INDEX if NOT EXISTS idx_discos_vendido ON discos(vendido);

CREATE OR REPLACE TRIGGER trg_discos_updated_at -- Cria um "gatilho" com o nome trg_discos_updated_at
    BEFORE UPDATE ON discos -- Se ele já existir, será atualizado pela nova definição
    FOR EACH ROW EXECUTE FUNCTION fn_set_updated_at(); -- Vai "disparar" uma função com o nome fn_set_updated_at 

-- Tabela de cd's
-- Criando uma tabela igual as outras, só é criada se ela não existir
CREATE TABLE if NOT EXISTS cds (
    id                  SERIAL          PRIMARY KEY,
    usuario_id          INTEGER         NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
    -- chava estrangeira, vincula essa coluna com a tabela usuarios pra pegar o valor do id.  
    -- Para evitar erros, a exclusão em cascata serve para que, quando o ID do usuário for apagado, qualquer valor relacionado a ele seja apagado junto.
    nome                VARCHAR(120)    NOT NULL,
    genero              VARCHAR(120)    NOT NULL,
    ano                 VARCHAR(4)      NOT NULL,
    preco               NUMERIC(10,2)   NOT NULL, -- Serve para armazenar números decimais. Aceita até 10 digitos e somente 2 casas decimais.
    estado_capa         VARCHAR(30)     NOT NULL,
    estado_disco        VARCHAR(30)     NOT NULL,
    descricao           TEXT,
    imagem              VARCHAR(255)    NOT NULL DEFAULT 'img/placeholder.jpg',
    ativo               BOOLEAN         NOT NULL DEFAULT TRUE,
    vendido             BOOLEAN         NOT NULL DEFAULT FALSE,
    desconto_percent    INT             NOT NULL DEFAULT 0,
    created_at          TIMESTAMP       NOT NULL DEFAULT NOW(), -- O TIMESTAMP serve para armazenar dia e horário que o disco foi criado
    updated_at          TIMESTAMP       NOT NULL DEFAULT NOW()
);

-- Criando três indices para as colunas tipo, ativo e vendido
CREATE INDEX if NOT EXISTS idx_cds_ativo   ON cds(ativo);
CREATE INDEX if NOT EXISTS idx_cds_vendido ON cds(vendido);

CREATE OR REPLACE TRIGGER trg_cds_updated_at -- Cria um "gatilho" com o nome trg_cds_updated_at
    BEFORE UPDATE ON cds -- Se ele já existir, será atualizado pela nova definição
    FOR EACH ROW EXECUTE FUNCTION fn_set_updated_at(); -- Vai "disparar" uma função com o nome fn_set_updated_at 

-- Tabela lotes discos
-- Criando uma tabela igual as outras, só é criada se ela não existir
CREATE TABLE if NOT EXISTS lotedisco ( 
    id              SERIAL          PRIMARY KEY,
    usuario_id      INTEGER         NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
    titulo          VARCHAR(200)    NOT NULL,
    descricao       TEXT,
    imagem          VARCHAR(255)    NOT NULL DEFAULT 'img/placeholder.jpg',
    preco           NUMERIC(10,2)   NOT NULL,
    ativo           BOOLEAN         NOT NULL DEFAULT TRUE,
    vendido         BOOLEAN         NOT NULL DEFAULT FALSE,
    created_at      TIMESTAMP       NOT NULL DEFAULT NOW(), -- O TIMESTAMP serve para armazenar dia e horário que o disco foi criado
    updated_at      TIMESTAMP       NOT NULL DEFAULT NOW()
);

CREATE INDEX if NOT EXISTS idx_lotedisco_ativo ON lotedisco(ativo);

CREATE OR REPLACE TRIGGER trg_lotedisco_updated_at
    BEFORE UPDATE ON lotedisco
    FOR EACH ROW EXECUTE FUNCTION fn_set_updated_at();

-- Tabela lotes cds
-- Criando uma tabela igual as outras, só é criada se ela não existir
CREATE TABLE if NOT EXISTS lotecd ( 
    id              SERIAL          PRIMARY KEY,
    usuario_id      INTEGER         NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
    titulo          VARCHAR(200)    NOT NULL,
    descricao       TEXT,
    imagem          VARCHAR(255)    NOT NULL DEFAULT 'img/placeholder.jpg',
    preco           NUMERIC(10,2)   NOT NULL,
    ativo           BOOLEAN         NOT NULL DEFAULT TRUE,
    vendido         BOOLEAN         NOT NULL DEFAULT FALSE,
    created_at      TIMESTAMP       NOT NULL DEFAULT NOW(), -- O TIMESTAMP serve para armazenar dia e horário que o disco foi criado
    updated_at      TIMESTAMP       NOT NULL DEFAULT NOW()
);

CREATE INDEX if NOT EXISTS idx_lotecd_ativo ON lotecd(ativo);

CREATE OR REPLACE TRIGGER trg_lotecd_updated_at
    BEFORE UPDATE ON lotecd
    FOR EACH ROW EXECUTE FUNCTION fn_set_updated_at();

-- Tabela de itens do lote de discos
CREATE TABLE if NOT EXISTS lotedisco_itens (
    lotedisco_id INTEGER NOT NULL REFERENCES lotedisco(id) ON DELETE CASCADE,
    discos_id INTEGER NOT NULL REFERENCES discos(id) ON DELETE CASCADE,
    PRIMARY KEY (lotedisco_id, discos_id)
);

-- Tabela de itens do lote de cds
CREATE TABLE if NOT EXISTS lotecd_itens (
    lotecd_id INTEGER NOT NULL REFERENCES lotecd(id) ON DELETE CASCADE,
    cds_id INTEGER NOT NULL REFERENCES cds(id) ON DELETE CASCADE,
    PRIMARY KEY (lotecd_id, cds_id)
);

-- Colocando alguns dados para teste 

-- Usuário de teste (senha: 123456)
-- Criando Usuário Administrativo de Teste
INSERT INTO usuarios (id, nome, email, senha) VALUES
(1, 'Admin RetroMusic', 'admin@retromusic.com', '12345')
ON CONFLICT DO NOTHING;

-- Inserindo os itens nas tabelas corretas (discos e cds separados)

-- Tabela de Discos (Vinis)
INSERT INTO discos 
    (id, usuario_id, nome, genero, ano, preco, estado_capa, estado_disco, descricao, imagem, desconto_percent)
VALUES
(1, 1, 'Portals — Melanie Martinez', 'Pop/Alternativo', '2023', 300.99, 'Near Mint (NM)', 'Muito Bom (VG+)', 'Edição de vinil colorida — rosa e dourado. Excelente estado.', 'img/disco_melaniePortals.jpg', 30),
(2, 1, 'Alive! — KISS', 'Rock', '1975', 180.00, 'Bom (VG)', 'Bom (VG)', 'Álbum ao vivo clássico. Algumas marcas de uso, som excelente.', 'img/disco_Kiss.jpg', 30),
(3, 1, 'Dark Side of the Moon — Pink Floyd', 'Rock Progressivo', '1973', 450.00, 'Near Mint (NM)', 'Near Mint (NM)', 'Prensagem original. Raridade.', 'img/placeholder.jpg', 0);

-- Tabela de CDs
INSERT INTO cds 
    (id, usuario_id, nome, genero, ano, preco, estado_capa, estado_disco, descricao, imagem, desconto_percent)
VALUES
(1, 1, 'Thriller — Michael Jackson', 'Pop', '1982', 35.00, 'Muito Bom (VG+)', 'Mint (M)', 'CD original sem arranhões.', 'img/placeholder.jpg', 0);

-- Tabela de Lotes de Discos
INSERT INTO lotedisco (id, usuario_id, titulo, descricao, preco) VALUES
(1, 1, 'Clássicos do Rock — Lote com 2 discos', 'Dark Side of the Moon e Alive! inclusos no pacote.', 550.00);

-- Vincular os discos cadastrados acima (ID 2 e ID 3) ao Lote ID 1
INSERT INTO lotedisco_itens (lotedisco_id, discos_id) VALUES 
(1, 2), 
(1, 3);

SELECT * FROM usuarios;

-- Atualiza o contador da tabela de discos para o próximo número livre real
SELECT setval(pg_get_serial_sequence('discos', 'id'), COALESCE(MAX(id), 0) + 1, false) FROM discos;

-- Atualiza a tabela de CDs (mesma coisa do de cima)
SELECT setval(pg_get_serial_sequence('cds', 'id'), COALESCE(MAX(id), 0) + 1, false) FROM cds;
-- MAX(id) - busca o  maior número de id atualmente gravado na tabela.
-- COALESCE(MAX(id), 0) - se a tabela estiver vazia o sistema usa o número 0.
