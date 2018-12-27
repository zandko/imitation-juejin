<?php

namespace App\Console\Commands\Elasticsearch;

use Illuminate\Console\Command;
use App\Models\Article;

class SyncArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'es:sync-articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '将文章数据同步到 Elasticsearch';

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
        $es = app('es');

        Article::query()
            ->chunkById(100, function ($articles) use ($es) {
                $this->info(sprintf('正在同步 ID 范围为 %s 至 %s 的文章', $articles->first()->id, $articles->last()->id));

                $req = ['body' => []];

                foreach ($articles as $article) {
                    $data = $article->toESArray();

                    $req['body'][] = [
                        'index' => [
                            '_index' => 'articles',
                            '_type' => '_doc',
                            '_id' => $data['id'],
                        ],
                    ];

                    $req['body'][] = $data;
                }

                try {
                    $es->bulk($req);
                } catch (\Exception $exception) {
                    $this->error($exception->getMessage());
                }
            });

        $this->info('同步完成');
    }
}
