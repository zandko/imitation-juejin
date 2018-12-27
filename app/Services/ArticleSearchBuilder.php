<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Lable;
use Log;

class ArticleSearchBuilder
{
    /**
     * 初始化查询
     * @var array
     */
    protected $params = [
        'index' => 'articles',
        'type' => '_doc',
        'body' => [
            'query' => [
                'bool' => [
                    'filter' => [],
                    'must' => []
                ],
            ]
        ]
    ];

    /**
     * 分页
     * @param $size
     * @param $page
     * @return $this
     */
    public function paginate($size, $page)
    {
        $this->params['body']['from'] = ($page - 1) * $size;
        $this->params['body']['size'] = $size;

        return $this;
    }

    /**
     * 状态
     * @return $this
     */
    public function state()
    {
        $this->params['body']['query']['bool']['filter'][] = [
            'term' => [
                'state' => 1,
            ]
        ];

        return $this;
    }

    /**
     * 分类文章
     * @param Category $category
     * @return $this
     */
    public function category(Category $category)
    {
        $this->params['body']['query']['bool']['filter'][] = [
            'term' => [
                'category_id' => $category->id,
            ]
        ];

        return $this;
    }

    /**
     * 标签文章
     * @param Lable $lable
     * @return $this
     */
    public function lable(Lable $lable)
    {
        $this->params['body']['query']['bool']['filter'][] = [
            'term' => [
                'lable_id' => $lable->id,
            ]
        ];

        return $this;
    }

    public function isLoginTag($isLogin = false)
    {
        $arr = [1, 2, 3, 4, 5, 6, 7];

        if ($isLogin) {
            $arr = array_unique(array_merge($arr, $isLogin));
        }

        $this->params['body']['query']['bool']['filter'][] = [
            'terms' => [
                'lable_id' => $arr
            ]
        ];

        return $this;
    }

    /**
     * 关键字查询
     * @param $keywords
     * @return $this
     */
    public function keywords($keywords)
    {
        $keywords = is_array($keywords) ? $keywords : [$keywords];

        foreach ($keywords as $keyword) {
            $this->params['body']['query']['bool']['filter'][] = [
                'multi_match' => [
                    'query' => $keyword,
                    'fields' => [
                        'title^3',
                        'content^2',
                        'category^2',
                        'lable^1',
                        'category_description',
                        'lable_description',
                        'user_name',
                    ]
                ]
            ];
        }

        return $this;
    }

    /**
     * 排序
     * @param $field
     * @param $direction
     * @return $this
     */
    public function orderBy($field, $direction)
    {
        if (!isset($this->params['body']['sort'])) {
            $this->params['body']['sort'] = [];
        }

        $this->params['body']['sort'][] = [
            $field => $direction
        ];

        return $this;
    }

    /**
     * 筛选规定时间内的热门文章
     * @return $this
     */
    public function DateTimeOrderBy($startDateTime = null, $endDateTime = null, $historyDateTime = true)
    {
        if (!$historyDateTime) {
            $this->params['body']['query']['bool']['must'][] = [
                'range' => [
                    'created_at' => [
                        'gte' => strtotime($startDateTime),
                        'lte' => strtotime($endDateTime),
                    ]
                ]
            ];
        }

        $this->params['body']['sort'][] = [
            'popular_order' => 'desc'
        ];

        return $this;
    }

    /**
     * 默认的热门排行
     * @return $this
     */
    public function popular()
    {
        $this->params['body']['query']['bool']['filter'][] = [
            'term' => [
                'state' => 2,
            ]
        ];

        $this->DateTimeOrderBy();

        return $this;
    }

    /**
     * 返回
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}