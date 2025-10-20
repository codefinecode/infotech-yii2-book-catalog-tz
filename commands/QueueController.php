<?php

namespace app\commands;

use yii\console\Controller;
use yii\queue\file\Queue;

class QueueController extends Controller
{
    public function actionListen()
    {
        /** @var Queue $queue */
        $queue = \Yii::$app->queue;
        $queue->run(false); // false - бесконечный режим
    }
    
    public function actionWork()
    {
        /** @var Queue $queue */
        $queue = \Yii::$app->queue;
        $queue->run(true); // true - однократный режим
    }
    
    public function actionInfo()
    {
        /** @var Queue $queue */
        $queue = \Yii::$app->queue;
        echo "Queue driver: " . get_class($queue) . "\n";
        echo "Queue path: " . $queue->path . "\n";
    }
    
    public function actionClear()
    {
        /** @var Queue $queue */
        $queue = \Yii::$app->queue;
        $queue->clear();
        echo "Queue cleared\n";
    }
}