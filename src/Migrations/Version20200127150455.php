<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200127150455 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("INSERT INTO app.award (name, type, amount) VALUES ('Money', 'money', 1000)");
        $this->addSql("INSERT INTO app.award (name, type, amount) VALUES ('Loyalty points', 'loyalty', null)");
        $this->addSql("INSERT INTO app.award (name, type, amount) VALUES ('Code: The Hidden Language of Computer Hardware and Software', 'prize', 10)");
        $this->addSql("INSERT INTO app.award (name, type, amount) VALUES ('The Pragmatic Programmer Andrew Hunt and Dave Thomas', 'prize', 10)");
        $this->addSql("INSERT INTO app.award (name, type, amount) VALUES ('Introduction to Algorithms Thomas H. Cormen', 'prize', 10)");
    }

    public function down(Schema $schema): void
    {
    }
}
