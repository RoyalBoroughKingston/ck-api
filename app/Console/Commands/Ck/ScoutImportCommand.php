<?php

namespace App\Console\Commands\Ck;

use Laravel\Scout\Console\ImportCommand;

class ScoutImportCommand extends ImportCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ck:scout-import {model} {--c|chunk=}';
}
