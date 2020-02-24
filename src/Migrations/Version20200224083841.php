<?php

declare(strict_types=1);

/*
 * This file is part of the vseth-semesterly-reports project.
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
final class Version20200224083841 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE setting (id INT AUTO_INCREMENT NOT NULL, period_start DATETIME NOT NULL, period_end DATETIME NOT NULL, payment_prefix LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_remainder (id INT AUTO_INCREMENT NOT NULL, name LONGTEXT NOT NULL, subject LONGTEXT NOT NULL, body LONGTEXT NOT NULL, fee INT NOT NULL, due_at DATETIME NOT NULL, sent_to_all TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, last_changed_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, payment_remainder_id INT DEFAULT NULL, authentication_code LONGTEXT NOT NULL, email LONGTEXT NOT NULL, given_name LONGTEXT NOT NULL, family_name LONGTEXT NOT NULL, address LONGTEXT NOT NULL, phone LONGTEXT NOT NULL, category INT NOT NULL, discount INT NOT NULL, discount_description LONGTEXT DEFAULT NULL, last_payed_periodic_fee_end DATETIME DEFAULT NULL, amount_owed INT NOT NULL, amount_payed INT DEFAULT NULL, transaction_id LONGTEXT DEFAULT NULL, invoice_id INT DEFAULT NULL, invoice_link LONGTEXT DEFAULT NULL, payment_remainder_status INT NOT NULL, payment_remainder_status_at DATETIME DEFAULT NULL, INDEX IDX_8D93D649157A032F (payment_remainder_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, room INT NOT NULL, start DATETIME NOT NULL, end DATETIME NOT NULL, INDEX IDX_42C84955A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649157A032F FOREIGN KEY (payment_remainder_id) REFERENCES payment_remainder (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649157A032F');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('DROP TABLE setting');
        $this->addSql('DROP TABLE payment_remainder');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE reservation');
    }
}
