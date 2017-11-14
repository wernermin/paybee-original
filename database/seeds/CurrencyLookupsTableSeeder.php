<?php

use Illuminate\Database\Seeder;
use Ixudra\Curl\Facades\Curl;

class CurrencyLookupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $url = 'https://api.coindesk.com/v1/bpi/supported-currencies.json';
        
        $response = Curl::to($url)->get();
        $currencies = json_decode($response,true);
        $date = date("Y-m-d H:i:s");
        
        for($i = 0;$i<count($currencies);$i++)
        {
            if($currencies[$i]['currency'] != "" && $currencies[$i]['country'] != "")
            {
                DB::table('currency_lookups')->insert([
                    'code' => $currencies[$i]['currency'],
                    'description' => trim($currencies[$i]['country']),
                    'created_at' => $date,
                ]);
            }
        }
    }
}
