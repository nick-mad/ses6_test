<?php

declare(strict_types=1);

namespace App\Application\Subscription\Service;

interface ScannerServiceInterface
{
    public function scan(): void;
}
