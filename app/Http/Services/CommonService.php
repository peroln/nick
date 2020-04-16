<?php

namespace App\Http\Services;


use App\Charts\SampleChart;
use App\Client;
use App\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;


class CommonService
{
    /**
     * @param Request $request
     * @return Builder
     */
    public function indexQuery(Request $request)
    {

        if (!$request->filled(['area', 'keyword'])) {

            $result = Product::with('client');
        } else {
            $query = $this->preparationQuery();
            if ($request->area === 'All' && $request->keyword) {
                $result = $this->createRequestAll($request, $query);
            }
            if ($request->area === 'Client' && $request->keyword) {
                $result = $this->createRequestClient($request, $query);
            }
            if ($request->area === 'Product' && $request->keyword) {
                $result = $this->createRequestProduct($request, $query);
            }
            if ($request->area === 'Total' && $request->keyword) {
                $result = $this->createRequestTotal($request, $query);
            }
            if ($request->area === 'Date' && $request->keyword) {
                $result = $this->createRequestDate($request, $query);
            }
        }
        return $result;
    }


    /**
     * @return Builder
     */
    private function preparationQuery(): Builder
    {
        return Product::query()
            ->join('clients as c', 'products.client_id', '=', 'c.id')
            ->select(
                'c.name as client',
                'c.id as client_id',
                'products.id as id',
                'products.name as name',
                'products.created_at as created_at',
                'products.total as total'
            );
    }

    /**
     * @param string $str
     * @return false|string
     */
    private function performDateString(string $str = ''): string
    {
        if (\DateTime::createFromFormat('d-m-Y', $str) !== FALSE) {
            return date("Y-m-d H:i:s", strtotime($str));
        } else {
            return '';
        }
    }

    /**
     * @param Request $request
     * @param Builder $query
     * @return Builder
     */
    private function createRequestAll(Request $request, Builder $query): Builder
    {
        $query = $this->createRequestDate($request, $query);
        $query = $this->createRequestClient($request, $query);
        $query = $this->createRequestProduct($request, $query);
        return $this->createRequestTotal($request, $query);

    }

    /**
     * @param Request $request
     * @param Builder $query
     * @return Builder
     */
    private function createRequestClient(Request $request, Builder $query): Builder
    {
        return $query->orWhere('c.name', 'LIKE', '%' . $request->keyword . '%');
    }

    /**
     * @param Request $request
     * @param Builder $query
     * @return Builder
     */
    private function createRequestProduct(Request $request, Builder $query): Builder
    {
        return $query->orWhere('products.name', 'LIKE', '%' . $request->keyword . '%');
    }

    /**
     * @param Request $request
     * @param Builder $query
     * @return Builder
     */
    private function createRequestTotal(Request $request, Builder $query): Builder
    {
        return $query->orWhere('total', $request->keyword);
    }

    /**
     * @param Request $request
     * @param Builder $query
     * @return Builder
     */
    private function createRequestDate(Request $request, Builder $query): Builder
    {
        $date = $this->performDateString($request->keyword);
        if ($date) {
            return $query->orWhereDate('products.created_at', $date);
        }
        return $query;
    }

    /**
     * @param Collection $collection
     * @param bool $separation
     * @return SampleChart
     */
    public function chart(Collection $collection, $separation = true): SampleChart
    {
        $collet_created_at = $this->createChartAxis($collection);
        $common_x_axis = $collet_created_at->keys()->toArray();

        if ($separation && count($collection)) {
            return $this->createSeparatedCharts($collection, $common_x_axis);
        }

        $common_x_axis = $collet_created_at->keys()->toArray();
        $y_axis = $collet_created_at->values()->toArray();

        $chart = new SampleChart();

        $chart->labels($common_x_axis);
        $chart->dataset('Common', 'line', $y_axis);
        return $chart;
    }

    /**
     * @param Collection $collection
     * @param $common_x_axis
     * @return SampleChart
     */
    private function createSeparatedCharts(Collection $collection, $common_x_axis): SampleChart
    {
        $client_groups = $collection->groupBy('client_id');
        if (is_object($client_groups) && count($client_groups)) {
            $clients = Client::all();
            $array_charts = [];
            foreach ($client_groups as $client_id => $group) {
                $collet_created_at = $this->createChartAxis($group);
                $y_axis = [];
                foreach ($common_x_axis as $key_x => $date) {
                    foreach ($collet_created_at as $key_y => $value) {
                        if ($date === $key_y) {
                            $y_axis [$key_x] = $value;
                            break;
                        }
                        $y_axis [$key_x] = 0;
                    }

                }
                [$color, $fill_color] = $this->setColorChart($client_id);
                $client_name = optional($clients->find($client_id))->name;
                $array_charts[$client_name] = [
                    'x' => $common_x_axis,
                    'y' => $y_axis,
                    'color' => $color,
                    'fill_color' => $fill_color
                ];
            }
        }


        $chart = new SampleChart();
        foreach ($array_charts as $chart_name => $axis) {
            $chart->labels($common_x_axis);
            $chart->dataset($chart_name, 'line', $axis['y'])
                ->color($axis['color'])
                ->backgroundcolor($axis['fill_color']);
        }
        return $chart;
    }

    private function setColorChart($client_id)
    {
        switch ($client_id) {
            case 1:
                $color = 'rgba(255, 99, 132, 1.0)';
                $fill_color = 'rgba(255, 99, 132, 0.2)';
                break;
            case 2:
                $color = 'rgba(22,160,133, 1.0)';
                $fill_color = 'rgba(22,160,133, 0.2)';
                break;
            case 3:
                $color = 'rgba(255, 205, 86, 1.0)';
                $fill_color = 'rgba(255, 205, 86, 0.2';
                break;
            default :
                $color = 'rgba(51,105,232, 1.0)';
                $fill_color = 'rgba(51,105,232, 0.2)';
        }
        return [$color, $fill_color];
    }

    /**
     * @param Collection $collection
     * @return \Illuminate\Support\Collection
     */
    private function createChartAxis(Collection $collection): \Illuminate\Support\Collection
    {
        return $collection->groupBy(function ($product) {
            return $product->created_at->toDateString();
        })->map(function ($row) {
            return $row->sum('total');
        });
    }
}
