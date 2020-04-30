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
final class Version20200430092008 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_8D93D649157A032F');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, payment_remainder_id, authentication_code, email, given_name, family_name, address, phone, category, discount, discount_description, last_payed_periodic_fee_end, amount_owed, amount_payed, transaction_id, invoice_id, invoice_link, payment_remainder_status, payment_remainder_status_at, marked_as_payed FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, payment_remainder_id INTEGER DEFAULT NULL, authentication_code CLOB NOT NULL COLLATE BINARY, email CLOB NOT NULL COLLATE BINARY, given_name CLOB NOT NULL COLLATE BINARY, family_name CLOB NOT NULL COLLATE BINARY, address CLOB NOT NULL COLLATE BINARY, phone CLOB NOT NULL COLLATE BINARY, category INTEGER NOT NULL, discount INTEGER NOT NULL, discount_description CLOB DEFAULT NULL COLLATE BINARY, last_payed_periodic_fee_end DATETIME DEFAULT NULL, amount_owed INTEGER NOT NULL, amount_payed INTEGER DEFAULT NULL, transaction_id CLOB DEFAULT NULL COLLATE BINARY, invoice_id INTEGER DEFAULT NULL, invoice_link CLOB DEFAULT NULL COLLATE BINARY, payment_remainder_status INTEGER NOT NULL, payment_remainder_status_at DATETIME DEFAULT NULL, marked_as_payed BOOLEAN DEFAULT \'0\' NOT NULL, out_of_opening_times_discount INTEGER DEFAULT 0 NOT NULL, CONSTRAINT FK_8D93D649157A032F FOREIGN KEY (payment_remainder_id) REFERENCES payment_remainder (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO user (id, payment_remainder_id, authentication_code, email, given_name, family_name, address, phone, category, discount, discount_description, last_payed_periodic_fee_end, amount_owed, amount_payed, transaction_id, invoice_id, invoice_link, payment_remainder_status, payment_remainder_status_at, marked_as_payed) SELECT id, payment_remainder_id, authentication_code, email, given_name, family_name, address, phone, category, discount, discount_description, last_payed_periodic_fee_end, amount_owed, amount_payed, transaction_id, invoice_id, invoice_link, payment_remainder_status, payment_remainder_status_at, marked_as_payed FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE INDEX IDX_8D93D649157A032F ON user (payment_remainder_id)');
        $this->addSql('DROP INDEX IDX_42C84955A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__reservation AS SELECT id, user_id, created_at, modified_at, room, start, "end" FROM reservation');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('CREATE TABLE reservation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, room INTEGER NOT NULL, start DATETIME NOT NULL, "end" DATETIME NOT NULL, CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO reservation (id, user_id, created_at, modified_at, room, start, "end") SELECT id, user_id, created_at, modified_at, room, start, "end" FROM __temp__reservation');
        $this->addSql('DROP TABLE __temp__reservation');
        $this->addSql('CREATE INDEX IDX_42C84955A76ED395 ON reservation (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_42C84955A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__reservation AS SELECT id, user_id, created_at, modified_at, room, start, "end" FROM reservation');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('CREATE TABLE reservation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, room INTEGER NOT NULL, start DATETIME NOT NULL, "end" DATETIME NOT NULL)');
        $this->addSql('INSERT INTO reservation (id, user_id, created_at, modified_at, room, start, "end") SELECT id, user_id, created_at, modified_at, room, start, "end" FROM __temp__reservation');
        $this->addSql('DROP TABLE __temp__reservation');
        $this->addSql('CREATE INDEX IDX_42C84955A76ED395 ON reservation (user_id)');
        $this->addSql('DROP INDEX IDX_8D93D649157A032F');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, payment_remainder_id, authentication_code, email, given_name, family_name, address, phone, category, discount, discount_description, last_payed_periodic_fee_end, amount_owed, amount_payed, transaction_id, invoice_id, invoice_link, payment_remainder_status, marked_as_payed, payment_remainder_status_at FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, payment_remainder_id INTEGER DEFAULT NULL, authentication_code CLOB NOT NULL, email CLOB NOT NULL, given_name CLOB NOT NULL, family_name CLOB NOT NULL, address CLOB NOT NULL, phone CLOB NOT NULL, category INTEGER NOT NULL, discount INTEGER NOT NULL, discount_description CLOB DEFAULT NULL, last_payed_periodic_fee_end DATETIME DEFAULT NULL, amount_owed INTEGER NOT NULL, amount_payed INTEGER DEFAULT NULL, transaction_id CLOB DEFAULT NULL, invoice_id INTEGER DEFAULT NULL, invoice_link CLOB DEFAULT NULL, payment_remainder_status INTEGER NOT NULL, marked_as_payed BOOLEAN DEFAULT \'0\' NOT NULL, payment_remainder_status_at DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO user (id, payment_remainder_id, authentication_code, email, given_name, family_name, address, phone, category, discount, discount_description, last_payed_periodic_fee_end, amount_owed, amount_payed, transaction_id, invoice_id, invoice_link, payment_remainder_status, marked_as_payed, payment_remainder_status_at) SELECT id, payment_remainder_id, authentication_code, email, given_name, family_name, address, phone, category, discount, discount_description, last_payed_periodic_fee_end, amount_owed, amount_payed, transaction_id, invoice_id, invoice_link, payment_remainder_status, marked_as_payed, payment_remainder_status_at FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE INDEX IDX_8D93D649157A032F ON user (payment_remainder_id)');
    }
}
