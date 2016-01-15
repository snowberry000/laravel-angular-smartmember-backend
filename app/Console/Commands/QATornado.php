<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Curl;
use Config;


class QATornado extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qa-tornado';
    protected $filename = 'protractor.config.soy.js';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'run protractor tests';
    
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
        //set_time_limit(916000);
        echo "Running Tests".PHP_EOL ;
        while(true)
        {
            
            echo "nowrunning".PHP_EOL;
            $this->runTests();
            echo "executed".PHP_EOL;
            sleep(900);
        }
            
    }

    // private function getLogAndPostSlack($url,$session_id)
    // {
    //     $req=curl_init($url.'/sessions.json?status=running&limit=1&hashed_id='.$session_id);

    //     $optArray = array(
    //         CURLOPT_RETURNTRANSFER => true
    //     );
    //     curl_setopt_array($req,$optArray);
    //     $resp=json_decode(curl_exec($req),true);

    //     for ( ; sizeof($resp) == 0 ; ) { 
    //         sleep("120");
    //         $resp=json_decode(curl_exec($req),true);
    //     }

    //     curl_close($req);

    //     echo($resp[0]["automation_session"]["logs"]);

    //     // $slack = Curl::post("https://slack.com/api/chat.postMessage",array(
    //     //     "token" => Config::get('integration.slack.token'),
    //     //     "channel" => Config::get('integration.slack.channel'),
    //     //     "text" => $resp[0]["automation_session"]["logs"],
    //     //     "username" => Config::get('integration.slack.username')
    //     // ),array());


    // }

    // private function checkForRunningProcess($url)
    // {
    //     sleep("30");
    //     $req=curl_init($url.'/sessions.json?status=running&limit=1');
        
    //     $optArray = array(
    //         CURLOPT_RETURNTRANSFER => true
    //     );
    //     curl_setopt_array($req,$optArray);
    //     $resp=json_decode(curl_exec($req),true);
    //     echo "first response";
    //     $length=0;
    //     $length=sizeof($resp);
        
    //     for (; $length == 0 ; ) { 
    //         sleep("120");
    //         $resp=json_decode(curl_exec($req),true);
    //         $length=sizeof($resp);
    //     }

    //     $session_id=$resp[0]["automation_session"]["hashed_id"];

    //     curl_close($req);
    //     //$this->getLogAndPostSlack($url,$session_id);

    // }

    private function runTests()
    {
        $output="";
        ini_set('max_execution_time', 916000);
        ini_set("default_socket_timeout", 916000);
        exec('cd ../app/tests && chmod 777 tests.sh && ./tests.sh && xvfb-run protractor '.$this->filename,$output);
        //exec('cd../app/tests && protractor '.$this->filename,$output);
        $nextIsResult=0;
        $failedTests=array('Failed Tests:');
        $summary="";
        foreach ($output as $key => $value) {

            if(strpos($value,' - fail') !== false)
            {
                array_push($failedTests,$value);
            }
                
            if($nextIsResult)
            {
                $summary=$value;
                break;
            }
            if(strpos($value,'Finished in') !== false)
            {
                $nextIsResult=true;
            }
        }


        // $req=curl_init("https://mohammadasif3:ETNAG2vEMa2uqLRNsUss@www.browserstack.com/automate/builds.json");
        
        // $optArray = array(
        //     CURLOPT_RETURNTRANSFER => true
        // );

        // curl_setopt_array($req,$optArray);
        // $resp=json_decode(curl_exec($req),true);
        // curl_close($req);

        // $url="https://mohammadasif3:ETNAG2vEMa2uqLRNsUss@www.browserstack.com/automate/builds/".$resp[0]["automation_build"]["hashed_id"];
        // $req=curl_init($url.'/sessions.json?limit=1');
        // $optArray = array(
        //     CURLOPT_RETURNTRANSFER => true
        // );
        // curl_setopt_array($req,$optArray);
        // $resp=json_decode(curl_exec($req),true);

            $slack = Curl::post("https://slack.com/api/chat.postMessage",array(
            "token" => Config::get('integration.slack.token'),
            "channel" => Config::get('integration.slack.channel'),
            "text" => 'https://www.browserstack.com/automate/',//$resp[0]["automation_session"]["logs"],
            "username" => Config::get('integration.slack.username')
        ),array());

         $summaryResult=explode('m', $summary);
        // //$resp=json_decode(curl_exec($req),true);

            $slack = Curl::post("https://slack.com/api/chat.postMessage",array(
            "token" => Config::get('integration.slack.token'),
            "channel" => Config::get('integration.slack.channel'),
            "text" => $summaryResult[0].'m  '.$summaryResult[1],
            "username" => Config::get('integration.slack.username')
        ),array());

        foreach ($failedTests as $key => $value) {
            $slack = Curl::post("https://slack.com/api/chat.postMessage",array(
                        "token" => Config::get('integration.slack.token'),
                        "channel" => Config::get('integration.slack.channel'),
                        "text" => $value,
                        "username" => Config::get('integration.slack.username')
                    ),array());
            // echo $value.PHP_EOL;
        }



    }
}
