<?php

/**
 * FestivalEditionArticleTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class FestivalEditionArticleTable extends Doctrine_Table
{
	public function updateObjects($articleId, $festivalEditions)
	{
		Doctrine_Query::create()
			->delete('FestivalEditionArticle pa')
			->where('pa.article_id = ?', $articleId)
			->execute();
			
		foreach ($festivalEditions as $festivalEdition){
			$festivalEditionArticle = new FestivalEditionArticle();
			$festivalEditionArticle->setArticleId($articleId);
			$festivalEditionArticle->setFestivalEditionId($festivalEdition);
			$festivalEditionArticle->save();
		}
	}

	public function getRelatedArticles($articleId)
	{
		$festivalEditions = Doctrine_Query::create()
			->from('FestivalEditionArticle fa')
			->where('fa.article_id = ?', $articleId)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$festivalEditionIds = array();
		foreach($festivalEditions as $festivalEdition){
			$festivalEditionIds[] = $festivalEdition['festival_edition_id'];
		}

		$articleIds = array();
                if (count($festivalEditionIds) > 0){
                    $articles = Doctrine_Query::create()
                            ->from('FestivalEditionArticle fea')
                            ->whereIn('fea.festival_edition_id', $festivalEditionIds)
                            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
                    foreach($articles as $article){
                            $articleIds[] = $article['article_id'];
                    }
                }

		return $articleIds;
	}
}