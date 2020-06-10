<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Get:UpdateDay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update count_day, total_day';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::beginTransaction();
        try {
            DB::table('tracking')->update(['count_day' => DB::raw('DATEDIFF(now(), tracking_date)')]);

            DB::table('detail')->update(['total_day' => DB::raw('DATEDIFF(now(), process_date)')]);

            DB::commit();

            var_dump('Complete');
        } catch (Exception $e) {
            DB::rollBack();

            var_dump('Error');
            throw new Exception($e->getMessage());
        }
    }
}
