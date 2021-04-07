<?php
namespace Seeds\Local;

use Illuminate\Database\Seeder;

class TicketSectionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\TicketSection::class, 300)->create();
    }
}
