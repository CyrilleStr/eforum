<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210305184755 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post CHANGE category_id category_id INT NOT NULL, CHANGE type_id type_id INT NOT NULL');
        $this->addSql('CREATE FULLTEXT INDEX IDX_5A8A6C8D2B36786B6DE44026 ON post (title, description)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_5A8A6C8D2B36786B6DE44026 ON post');
        $this->addSql('ALTER TABLE post CHANGE category_id category_id INT DEFAULT NULL, CHANGE type_id type_id INT DEFAULT NULL');
    }
}
