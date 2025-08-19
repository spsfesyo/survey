<?php

namespace App\Console\Commands;

use Illuminate\Http\Request;
use Illuminate\Console\Command;
use App\Http\Controllers\AdminBlastController;

class BlastWhatsappBatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blast:whatsapp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Blast WA batch per 100 data otomatis';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $controller = new AdminBlastController();
        $controller->BlastingWa(new Request());

        $this->info('Scheduler: batch blasting executed.');
    }
}
