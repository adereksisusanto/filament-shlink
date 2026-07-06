<?php

namespace Adereksisusanto\FilamentShlink\Commands;

use Illuminate\Console\Command;

class FilamentShlinkCommand extends Command
{
    public $signature = 'filament-shlink';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
