--
-- PostgreSQL database dump
--

\restrict L6JpCShZasAai2gcdCJCqG2baTWjXiusUsRY7yLgZEeyjLlXWnuuJTcm6iLSIPC

-- Dumped from database version 18.4
-- Dumped by pg_dump version 18.4

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: fn_set_updated_at(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.fn_set_updated_at() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.fn_set_updated_at() OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: cds; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cds (
    id integer NOT NULL,
    usuario_id integer NOT NULL,
    nome character varying(120) NOT NULL,
    genero character varying(120) NOT NULL,
    ano character varying(4) NOT NULL,
    preco numeric(10,2) NOT NULL,
    estado_capa character varying(30) NOT NULL,
    estado_disco character varying(30) NOT NULL,
    descricao text,
    imagem character varying(255) DEFAULT 'img/placeholder.jpg'::character varying NOT NULL,
    ativo boolean DEFAULT true NOT NULL,
    vendido boolean DEFAULT false NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    desconto_percent integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.cds OWNER TO postgres;

--
-- Name: cds_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.cds_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.cds_id_seq OWNER TO postgres;

--
-- Name: cds_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.cds_id_seq OWNED BY public.cds.id;


--
-- Name: discos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.discos (
    id integer NOT NULL,
    usuario_id integer NOT NULL,
    nome character varying(120) NOT NULL,
    genero character varying(120) NOT NULL,
    ano character varying(4) NOT NULL,
    preco numeric(10,2) NOT NULL,
    estado_capa character varying(30) NOT NULL,
    estado_disco character varying(30) NOT NULL,
    descricao text,
    imagem character varying(255) DEFAULT 'img/placeholder.jpg'::character varying NOT NULL,
    ativo boolean DEFAULT true NOT NULL,
    vendido boolean DEFAULT false NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    desconto_percent integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.discos OWNER TO postgres;

--
-- Name: discos_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.discos_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.discos_id_seq OWNER TO postgres;

--
-- Name: discos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.discos_id_seq OWNED BY public.discos.id;


--
-- Name: lotecd; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.lotecd (
    id integer NOT NULL,
    usuario_id integer NOT NULL,
    titulo character varying(200) NOT NULL,
    descricao text,
    imagem character varying(255) DEFAULT 'img/placeholder.jpg'::character varying NOT NULL,
    preco numeric(10,2) NOT NULL,
    ativo boolean DEFAULT true NOT NULL,
    vendido boolean DEFAULT false NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.lotecd OWNER TO postgres;

--
-- Name: lotecd_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.lotecd_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.lotecd_id_seq OWNER TO postgres;

--
-- Name: lotecd_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.lotecd_id_seq OWNED BY public.lotecd.id;


--
-- Name: lotecd_itens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.lotecd_itens (
    lotecd_id integer NOT NULL,
    cds_id integer NOT NULL
);


ALTER TABLE public.lotecd_itens OWNER TO postgres;

--
-- Name: lotedisco; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.lotedisco (
    id integer NOT NULL,
    usuario_id integer NOT NULL,
    titulo character varying(200) NOT NULL,
    descricao text,
    imagem character varying(255) DEFAULT 'img/placeholder.jpg'::character varying NOT NULL,
    preco numeric(10,2) NOT NULL,
    ativo boolean DEFAULT true NOT NULL,
    vendido boolean DEFAULT false NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.lotedisco OWNER TO postgres;

--
-- Name: lotedisco_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.lotedisco_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.lotedisco_id_seq OWNER TO postgres;

--
-- Name: lotedisco_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.lotedisco_id_seq OWNED BY public.lotedisco.id;


--
-- Name: lotedisco_itens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.lotedisco_itens (
    lotedisco_id integer NOT NULL,
    discos_id integer NOT NULL
);


ALTER TABLE public.lotedisco_itens OWNER TO postgres;

--
-- Name: usuarios; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.usuarios (
    id integer NOT NULL,
    nome character varying(120) NOT NULL,
    email character varying(180) NOT NULL,
    senha character varying(180) NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    updated_at timestamp without time zone DEFAULT now() CONSTRAINT usuarios_update_at_not_null NOT NULL
);


ALTER TABLE public.usuarios OWNER TO postgres;

--
-- Name: usuarios_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.usuarios_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.usuarios_id_seq OWNER TO postgres;

--
-- Name: usuarios_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.usuarios_id_seq OWNED BY public.usuarios.id;


--
-- Name: cds id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cds ALTER COLUMN id SET DEFAULT nextval('public.cds_id_seq'::regclass);


--
-- Name: discos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.discos ALTER COLUMN id SET DEFAULT nextval('public.discos_id_seq'::regclass);


--
-- Name: lotecd id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lotecd ALTER COLUMN id SET DEFAULT nextval('public.lotecd_id_seq'::regclass);


--
-- Name: lotedisco id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lotedisco ALTER COLUMN id SET DEFAULT nextval('public.lotedisco_id_seq'::regclass);


--
-- Name: usuarios id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios ALTER COLUMN id SET DEFAULT nextval('public.usuarios_id_seq'::regclass);


--
-- Data for Name: cds; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cds (id, usuario_id, nome, genero, ano, preco, estado_capa, estado_disco, descricao, imagem, ativo, vendido, created_at, updated_at, desconto_percent) FROM stdin;
1	1	Thriller — Michael Jackson	Pop	1982	35.00	Muito Bom (VG+)	Mint (M)	CD original sem arranhões.	img/placeholder.jpg	t	f	2026-06-11 13:33:15.994179	2026-06-11 13:33:15.994179	0
2	2	Britney Spears	Pop	2000	80.00	Regular (G)	Mint (M)	O disco está em perfeitas condições... Porém a minha cachorra comeu a cama, então ela está bem ruinzinha	img/prod_6a3185a2499f50.23320843.webp	t	f	2026-06-16 14:19:30.304869	2026-06-16 14:19:30.304869	0
\.


--
-- Data for Name: discos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.discos (id, usuario_id, nome, genero, ano, preco, estado_capa, estado_disco, descricao, imagem, ativo, vendido, created_at, updated_at, desconto_percent) FROM stdin;
1	1	Portals — Melanie Martinez	Pop/Alternativo	2023	300.99	Near Mint (NM)	Muito Bom (VG+)	Edição de vinil colorida — rosa e dourado. Excelente estado.	img/disco_melaniePortals.jpg	t	f	2026-06-11 13:29:50.546666	2026-06-11 13:29:50.546666	30
2	1	Alive! — KISS	Rock	1975	180.00	Bom (VG)	Bom (VG)	Álbum ao vivo clássico. Algumas marcas de uso, som excelente.	img/disco_Kiss.jpg	t	f	2026-06-11 13:29:50.546666	2026-06-11 13:29:50.546666	30
3	1	Dark Side of the Moon — Pink Floyd	Rock Progressivo	1973	450.00	Near Mint (NM)	Near Mint (NM)	Prensagem original. Raridade.	img/placeholder.jpg	t	f	2026-06-11 13:29:50.546666	2026-06-11 13:29:50.546666	0
4	2	Chico Buarque	MPB	2000	2000.00	Mint (M)	Mint (M)	as melhores do chiquinho	img/prod_6a318431875924.20169883.jpg	t	f	2026-06-16 14:13:21.565246	2026-06-16 14:13:21.565246	0
\.


--
-- Data for Name: lotecd; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.lotecd (id, usuario_id, titulo, descricao, imagem, preco, ativo, vendido, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: lotecd_itens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.lotecd_itens (lotecd_id, cds_id) FROM stdin;
\.


--
-- Data for Name: lotedisco; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.lotedisco (id, usuario_id, titulo, descricao, imagem, preco, ativo, vendido, created_at, updated_at) FROM stdin;
1	1	Clássicos do Rock — Lote com 2 discos	Dark Side of the Moon e Alive! inclusos no pacote.	img/placeholder.jpg	550.00	t	f	2026-06-11 13:33:28.86502	2026-06-11 13:33:28.86502
\.


--
-- Data for Name: lotedisco_itens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.lotedisco_itens (lotedisco_id, discos_id) FROM stdin;
1	2
1	3
\.


--
-- Data for Name: usuarios; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.usuarios (id, nome, email, senha, created_at, updated_at) FROM stdin;
1	Admin RetroMusic	admin@retromusic.com	12345	2026-06-11 13:29:42.589334	2026-06-11 14:01:36.404887
2	Yoyo	barbet@gmail.com	$2y$12$1CS5.xjgzxM.FmijNsmVSOfdJBfYH65/SJMMAq8/HtBlCl6WigjFq	2026-06-16 13:26:00.787299	2026-06-16 13:38:34.983812
\.


--
-- Name: cds_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.cds_id_seq', 2, true);


--
-- Name: discos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.discos_id_seq', 4, true);


--
-- Name: lotecd_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.lotecd_id_seq', 1, false);


--
-- Name: lotedisco_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.lotedisco_id_seq', 1, false);


--
-- Name: usuarios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.usuarios_id_seq', 2, true);


--
-- Name: cds cds_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cds
    ADD CONSTRAINT cds_pkey PRIMARY KEY (id);


--
-- Name: discos discos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.discos
    ADD CONSTRAINT discos_pkey PRIMARY KEY (id);


--
-- Name: lotecd_itens lotecd_itens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lotecd_itens
    ADD CONSTRAINT lotecd_itens_pkey PRIMARY KEY (lotecd_id, cds_id);


--
-- Name: lotecd lotecd_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lotecd
    ADD CONSTRAINT lotecd_pkey PRIMARY KEY (id);


--
-- Name: lotedisco_itens lotedisco_itens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lotedisco_itens
    ADD CONSTRAINT lotedisco_itens_pkey PRIMARY KEY (lotedisco_id, discos_id);


--
-- Name: lotedisco lotedisco_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lotedisco
    ADD CONSTRAINT lotedisco_pkey PRIMARY KEY (id);


--
-- Name: usuarios usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_pkey PRIMARY KEY (id);


--
-- Name: idx_cds_ativo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_cds_ativo ON public.cds USING btree (ativo);


--
-- Name: idx_cds_vendido; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_cds_vendido ON public.cds USING btree (vendido);


--
-- Name: idx_discos_ativo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_discos_ativo ON public.discos USING btree (ativo);


--
-- Name: idx_discos_vendido; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_discos_vendido ON public.discos USING btree (vendido);


--
-- Name: idx_lotecd_ativo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_lotecd_ativo ON public.lotecd USING btree (ativo);


--
-- Name: idx_lotedisco_ativo; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_lotedisco_ativo ON public.lotedisco USING btree (ativo);


--
-- Name: cds trg_cds_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_cds_updated_at BEFORE UPDATE ON public.cds FOR EACH ROW EXECUTE FUNCTION public.fn_set_updated_at();


--
-- Name: discos trg_discos_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_discos_updated_at BEFORE UPDATE ON public.discos FOR EACH ROW EXECUTE FUNCTION public.fn_set_updated_at();


--
-- Name: lotecd trg_lotecd_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_lotecd_updated_at BEFORE UPDATE ON public.lotecd FOR EACH ROW EXECUTE FUNCTION public.fn_set_updated_at();


--
-- Name: lotedisco trg_lotedisco_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_lotedisco_updated_at BEFORE UPDATE ON public.lotedisco FOR EACH ROW EXECUTE FUNCTION public.fn_set_updated_at();


--
-- Name: usuarios trg_usuarios_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_usuarios_updated_at BEFORE UPDATE ON public.usuarios FOR EACH ROW EXECUTE FUNCTION public.fn_set_updated_at();


--
-- Name: cds cds_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cds
    ADD CONSTRAINT cds_usuario_id_fkey FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON DELETE CASCADE;


--
-- Name: discos discos_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.discos
    ADD CONSTRAINT discos_usuario_id_fkey FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON DELETE CASCADE;


--
-- Name: lotecd_itens lotecd_itens_cds_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lotecd_itens
    ADD CONSTRAINT lotecd_itens_cds_id_fkey FOREIGN KEY (cds_id) REFERENCES public.cds(id) ON DELETE CASCADE;


--
-- Name: lotecd_itens lotecd_itens_lotecd_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lotecd_itens
    ADD CONSTRAINT lotecd_itens_lotecd_id_fkey FOREIGN KEY (lotecd_id) REFERENCES public.lotecd(id) ON DELETE CASCADE;


--
-- Name: lotecd lotecd_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lotecd
    ADD CONSTRAINT lotecd_usuario_id_fkey FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON DELETE CASCADE;


--
-- Name: lotedisco_itens lotedisco_itens_discos_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lotedisco_itens
    ADD CONSTRAINT lotedisco_itens_discos_id_fkey FOREIGN KEY (discos_id) REFERENCES public.discos(id) ON DELETE CASCADE;


--
-- Name: lotedisco_itens lotedisco_itens_lotedisco_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lotedisco_itens
    ADD CONSTRAINT lotedisco_itens_lotedisco_id_fkey FOREIGN KEY (lotedisco_id) REFERENCES public.lotedisco(id) ON DELETE CASCADE;


--
-- Name: lotedisco lotedisco_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lotedisco
    ADD CONSTRAINT lotedisco_usuario_id_fkey FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict L6JpCShZasAai2gcdCJCqG2baTWjXiusUsRY7yLgZEeyjLlXWnuuJTcm6iLSIPC

