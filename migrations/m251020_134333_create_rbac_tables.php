<?php

use yii\db\Migration;

class m251020_134333_create_rbac_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        // Create tables
        $this->createTable($auth->ruleTable, [
            'name' => $this->string(64)->notNull(),
            'data' => $this->binary(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY (name)',
        ]);

        $this->createTable($auth->itemTable, [
            'name' => $this->string(64)->notNull(),
            'type' => $this->smallInteger()->notNull(),
            'description' => $this->text(),
            'rule_name' => $this->string(64),
            'data' => $this->binary(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY (name)',
            'FOREIGN KEY (rule_name) REFERENCES ' . $auth->ruleTable . ' (name) ON DELETE SET NULL ON UPDATE CASCADE',
        ]);

        $this->createTable($auth->itemChildTable, [
            'parent' => $this->string(64)->notNull(),
            'child' => $this->string(64)->notNull(),
            'PRIMARY KEY (parent, child)',
            'FOREIGN KEY (parent) REFERENCES ' . $auth->itemTable . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (child) REFERENCES ' . $auth->itemTable . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
        ]);

        $this->createTable($auth->assignmentTable, [
            'item_name' => $this->string(64)->notNull(),
            'user_id' => $this->string(64)->notNull(),
            'created_at' => $this->integer(),
            'PRIMARY KEY (item_name, user_id)',
            'FOREIGN KEY (item_name) REFERENCES ' . $auth->itemTable . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
        ]);

        // Create roles
        $user = $auth->createRole('user');
        $user->description = 'Authenticated User';
        $auth->add($user);

        // Create permissions
        $manageBooks = $auth->createPermission('manageBooks');
        $manageBooks->description = 'Manage books (CRUD)';
        $auth->add($manageBooks);

        // Assign permissions to roles
        $auth->addChild($user, $manageBooks);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
         $auth = Yii::$app->authManager;
        
        $this->dropTable($auth->assignmentTable);
        $this->dropTable($auth->itemChildTable);
        $this->dropTable($auth->itemTable);
        $this->dropTable($auth->ruleTable);
    }
}
