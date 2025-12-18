/* ================================================================
   CRIAÇÃO DO BANCO DE DADOS
================================================================ */
CREATE DATABASE IF NOT EXISTS rpg_system
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE rpg_system;

/* ================================================================
   1. USERS
================================================================ */
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    nickname VARCHAR(100),
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* ================================================================
   2. ROLES
================================================================ */
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* único seed */
INSERT INTO roles (role_name, description) VALUES
('admin', 'Administrador completo do sistema.'),
('dungeon_master', 'Criador e mestre das campanhas.'),
('player', 'Jogador participante das campanhas.');

/* ================================================================
   3. PERMISSIONS
================================================================ */
CREATE TABLE permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    permission_name VARCHAR(150) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* ================================================================
   4. ROLE_PERMISSIONS (N:N)
================================================================ */
CREATE TABLE role_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE (role_id, permission_id),

    CONSTRAINT fk_role_permissions_role
        FOREIGN KEY (role_id) REFERENCES roles(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_role_permissions_permission
        FOREIGN KEY (permission_id) REFERENCES permissions(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* ================================================================
   5. USER_ROLES (N:N)
================================================================ */
CREATE TABLE user_roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE (user_id, role_id),

    CONSTRAINT fk_user_roles_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_user_roles_role
        FOREIGN KEY (role_id) REFERENCES roles(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* ================================================================
   6. RACES
================================================================ */
CREATE TABLE races (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* ================================================================
   7. RACE_ATTRIBUTES
================================================================ */
CREATE TABLE race_attributes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    race_id INT NOT NULL,
    attribute_name VARCHAR(100) NOT NULL,
    attribute_value INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE (race_id, attribute_name),

    CONSTRAINT fk_race_attributes_race
        FOREIGN KEY (race_id) REFERENCES races(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* ================================================================
   8. ORDERS (ANTES: CLASSES)
================================================================ */
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    required_race_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_orders_required_race
        FOREIGN KEY (required_race_id) REFERENCES races(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* ================================================================
   9. ORDER_ATTRIBUTES (ANTES: CLASS_ATTRIBUTES)
================================================================ */
CREATE TABLE order_attributes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    attribute_name VARCHAR(100) NOT NULL,
    attribute_value INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE (order_id, attribute_name),

    CONSTRAINT fk_order_attributes_order
        FOREIGN KEY (order_id) REFERENCES orders(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* ================================================================
   10. CHARACTERS
================================================================ */
CREATE TABLE characters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    description TEXT,
    race_id INT,
    order_id INT,
    created_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_characters_race
        FOREIGN KEY (race_id) REFERENCES races(id)
        ON DELETE SET NULL,

    CONSTRAINT fk_characters_order
        FOREIGN KEY (order_id) REFERENCES orders(id)
        ON DELETE SET NULL,

    CONSTRAINT fk_characters_created_by
        FOREIGN KEY (created_by) REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* ================================================================
   11. USER_CHARACTERS
================================================================ */
CREATE TABLE user_characters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    character_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE (user_id, character_id),

    CONSTRAINT fk_user_characters_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_user_characters_character
        FOREIGN KEY (character_id) REFERENCES characters(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* ================================================================
   12. ELEMENT_TYPES
================================================================ */
CREATE TABLE element_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO element_types (name, description)
VALUES (
    'Normal',
    'O tipo Normal representa tudo aquilo que é comum, físico e não especializado. Criaturas e entidades desse tipo existem dentro das regras básicas do mundo material, sem recorrer a magia, conceitos abstratos ou forças sobrenaturais. Por não possuir especialização, o Normal não apresenta vantagens contra outros tipos, mas torna-se vulnerável a técnicas avançadas, ataques mentais e forças que exploram o medo e a corrupção. É o estado natural da existência antes da influência do extraordinário.'
);
INSERT INTO element_types (name, description)
VALUES (
    'Fogo',
    'O tipo Fogo representa calor extremo, combustão, destruição acelerada e transformação violenta da matéria. É a manifestação da energia em seu estado mais instável, capaz de consumir, purificar ou remodelar tudo ao seu redor. Criaturas e entidades de Fogo são agressivas por natureza, prosperam na destruição e na mudança rápida, mas dependem de combustível e controle para não se extinguirem. O Fogo domina o que é orgânico, frágil ou frio, porém sucumbe diante de forças que o apagam, o contêm ou o isolam.'
);
INSERT INTO element_types (name, description)
VALUES (
    'Água',
    'O tipo Água representa fluidez, adaptação, profundidade e persistência. É o elemento que molda o mundo ao longo do tempo, não pela força imediata, mas pela constância. A Água apaga o fogo, corrói estruturas sólidas e se infiltra onde outras forças não alcançam. Criaturas desse tipo tendem a ser resilientes, flexíveis e difíceis de conter, mas tornam-se vulneráveis a forças que interrompem seu fluxo, drenam sua energia ou a transformam em um estado rígido e controlável.'
);
INSERT INTO element_types (name, description)
VALUES (
    'Grama',
    'O tipo Grama representa vida orgânica, crescimento, regeneração e a força da natureza em seu estado mais persistente. Diferente de elementos destrutivos imediatos, a Grama domina através da expansão lenta, da adaptação ao ambiente e da capacidade de se recuperar mesmo após grandes perdas. Criaturas desse tipo utilizam o terreno, a absorção de energia e a conexão com o ambiente para vencer, mas tornam-se frágeis diante de forças que consomem, congelam ou corrompem a vida.'
);
INSERT INTO element_types (name, description)
VALUES (
    'Elétrico',
    'O tipo Elétrico representa energia pura em movimento, velocidade extrema e descarga instantânea. Diferente de outros elementos, o Elétrico não se acumula nem persiste: ele acontece em um instante decisivo. Criaturas e entidades desse tipo dominam pela rapidez, pela surpresa e pela capacidade de sobrecarregar sistemas vivos ou artificiais. No entanto, sua dependência de condução e fluxo o torna vulnerável a materiais isolantes, aterramento e estruturas que dissipam ou absorvem energia.'
);
INSERT INTO element_types (name, description)
VALUES (
    'Terra',
    'O tipo Terra representa estabilidade, solidez, sustentação e domínio do terreno. É o elemento que dá forma ao mundo físico, controlando espaço, peso e resistência. Criaturas desse tipo utilizam o solo como extensão de si mesmas, manipulando terreno, enterrando ameaças e anulando forças que dependem de condução, movimento ou instabilidade. No entanto, a Terra é vulnerável a forças que a atravessam, a desgastam ao longo do tempo ou que se aproveitam de sua rigidez para quebrá-la.'
);
INSERT INTO element_types (name, description)
VALUES (
    'Pedra',
    'O tipo Pedra representa rigidez extrema, durabilidade e resistência bruta. Diferente da Terra, que molda e controla o terreno, a Pedra existe como matéria sólida e inflexível, feita para suportar impacto, pressão e desgaste. Criaturas desse tipo são difíceis de quebrar e raramente recuam, utilizando sua massa e dureza como arma. No entanto, sua rigidez excessiva as torna vulneráveis a forças que exploram fraturas, corrosão prolongada ou impacto concentrado.'
);
INSERT INTO element_types (name, description)
VALUES (
    'Gelo',
    'O tipo Gelo representa frio extremo, estagnação e a interrupção violenta do movimento. Diferente da Água, que se adapta e flui, o Gelo impõe rigidez, paralisa processos e torna estruturas frágeis. Criaturas desse tipo dominam ao desacelerar, congelar e quebrar seus inimigos, explorando a perda de mobilidade e a fragilidade causada pelo frio intenso. No entanto, o Gelo é instável por natureza e sucumbe facilmente a fontes de calor, impacto concentrado ou forças que exigem resistência contínua.'
);
INSERT INTO element_types (name, description)
VALUES (
    'Voador',
    'O tipo Voador representa mobilidade aérea, liberdade de movimento e domínio da posição. Criaturas desse tipo não dependem do solo e utilizam altitude, velocidade e ângulo como suas principais armas. O Voador vence evitando confrontos diretos, atacando de cima e explorando a incapacidade do inimigo de alcançá-lo. No entanto, sua dependência do ar e da estabilidade em voo o torna extremamente vulnerável a forças que interrompem movimento, atingem à distância ou exploram a falta de ancoragem.'
);
INSERT INTO element_types (name, description)
VALUES (
    'Lutador',
    'O tipo Lutador representa disciplina corporal, técnica refinada e domínio absoluto do combate físico. Diferente do tipo Normal, que atua de forma instintiva ou improvisada, o Lutador transforma o próprio corpo em uma arma precisa, explorando pontos fracos, ritmo e impacto concentrado. Criaturas desse tipo superam adversários através de treino, controle e resistência mental. No entanto, sua dependência do contato direto e da força física o torna vulnerável a forças que ignoram o corpo, manipulam a mente ou exploram estados intangíveis.'
);
INSERT INTO element_types (name, description)
VALUES (
    'Veneno',
    'O tipo Veneno representa toxinas, contaminação e a degradação gradual da vida. Diferente de forças destrutivas imediatas, o Veneno atua de forma silenciosa e persistente, enfraquecendo o inimigo ao longo do tempo. Criaturas desse tipo exploram a vulnerabilidade biológica, infiltrando-se no organismo, no solo ou no ambiente para causar dano contínuo. No entanto, o Veneno perde eficácia contra entidades que não possuem metabolismo vivo ou que conseguem neutralizar, purificar ou ignorar substâncias tóxicas.'
);
INSERT INTO element_types (name, description)
VALUES (
    'Inseto',
    'O tipo Inseto representa organismos pequenos, numerosos e altamente adaptáveis. Diferente de criaturas maiores e mais poderosas, os Insetos dominam pela quantidade, pela velocidade de reprodução e pela capacidade de explorar brechas. Criaturas desse tipo utilizam enxames, venenos naturais, carapaças e estratégias oportunistas para vencer inimigos muito maiores. No entanto, sua fragilidade física e dependência biológica os tornam extremamente vulneráveis a forças destrutivas, ambientais ou que eliminam grandes áreas de uma só vez.'
);
INSERT INTO element_types (name, description)
VALUES (
    'Ferro',
    'O tipo Ferro representa metal trabalhado, resistência estrutural e a aplicação da matéria com propósito e técnica. Diferente da Pedra, que existe em estado bruto, o Ferro é moldado, refinado e utilizado como ferramenta, armadura e arma. Criaturas desse tipo são extremamente duráveis, resistentes a danos biológicos e pouco afetadas por condições ambientais comuns. No entanto, o Ferro sofre contra forças que corroem, oxidam ou interferem diretamente em sua integridade estrutural ou energética.'
);
INSERT INTO element_types (name, description)
VALUES (
    'Psíquico',
    'O tipo Psíquico representa a mente, a consciência e o domínio da realidade através do pensamento. Criaturas desse tipo atacam não o corpo, mas a percepção, a vontade e o controle do próprio ser. O Psíquico vence ao manipular emoções, prever ações e impor sua força mental sobre inimigos despreparados. No entanto, sua dependência da racionalidade e da consciência o torna vulnerável a forças caóticas, instintivas ou que simplesmente não possuem mente para ser controlada.'
);
INSERT INTO element_types (name, description)
VALUES (
    'Fantasma',
    'O tipo Fantasma representa entidades intangíveis, ecos da existência e consciências que não pertencem mais ao plano material. Criaturas desse tipo não seguem as leis físicas convencionais, atravessando matéria sólida e ignorando ataques puramente corporais. O Fantasma domina através do medo, da imprevisibilidade e da incapacidade do inimigo de interagir diretamente com ele. No entanto, forças que atuam no plano espiritual, emocional ou conceitual conseguem atingi-lo e até mesmo bani-lo.'
);
INSERT INTO element_types (name, description)
VALUES (
    'Sombrio',
    'O tipo Sombrio representa o medo, a corrupção, os instintos primitivos e tudo aquilo que se esconde nas profundezas da mente e da existência. Diferente do Fantasma, que é intangível, o Sombrio atua corroendo emoções, vontades e princípios, explorando inseguranças e desejos ocultos. Criaturas desse tipo vencem ao quebrar a confiança do inimigo, espalhar pânico e impor caos psicológico. No entanto, forças que iluminam, purificam ou impõem ordem emocional conseguem neutralizar sua influência.'
);
INSERT INTO element_types (name, description)
VALUES (
    'Fada',
    'O tipo Fada representa magia natural, emoções intensas e forças que existem fora da lógica racional. Diferente do Arcano, que é magia estudada e controlada, a Fada manifesta encantamentos instintivos, laços emocionais e leis próprias da natureza mágica. Criaturas desse tipo vencem através de empatia, ilusão, vínculos e quebra de expectativas, mas tornam-se vulneráveis a estruturas rígidas, pragmatismo extremo e forças que impõem ordem absoluta.'
);
INSERT INTO element_types (name, description)
VALUES (
    'Dragão',
    'O tipo Dragão representa forças primordiais, ancestrais e dominantes do mundo. Dragões não são apenas criaturas elementais, mas manifestações vivas do poder bruto da criação, combinando resistência extrema, instinto superior e presença esmagadora. Entidades desse tipo impõem respeito e destruição apenas por existirem. No entanto, apesar de seu poder, os Dragões não são absolutos: forças que quebram o ciclo da criação, manipulam emoções profundas ou impõem regras sobrenaturais conseguem enfrentá-los.'
);
INSERT INTO element_types (name, description)
VALUES (
    'Divino',
    'O tipo Divino representa autoridade sagrada, propósito absoluto e poder emanado de entidades superiores, como anjos, santos, avatares ou deuses menores. Diferente do tipo Luz, que é energia e revelação, o Divino é julgamento: ele impõe uma ordem que não depende das leis do mundo material. Quando um ser Divino se manifesta, não é apenas um combate — é um acontecimento. O Divino domina aquilo que é corrompido, profano ou sustentado por forças antinaturais, mas ainda assim pode ser enfrentado por conceitos que distorcem a realidade, negam a ordem ou pertencem a uma hierarquia oposta.'
);
INSERT INTO element_types (name, description)
VALUES (
    'Demônio',
    'O tipo Demônio representa corrupção consciente, tentação, pactos profanos e a quebra deliberada da ordem natural e moral. Diferente do Sombrio, que atua através do medo e da instabilidade emocional, o Demônio age com intenção, oferecendo poder em troca de submissão ou ruína. Entidades demoníacas distorcem regras, manipulam desejos e exploram falhas na vontade e na fé. Sua presença altera o ambiente, contamina decisões e transforma conflitos em tragédias. No entanto, forças que impõem ordem absoluta, revelam a verdade ou anulam pactos conseguem enfrentá-los.'
);
INSERT INTO element_types (name, description)
VALUES (
    'Luz',
    'O tipo Luz representa revelação, clareza, verdade e a exposição do que está oculto. Diferente do Divino, que impõe julgamento e autoridade, a Luz simplesmente mostra o que é — sem misericórdia ou negociação. Criaturas desse tipo dissipam ilusões, revelam corrupções e enfraquecem tudo que depende de segredos, medo ou engano. No entanto, a Luz não é destruição absoluta: ela pode ser evitada, distorcida ou anulada por forças que dobram a realidade, absorvem energia ou se alimentam da própria exposição.'
);
INSERT INTO element_types (name, description)
VALUES (
    'Arcano',
    'O tipo Arcano representa magia estudada, conhecimento proibido e a manipulação consciente das forças que regem a realidade. Diferente da Fada, cuja magia é instintiva, ou do Divino, que impõe autoridade, o Arcano existe através do entendimento, da experimentação e do domínio técnico do sobrenatural. Criaturas e entidades arcanas moldam leis naturais, criam rituais e distorcem fenômenos através do saber acumulado. No entanto, sua dependência de foco, estrutura e preparação o torna vulnerável a forças que interrompem concentração, quebram fórmulas ou impõem caos.'
);
INSERT INTO element_types (name, description)
VALUES (
    'Construto',
    'O tipo Construto representa entidades artificiais animadas por magia, runas ou forças externas, como golens, guardiões arcanos e elementais vinculados. Diferente de criaturas vivas, Construtos não possuem metabolismo, emoções ou instintos naturais, operando de acordo com comandos, vínculos ou propósitos definidos por seu criador. São extremamente resistentes a condições biológicas e psicológicas, mas dependem de energia, estabilidade mágica e integridade estrutural para funcionar.'
);
INSERT INTO element_types (name, description)
VALUES (
    'Máquina',
    'O tipo Máquina representa tecnologia, lógica fria e funcionamento baseado em sistemas mecânicos ou eletrônicos. Diferente dos Construtos, que dependem de magia ou vínculos arcanos, as Máquinas operam por engrenagens, circuitos, algoritmos e energia física mensurável. Entidades desse tipo são precisas, consistentes e imunes a falhas emocionais ou biológicas. No entanto, sua rigidez lógica e dependência de energia e manutenção as tornam vulneráveis a interferências que causem sobrecarga, corrosão ou quebra de sistemas.'
);
INSERT INTO element_types (name, description)
VALUES (
    'Morto-vivo',
    'O tipo Morto-vivo representa a negação do ciclo natural da vida e da morte. Criaturas desse tipo são corpos que deveriam descansar, mas continuam existindo através de necromancia, maldições ou forças externas. Diferente dos Fantasmas, os Mortos-vivos possuem forma física, ainda que corrompida, e agem de maneira instintiva ou compulsiva. Eles não sentem dor, medo ou cansaço, avançando implacavelmente até serem destruídos ou purificados. No entanto, sua existência instável os torna vulneráveis a forças que restauram a ordem natural ou consomem a energia que os sustenta.'
);
INSERT INTO element_types (name, description)
VALUES (
    'Tempo',
    'O tipo Tempo representa a passagem inevitável, a decadência, a repetição e a impossibilidade de permanência. Diferente de forças destrutivas imediatas, o Tempo não ataca — ele desgasta. Tudo que existe está sujeito ao envelhecimento, à obsolescência ou ao esquecimento. Entidades desse tipo manipulam aceleração, estagnação, regressão e ciclos, vencendo não pela força, mas pela certeza de que nada permanece intacto para sempre. No entanto, o Tempo encontra limites diante de forças que existem fora da linearidade, se alimentam do momento presente ou negam o próprio conceito de duração.'
);
INSERT INTO element_types (name, description)
VALUES (
    'Espaço',
    'O tipo Espaço representa distância, posição, fronteira e a estrutura que define onde algo pode ou não existir. Diferente do Tempo, que age sobre a duração, o Espaço controla separação, isolamento e presença. Entidades espaciais dobram caminhos, criam barreiras impossíveis, separam causas de efeitos e tornam ataques irrelevantes simplesmente por não permitir que alcancem seu alvo. O Espaço vence não pela força, mas pela negação do contato. No entanto, forças que não dependem de posição, que existem apenas no instante ou que se manifestam internamente conseguem escapar de seu domínio.'
);
INSERT INTO element_types (name, description)
VALUES (
    'Som',
    'O tipo Som representa vibração, ressonância e impacto imediato. Diferente de forças que se acumulam ou persistem, o Som existe apenas no instante em que acontece, atravessando matéria, energia e até conceitos abstratos. Ele não depende de contato físico direto, nem de presença contínua: quando ocorre, já causou seu efeito. Criaturas e entidades desse tipo dominam ao quebrar concentração, romper estruturas internas e desestabilizar aquilo que exige foco, silêncio ou permanência. No entanto, o Som perde força diante de massas extremamente densas ou ambientes que absorvem completamente vibrações.'
);
INSERT INTO element_types (name, description)
VALUES (
    'Sangue',
    'O tipo Sangue representa vida em movimento, instinto, herança e a força vital que corre dentro dos corpos. Diferente de Grama, que simboliza a vida natural externa, o Sangue atua internamente, afetando diretamente o organismo, a linhagem e a essência física de um ser. Criaturas desse tipo dominam através de maldições sanguíneas, hemomancia, vínculo corporal e exploração de fraquezas internas. O Sangue ignora barreiras externas e conceitos espaciais, mas se torna vulnerável a forças que purificam, congelam ou interrompem o fluxo vital.'
);

/* ================================================================
   13. ELEMENT_TYPE_RELATIONS
================================================================ */
CREATE TABLE element_type_relations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    source_element_id INT NOT NULL,
    target_element_id INT NOT NULL,
    relation_type ENUM('strength', 'weakness', 'immunity') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_relation_source
        FOREIGN KEY (source_element_id)
        REFERENCES element_types(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_relation_target
        FOREIGN KEY (target_element_id)
        REFERENCES element_types(id)
        ON DELETE CASCADE,

    CONSTRAINT uq_element_relation
        UNIQUE (source_element_id, target_element_id, relation_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `element_type_relations` (`source_element_id`, `target_element_id`, `relation_type`) VALUES
(1, 10, 'weakness'),
(1, 14, 'weakness'),
(1, 16, 'weakness'),
(2, 4, 'strength'),
(2, 8, 'strength'),
(2, 12, 'strength'),
(2, 3, 'weakness'),
(2, 6, 'weakness'),
(2, 7, 'weakness'),
(2, 28, 'immunity'),
(3, 2, 'strength'),
(3, 7, 'strength'),
(3, 6, 'strength'),
(3, 5, 'weakness'),
(3, 4, 'weakness'),
(3, 8, 'weakness'),
(3, 2, 'immunity'),
(4, 3, 'strength'),
(4, 6, 'strength'),
(4, 7, 'strength'),
(4, 2, 'weakness'),
(4, 8, 'weakness'),
(4, 11, 'weakness'),
(4, 3, 'immunity'),
(5, 3, 'strength'),
(5, 9, 'strength'),
(5, 24, 'strength'),
(5, 6, 'weakness'),
(5, 7, 'weakness'),
(5, 28, 'immunity'),
(6, 5, 'strength'),
(6, 2, 'strength'),
(6, 11, 'strength'),
(6, 3, 'weakness'),
(6, 4, 'weakness'),
(6, 8, 'weakness'),
(6, 5, 'immunity'),
(7, 2, 'strength'),
(7, 12, 'strength'),
(7, 9, 'strength'),
(7, 3, 'weakness'),
(7, 4, 'weakness'),
(7, 10, 'weakness'),
(7, 28, 'immunity'),
(8, 4, 'strength'),
(8, 6, 'strength'),
(8, 9, 'strength'),
(8, 2, 'weakness'),
(8, 10, 'weakness'),
(8, 7, 'weakness'),
(8, 3, 'immunity'),
(9, 4, 'strength'),
(9, 12, 'strength'),
(9, 10, 'strength'),
(9, 5, 'weakness'),
(9, 8, 'weakness'),
(9, 7, 'weakness'),
(9, 6, 'immunity'),
(10, 1, 'strength'),
(10, 7, 'strength'),
(10, 8, 'strength'),
(10, 14, 'weakness'),
(10, 15, 'weakness'),
(10, 9, 'weakness'),
(11, 4, 'strength'),
(11, 1, 'strength'),
(11, 12, 'strength'),
(11, 6, 'weakness'),
(11, 14, 'weakness'),
(11, 13, 'weakness'),
(11, 25, 'immunity'),
(12, 4, 'strength'),
(12, 14, 'strength'),
(12, 16, 'strength'),
(12, 2, 'weakness'),
(12, 7, 'weakness'),
(12, 9, 'weakness'),
(12, 28, 'immunity'),
(13, 12, 'strength'),
(13, 4, 'strength'),
(13, 17, 'strength'),
(13, 2, 'weakness'),
(13, 5, 'weakness'),
(13, 3, 'weakness'),
(13, 11, 'immunity'),
(14, 10, 'strength'),
(14, 11, 'strength'),
(14, 1, 'strength'),
(14, 16, 'weakness'),
(14, 12, 'weakness'),
(14, 15, 'weakness'),
(15, 1, 'strength'),
(15, 10, 'strength'),
(15, 12, 'strength'),
(15, 14, 'weakness'),
(15, 16, 'weakness'),
(15, 21, 'weakness'),
(15, 1, 'immunity'),
(16, 14, 'strength'),
(16, 15, 'strength'),
(16, 1, 'strength'),
(16, 21, 'weakness'),
(16, 17, 'weakness'),
(16, 19, 'weakness'),
(17, 16, 'strength'),
(17, 18, 'strength'),
(17, 10, 'strength'),
(17, 13, 'weakness'),
(17, 11, 'weakness'),
(17, 28, 'weakness'),
(17, 18, 'immunity'),
(18, 2, 'strength'),
(18, 3, 'strength'),
(18, 5, 'strength'),
(18, 17, 'weakness'),
(18, 16, 'weakness'),
(18, 8, 'weakness'),
(18, 1, 'immunity'),
(19, 25, 'strength'),
(19, 20, 'strength'),
(19, 16, 'strength'),
(19, 26, 'weakness'),
(19, 27, 'weakness'),
(19, 1, 'immunity'),
(20, 19, 'strength'),
(20, 25, 'strength'),
(20, 14, 'strength'),
(20, 21, 'weakness'),
(20, 19, 'weakness'),
(20, 17, 'weakness'),
(20, 1, 'immunity'),
(21, 16, 'strength'),
(21, 15, 'strength'),
(21, 20, 'strength'),
(21, 27, 'weakness'),
(21, 26, 'weakness'),
(21, 13, 'weakness'),
(22, 19, 'strength'),
(22, 20, 'strength'),
(22, 23, 'strength'),
(22, 28, 'weakness'),
(22, 10, 'weakness'),
(22, 12, 'weakness'),
(22, 1, 'immunity'),
(23, 1, 'strength'),
(23, 12, 'strength'),
(23, 25, 'strength'),
(23, 22, 'weakness'),
(23, 5, 'weakness'),
(23, 3, 'weakness'),
(23, 11, 'immunity'),
(24, 1, 'strength'),
(24, 12, 'strength'),
(24, 16, 'strength'),
(24, 5, 'weakness'),
(24, 3, 'weakness'),
(24, 22, 'weakness'),
(24, 11, 'immunity'),
(25, 1, 'strength'),
(25, 14, 'strength'),
(25, 12, 'strength'),
(25, 19, 'weakness'),
(25, 21, 'weakness'),
(25, 2, 'weakness'),
(25, 11, 'immunity'),
(26, 19, 'strength'),
(26, 13, 'strength'),
(26, 24, 'strength'),
(26, 27, 'weakness'),
(26, 28, 'weakness'),
(26, 17, 'weakness'),
(26, 1, 'immunity'),
(27, 21, 'strength'),
(27, 19, 'strength'),
(27, 24, 'strength'),
(27, 26, 'weakness'),
(27, 28, 'weakness'),
(27, 29, 'weakness'),
(27, 6, 'immunity'),
(28, 22, 'strength'),
(28, 17, 'strength'),
(28, 26, 'strength'),
(28, 7, 'weakness'),
(28, 13, 'weakness'),
(28, 3, 'weakness'),
(28, 15, 'immunity'),
(29, 27, 'strength'),
(29, 1, 'strength'),
(29, 10, 'strength'),
(29, 21, 'weakness'),
(29, 8, 'weakness'),
(29, 11, 'weakness'),
(29, 15, 'immunity');

/* ================================================================
   14. ABILITIES (personagem)
================================================================ */
CREATE TABLE abilities (
    id INT AUTO_INCREMENT PRIMARY KEY,

    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,

    arcane_title VARCHAR(150),
    arcane_description TEXT,

    mana_cost INT NOT NULL DEFAULT 0,
    arcane_mana_cost INT DEFAULT NULL,

    dice_formula VARCHAR(50) NULL,
    base_damage INT NOT NULL DEFAULT 0,
    bonus_speed INT NOT NULL DEFAULT 0,

    element_type_id INT NULL,

    required_race_id INT NULL,
    required_order_id INT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_abilities_element
        FOREIGN KEY (element_type_id) REFERENCES element_types(id)
        ON DELETE SET NULL,

    CONSTRAINT fk_abilities_required_race
        FOREIGN KEY (required_race_id) REFERENCES races(id)
        ON DELETE SET NULL,

    CONSTRAINT fk_abilities_required_order
        FOREIGN KEY (required_order_id) REFERENCES orders(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



/* ================================================================
   15. CHARACTER_ABILITIES
================================================================ */
CREATE TABLE character_abilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    character_id INT NOT NULL,
    ability_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE (character_id, ability_id),

    CONSTRAINT fk_character_abilities_character
        FOREIGN KEY (character_id) REFERENCES characters(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_character_abilities_ability
        FOREIGN KEY (ability_id) REFERENCES abilities(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



/* ================================================================
   16. ITEMS
================================================================ */
CREATE TABLE items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    value INT NOT NULL DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



/* ================================================================
   17. ITEM_ELEMENT_TYPES (N:N)
================================================================ */
CREATE TABLE item_element_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    element_type_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE (item_id, element_type_id),

    CONSTRAINT fk_item_element_item
        FOREIGN KEY (item_id) REFERENCES items(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_item_element_type
        FOREIGN KEY (element_type_id) REFERENCES element_types(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



/* ================================================================
   18. ITEM_ABILITIES
================================================================ */
CREATE TABLE item_abilities (
    id INT AUTO_INCREMENT PRIMARY KEY,

    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,

    dice_formula VARCHAR(50) NULL,

    base_damage INT NOT NULL DEFAULT 0,
    bonus_damage INT NOT NULL DEFAULT 0,
    bonus_accuracy INT NOT NULL DEFAULT 0,
    bonus_speed INT NOT NULL DEFAULT 0,

    is_consumable BOOLEAN NOT NULL DEFAULT 0,
    max_uses INT NULL,

    override_element_type_id INT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_item_abilities_override_element
        FOREIGN KEY (override_element_type_id) REFERENCES element_types(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/* ================================================================
   19. ITEM_ITEM_ABILITIES
================================================================ */
CREATE TABLE item_item_abilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    item_ability_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE (item_id, item_ability_id),

    CONSTRAINT fk_iia_item
        FOREIGN KEY (item_id) REFERENCES items(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_iia_ability
        FOREIGN KEY (item_ability_id) REFERENCES item_abilities(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* ================================================================
   20. WEAPON_DAMAGE_TYPES
================================================================ */
CREATE TABLE weapon_damage_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,

    primary_attribute VARCHAR(50) NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* ================================================================
   21. WEAPONS
================================================================ */
CREATE TABLE weapons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,

    weapon_damage_type_id INT NOT NULL,

    dice_formula VARCHAR(50) NOT NULL,
    base_damage INT NOT NULL DEFAULT 0,
    bonus_accuracy INT NOT NULL DEFAULT 0,
    bonus_speed INT NOT NULL DEFAULT 0,

    ammo_item_id INT NULL,
    ammo_per_use INT NOT NULL DEFAULT 1,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_weapons_item
        FOREIGN KEY (item_id) REFERENCES items(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_weapons_damage_type
        FOREIGN KEY (weapon_damage_type_id) REFERENCES weapon_damage_types(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_weapons_ammo_item
        FOREIGN KEY (ammo_item_id) REFERENCES items(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* ================================================================
   22. WEAPON_ELEMENT_TYPES
================================================================ */
CREATE TABLE weapon_element_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    weapon_id INT NOT NULL,
    element_type_id INT NOT NULL,

    UNIQUE (weapon_id, element_type_id),

    CONSTRAINT fk_weapon_element_weapon
        FOREIGN KEY (weapon_id) REFERENCES weapons(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_weapon_element_type
        FOREIGN KEY (element_type_id) REFERENCES element_types(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* ================================================================
   23. WEAPON_ABILITIES
================================================================ */
CREATE TABLE weapon_abilities (
    id INT AUTO_INCREMENT PRIMARY KEY,

    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,

    dice_formula VARCHAR(50) NULL,

    base_damage INT NOT NULL DEFAULT 0,
    bonus_damage INT NOT NULL DEFAULT 0,
    bonus_accuracy INT NOT NULL DEFAULT 0,
    bonus_speed INT NOT NULL DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* ================================================================
   24. WEAPON_ABILITY_ELEMENT_TYPES
================================================================ */
CREATE TABLE weapon_ability_element_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    weapon_ability_id INT NOT NULL,
    element_type_id INT NOT NULL,

    UNIQUE (weapon_ability_id, element_type_id),

    CONSTRAINT fk_waet_ability
        FOREIGN KEY (weapon_ability_id) REFERENCES weapon_abilities(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_waet_element
        FOREIGN KEY (element_type_id) REFERENCES element_types(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/* ================================================================
   20. ARMOR_SLOTS
================================================================ */
CREATE TABLE armor_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    is_exclusive BOOLEAN NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* ================================================================
   21. ARMORS
================================================================ */

CREATE TABLE armors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,

    armor_slot_id INT NOT NULL,

    armor_class_bonus INT NOT NULL DEFAULT 0,

    min_strength_required INT NOT NULL DEFAULT 0,
    speed_penalty INT NOT NULL DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_armors_item
        FOREIGN KEY (item_id) REFERENCES items(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_armors_slot
        FOREIGN KEY (armor_slot_id) REFERENCES armor_slots(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* ================================================================
   22. ARMOR_ELEMENT_TYPES
================================================================ */
CREATE TABLE armor_element_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    armor_id INT NOT NULL,
    element_type_id INT NOT NULL,

    UNIQUE (armor_id, element_type_id),

    CONSTRAINT fk_armor_element_armor
        FOREIGN KEY (armor_id) REFERENCES armors(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_armor_element_type
        FOREIGN KEY (element_type_id) REFERENCES element_types(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* ================================================================
   23. ARMOR_ABILITIES
================================================================ */

CREATE TABLE armor_abilities (
    id INT AUTO_INCREMENT PRIMARY KEY,

    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,

    dice_formula VARCHAR(50) NULL,
    base_damage INT NOT NULL DEFAULT 0,

    armor_class_bonus INT NOT NULL DEFAULT 0,
    bonus_speed INT NOT NULL DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* ================================================================
   24. ARMOR_ARMOR_ABILITIES
================================================================ */

CREATE TABLE armor_armor_abilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    armor_id INT NOT NULL,
    armor_ability_id INT NOT NULL,

    UNIQUE (armor_id, armor_ability_id),

    CONSTRAINT fk_aaa_armor
        FOREIGN KEY (armor_id) REFERENCES armors(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_aaa_ability
        FOREIGN KEY (armor_ability_id) REFERENCES armor_abilities(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* ================================================================
   25. MONSTERS
================================================================ */
CREATE TABLE monsters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,

    base_hp INT NOT NULL DEFAULT 1,
    base_ac INT NOT NULL DEFAULT 10,
    base_speed INT NOT NULL DEFAULT 6,

    actions_per_turn INT NOT NULL DEFAULT 3,

    base_str INT NOT NULL DEFAULT 1,
    base_dex INT NOT NULL DEFAULT 1,
    base_con INT NOT NULL DEFAULT 1,
    base_wis INT NOT NULL DEFAULT 1,
    base_int INT NOT NULL DEFAULT 1,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/* ================================================================
   26. MONSTER_ELEMENT_TYPES (N:N)
================================================================ */
CREATE TABLE monster_element_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    monster_id INT NOT NULL,
    element_type_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE (monster_id, element_type_id),

    CONSTRAINT fk_monster_element_monster
        FOREIGN KEY (monster_id) REFERENCES monsters(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_monster_element_type
        FOREIGN KEY (element_type_id) REFERENCES element_types(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/* ================================================================
   27. MONSTER_ATTACKS (ataques básicos reutilizáveis)
================================================================ */
CREATE TABLE monster_attacks (
    id INT AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(150) NOT NULL,
    description TEXT,

    dice_formula VARCHAR(50) NOT NULL,
    base_damage INT NOT NULL DEFAULT 0,
    bonus_accuracy INT NOT NULL DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/* ================================================================
   28. MONSTER_ATTACK_ELEMENT_TYPES (N:N)
================================================================ */
CREATE TABLE monster_attack_element_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    monster_attack_id INT NOT NULL,
    element_type_id INT NOT NULL,

    UNIQUE (monster_attack_id, element_type_id),

    CONSTRAINT fk_maet_attack
        FOREIGN KEY (monster_attack_id) REFERENCES monster_attacks(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_maet_element
        FOREIGN KEY (element_type_id) REFERENCES element_types(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/* ================================================================
   29. MONSTER_ATTACK_LINKS (N:N)
================================================================ */
CREATE TABLE monster_attack_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    monster_id INT NOT NULL,
    monster_attack_id INT NOT NULL,

    UNIQUE (monster_id, monster_attack_id),

    CONSTRAINT fk_mal_monster
        FOREIGN KEY (monster_id) REFERENCES monsters(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_mal_attack
        FOREIGN KEY (monster_attack_id) REFERENCES monster_attacks(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/* ================================================================
   30. MONSTER_ABILITIES (habilidades especiais)
================================================================ */
CREATE TABLE monster_abilities (
    id INT AUTO_INCREMENT PRIMARY KEY,

    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,

    dice_formula VARCHAR(50) NULL,
    base_damage INT NOT NULL DEFAULT 0,
    bonus_damage INT NOT NULL DEFAULT 0,
    bonus_speed INT NOT NULL DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/* ================================================================
   31. MONSTER_ABILITY_ELEMENT_TYPES (N:N)
================================================================ */
CREATE TABLE monster_ability_element_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    monster_ability_id INT NOT NULL,
    element_type_id INT NOT NULL,

    UNIQUE (monster_ability_id, element_type_id),

    CONSTRAINT fk_maet_ability
        FOREIGN KEY (monster_ability_id) REFERENCES monster_abilities(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_maet_element
        FOREIGN KEY (element_type_id) REFERENCES element_types(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/* ================================================================
   32. MONSTER_ABILITY_LINKS (N:N)
================================================================ */
CREATE TABLE monster_ability_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    monster_id INT NOT NULL,
    monster_ability_id INT NOT NULL,

    UNIQUE (monster_id, monster_ability_id),

    CONSTRAINT fk_monster_ability_link_monster
        FOREIGN KEY (monster_id) REFERENCES monsters(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_monster_ability_link_ability
        FOREIGN KEY (monster_ability_id) REFERENCES monster_abilities(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;




/* ================================================================
   33. CAMPAIGNS
================================================================ */
CREATE TABLE campaigns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,

    created_by INT NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_campaigns_created_by
        FOREIGN KEY (created_by) REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



/* ================================================================
   34. CAMPAIGN_CHARACTERS
================================================================ */
CREATE TABLE campaign_characters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campaign_id INT NOT NULL,
    user_id INT NOT NULL,
    character_id INT NOT NULL,
    level INT NOT NULL DEFAULT 1,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_camp_chars_campaign
        FOREIGN KEY (campaign_id) REFERENCES campaigns(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_camp_chars_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_camp_chars_character
        FOREIGN KEY (character_id) REFERENCES characters(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



/* ================================================================
   35. CAMPAIGN_CHARACTER_ABILITIES
================================================================ */
CREATE TABLE campaign_character_abilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campaign_character_id INT NOT NULL,
    ability_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE(campaign_character_id, ability_id),

    CONSTRAINT fk_cc_abilities_cc
        FOREIGN KEY (campaign_character_id) REFERENCES campaign_characters(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_cc_abilities_ability
        FOREIGN KEY (ability_id) REFERENCES abilities(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



/* ================================================================
   36. CAMPAIGN_CHARACTER_ITEMS
================================================================ */
CREATE TABLE campaign_character_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campaign_character_id INT NOT NULL,
    item_id INT NOT NULL,

    quantity INT NOT NULL DEFAULT 1,
    is_active BOOLEAN NOT NULL DEFAULT 1,
    is_equipped BOOLEAN NOT NULL DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_cc_items_cc
        FOREIGN KEY (campaign_character_id) REFERENCES campaign_characters(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_cc_items_item
        FOREIGN KEY (item_id) REFERENCES items(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* ================================================================
   37. CAMPAIGN_CHARACTER_WEAPON
================================================================ */
CREATE TABLE campaign_character_weapons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campaign_character_id INT NOT NULL,
    weapon_id INT NOT NULL,

    is_equipped BOOLEAN NOT NULL DEFAULT 0,
    is_active BOOLEAN NOT NULL DEFAULT 1,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_cc_weapons_cc
        FOREIGN KEY (campaign_character_id) REFERENCES campaign_characters(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_cc_weapons_weapon
        FOREIGN KEY (weapon_id) REFERENCES weapons(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/* ================================================================
   38. CAMPAIGN_CHARACTER_ARMORS
================================================================ */

CREATE TABLE campaign_character_armors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campaign_character_id INT NOT NULL,
    armor_id INT NOT NULL,

    is_equipped BOOLEAN NOT NULL DEFAULT 0,
    is_active BOOLEAN NOT NULL DEFAULT 1,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_cc_armors_cc
        FOREIGN KEY (campaign_character_id) REFERENCES campaign_characters(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_cc_armors_armor
        FOREIGN KEY (armor_id) REFERENCES armors(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* ================================================================
   39. ENCOUNTERS
================================================================ */
CREATE TABLE encounters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campaign_id INT NOT NULL,

    name VARCHAR(150),
    description TEXT,

    status ENUM('pending','active','finished') NOT NULL DEFAULT 'pending',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_encounters_campaign
        FOREIGN KEY (campaign_id) REFERENCES campaigns(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



/* ================================================================
   40. ENCOUNTER_PLAYERS
================================================================ */
CREATE TABLE encounter_players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    encounter_id INT NOT NULL,
    campaign_character_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE(encounter_id, campaign_character_id),

    CONSTRAINT fk_enc_player_enc
        FOREIGN KEY (encounter_id) REFERENCES encounters(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_enc_player_cc
        FOREIGN KEY (campaign_character_id) REFERENCES campaign_characters(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



/* ================================================================
   41. ENCOUNTER_MONSTERS
================================================================ */
CREATE TABLE encounter_monsters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    encounter_id INT NOT NULL,
    monster_id INT NOT NULL,

    monster_level INT NOT NULL DEFAULT 1,
    current_hp INT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_enc_mon_enc
        FOREIGN KEY (encounter_id) REFERENCES encounters(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_enc_mon_mon
        FOREIGN KEY (monster_id) REFERENCES monsters(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* ================================================================
   42. PERKS
================================================================ */

CREATE TABLE perks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,

    type ENUM('passive', 'active') NOT NULL,

    mana_cost INT NOT NULL DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* ================================================================
   43. PERKS_ATTRIBUTES
================================================================ */

CREATE TABLE perk_attributes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    perk_id INT NOT NULL,

    attribute_name VARCHAR(100) NOT NULL,
    attribute_value INT NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_perk_attributes_perk
        FOREIGN KEY (perk_id) REFERENCES perks(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* ================================================================
   44. PERKS_FLAGS
================================================================ */

CREATE TABLE perk_flags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    perk_id INT NOT NULL,

    flag_name VARCHAR(100) NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_perk_flags_perk
        FOREIGN KEY (perk_id) REFERENCES perks(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* ================================================================
   45. ORDER_PERKS
================================================================ */

CREATE TABLE order_perks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    perk_id INT NOT NULL,

    required_level INT NOT NULL DEFAULT 1,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_order_perks_order
        FOREIGN KEY (order_id) REFERENCES orders(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_order_perks_perk
        FOREIGN KEY (perk_id) REFERENCES perks(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* ================================================================
   46. RACE_PERKS
================================================================ */

CREATE TABLE race_perks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    race_id INT NOT NULL,
    perk_id INT NOT NULL,

    required_level INT NOT NULL DEFAULT 1,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_race_perks_race
        FOREIGN KEY (race_id) REFERENCES races(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_race_perks_perk
        FOREIGN KEY (perk_id) REFERENCES perks(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* ================================================================
   47. RACE_PERKS
================================================================ */

CREATE TABLE perk_element_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    perk_id INT NOT NULL,
    element_type_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE (perk_id, element_type_id),

    CONSTRAINT fk_perk_element_perk
        FOREIGN KEY (perk_id) REFERENCES perks(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_perk_element_type
        FOREIGN KEY (element_type_id) REFERENCES element_types(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* ================================================================
   48. PERK_ABILITIES
================================================================ */

CREATE TABLE perk_abilities (
    id INT AUTO_INCREMENT PRIMARY KEY,

    perk_id INT NOT NULL,

    name VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,

    dice_formula VARCHAR(50) NULL,
    base_damage INT NOT NULL DEFAULT 0,

    bonus_accuracy INT NOT NULL DEFAULT 0,
    bonus_damage INT NOT NULL DEFAULT 0,
    bonus_speed INT NOT NULL DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_perk_abilities_perk
        FOREIGN KEY (perk_id) REFERENCES perks(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
