<?php
/**
 * File PrestaSelect.php
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * It is available through the world-wide-web at this URL:
 * http://involic.com/license.txt
 * If you are unable to obtain it through the world-wide-web,
 * please send an email to license@involic.com so
 * we can send you a copy immediately.
 *
 * eBay Listener Itegration with PrestaShop e-commerce platform.
 * Adding possibilty list PrestaShop Product dirrectly to eBay.
 *
 * @author      Involic <contacts@involic.com>
 * @copyright   Copyright (c) 2011-2015 by Involic (http://www.involic.com)
 * @license     http://involic.com/license.txt
 */

/**
 * DB Access wrapper class.
 * Used for models, working with grid
 *
 */
class PrestaSelect
{
    const EXTRA_WHERE_KEY = 'EXTRA_WHERE_KEY';

    protected $_collectionSql = null;
    protected $_orderByValue = null;
    protected $_groupBy = null;
    protected $_currentPageNumber = 0;
    protected $_countPerPage = 10;
    protected $_totalCount = null;
    protected $_filter = null;
    protected $_filters = null;
    protected $_joinsList = null;
    protected $_addJoinedFields = null;
    protected $_fields = null;
    protected $_tableName = null;

    public function __construct($tableName)
    {
        $this->_tableName = $tableName;
    }
    /**
     * This method prepare select fields for query
     */
    public function select()
    {
        $this->_collectionSql = "SELECT SQL_CALC_FOUND_ROWS `mt`.* ";
        if (!is_null($this->_addJoinedFields)) {
            foreach ($this->_addJoinedFields as $joinedField) {
                $this->_collectionSql.="," . $joinedField;
            }
        }

        if (!is_null($this->_fields)) {
            $this->_collectionSql.="," . $this->_fields;
        }

        $this->_collectionSql.=" ";

        $this->_collectionSql.="FROM " . _DB_PREFIX_ . $this->_tableName . ' as `mt`';

        return $this;
    }

    public function addFields($fields)
    {
        $this->_fields = $fields;
        $this->select();
    }

    public function getSQL()
    {
        $this->select();
        return $this->_collectionSql;
    }

    public function resetJoin()
    {
        $this->_addJoinedFields = array();

        $this->_joinsList = array();
    }

    // ################ Joins
    /**
     *
     * @param <type> $joinType
     * @param <type> $table
     * @param <type> $fields
     * @param <type> $joinOn 
     */
    public function addJoin($joinType = "left", $table, $fields, $joinOn, $specialField = false)
    {
        if (!is_array($table)) {
            $tableName = $tableAlias = $table;
        } else {
            $tableAlias = array_keys($table);
            $tableAlias = $tableAlias[0];
            $tableName = $table[$tableAlias];
        }

        if (!is_array($fields)) {
            $fields = array($fields);
        }

        $totalFieldsParced = array();
        foreach ($fields as $fieldAlias => $fieldName) {
            if (!$specialField) {
                $totalFieldsParced[] = "`{$tableAlias}`." . $fieldName . (!is_int($fieldAlias) ? " as $fieldAlias" : "");
            } else {
                $totalFieldsParced[] = $fieldName . (!is_int($fieldAlias) ? " as $fieldAlias" : "");
            }
        }

        $this->_addJoinedFields[] = implode(",", $totalFieldsParced);

        $this->_joinsList[] = array(
            'type' => $joinType,
            'table' => $tableName,
            'alias' => $tableAlias,
            'on' => $joinOn
        );

        // Reinit total select
        $this->select();
    }

    // ################ Filtering
    public function setExtraWhere($extraWhere)
    {
        $this->addFilter(self::EXTRA_WHERE_KEY, $extraWhere);
    }

    public function addFilter($key, $filter) {
        if (is_null($this->_filters)) {
            $this->_filters = array();
        }
        $this->_filters[$key] = $filter;
    }
    
    public function addFilters($filtersList)
    {
        if (is_null($this->_filters)) {
            $this->_filters = $filtersList;
        } else if (is_array($this->_filters)) {
            $this->_filters = $this->_filters + $filtersList;
        }
    }

    // ############ Sortings
    public function order($orderByValue = "")
    {
        $this->_orderByValue = $orderByValue;
        return $this;
    }

    /**
     * Group by
     * @param string $groupByValue
     * @return $this
     */
    public function groupBy($groupByValue = "")
    {
        $this->_groupBy = $groupByValue;
        return $this;
    }

    // ############ Paginations

    public function setCurrentPageNumber($page)
    {
        if ($page < 1) {
            $page = 1;
        }
        $this->_currentPageNumber = $page;
        return $this;
    }

    public function setCountPerPage($countInPage)
    {
        $this->_countPerPage = $countInPage;
        return $this;
    }

    public function setTotalItemsCount($totalCount)
    {
        $this->_totalCount = $totalCount;
    }

    public function getTotalItemsCount()
    {
        return $this->_totalCount;
    }

    // ############ Loader
    public function getItems()
    {
        if (is_null($this->_collectionSql)) {
            $this->select();
        }

        $sql = $this->_collectionSql;
        if (!is_null($this->_joinsList)) {
            foreach ($this->_joinsList as $singleJoin) {
                $sql.=" " . $singleJoin['type'] . " JOIN " . $singleJoin['table'] . " as `" . $singleJoin['alias'] . "` on " . $singleJoin['on'];
            }
        }
        if (!is_null($this->_filter) || (!is_null($this->_filters) && $this->_filters != array())) {
            $totalFilter = "";
            !is_null($this->_filter) && $totalFilter.= $this->_filter;

            if (!is_null($this->_filters)) {
                foreach ($this->_filters as $filterVariable => $filterValue) {
                    ($totalFilter != "") && $totalFilter.=" AND ";
                    if ($filterVariable == self::EXTRA_WHERE_KEY) {
                        $totalFilter.= $filterValue;
                        continue;
                    }

                    if (is_int($filterValue)) {
                        $totalFilter.= $filterVariable."=" . $filterValue;
                    } else {
                        $totalFilter.= $filterVariable." LIKE '%" . pSQL($filterValue) . "%'";
                    }
                }
            }
            $totalFilter != "" && $sql.=" WHERE " . $totalFilter;
        }
        if ($this->_groupBy != null) {
            $sql.= " GROUP BY " . $this->_groupBy;
        }

        if ($this->_orderByValue != null) {
            $sql.= " ORDER BY " . $this->_orderByValue;
        }

        if ($this->_currentPageNumber > 0 && $this->_countPerPage > 0) {
            $sql.=" LIMIT " . ($this->_currentPageNumber - 1) * $this->_countPerPage . ", {$this->_countPerPage}";
        }

        $queryResult = Db::getInstance()->ExecuteS($sql, true, false);
        $r = Db::getInstance()->getRow("SELECT FOUND_ROWS() as total", false);
        $this->setTotalItemsCount($r['total']);
        return $queryResult;
    }

}