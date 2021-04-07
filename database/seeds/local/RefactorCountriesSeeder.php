<?php
namespace Seeds\Local;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RefactorCountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file = File::get(database_path('seeds/data/countries.json'));
        $countries = json_decode($file, true);
        $countryFields = [];
        foreach ($countries as $country){
            $countryFields = [
                'name' => $country['name'],
                'iso3' => $country['iso3'],
                'iso2' => $country['iso2'],
                'phonecode' => $country['phone_code'],
                'capital' => $country['capital'],
                'currency' => $country['currency']
            ];
            Country::create($countryFields);
        }
    }
}
