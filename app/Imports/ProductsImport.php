<?php

namespace App\Imports;

use App\{Client, Product};
use Maatwebsite\Excel\Concerns\{WithHeadingRow, ToModel};


class ProductsImport implements ToModel, WithHeadingRow
{
    private $clients;

    public function __construct()
    {
        $this->setClients();
    }

    /**
     * @param array $row
     * @return Product|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Model[]|null
     * @throws \Exception
     */
        public function model(array $row)
        {
                return new Product([
                    'client_id' => optional($this->clients->where('name', $row['client'])->first())->id,
                    'name' => $row['product'],
                    'total' => $row['total'],
                    'created_at' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date']),

                ]);
        }

    /**
     * @throws \Exception
     */
        private function setClients(): void{
            $clients = Client::all();
            if(count($clients)){
                $this->clients = $clients;
            }else{
                throw new \Exception('The array of the clients is empty');
            }
        }
}
