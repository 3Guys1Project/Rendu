<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240928164624 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment ALTER sender TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE comment ALTER recipient TYPE VARCHAR(255)');
//        $this->addSql('ALTER TABLE "user" ADD code VARCHAR(20) NOT NULL');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_CODE ON "user" (code)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_CODE');
        $this->addSql('ALTER TABLE "user" DROP code');
        $this->addSql('ALTER TABLE "comment" ALTER sender TYPE INT');
        $this->addSql('ALTER TABLE "comment" ALTER recipient TYPE INT');
    }
}
