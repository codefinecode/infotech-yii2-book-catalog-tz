<?php

namespace app\services;

use Yii;
use app\models\Author;

class ReportService
{
    public function getTopAuthorsByYear($year = null, $limit = 10)
    {
        $year = $year ?: date('Y');
        $cacheKey = "top_authors_{$year}_{$limit}";
        
        return Yii::$app->cache->getOrSet($cacheKey, function() use ($year, $limit) {
            return Author::find()
                ->select([
                    'authors.*',
                    'books_count' => 'COUNT(book_authors.book_id)'
                ])
                ->innerJoinWith('books')
                ->andWhere(['books.year' => $year])
                ->groupBy('authors.id')
                ->orderBy(['books_count' => SORT_DESC])
                ->limit($limit)
                ->all();
        }, 3600); // Кэш на 1 час
    }
}