<?php

declare(strict_types=1);

/*
 * This file is part of the vseth-musikzimmer-pay project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200226165012 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE payment_remainder (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name CLOB NOT NULL, subject CLOB NOT NULL, body CLOB NOT NULL, fee INTEGER NOT NULL, due_at DATETIME NOT NULL, sent_to_all BOOLEAN NOT NULL, created_at DATETIME NOT NULL, last_changed_at DATETIME NOT NULL)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, payment_remainder_id INTEGER DEFAULT NULL, authentication_code CLOB NOT NULL, email CLOB NOT NULL, given_name CLOB NOT NULL, family_name CLOB NOT NULL, address CLOB NOT NULL, phone CLOB NOT NULL, category INTEGER NOT NULL, discount INTEGER NOT NULL, discount_description CLOB DEFAULT NULL, last_payed_periodic_fee_end DATETIME DEFAULT NULL, amount_owed INTEGER NOT NULL, amount_payed INTEGER DEFAULT NULL, transaction_id CLOB DEFAULT NULL, invoice_id INTEGER DEFAULT NULL, invoice_link CLOB DEFAULT NULL, payment_remainder_status INTEGER NOT NULL, payment_remainder_status_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_8D93D649157A032F ON user (payment_remainder_id)');
        $this->addSql('CREATE TABLE setting (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, period_start DATETIME NOT NULL, period_end DATETIME NOT NULL, payment_prefix CLOB NOT NULL)');
        $this->addSql('CREATE TABLE reservation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, room INTEGER NOT NULL, start DATETIME NOT NULL, "end" DATETIME NOT NULL)');
        $this->addSql('CREATE INDEX IDX_42C84955A76ED395 ON reservation (user_id)');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE organisation');
        $this->addSql('DROP TABLE semester_report');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE event (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, semester INTEGER NOT NULL, name_de CLOB DEFAULT NULL COLLATE BINARY, name_en CLOB DEFAULT NULL COLLATE BINARY, description_de CLOB DEFAULT NULL COLLATE BINARY, description_en CLOB DEFAULT NULL COLLATE BINARY, location CLOB NOT NULL COLLATE BINARY, start_date DATETIME DEFAULT NULL, end_date DATETIME DEFAULT NULL, need_financial_support BOOLEAN NOT NULL, organisation_id INTEGER DEFAULT NULL, show_in_calender BOOLEAN DEFAULT \'1\' NOT NULL, revenue INTEGER NOT NULL, expenditure INTEGER NOT NULL)');
        $this->addSql('CREATE TABLE organisation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name CLOB NOT NULL COLLATE BINARY, email CLOB NOT NULL COLLATE BINARY, relation_since_semester INTEGER NOT NULL, authentication_code VARCHAR(255) NOT NULL COLLATE BINARY)');
        $this->addSql('CREATE TABLE semester_report (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, semester INTEGER NOT NULL, submitted_date_time DATETIME NOT NULL, political_events_description CLOB DEFAULT NULL COLLATE BINARY, comments CLOB DEFAULT NULL COLLATE BINARY, organisation_id INTEGER DEFAULT NULL)');
        $this->addSql('DROP TABLE payment_remainder');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE setting');
        $this->addSql('DROP TABLE reservation');
    }
}
