<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 2019-03-13
 * Time: 17:34
 */

namespace App\Http\Services;


use App\Models\City;
use App\Models\Meter\Meter;
use function count;

class MeterService
{
    /**
     * @var Meter
     */
    private $meter;

    /**
     * MeterService constructor.
     *
     * @param Meter $meter
     */
    public function __construct(Meter $meter)
    {
        $this->meter = $meter;
    }

    public function getMetersInCity(City $city): City
    {
        $cityId = $city->id;
        $meters = $this->meter::whereHas('meterParameter', function ($q) use ($cityId) {
            $q->whereHas('address', function ($q) use ($cityId) {
                $q->where('city_id', $cityId);
            });
        })->get();
        $city['meters'] = $meters;
        $city['metersCount'] = count($meters);
        return $city;
    }

    public function getMetersInClusterwithConnectionType($clusterId, $connectionTypeId)
    {
        return $this->meter::whereHas('meterParameter',
            static function ($q) use ($clusterId, $connectionTypeId) { //meter.meter_parameter
                $q->where('connection_group_id', $connectionTypeId)
                    ->whereHas('address', function ($q) use ($clusterId) { //meter.meter_parameter.address
                        $q->whereHas('city', function ($q) use ($clusterId) { //meter.meter_parameter.address.city
                            $q->where('cluster_id', $clusterId);

                        });
                    });
            })
            ->get();
    }

    public function getMetersInMiniGrid($miniGridId)
    {
        return $this->meter::whereHas('meterParameter',
            static function ($q) use ($miniGridId) { //meter.meter_parameter
                $q->whereHas('address', function ($q) use ($miniGridId) { //meter.meter_parameter.address
                    $q->whereHas('city', function ($q) use ($miniGridId) { //meter.meter_parameter.address.city
                        $q->where('mini_grid_id', $miniGridId);

                    });
                });
            })
            ->get();
    }

    public function getMetersInCityWithConnectionType(City $city, $connectionTypeId): City
    {
        $cityId = $city->id;
        $meters = $this->meter::whereHas('meterParameter', static function ($q) use ($cityId, $connectionTypeId) {
            $q->where('connection_type_id', $connectionTypeId)
                ->whereHas('address', function ($q) use ($cityId) {
                    $q->where('city_id', $cityId);
                });
        })->get();
        $city['meters'] = $meters;
        $city['metersCount'] = count($meters);
        return $city;
    }
}
