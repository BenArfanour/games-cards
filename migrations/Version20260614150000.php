<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260614150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create refresh_tokens table for JWT refresh tokens';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('refresh_tokens');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('refresh_token', 'string', ['length' => 128]);
        $table->addColumn('username', 'string', ['length' => 255]);
        $table->addColumn('valid', 'datetime');
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['refresh_token']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('refresh_tokens');
    }
}
