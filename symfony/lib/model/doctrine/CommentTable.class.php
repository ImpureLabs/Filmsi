<?php

/**
 * CommentTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class CommentTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object CommentTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Comment');
    }

	public function getActiveByModel($model, $modelLibraryId, $ip)
	{	
		return Doctrine_Query::create()
			->from('Comment c')
			->where('c.model = ?', $model)
			->andWhere('c.model_library_id = ?', $modelLibraryId)
			->andWhere('c.state = 1 OR c.ip = ?', $ip)
			->execute();
	}

	public function getCountByEntity($model, $modelLibraryId)
	{
		$q = Doctrine_Query::create()
			->from('Comment c')
			->select('COUNT(c.id) count')
			->where('c.model = ?', $model)
			->andWhere('c.model_library_id = ?', $modelLibraryId)
			->andWhere('c.state = 1')
			->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);

		return $q['count'];
	}

        public function getFiltered($filters, $limit= null)
	{
		$q = Doctrine_Query::create()
			->from('Comment c')
			->orderBy('c.created_at DESC');

		if(isset($limit)){
			$q->limit($limit);
		}

		if (isset($filters['email']) && !empty($filters['email'])){
			$q->addWhere('c.email = ?', $filters['email']);
		}

		if (isset($filters['model']) && !empty($filters['model'])){
			$q->addWhere('c.model = ?', $filters['model']);
		}

		if (isset($filters['model_library_id']) && !empty($filters['model_library_id'])){
			$q->addWhere('c.model_library_id = ?', $filters['model_library_id']);
		}

		if (isset($filters['date_from']) && !empty($filters['date_from'])){
			$q->addWhere('c.created_at >= ?', $filters['date_from']);
		}

		if (isset($filters['date_to']) && !empty($filters['date_to'])){
			$q->addWhere('c.created_at <= ?', $filters['date_to']);
		}

		if (isset($filters['offset'])){
			$q->offset($filters['offset']);
		}


		return $q->execute();
	}

        public function countFiltered($filters)
        {
            $q = Doctrine_Query::create()
                    ->select('COUNT(c.id) count')
                    ->from('Comment c')
                    ->orderBy('c.created_at DESC');

            if (isset($filters['email']) && !empty($filters['email'])){
                    $q->addWhere('c.email = ?', $filters['email']);
            }

            if (isset($filters['model']) && !empty($filters['model'])){
                    $q->addWhere('c.model = ?', $filters['model']);
            }

            if (isset($filters['model_library_id']) && !empty($filters['model_library_id'])){
                    $q->addWhere('c.model_library_id = ?', $filters['model_library_id']);
            }

            if (isset($filters['date_from']) && !empty($filters['date_from'])){
                    $q->addWhere('c.created_at >= ?', $filters['date_from']);
            }

            if (isset($filters['date_to']) && !empty($filters['date_to'])){
                    $q->addWhere('c.created_at <= ?', $filters['date_to']);
            }
            
            $counter = $q->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);

            return $counter['count'];
        }

        public function deleteByIds($ids)
        {
            return Doctrine_Query::create()
                ->delete('Comment c')
                ->whereIn('c.id', $ids)
                ->execute();
        }
}