<?php

use yii\db\Migration;

/**
 * Handles the creation of the initial database schema.
 */
class m251120_235243_create_initial_schema extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Desativar verificação de chaves estrangeiras para criar tabelas em qualquer ordem
        $this->execute("SET FOREIGN_KEY_CHECKS = 0;");

        // 1. Tabela USER (Padrão Yii2)
        $this->execute("
            CREATE TABLE user (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255) NOT NULL UNIQUE,
                auth_key VARCHAR(32) NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                password_reset_token VARCHAR(255) UNIQUE,
                email VARCHAR(255) NOT NULL UNIQUE,
                status SMALLINT NOT NULL DEFAULT 10,
                created_at INT NOT NULL,
                updated_at INT NOT NULL,
                verification_token VARCHAR(255) DEFAULT NULL
            ) ENGINE=InnoDB;
        ");

        // 2. Tabela USER_PROFILE
        $this->execute("
            CREATE TABLE user_profile (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                nif VARCHAR(9) DEFAULT NULL,
                phone VARCHAR(20) DEFAULT NULL,
                role ENUM('ADM', 'ORG', 'PART', 'EVAL') NOT NULL DEFAULT 'PART',
                FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
            ) ENGINE=InnoDB;
        ");

        // 3. Tabela EVENT
        $this->execute("
            CREATE TABLE event (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                description TEXT,
                start_date DATE NOT NULL,
                end_date DATE NOT NULL,
                submission_deadline DATE DEFAULT NULL,
                evaluation_deadline DATE DEFAULT NULL,
                status ENUM('open', 'closed', 'running', 'finished') NOT NULL DEFAULT 'open'
            ) ENGINE=InnoDB;
        ");

        // 4. Tabela TICKET_TYPE
        $this->execute("
            CREATE TABLE ticket_type (
                id INT AUTO_INCREMENT PRIMARY KEY,
                event_id INT NOT NULL,
                name VARCHAR(100) NOT NULL,
                price DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
                FOREIGN KEY (event_id) REFERENCES event(id) ON DELETE CASCADE
            ) ENGINE=InnoDB;
        ");

        // 5. Tabela REGISTRATION
        $this->execute("
            CREATE TABLE registration (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                event_id INT NOT NULL,
                ticket_type_id INT NOT NULL,
                registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                payment_status ENUM('pending', 'paid', 'cancelled') NOT NULL DEFAULT 'pending',
                FOREIGN KEY (user_id) REFERENCES user(id),
                FOREIGN KEY (event_id) REFERENCES event(id),
                FOREIGN KEY (ticket_type_id) REFERENCES ticket_type(id),
                UNIQUE KEY unique_registration (user_id, event_id)
            ) ENGINE=InnoDB;
        ");

        // 6. Tabela ARTICLE
        $this->execute("
            CREATE TABLE article (
                id INT AUTO_INCREMENT PRIMARY KEY,
                registration_id INT NOT NULL,
                title VARCHAR(255) NOT NULL,
                abstract TEXT,
                file_path VARCHAR(512) NOT NULL,
                status ENUM('submitted', 'in_review', 'accepted', 'rejected') NOT NULL DEFAULT 'submitted',
                FOREIGN KEY (registration_id) REFERENCES registration(id) ON DELETE CASCADE
            ) ENGINE=InnoDB;
        ");

        // 7. Tabela EVALUATION
        $this->execute("
            CREATE TABLE evaluation (
                id INT AUTO_INCREMENT PRIMARY KEY,
                article_id INT NOT NULL,
                evaluator_id INT NOT NULL,
                score DECIMAL(3, 2) DEFAULT NULL,
                comments TEXT,
                evaluation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (article_id) REFERENCES article(id) ON DELETE CASCADE,
                FOREIGN KEY (evaluator_id) REFERENCES user(id),
                UNIQUE KEY unique_evaluation (article_id, evaluator_id)
            ) ENGINE=InnoDB;
        ");

        // 8. Tabela VENUE
        $this->execute("
            CREATE TABLE venue (
                id INT AUTO_INCREMENT PRIMARY KEY,
                event_id INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                capacity INT DEFAULT NULL,
                FOREIGN KEY (event_id) REFERENCES event(id) ON DELETE CASCADE
            ) ENGINE=InnoDB;
        ");

        // 9. Tabela SESSION
        $this->execute("
            CREATE TABLE session (
                id INT AUTO_INCREMENT PRIMARY KEY,
                event_id INT NOT NULL,
                venue_id INT DEFAULT NULL,
                title VARCHAR(255) NOT NULL,
                start_time TIME DEFAULT NULL,
                end_time TIME DEFAULT NULL,
                FOREIGN KEY (event_id) REFERENCES event(id) ON DELETE CASCADE,
                FOREIGN KEY (venue_id) REFERENCES venue(id) ON DELETE SET NULL
            ) ENGINE=InnoDB;
        ");

        // 10. Tabela ORGANIZER_EVENT
        $this->execute("
            CREATE TABLE organizer_event (
                user_id INT NOT NULL,
                event_id INT NOT NULL,
                role_description VARCHAR(100) DEFAULT NULL,
                PRIMARY KEY (user_id, event_id),
                FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
                FOREIGN KEY (event_id) REFERENCES event(id) ON DELETE CASCADE
            ) ENGINE=InnoDB;
        ");

        // 11. Tabela USER_SESSION_FAVORITE
        $this->execute("
            CREATE TABLE user_session_favorite (
                user_id INT NOT NULL,
                session_id INT NOT NULL,
                PRIMARY KEY (user_id, session_id),
                FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
                FOREIGN KEY (session_id) REFERENCES session(id) ON DELETE CASCADE
            ) ENGINE=InnoDB;
        ");

        // 12. Tabela SESSION_FEEDBACK
        $this->execute("
            CREATE TABLE session_feedback (
                id INT AUTO_INCREMENT PRIMARY KEY,
                session_id INT NOT NULL,
                user_id INT NOT NULL,
                rating INT NOT NULL,
                comment TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (session_id) REFERENCES session(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES user(id),
                UNIQUE KEY unique_feedback (session_id, user_id)
            ) ENGINE=InnoDB;
        ");

        // 13. Tabela SESSION_QUESTION
        $this->execute("
            CREATE TABLE session_question (
                id INT AUTO_INCREMENT PRIMARY KEY,
                session_id INT NOT NULL,
                user_id INT NOT NULL,
                question_text TEXT NOT NULL,
                status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (session_id) REFERENCES session(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES user(id)
            ) ENGINE=InnoDB;
        ");

        // Reativar verificação
        $this->execute("SET FOREIGN_KEY_CHECKS = 1;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("SET FOREIGN_KEY_CHECKS = 0;");

        // Apagar na ordem inversa ou simplesmente dropar tudo
        $this->dropTable('session_question');
        $this->dropTable('session_feedback');
        $this->dropTable('user_session_favorite');
        $this->dropTable('organizer_event');
        $this->dropTable('session');
        $this->dropTable('venue');
        $this->dropTable('evaluation');
        $this->dropTable('article');
        $this->dropTable('registration');
        $this->dropTable('ticket_type');
        $this->dropTable('event');
        $this->dropTable('user_profile');
        $this->dropTable('user');

        $this->execute("SET FOREIGN_KEY_CHECKS = 1;");
    }
}