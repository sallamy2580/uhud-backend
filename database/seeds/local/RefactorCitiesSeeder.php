<?php
namespace Seeds\Local;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class RefactorCitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file = File::get(database_path('seeds/data/cities.json'));
        $cities = json_decode($file, true);
        foreach ($cities as $city){
            $cityFields = [
                'name' => $city['name'],
                'country_id' => $city['country_id'],
                'state_id' => $city['state_id']
            ];
            City::create($cityFields);
        }
    }
}
