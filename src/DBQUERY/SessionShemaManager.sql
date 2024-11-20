-- Supprime le schéma si existant
DROP SCHEMA IF EXISTS session_manager;

-- Crée un nouveau schéma

CREATE SCHEMA session_manager;

-- Crée une table pour gérer les sessions
CREATE TABLE IF NOT EXISTS session_manager.sessions (
    session_id VARCHAR(255) NOT NULL, -- Identifiant unique de session
    user_id INT NOT NULL,            -- ID utilisateur lié à la session
    user_role ENUM('client','repairman') NOT NULL, -- Rôle de l'utilisateur
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Horodatage de création
    expired_at TIMESTAMP  NOT NULL -- Horodatage d'expiration
);