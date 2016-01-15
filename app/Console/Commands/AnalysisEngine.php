<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PermalinkStats;
use App\Models\DownloadHistory;
use App\Models\ContentStats;

use Exception;

class AnalysisEngine extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analysis-engine';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process content statistics.';

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
        $this->computeStats();   
    }

    private function computeStats()
    {
        $types = ['lessons'];
        foreach ($types as $type)
        {
            $this->computeDailyVisits($type);
            $this->computeTotalVisits($type);
            $this->computeTopContent(5, $type);
        }

        $this->computeDownloadStats();
    }

    private function computeDailyVisits($type = 'lessons')
    {
        $views_per_day = PermalinkStats::whereHas('permalink', function($q) use ($type) {
                    $q->whereType($type);
                })->select(\DB::raw('count(*) as views, site_id'))
                  ->groupBy(\DB::raw('site_id, DATE(created_at)'))
                  ->orderBy(\DB::raw('site_id, DATE(created_at)'))
                  ->get();

        $viewsperday = [];
        $site_id = 0;
        $sum = 0; $count = 0;
        foreach ($views_per_day as $stat)
        {
            if ($stat->site_id != $site_id)
            {
                if (count($viewsperday) > 0)
                {
                    $content = ContentStats::firstOrNew(['site_id' => $site_id, 'meta_key' => $type .'_views_perday']);
                    $content->meta_value = implode(',', $viewsperday);
                    $content->save();

                    if ($count > 0 && $sum > 0)
                    {
                        $content = ContentStats::firstOrNew(['site_id' => $site_id, 'meta_key' => $type . '_avg_daily_visits']);
                        $content->meta_value = $sum / $count;
                        $content->save();
                    }
                    
                    $sum = $count = 0;
                    $viewsperday = [];
                }
            }

            $viewsperday[] = $stat->views;
            $count++;
            $sum += $stat->views;
            $site_id = $stat->site_id;
        }  

        if (count($viewsperday) > 0)
        {
            $content = ContentStats::firstOrNew(['site_id' => $site_id, 'meta_key' => $type . '_views_perday']);
            $content->meta_value = implode(',', $viewsperday);
            $content->save();

            if ($count > 0 && $sum > 0)
            {
                $content = ContentStats::firstOrNew(['site_id' => $site_id, 'meta_key' => $type . '_avg_daily_visits']);
                $content->meta_value = $sum / $count;
                $content->save();
            }
        }

    }

    private function computeTotalVisits($type = 'lessons')
    {
        $view_stats = PermalinkStats::whereHas('permalink', function($q) use ($type) {
                    $q->whereType($type);
                })->select(\DB::raw('count(*) as views, site_id'))
                  ->groupBy('site_id')
                  ->get();

        foreach ($view_stats as $stat)
        {
            $content = ContentStats::firstOrNew(['site_id' => $stat->site_id, 'meta_key' => $type . '_total_views']);
            $content->meta_value = $stat->views;
            $content->save();
        }  
    }

    private function computeTopContent($limit = 5, $type = 'lessons')
    {
        $top_contents = PermalinkStats::with('permalink')->whereHas('permalink', function($q) use ($type) {
                    $q->whereType($type);
                })->select(\DB::raw('permalink_id, count(*) as views, site_id'))
                  ->groupBy(\DB::raw('site_id, permalink_id'))
                  ->orderBy(\DB::raw('site_id, views'), 'DESC')
                  ->get();


        $top = [];
        $top_count = [];
        $site_id = 0;
        foreach ($top_contents as $content)
        {
            if ($content->site_id != $site_id)
            {
                if (count($top) > 0)
                {
                    $list = implode(',', array_slice($top, 0, $limit) );
                    $cs = ContentStats::firstOrNew(['site_id' => $site_id, 'meta_key' => $type . '_top']);
                    $cs->meta_value = $list;
                    $cs->save();

                    $list = implode(',', array_slice($top_count, 0, $limit) );
                    $cs = ContentStats::firstOrNew(['site_id' => $site_id, 'meta_key' => $type . '_top_views']);
                    $cs->meta_value = $list;
                    $cs->save();
                    $top = [];
                    $top_count = [];
                }
            }

            $top[] = $content->permalink->target_id;
            $top_count[] = $content->views;
            $site_id = $content->site_id;
        }

        if (count($top) > 0)
        {
            $list = implode(',', array_slice($top, 0, 5) );
            $cs = ContentStats::firstOrNew(['site_id' => $site_id, 'meta_key' => $type . '_top']);
            $cs->meta_value = $list;
            $cs->save();

            $list = implode(',', array_slice($top_count, 0, 5) );
            $cs = ContentStats::firstOrNew(['site_id' => $site_id, 'meta_key' => $type . '_top_views']);
            $cs->meta_value = $list;
            $cs->save();

            $top = [];
        }
    }

    private function computeDownloadStats()
    {
        $this->computeTotalDownloads();
        $this->computeDailyDownloads();
        $this->computeTopDownloads();
    }

    private function computeTotalDownloads()
    {
         $total_downloads = DownloadHistory::select(\DB::raw('count(*) as views, site_id'))
                  ->groupBy('site_id')
                  ->get();

        foreach ($total_downloads as $stat)
        {
            $content = ContentStats::firstOrNew(['site_id' => $stat->site_id, 'meta_key' =>  'total_downloads']);
            $content->meta_value = $stat->views;
            $content->save();
        }     
    }

    private function computeDailyDownloads()
    {
        $downloads_per_day = DownloadHistory::select(\DB::raw('count(*) as views, site_id'))
                  ->groupBy(\DB::raw('site_id, DATE(created_at)'))
                  ->orderBy(\DB::raw('site_id, DATE(created_at)'))
                  ->get();

        $downperday = [];
        $site_id = 0;
        $sum = 0; $count = 0;
        foreach ($downloads_per_day as $stat)
        {
            if ($stat->site_id != $site_id)
            {
                if (count($downperday) > 0)
                {
                    $content = ContentStats::firstOrNew(['site_id' => $site_id, 'meta_key' => 'downloads_perday']);
                    $content->meta_value = implode(',', $downperday);
                    $content->save();

                    if ($count > 0 && $sum > 0)
                    {
                        $content = ContentStats::firstOrNew(['site_id' => $site_id, 'meta_key' => 'avg_daily_downloads']);
                        $content->meta_value = $sum / $count;
                        $content->save();
                    }
                    
                    $sum = $count = 0;
                    $downperday = [];
                }
            }

            $downperday[] = $stat->views;
            $count++;
            $sum += $stat->views;
            $site_id = $stat->site_id;
        }  

        if (count($downperday) > 0)
        {
            $content = ContentStats::firstOrNew(['site_id' => $site_id, 'meta_key' => 'downloads_perday']);
            $content->meta_value = implode(',', $downperday);
            $content->save();

            if ($count > 0 && $sum > 0)
            {
                $content = ContentStats::firstOrNew(['site_id' => $site_id, 'meta_key' => 'avg_daily_downloads']);
                $content->meta_value = $sum / $count;
                $content->save();
            }
        }
    }
    private function computeTopDownloads($limit = 5)
    {
        $top_contents = DownloadHistory::select(\DB::raw('download_id, count(*) as views, site_id'))
                  ->groupBy(\DB::raw('site_id, download_id'))
                  ->orderBy(\DB::raw('site_id, views'), 'DESC')
                  ->get();

        $top = [];
        $top_count = [];
        $site_id = 0;
        foreach ($top_contents as $content)
        {
            if ($content->site_id != $site_id)
            {
                if (count($top) > 0)
                {
                    $list = implode(',', array_slice($top, 0, $limit) );
                    $cs = ContentStats::firstOrNew(['site_id' => $site_id, 'meta_key' => 'top_downloads']);
                    $cs->meta_value = $list;
                    $cs->save();

                    $list = implode(',', array_slice($top_count, 0, $limit) );
                    $cs = ContentStats::firstOrNew(['site_id' => $site_id, 'meta_key' => 'top_downloads_views']);
                    $cs->meta_value = $list;
                    $cs->save();
                    $top = [];
                    $top_count = [];
                }
            }

            $top[] = $content->download_id;
            $top_count[] = $content->views;
            $site_id = $content->site_id;
        }

        if (count($top) > 0)
        {
            $list = implode(',', array_slice($top, 0, $limit) );
            $cs = ContentStats::firstOrNew(['site_id' => $site_id, 'meta_key' => 'top_downloads']);
            $cs->meta_value = $list;
            $cs->save();

            $list = implode(',', array_slice($top_count, 0, $limit) );
            $cs = ContentStats::firstOrNew(['site_id' => $site_id, 'meta_key' => 'top_downloads_views']);
            $cs->meta_value = $list;
            $cs->save();

            $top = [];
            $top_count = [];
        }
    }

}
