<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Windwalker\Model\Helper\QueryHelper;
use Windwalker\Model\ListModel;

/**
 * Class FlowerModelSakuras
 *
 * @since 1.0
 */
class FlowerModelSakuras extends ListModel
{
	/**
	 * populateState
	 *
	 * @param null $ordering
	 * @param null $direction
	 *
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$queryHelper = $this->container->get('model.sakuras.helper.query');

		$queryHelper->addTable('sakura', '#__flower_sakuras')
			->addTable('category',  '#__categories', 'sakura.catid      = category.id')
			->addTable('user',      '#__users',      'sakura.created_by = user.id')
			->addTable('viewlevel', '#__viewlevels', 'sakura.access     = viewlevel.id')
			->addTable('lang',      '#__languages',  'sakura.language   = lang.lang_code');

		$this->filterFields = array_merge($this->filterFields, $queryHelper->getFilterFields());

		$this->state->set('query.select', $queryHelper->getSelectFields(QueryHelper::COLS_WITH_FIRST));

		parent::populateState('sakura.catid, sakura.ordering', 'ASC');
	}

	/**
	 * processFilters
	 *
	 * @param JDatabaseQuery $query
	 * @param array          $filters
	 *
	 * @return  JDatabaseQuery
	 */
	protected function processFilters(\JDatabaseQuery $query, $filters = array())
	{
		// If no state filter, set published >= 0
		if (!isset($filters['sakura.published']))
		{
			$query->where($query->quoteName('sakura.published') . ' >= 0');
		}

		return parent::processFilters($query, $filters);
	}

	/**
	 * configureFilters
	 *
	 * @param \Windwalker\Model\Filter\SearchHelper $filterHelper
	 *
	 * @return  void
	 */
	protected function configureFilters($filterHelper)
	{
	}

	/**
	 * configureSearches
	 *
	 * @param \Windwalker\Model\Filter\SearchHelper $searchHelper
	 *
	 * @return  void
	 */
	protected function configureSearches($searchHelper)
	{
	}
}