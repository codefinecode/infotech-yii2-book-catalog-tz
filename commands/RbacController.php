<?php

namespace app\commands;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        // Create permission
        $manageBooks = $auth->createPermission('manageBooks');
        $manageBooks->description = 'Manage books (CRUD)';
        $auth->add($manageBooks);

        // Create role
        $user = $auth->createRole('user');
        $user->description = 'Authenticated User';
        $auth->add($user);

        // Assign permission to role
        $auth->addChild($user, $manageBooks);

        echo "RBAC initialized successfully.\n";
    }

    public function actionAssignUserRole($userId = 1)
    {
        $auth = Yii::$app->authManager;
        $userRole = $auth->getRole('user');
        $auth->assign($userRole, $userId);
        echo "Role 'user' assigned to user ID: $userId\n";
    }
}