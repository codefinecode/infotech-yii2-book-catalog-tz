<?php
declare(strict_types=1);

namespace app\queries;

use yii\db\ActiveQuery;

class AuthorQuery extends ActiveQuery
{
    public function withBooksCount(): self
    {
        $this->select([
            'authors.*',
            'books_count' => 'COUNT(book_authors.book_id)'
        ])
        ->leftJoin('{{%book_authors}}', '{{%book_authors}}.author_id = {{%authors}}.id')
        ->groupBy('authors.id');
        return $this;
    }

    public function topByYear(int $year): self
    {
        $this->innerJoin('{{%book_authors}} ba', 'ba.author_id = {{%authors}}.id')
            ->innerJoin('{{%books}} b', 'b.id = ba.book_id')
            ->andWhere(['b.year' => $year])
            ->select([
                'authors.*',
                'books_count' => 'COUNT(b.id)'
            ])
            ->groupBy('authors.id')
            ->orderBy(['books_count' => SORT_DESC]);
        return $this;
    }
}
