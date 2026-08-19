<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ScrapingController;

class ScrapeAllNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:scrape {club? : Nom específic del club (fcb, reus, palau, cerdanyola, regio, noia, caldes, shum)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executa l’scraping de notícies de tots els clubs o d’un de concret';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $club = strtolower($this->argument('club') ?? 'all');

        $scrapers = [
            'fcb'        => ['name' => 'FC Barcelona', 'action' => fn() => ScrapingController::scrapeFCBarcelona()],
            'reus'       => ['name' => 'Reus Deportiu', 'action' => fn() => ScrapingController::scrapeReus()],
            'palau'      => ['name' => 'HC Palau', 'action' => fn() => ScrapingController::scrapePalau()],
            'cerdanyola' => ['name' => 'Cerdanyola CH', 'action' => fn() => ScrapingController::scrapeCerdanyola()],
            'regio'      => ['name' => 'Regió 7', 'action' => fn() => ScrapingController::scrapeRegio()],
            'noia'       => ['name' => 'CE Noia Freixenet', 'action' => fn() => ScrapingController::scrapeNoia()],
            'caldes'     => ['name' => 'CH Caldes', 'action' => fn() => ScrapingController::scrapeCaldes()],
            'shum'       => ['name' => 'SHUM Maçanet', 'action' => fn() => ScrapingController::scrapeShum()],
            'amunt'      => ['name' => 'CE Arenys de Munt', 'action' => fn() => ScrapingController::scrapeAmunt()],
            'lesportiu'  => ['name' => "L'Esportiu de Catalunya", 'action' => fn() => ScrapingController::scrapeLesportiu()],
        ];

        $this->info("Iniciant scraping de notícies...");

        foreach ($scrapers as $key => $scraper) {
            if ($club !== 'all' && $club !== $key) {
                continue;
            }

            $this->output->write(" - Scraping {$scraper['name']}... ");
            try {
                ($scraper['action'])();
                $this->info(" [OK]");
            } catch (\Exception $e) {
                $this->error(" [ERROR: " . $e->getMessage() . "]");
            }
        }

        $this->info("Procés de scraping finalitzat amb èxit!");
    }
}
