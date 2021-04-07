<?php
namespace Seeds\Local;

use App\Models\State;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class RefactorStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file = File::get(database_path('seeds/data/states.json'));
        $states = json_decode($file, true);
        $stateFields = [];
        foreach ($states as $state){
            $stateFields = [
                'name' => $state['name'],
                'country_id' => $state['country_id']
            ];
            State::create($stateFields);
        }
    }
}
