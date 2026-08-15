<?php

namespace App\Console\Commands;

use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Throwable;

class SyncVehicleCatalog extends Command
{
    protected $signature = 'vehicles:sync-catalog';

    protected $description = 'Sincroniza marcas e modelos de automóveis com o catálogo FIPE';

    private string $baseUrl = 'https://parallelum.com.br/fipe/api/v1';

    public function handle(): int
    {
        $this->newLine();
        $this->info('InfoGate Gestão - Catálogo Automotivo');
        $this->line('Sincronizando marcas e modelos...');
        $this->newLine();

        try {
            $brandsResponse = Http::acceptJson()
                ->timeout(30)
                ->retry(3, 1000)
                ->get($this->baseUrl . '/carros/marcas');

            if (! $brandsResponse->successful()) {
                $this->error('Não foi possível consultar as marcas.');

                return self::FAILURE;
            }

            $brands = $brandsResponse->json();

            if (! is_array($brands) || empty($brands)) {
                $this->error('A API não retornou marcas.');

                return self::FAILURE;
            }

            $totalBrands = 0;
            $totalModels = 0;
            $errors = 0;

            $progress = $this->output->createProgressBar(count($brands));
            $progress->start();

            foreach ($brands as $brandData) {
                $brandCode = (string) ($brandData['codigo'] ?? '');
                $brandName = trim((string) ($brandData['nome'] ?? ''));

                if ($brandCode === '' || $brandName === '') {
                    $progress->advance();
                    continue;
                }

                try {
                    $brand = VehicleBrand::updateOrCreate(
                        [
                            'external_code' => $brandCode,
                        ],
                        [
                            'name' => $brandName,
                            'source' => 'fipe',
                            'is_active' => true,
                        ]
                    );

                    $totalBrands++;

                    $modelsResponse = Http::acceptJson()
                        ->timeout(30)
                        ->retry(3, 1000)
                        ->get(
                            $this->baseUrl
                            . '/carros/marcas/'
                            . $brandCode
                            . '/modelos'
                        );

                    if (! $modelsResponse->successful()) {
                        $errors++;
                        $progress->advance();
                        continue;
                    }

                    $data = $modelsResponse->json();
                    $models = $data['modelos'] ?? [];

                    if (! is_array($models)) {
                        $errors++;
                        $progress->advance();
                        continue;
                    }

                    foreach ($models as $modelData) {
                        $modelCode = (string) ($modelData['codigo'] ?? '');
                        $modelName = trim((string) ($modelData['nome'] ?? ''));

                        if ($modelCode === '' || $modelName === '') {
                            continue;
                        }

                        VehicleModel::updateOrCreate(
                            [
                                'vehicle_brand_id' => $brand->id,
                                'external_code' => $modelCode,
                            ],
                            [
                                'name' => $modelName,
                                'source' => 'fipe',
                                'is_active' => true,
                            ]
                        );

                        $totalModels++;
                    }

                    usleep(150000);
                } catch (Throwable $e) {
                    $errors++;
                    report($e);
                }

                $progress->advance();
            }

            $progress->finish();

            $this->newLine(2);
            $this->info('Sincronização concluída.');

            $this->table(
                ['Item', 'Quantidade'],
                [
                    ['Marcas processadas', $totalBrands],
                    ['Modelos processados', $totalModels],
                    ['Erros', $errors],
                ]
            );

            return self::SUCCESS;
        } catch (Throwable $e) {
            report($e);

            $this->error(
                'Falha durante a sincronização: ' . $e->getMessage()
            );

            return self::FAILURE;
        }
    }
}
