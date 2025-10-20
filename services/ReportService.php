<?php
declare(strict_types=1);

namespace app\services;

use Yii;
use app\models\Author;

class ReportService
{
    public function getTopAuthorsByYear(?int $year = null, int $limit = 10): array
    {
        $year = $year ?: (int)date('Y');
        $cacheKey = "top_authors_{$year}_{$limit}";
        
        return Yii::$app->cache->getOrSet($cacheKey, function() use ($year, $limit) {
            return Author::find()
                ->topByYear($year)
                ->limit($limit)
                ->all();
        }, 3600);
    }
}