<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\services\ReportService;

class ReportController extends Controller
{
    public function actionTopAuthors($year = null)
    {
        $year = $year ?: date('Y');
        $reportService = Yii::$container->get(ReportService::class);
        
        $topAuthors = $reportService->getTopAuthorsByYear($year);

        return $this->render('top-authors', [
            'topAuthors' => $topAuthors,
            'year' => $year,
        ]);
    }
}