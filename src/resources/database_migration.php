<?php

use Doctrine\DBAL\Schema\Schema;

/**
 * A simple migration to be used with the DatabaseStore.
 * For the SQL output call: $schema->toSql(<Adapter>)
 */
$schema = new Schema;
$table = $schema->createTable('storage');

$id = $table->addColumn('id', 'integer');
$id->setAutoincrement(true);
$id->setUnsigned(true);

$sid = $table->addColumn('sid', 'string');
$sid->setLength(192);

$key = $table->addColumn('key', 'string');
$key->setLength(192);

$val = $table->addColumn('val', 'blob');

$table->setPrimaryKey(['id']);
$table->addOption('charset', 'utf8mb4');

return $schema;
