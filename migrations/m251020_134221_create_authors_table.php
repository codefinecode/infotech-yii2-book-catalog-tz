<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%authors}}`.
 */
class m251020_134221_create_authors_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%authors}}', [
            'id' => $this->primaryKey(),
            'full_name' => $this->string(255)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-authors-full_name', '{{%authors}}', 'full_name');
        
        // Demo data
        $this->batchInsert('{{%authors}}', ['full_name', 'created_at', 'updated_at'], [
            ['Лев Толстой', time(), time()],
            ['Федор Достоевский', time(), time()],
            ['Антон Чехов', time(), time()],
            ['Александр Пушкин', time(), time()],
        ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%authors}}');
    }
}
