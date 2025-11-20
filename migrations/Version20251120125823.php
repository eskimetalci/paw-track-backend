<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251120125823 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE animal (id UUID NOT NULL, name VARCHAR(255) NOT NULL, species VARCHAR(255) NOT NULL, breed VARCHAR(255) DEFAULT NULL, dob DATE NOT NULL, weight DOUBLE PRECISION DEFAULT NULL, avatar VARCHAR(500) DEFAULT NULL, owner_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_6AAB231F7E3C61F9 ON animal (owner_id)');
        $this->addSql('CREATE TABLE blog_post (id UUID NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content TEXT NOT NULL, tags JSON NOT NULL, target_species VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, published BOOLEAN NOT NULL, published_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, featured_image VARCHAR(500) DEFAULT NULL, excerpt TEXT DEFAULT NULL, author_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BA5AE01D989D9B62 ON blog_post (slug)');
        $this->addSql('CREATE INDEX IDX_BA5AE01DF675F31B ON blog_post (author_id)');
        $this->addSql('CREATE TABLE medicine_log (id UUID NOT NULL, medicine_name VARCHAR(255) NOT NULL, dosage VARCHAR(100) NOT NULL, frequency VARCHAR(100) NOT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, notes TEXT DEFAULT NULL, prescribed_by VARCHAR(255) DEFAULT NULL, animal_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_411945C8E962C16 ON medicine_log (animal_id)');
        $this->addSql('CREATE TABLE poo_log (id UUID NOT NULL, recorded_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, bristol_score SMALLINT NOT NULL, color VARCHAR(255) NOT NULL, contents JSON NOT NULL, photo_url VARCHAR(500) DEFAULT NULL, notes TEXT DEFAULT NULL, animal_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_70D4FC658E962C16 ON poo_log (animal_id)');
        $this->addSql('CREATE TABLE "user" (id UUID NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE TABLE vaccine_log (id UUID NOT NULL, vaccine_name VARCHAR(255) NOT NULL, batch_number VARCHAR(255) DEFAULT NULL, administered_at DATE NOT NULL, next_due_date DATE DEFAULT NULL, clinic_name VARCHAR(255) NOT NULL, veterinarian_name VARCHAR(255) DEFAULT NULL, notes TEXT DEFAULT NULL, animal_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_E6AD6D298E962C16 ON vaccine_log (animal_id)');
        $this->addSql('ALTER TABLE animal ADD CONSTRAINT FK_6AAB231F7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE blog_post ADD CONSTRAINT FK_BA5AE01DF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE medicine_log ADD CONSTRAINT FK_411945C8E962C16 FOREIGN KEY (animal_id) REFERENCES animal (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE poo_log ADD CONSTRAINT FK_70D4FC658E962C16 FOREIGN KEY (animal_id) REFERENCES animal (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE vaccine_log ADD CONSTRAINT FK_E6AD6D298E962C16 FOREIGN KEY (animal_id) REFERENCES animal (id) NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animal DROP CONSTRAINT FK_6AAB231F7E3C61F9');
        $this->addSql('ALTER TABLE blog_post DROP CONSTRAINT FK_BA5AE01DF675F31B');
        $this->addSql('ALTER TABLE medicine_log DROP CONSTRAINT FK_411945C8E962C16');
        $this->addSql('ALTER TABLE poo_log DROP CONSTRAINT FK_70D4FC658E962C16');
        $this->addSql('ALTER TABLE vaccine_log DROP CONSTRAINT FK_E6AD6D298E962C16');
        $this->addSql('DROP TABLE animal');
        $this->addSql('DROP TABLE blog_post');
        $this->addSql('DROP TABLE medicine_log');
        $this->addSql('DROP TABLE poo_log');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE vaccine_log');
    }
}
