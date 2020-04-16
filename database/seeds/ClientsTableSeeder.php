<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{Log, DB};

class ClientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            self::checkConfigFileSeed();
            $client_arr = $this->preparationData();
            DB::table('clients')->insert($client_arr);
        } catch (Throwable $e) {
            $this->command->error($e->getMessage());
            Log::error($e->getMessage());
            exit;
        }

    }

    /**
     * @return array
     * @throws Exception
     */
    private function preparationData(): array
    {
        $client_arr = config('seed.clients');
        if (is_array($client_arr) && count($client_arr)) {
            $client_arr = collect($client_arr);
            $client_arr->transform(function ($value) {
                return ['name' => $value];
            });
            return $client_arr->toArray();
        } else {
            throw new Exception('The array \'client\' in config/seed.php file is empty.');
        }

    }

    /**
     * @throws Exception
     */
    public static function checkConfigFileSeed(): void
    {
        if (!is_readable(config_path('seed') . '.php')) {
            throw new Exception('The config file \'seed.php\' is not readable.');
        }
    }
}
