<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateSubscriptionsTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('subscriptions');
        $table->addColumn('email', 'string', ['limit' => 255])
            ->addColumn('repo', 'string', ['limit' => 255])
            ->addColumn('token', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('confirmed', 'boolean', ['default' => false])
            ->addColumn('last_seen_tag', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('last_scanned_at', 'timestamp', ['null' => true])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('unsubscribed_at', 'timestamp', ['null' => true])
            ->addIndex(['email', 'repo'], ['unique' => true])
            ->addIndex(['token'], ['unique' => true])
            ->addIndex(['last_scanned_at'])
            ->create();
    }
}
