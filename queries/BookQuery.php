<?php
declare(strict_types=1);

namespace app\queries;

use yii\db\ActiveQuery;

class BookQuery extends ActiveQuery
{
    public function byYear(?int $year): self
    {
        if ($year !== null) {
            $this->andWhere(['year' => $year]);
        }
        return $this;
    }

    public function withAuthors(): self
    {
        return $this->with(['authors']);
    }

    public function recent(int $limit = 10): self
    {
        return $this->orderBy(['created_at' => SORT_DESC])->limit($limit);
    }
}
