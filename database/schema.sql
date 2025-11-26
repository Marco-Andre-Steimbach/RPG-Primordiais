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

    UNIQUE(role_id, permission_id),

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

    UNIQUE(user_id, role_id),

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

    required_race_id INT NULL, -- ordens restritas a raças específicas

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
   12. WEAPON_DAMAGE_TYPES
================================================================ */
CREATE TABLE weapon_damage_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    primary_attribute_name VARCHAR(100) NOT NULL,
    secondary_attribute_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



/* ================================================================
   13. ELEMENT_TYPES (UNIFICADO: elemental, criatura, categoria)
================================================================ */
CREATE TABLE element_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,

    bonus_accuracy INT NOT NULL DEFAULT 0,
    bonus_damage INT NOT NULL DEFAULT 0,
    bonus_speed INT NOT NULL DEFAULT 0,

    weakness_element_id INT NULL,
    immunity_element_id INT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_element_weakness
        FOREIGN KEY (weakness_element_id) REFERENCES element_types(id)
        ON DELETE SET NULL,

    CONSTRAINT fk_element_immunity
        FOREIGN KEY (immunity_element_id) REFERENCES element_types(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



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

    is_weapon TINYINT(1) NOT NULL DEFAULT 1,
    is_magic  TINYINT(1) NOT NULL DEFAULT 0,

    base_damage INT NOT NULL DEFAULT 0,
    base_accuracy INT NOT NULL DEFAULT 0,
    bonus_speed INT NOT NULL DEFAULT 0,

    weapon_damage_type_id INT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_items_weapon_type
        FOREIGN KEY (weapon_damage_type_id) REFERENCES weapon_damage_types(id)
        ON DELETE SET NULL
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
   18. ITEM_MAGIC_ABILITIES
================================================================ */
CREATE TABLE item_magic_abilities (
    id INT AUTO_INCREMENT PRIMARY KEY,

    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,

    dice_formula VARCHAR(50) NULL,
    base_damage INT NOT NULL DEFAULT 0,

    daily_uses_limit INT NOT NULL DEFAULT 1,
    bonus_accuracy INT NOT NULL DEFAULT 0,
    bonus_damage INT NOT NULL DEFAULT 0,
    bonus_speed INT NOT NULL DEFAULT 0,

    element_type_id INT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_item_magic_ability_element
        FOREIGN KEY (element_type_id) REFERENCES element_types(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



/* ================================================================
   19. ITEM_MAGIC_ABILITY_LINKS
================================================================ */
CREATE TABLE item_magic_ability_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    item_magic_ability_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE (item_id, item_magic_ability_id),

    CONSTRAINT fk_item_magic_link_item
        FOREIGN KEY (item_id) REFERENCES items(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_item_magic_link_ability
        FOREIGN KEY (item_magic_ability_id) REFERENCES item_magic_abilities(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



/* ================================================================
   20. MONSTERS
================================================================ */
CREATE TABLE monsters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,

    base_hp INT NOT NULL DEFAULT 1,
    base_ac INT NOT NULL DEFAULT 10,
    base_speed INT NOT NULL DEFAULT 6,

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
   21. MONSTER_ELEMENT_TYPES
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
   22. MONSTER_ABILITIES
================================================================ */
CREATE TABLE monster_abilities (
    id INT AUTO_INCREMENT PRIMARY KEY,

    title VARCHAR(150) NOT NULL,
    description TEXT,

    dice_formula VARCHAR(50) NULL,
    base_damage INT NOT NULL DEFAULT 0,
    bonus_speed INT NOT NULL DEFAULT 0,

    physical_type_id INT NULL,
    element_type_id INT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_monster_abilities_physical
        FOREIGN KEY (physical_type_id) REFERENCES weapon_damage_types(id)
        ON DELETE SET NULL,

    CONSTRAINT fk_monster_abilities_element
        FOREIGN KEY (element_type_id) REFERENCES element_types(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



/* ================================================================
   23. MONSTER_ABILITY_LINKS
================================================================ */
CREATE TABLE monster_ability_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    monster_id INT NOT NULL,
    monster_ability_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE (monster_id, monster_ability_id),

    CONSTRAINT fk_monster_ability_link_monster
        FOREIGN KEY (monster_id) REFERENCES monsters(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_monster_ability_link_ability
        FOREIGN KEY (monster_ability_id) REFERENCES monster_abilities(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



/* ================================================================
   24. CAMPAIGNS
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
   25. CAMPAIGN_CHARACTERS
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
   26. CAMPAIGN_CHARACTER_ABILITIES
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
   27. CAMPAIGN_CHARACTER_ITEMS
================================================================ */
CREATE TABLE campaign_character_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campaign_character_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_cc_items_cc
        FOREIGN KEY (campaign_character_id) REFERENCES campaign_characters(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_cc_items_item
        FOREIGN KEY (item_id) REFERENCES items(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



/* ================================================================
   28. ENCOUNTERS
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
   29. ENCOUNTER_PLAYERS
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
   30. ENCOUNTER_MONSTERS
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
   31. ENCOUNTER_MONSTER_ABILITIES
================================================================ */
CREATE TABLE encounter_monster_abilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    encounter_monster_id INT NOT NULL,
    monster_ability_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE (encounter_monster_id, monster_ability_id),

    CONSTRAINT fk_enc_m_abilities_em
        FOREIGN KEY (encounter_monster_id) REFERENCES encounter_monsters(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_enc_m_abilities_ma
        FOREIGN KEY (monster_ability_id) REFERENCES monster_abilities(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
