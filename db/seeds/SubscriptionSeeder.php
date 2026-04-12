<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class SubscriptionSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run(): void
    {
        $data = [
            [
                'email' => 'test@example.com',
                'repo' => 'odan/slim4-skeleton',
                'token' => 'test-token',
                'confirmed' => 1,
                'last_seen_tag' => 'v1.0.0'
            ],
            [
                'email' => 'other@example.com',
                'repo' => 'slimphp/Slim',
                'token' => 'other-token',
                'confirmed' => 0
            ]
        ];

        $this->table('subscriptions')->insert($data)->saveData();
    }
}
