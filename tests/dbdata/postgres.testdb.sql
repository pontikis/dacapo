--
-- PostgreSQL database dump
--

-- Dumped from database version 9.6.7
-- Dumped by pg_dump version 9.6.7

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: test; Type: SCHEMA; Schema: -; Owner: testdb
--

CREATE SCHEMA test;


ALTER SCHEMA test OWNER TO testdb;

SET search_path = test, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: customers_el; Type: TABLE; Schema: test; Owner: testdb
--

CREATE TABLE customers_el (
    id integer NOT NULL,
    lastname character varying(100) NOT NULL,
    firstname character varying(100) NOT NULL,
    fathername character varying(100),
    gender integer,
    address character varying(200)
);


ALTER TABLE customers_el OWNER TO testdb;

--
-- Name: customers_el_id_seq; Type: SEQUENCE; Schema: test; Owner: testdb
--

CREATE SEQUENCE customers_el_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE customers_el_id_seq OWNER TO testdb;

--
-- Name: customers_el_id_seq; Type: SEQUENCE OWNED BY; Schema: test; Owner: testdb
--

ALTER SEQUENCE customers_el_id_seq OWNED BY customers_el.id;


--
-- Name: customers_en; Type: TABLE; Schema: test; Owner: testdb
--

CREATE TABLE customers_en (
    id integer NOT NULL,
    lastname character varying(100) NOT NULL,
    firstname character varying(100) NOT NULL,
    fathername character varying(100),
    gender integer,
    address character varying(200)
);


ALTER TABLE customers_en OWNER TO testdb;

--
-- Name: customers_en_id_seq; Type: SEQUENCE; Schema: test; Owner: testdb
--

CREATE SEQUENCE customers_en_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE customers_en_id_seq OWNER TO testdb;

--
-- Name: customers_en_id_seq; Type: SEQUENCE OWNED BY; Schema: test; Owner: testdb
--

ALTER SEQUENCE customers_en_id_seq OWNED BY customers_en.id;


--
-- Name: customers_el id; Type: DEFAULT; Schema: test; Owner: testdb
--

ALTER TABLE ONLY customers_el ALTER COLUMN id SET DEFAULT nextval('customers_el_id_seq'::regclass);


--
-- Name: customers_en id; Type: DEFAULT; Schema: test; Owner: testdb
--

ALTER TABLE ONLY customers_en ALTER COLUMN id SET DEFAULT nextval('customers_en_id_seq'::regclass);


--
-- Data for Name: customers_el; Type: TABLE DATA; Schema: test; Owner: testdb
--

INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (1, 'Γεωργίου', 'Γεώργιος', 'Παναγιώτης', 1, 'Γεωργίου Σεφέρη 35, Νεάπολη Συκεές, 567 28, Θεσσαλονίκη, Ελλάδα');
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (2, 'Γεωργόπουλος', 'Βασίλειος', 'Αναστάσιος', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (3, 'Γεωργιάδης', 'Αντώνης', 'Λάμπρος', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (4, 'Ιωάννου', 'Εμμανουήλ', 'Γεώργιος', 1, 'Λεωνίδα Ιασωνίδου 27, Θεσσαλονίκη, 546 35, Ελλάδα');
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (5, 'Δημητρίου', 'Ελευθέριος', 'Ανδρέας', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (6, 'Δημητρόπουλος', 'Στέφανος', 'Αναστάσιος', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (7, 'Δημητριάδης', 'Ιωάννης', 'Ηλίας', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (8, 'Δημητράκης', 'Χρήστος', 'Στυλιανός', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (9, 'Δημητρακάκης', 'Αναστάσιος', 'Χρήστος', 1, 'Βολουδάκη 12, Χανιά, 731 34, Ελλάδα');
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (10, 'Αναστασίου', 'Ηλίας', 'Κώστας', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (11, 'Αναστασόπουλος', 'Φώτιος', 'Ελευθέριος', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (12, 'Αναστασιάδης', 'Κώστας', 'Σπύρος', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (13, 'Θεοδώρου', 'Δημήτρης', 'Γεώργιος', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (14, 'Θεοδωρόπουλος', 'Ανδρέας', 'Παναγιώτης', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (15, 'Θεοδωράκης', 'Νικόλαος', 'Βασίλειος', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (16, 'Νικολάου', 'Παναγιώτης', 'Αλέξανδρος', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (17, 'Νικολάου', 'Σπύρος', 'Εμμανουήλ', 1, 'Ακαδημίας 14, Αθήνα, Κεντρικός Τομέας Αθηνών, Ελλάδα');
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (18, 'Νικολάου', 'Αλέξανδρος', 'Ιωάννης', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (19, 'Νικολόπουλος', 'Στυλιανός', 'Κώστας', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (20, 'Νικολακόπουλος', 'Λάμπρος', 'Αναστάσιος', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (21, 'Αθανασίου', 'Ιωάννης', 'Ελευθέριος', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (22, 'Αθανασόπουλος', 'Ανδρέας', 'Στέφανος', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (23, 'Αθανασιάδης', 'Βασίλειος', 'Ηλίας', 1, 'Ασκληπιού 32, Παλαιό Φάληρο, 175 64, Νότιος Τομέας Αθηνών, Ελλάδα');
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (24, 'Μιχαήλ', 'Παναγιώτης', 'Δημήτρης', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (25, 'Μιχαλόπουλος', 'Χρήστος', 'Βασίλειος', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (26, 'Γρηγορίου', 'Βασίλειος', 'Στέφανος', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (27, 'Γρηγορόπουλος', 'Ελευθέριος', 'Ηλίας', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (28, 'Παπαγεωργίου', 'Νικόλαος', 'Γεώργιος', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (29, 'Παπαγεωργόπουλος', 'Αλέξανδρος', 'Γεώργιος', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (30, 'Παπαϊωάννου', 'Δημήτρης', 'Αλέξανδρος', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (31, 'Παπαδημητρίου', 'Δημήτρης', 'Παναγιώτης', 1, 'Φιλικής Εταιρείας 14, Γλυφάδα, 166 74, Νότιος Τομέας Αθηνών, Ελλάδα');
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (32, 'Παπαδημητρόπουλος', 'Παναγιώτης', 'Σπύρος', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (33, 'Παπαναστασίου', 'Στέφανος', 'Κώστας', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (34, 'Παπαθεοδώρου', 'Κώστας', 'Αντώνης', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (35, 'Παπανικολάου', 'Λάμπρος', 'Παναγιώτης', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (36, 'Παπανικολόπουλος', 'Αντώνης', 'Λάμπρος', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (37, 'Παπαθανασίου', 'Κώστας', 'Ανδρέας', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (38, 'Παπαθανασόπουλος', 'Γεώργιος', 'Ανδρέας', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (39, 'Παπαδόπουλος', 'Στυλιανός', 'Στέφανος', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (40, 'Παπαδόπουλος', 'Στέφανος', 'Αλέξανδρος', 1, 'Pοδιας, Κορωπί, 194 00, Ανατολική Αττική, Ελλάδα');
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (41, 'Παπαδόπουλος', 'Λάμπρος', 'Νικόλαος', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (42, 'Χατζηγεωργίου', 'Παναγιώτης', 'Ιωάννης', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (43, 'Χατζηνικολάου', 'Νικόλαος', 'Δημήτρης', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (44, 'Χατζηπαύλου', 'Χρήστος', 'Κώστας', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (45, 'Αλεξόπουλος', 'Στέφανος', 'Σπύρος', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (46, 'Δήμου', 'Σπύρος', 'Εμμανουήλ', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (47, 'Δήμου', 'Παναγιώτης', 'Ιωάννης', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (48, 'Obrien', 'Zane', 'John', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (49, 'Hendrix', 'Zachary', 'Peter', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (50, 'Bird', 'Yardley', 'George', 1, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (51, 'Γεωργίου', 'Μαρία', 'Ηλίας', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (52, 'Γεωργίου', 'Ελένη', 'Ανδρέας', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (53, 'Γεωργοπούλου', 'Αικατερίνη', 'Λάμπρος', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (54, 'Γεωργιάδη', 'Βασιλική', 'Βασίλειος', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (55, 'Ιωάννου', 'Γεωργία', 'Νικόλαος', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (56, 'Ιωάννου', 'Σοφία', 'Γεώργιος', 2, 'Λεωφόρος Αθηνών Σουνίου, Σαρωνικός, 190 10, Ανατολική Αττική, Ελλάδα');
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (57, 'Ιωάννου', 'Αναστασία', 'Χρήστος', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (58, 'Δημητρίου', 'Ευαγγελία', 'Ελευθέριος', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (59, 'Δημητροπούλου', 'Ιωάννα', 'Κώστας', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (60, 'Δημητριάδη', 'Δήμητρα', 'Στέφανος', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (61, 'Δημητρακάκη', 'Χρύσα', 'Φώτιος', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (62, 'Αναστασίου', 'Χαρούλα', 'Κώστας', 2, 'Κύπρου 9, Χαλκίδα, 34100, Εύβοια, Ελλάδα');
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (63, 'Αναστασοπούλου', 'Δωροθέα', 'Ιωάννης', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (64, 'Αναστασιάδη', 'Πανωραία', 'Εμμανουήλ', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (65, 'Θεοδώρου', 'Ανδριανή', 'Αντώνης', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (66, 'Θεοδωροπούλου', 'Αργυρούλα', 'Δημήτρης', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (67, 'Νικολοπούλου', 'Αγγελική', 'Βασίλειος', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (68, 'Νικολακοπούλου', 'Κλειώ', 'Νικόλαος', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (69, 'Αθανασίου', 'Σάρα', 'Στέφανος', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (70, 'Αθανασίου', 'Ευριδίκη', 'Γεώργιος', 2, 'Λόρδου Βύρωνος 5, Ηράκλειο, 712 02, Ελλάδα');
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (71, 'Αθανασίου', 'Χρύσα', 'Στυλιανός', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (72, 'Αθανασιάδου', 'Αικατερίνη', 'Παναγιώτης', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (73, 'Αθανασιάδου', 'Ιωάννα', 'Ηλίας', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (74, 'Παπαγεωργίου', 'Κλειώ', 'Ιωάννης', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (75, 'Παπαϊωάννου', 'Ελένη', 'Σπύρος', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (76, 'Παπαδημητρίου', 'Χρύσα', 'Αλέξανδρος', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (77, 'Παπαναστασίου', 'Σάρα', 'Δημήτρης', 2, 'Αριάδνης 18-22, Ηράκλειο, 712 02, Ελλάδα');
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (78, 'Παπαθεοδώρου', 'Κλειώ', 'Εμμανουήλ', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (79, 'Παπανικολάου', 'Δήμητρα', 'Λάμπρος', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (80, 'Παπαθανασίου', 'Ιωάννα', 'Γεώργιος', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (81, 'Παπαδοπούλου', 'Σοφία', 'Ελευθέριος', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (82, 'Παπαδοπούλου', 'Αγγελική', 'Ανδρέας', 2, 'Αργοστολίου 30, Φυλή, 133 43, Δυτική Αττική, Ελλάδα');
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (83, 'Παπαδοπούλου', 'Χρύσα', 'Εμμανουήλ', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (84, 'Παπαδοπούλου', 'Ιωάννα', 'Γεώργιος', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (85, 'Παπαδοπούλου', 'Πανωραία', 'Ηλίας', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (86, 'Χατζηγεωργίου', 'Ευαγγελία', 'Αναστάσιος', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (87, 'Χατζηνικολάου', 'Ιωάννα', 'Χρήστος', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (88, 'Χατζηπαύλου', 'Σάρα', 'Αντώνης', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (89, 'Πανοπούλου', 'Δήμητρα', 'Φώτιος', 2, 'Αδαμόπουλου, Ξυλόκαστρο Ευρωστίνη, 204 00, Κορινθία, Ελλάδα');
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (90, 'Βαγενά', 'Κλειώ', 'Σπύρος', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (91, 'Παπασωτηρίου', 'Σοφία', 'Φώτιος', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (92, 'Νικολάου', 'Ευαγγελία', 'Γεώργιος', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (93, 'Νικολάου', 'Αγγελική', 'Βασίλειος', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (94, 'Μεταξά', 'Γεωργία', 'Σπύρος', 2, 'Μιστριωτού 12, Αθήνα, 112 55, Κεντρικός Τομέας Αθηνών, Ελλάδα');
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (95, 'Αλεξίου', 'Αικατερίνη', 'Εμμανουήλ', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (96, 'Ασημακοπούλου', 'Βασιλική', 'Στέφανος', 2, 'Αριστίππού 7, Αθήνα, 106 76, Ελλάδα');
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (97, 'Παπανδρέου', 'Ιωάννα', 'Στυλιανός', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (98, 'Παπανδρέου', 'Πανωραία', 'Ιωάννης', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (99, 'Παπανδρέου', 'Αγγελική', 'Αλέξανδρος', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (100, 'Γαλάνη', 'Ευριδίκη', 'Νικόλαος', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (101, 'Γαλάνη', 'Δήμητρα', 'Κώστας', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (102, 'Κοντομηνά', 'Ελένη', 'Χρήστος', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (103, 'Λαζαρίδου', 'Αργυρούλα', 'Αναστάσιος', 2, 'Πάρνηθος 36, Ηλιούπολη, 163 44, Κεντρικός Τομέας Αθηνών, Ελλάδα');
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (104, 'Καραγκούνη', 'Δωροθέα', 'Παναγιώτης', 2, NULL);
INSERT INTO customers_el (id, lastname, firstname, fathername, gender, address) VALUES (105, 'Gibson', 'Velma', 'John', 2, NULL);


--
-- Name: customers_el_id_seq; Type: SEQUENCE SET; Schema: test; Owner: testdb
--

SELECT pg_catalog.setval('customers_el_id_seq', 105, true);


--
-- Data for Name: customers_en; Type: TABLE DATA; Schema: test; Owner: testdb
--

INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (1, 'Robertson', 'Jerry', 'Kevin', 1, '01173 Doe Crossing Hill, Texas, 77346, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (2, 'Wallace', 'Craig', 'Jason', 1, '8024 Gale Trail, New York, 14263, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (3, 'Fowler', 'Jeremy', 'Shawn', 1, '23 Dottie Trail, Virginia, 20189, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (4, 'Johnston', 'Patrick', 'Dennis', 1, '03 Scott Terrace, Nevada, 89120, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (5, 'Gardner', 'Shawn', 'Eric', 1, '988 Wayridge Park, Arizona, 85255, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (6, 'Walker', 'Todd', 'Mark', 1, '92645 Reinke Road, Maryland, 21405, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (7, 'Bell', 'Bobby', 'Antonio', 1, '0 Valley Edge Pass, California, 94142, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (8, 'Carpenter', 'Roy', 'Wayne', 1, '69845 Mockingbird Circle, Alabama, 36177, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (9, 'Campbell', 'Ryan', 'Christopher', 1, '19 Warner Point, Montana, 59806, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (10, 'George', 'Philip', 'Howard', 1, '8 Gateway Place, Ohio, 44760, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (11, 'Hunter', 'Jason', 'Edward', 1, '435 Thompson Parkway, Texas, 78044, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (12, 'Riley', 'Ronald', 'Brandon', 1, '96706 Sugar Avenue, Florida, 34290, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (13, 'Campbell', 'Todd', 'Peter', 1, '9836 Knutson Plaza, Florida, 32891, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (14, 'Ray', 'Brandon', 'Benjamin', 1, '021 Superior Lane, Colorado, 80310, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (15, 'Ellis', 'Anthony', 'Frank', 1, '178 Marquette Hill, Florida, 34745, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (16, 'Ford', 'Ryan', 'Daniel', 1, '51723 Sheridan Trail, Ohio, 45238, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (17, 'Boyd', 'Brian', 'Sean', 1, '25 Riverside Trail, District of Columbia, 20436, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (18, 'Robinson', 'Ronald', 'Joshua', 1, '5 Merchant Park, North Carolina, 28278, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (19, 'Carr', 'Brian', 'Timothy', 1, '3 Oak Hill, Michigan, 48550, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (20, 'Clark', 'Albert', 'Ryan', 1, '2 Stuart Place, Oklahoma, 73124, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (21, 'Armstrong', 'Walter', 'Dennis', 1, '71 Mesta Road, Louisiana, 70183, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (22, 'Brooks', 'Steven', 'Adam', 1, '96571 Del Sol Way, California, 92196, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (23, 'Moore', 'Keith', 'Russell', 1, '9 Merry Avenue, Louisiana, 71208, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (24, 'Rose', 'Larry', 'Anthony', 1, '58343 Crowley Trail, California, 94177, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (25, 'Gonzalez', 'Alan', 'Edward', 1, '24427 Lake View Way, Connecticut, 6816, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (26, 'Scott', 'Bobby', 'Clarence', 1, '6 Karstens Avenue, New York, 10454, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (27, 'Rose', 'Justin', 'Jeffrey', 1, '84615 Rieder Alley, New Mexico, 87110, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (28, 'Ferguson', 'Edward', 'Dennis', 1, '89 Riverside Plaza, Florida, 33233, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (29, 'Turner', 'Thomas', 'Harold', 1, '563 Hollow Ridge Center, Texas, 76147, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (30, 'Murphy', 'Ryan', 'Victor', 1, '5 Rigney Lane, California, 92145, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (31, 'Jordan', 'Adam', 'Richard', 1, '4378 Veith Terrace, Texas, 77346, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (32, 'Bailey', 'Joe', 'Ralph', 1, '461 Carberry Lane, Texas, 78754, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (33, 'Oliver', 'Anthony', 'Joseph', 1, '32811 Daystar Way, Missouri, 63169, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (34, 'Rice', 'Peter', 'William', 1, '259 Warbler Drive, California, 92668, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (35, 'Rodriguez', 'Harry', 'Peter', 1, '9187 Chinook Lane, Ohio, 45271, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (36, 'Hill', 'Lawrence', 'Jose', 1, '15048 7th Place, Texas, 78205, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (37, 'Adams', 'Gary', 'Andrew', 1, '93454 Carioca Point, Texas, 88584, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (38, 'Warren', 'Keith', 'Carl', 1, '37 Warrior Park, Texas, 77386, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (39, 'Dunn', 'Douglas', 'Carlos', 1, '56 Ridgeway Trail, New York, 13251, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (40, 'Freeman', 'Martin', 'Antonio', 1, '81 Carpenter Circle, Texas, 75310, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (41, 'Lee', 'Justin', 'Stephen', 1, '0 Anzinger Point, Florida, 34981, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (42, 'Wheeler', 'Bobby', 'Brian', 1, '058 Melrose Alley, California, 93584, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (43, 'Woods', 'Howard', 'Clarence', 1, '53380 Esker Center, Maryland, 20851, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (44, 'Fowler', 'Albert', 'Benjamin', 1, '37669 Dennis Plaza, Nevada, 89155, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (45, 'Wilson', 'Gerald', 'Daniel', 1, '6 Longview Point, Texas, 77010, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (46, 'George', 'Adam', 'Gary', 1, '17 Straubel Street, New Jersey, 8650, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (47, 'Meyer', 'Bruce', 'Ronald', 1, '93 Hovde Park, Arizona, 85020, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (48, 'Franklin', 'Harold', 'Jesse', 1, '73272 Sheridan Circle, Texas, 76147, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (49, 'Wagner', 'Kenneth', 'Joe', 1, '1 Drewry Court, Texas, 78737, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (50, 'Young', 'Chris', 'Jason', 1, '05 Tennyson Junction, North Carolina, 28405, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (51, 'Fisher', 'Dorothy', 'Willie', 2, '68 Jay Way, California, 92835, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (52, 'Ward', 'Kelly', 'Nicholas', 2, '73 Haas Avenue, California, 90405, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (53, 'Washington', 'Anne', 'Harry', 2, '67612 Sutteridge Crossing, Arizona, 85053, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (54, 'Knight', 'Ann', 'Gerald', 2, '7482 1st Point, Washington, 99220, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (55, 'Murphy', 'Amanda', 'Jose', 2, '73 Main Park, Virginia, 23225, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (56, 'Nguyen', 'Shirley', 'Jesse', 2, '8786 Service Road, Pennsylvania, 15240, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (57, 'Russell', 'Emily', 'Donald', 2, '91 Hintze Park, Georgia, 30351, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (58, 'Garrett', 'Kathy', 'Carlos', 2, '9 7th Alley, Louisiana, 70593, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (59, 'Bowman', 'Bonnie', 'Stephen', 2, '36 Judy Alley, California, 93407, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (60, 'Kelly', 'Ann', 'Carlos', 2, '911 Arkansas Junction, Nebraska, 68197, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (61, 'Rodriguez', 'Brenda', 'Jack', 2, '0495 Brickson Park Street, New Jersey, 8608, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (62, 'Stevens', 'Lori', 'Gregory', 2, '0 Vidon Point, New York, 10305, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (63, 'Murphy', 'Sarah', 'Louis', 2, '782 Bartillon Street, Indiana, 46805, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (64, 'Oliver', 'Sarah', 'Todd', 2, '35619 Havey Point, Georgia, 30301, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (65, 'Oliver', 'Anne', 'Billy', 2, '962 Burrows Way, Georgia, 31106, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (66, 'Palmer', 'Donna', 'Patrick', 2, '7625 Schlimgen Park, Washington, 98175, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (67, 'Alvarez', 'Paula', 'Kenneth', 2, '10 Iowa Hill, Pennsylvania, 15205, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (68, 'Bishop', 'Nancy', 'Patrick', 2, '11922 Duke Avenue, Michigan, 48901, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (69, 'Flores', 'Kelly', 'Timothy', 2, '52 Melody Plaza, Pennsylvania, 18105, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (70, 'Carroll', 'Ann', 'Gerald', 2, '8 Ruskin Street, Indiana, 46852, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (71, 'Harrison', 'Kathleen', 'Gerald', 2, '13269 Monument Circle, District of Columbia, 20566, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (72, 'Duncan', 'Rose', 'Jonathan', 2, '3772 Sloan Plaza, Michigan, 48956, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (73, 'Wells', 'Ann', 'Matthew', 2, '254 Heffernan Junction, California, 92668, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (74, 'Young', 'Doris', 'Gary', 2, '00047 Sherman Pass, California, 92121, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (75, 'Wood', 'Phyllis', 'Todd', 2, '22 Union Pass, Arkansas, 72231, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (76, 'Brooks', 'Carol', 'Arthur', 2, '64 Alpine Hill, Missouri, 64101, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (77, 'Greene', 'Tammy', 'Keith', 2, '23 Rowland Terrace, Florida, 32405, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (78, 'Tucker', 'Stephanie', 'Timothy', 2, '942 Maywood Park, Florida, 32830, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (79, 'Banks', 'Sara', 'Ronald', 2, '11920 Mayfield Hill, New York, 10090, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (80, 'Mccoy', 'Melissa', 'Donald', 2, '3 Mifflin Crossing, Minnesota, 55166, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (81, 'Dunn', 'Patricia', 'Steve', 2, '6891 Anthes Lane, Connecticut, 6905, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (82, 'Hawkins', 'Virginia', 'Harry', 2, '48516 Rockefeller Alley, Texas, 75310, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (83, 'Hunter', 'Janet', 'Billy', 2, '6630 Randy Lane, Texas, 77260, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (84, 'Franklin', 'Janet', 'Keith', 2, '12 Graedel Pass, Ohio, 45807, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (85, 'Roberts', 'Gloria', 'Carl', 2, '9055 Redwing Park, Florida, 32511, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (86, 'Campbell', 'Martha', 'Jeremy', 2, '48 Steensland Circle, Florida, 33448, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (87, 'Bell', 'Annie', 'Ronald', 2, '5262 Starling Crossing, District of Columbia, 56944, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (88, 'Stephens', 'Evelyn', 'Mark', 2, '8887 Loftsgordon Lane, North Carolina, 28242, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (89, 'Murray', 'Tina', 'Steven', 2, '76179 Bobwhite Park, Arizona, 85743, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (90, 'Rogers', 'Pamela', 'Chris', 2, '77458 Sage Place, North Dakota, 58106, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (91, 'Stevens', 'Ruth', 'Adam', 2, '7 Maple Wood Circle, Maryland, 20918, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (92, 'Gonzales', 'Cheryl', 'Ronald', 2, '25 Washington Road, Texas, 77493, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (93, 'Coleman', 'Kathryn', 'Jerry', 2, '2801 Debra Junction, Kentucky, 40591, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (94, 'Banks', 'Cheryl', 'Edward', 2, '6501 Eggendart Way, Texas, 78291, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (95, 'Weaver', 'Brenda', 'Victor', 2, '2538 Dorton Avenue, California, 93584, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (96, 'King', 'Mildred', 'Robert', 2, '24 Sage Drive, Missouri, 63158, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (97, 'Lawson', 'Kathryn', 'Robert', 2, '61575 Continental Drive, Missouri, 64054, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (98, 'Hart', 'Sharon', 'Keith', 2, '6 Elka Street, Tennessee, 37914, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (99, 'Ross', 'Tina', 'Charles', 2, '347 Debs Court, Georgia, 31998, United States');
INSERT INTO customers_en (id, lastname, firstname, fathername, gender, address) VALUES (100, 'Holmes', 'Melissa', 'Gary', 2, '5 Forest Run Terrace, Texas, 88589, United States');


--
-- Name: customers_en_id_seq; Type: SEQUENCE SET; Schema: test; Owner: testdb
--

SELECT pg_catalog.setval('customers_en_id_seq', 100, true);


--
-- Name: customers_el customers_el_pkey; Type: CONSTRAINT; Schema: test; Owner: testdb
--

ALTER TABLE ONLY customers_el
    ADD CONSTRAINT customers_el_pkey PRIMARY KEY (id);


--
-- Name: customers_en customers_en_pkey; Type: CONSTRAINT; Schema: test; Owner: testdb
--

ALTER TABLE ONLY customers_en
    ADD CONSTRAINT customers_en_pkey PRIMARY KEY (id);


--
-- PostgreSQL database dump complete
--

