<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class AbstractRepository extends EntityRepository
{
    /**
     * @param array $columns
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @return array|null
     */
    public function findByWithColumns(
        array $columns,
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ): ?array {
        $primaryKey = $this->getEntityManager()->getClassMetadata($this->getEntityName())->getIdentifierFieldNames();
        if (array_diff($primaryKey, $columns)) {
            $columns = array_merge($columns, $primaryKey);
        }
        $qb = $this->getEntityManager()->createQueryBuilder();
        $alias = $this->getAlias();

        $qb->select("partial {$alias}.{" . implode(',', $columns) . "}")
            ->from($this->getEntityName(), $alias);
        foreach ($criteria as $column => $value) {
            if (is_array($value)) {
                $qb->andWhere("{$alias}.{$column} IN (:{$column})");
            } else {
                $qb->andWhere("{$alias}.{$column} = :{$column}");
            }
        }
        $qb->setParameters($criteria);
        if (!empty($orderBy)) {
            foreach ($orderBy as $column => $order) {
                $qb->addOrderBy($column, $order);
            }
        }
        if (!empty($limit)) {
            $qb->setMaxResults($limit);
        }
        if (!empty($offset)) {
            $qb->setFirstResult($offset);
        }
        return $qb->getQuery()
            ->getResult();
    }
}