<?php
/**
 * File Grid.php
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

class Grid
{

    const MASSACTION_TYPE_REDIRECT = 0;
    const MASSACTION_TYPE_SUBMIT = 1;

    /**
     * Columns array
     *
     * array(
     *      'header'    => string,
     *      'width'     => int,
     *      'sortable'  => bool,
     *      'index'     => string,      
     *      'format'    => string
     *      'total'     => string (sum, avg)
     * )
     * @var array
     */
    protected $_columns = array();
    /**
     * Page and sorting var names
     *
     * @var string
     */
    protected $_varNameLimit = 'pagination';
    protected $_varNamePage = 'paginator-page';
    protected $_varNameSort = 'sort-field';
    protected $_varNameDir = 'sort-dir';
    protected $_varNameFilter = 'filter';
    protected $_gridId = null;
    // Pagination, sorting and filtring parameters
    protected $_limit = null;
    protected $_page = null;
    protected $_sort = null;
    protected $_dir = null;
    protected $_filter = null;
    protected $_multiSelect = false;
    // Default pagination, sorting & filtring params value
    protected $_defaultLimit = 20;
    protected $_defaultPage = 1;
    protected $_defaultSort = 'id';
    protected $_defaultDir = "asc";
    protected $_defaultFilter = array();
    /**
     * @var AbstractModel
     */
    protected $_selectModel = null;
    protected $_primaryKeyName = 'id';
    protected $_limits = array(5 => "5", 10 => "10", 20 => "20", 100 => "100", 200 => '200', 999 => "999");
    protected $_loadedItems = null;
    protected $_headerTemplate = "widget/grid/header.phtml";
    protected $_contentTemplate = "widget/grid/content.phtml";
    protected $_footerTemplate = "widget/grid/footer.phtml";
    protected $_hiddensList = array();
    protected $_buttonsList = array();

    protected $_buttonsFooterList = array();

    protected $_massactionsList = array();
    protected $_massactionType = self::MASSACTION_TYPE_REDIRECT;
    protected $_gridHeader;
    protected $_shortView = false;

    public function __construct()
    {
        if ($this->_gridId == null) {
            $this->_gridId = uniqid();
        }
        
        $this->init();
    }


    protected function _prepareColumns()
    {
        return $this;
    }

    public function init()
    {
        $this->_reset();
        $this->_restoreCurrentCookiesValue();

        $this->_prepareColumns();
        $this->_prepareCollection();
    }
    
    /**
     * Reset sorting, filtring, pagination variables to default
     */
    protected function _reset()
    {
        $this->_limit = $this->_defaultLimit;
        $this->_page = $this->_defaultPage;
        $this->_sort = $this->_defaultSort;
        $this->_dir = $this->_defaultDir;
        $this->_filter = $this->_defaultFilter;
        if ($this->getSelect()) {
            $this->getSelect()->resetJoin();
        }
    }

    protected function _prepareCollection()
    {
        if (is_null($this->getSelect())) {
            throw new Exception(L::t("Please load select model"));
        }
        // Set Order
        $this->_sort = UrlHelper::getGet($this->_varNameSort, $this->_sort);
        $this->_setLocalCookiesValue('sort', $this->_sort);

        $this->_dir = UrlHelper::getGet($this->_varNameDir, $this->_dir);
        $this->_setLocalCookiesValue('dir', $this->_dir);

        if ($this->_sort != false && in_array($this->_dir, array("asc", "desc"))) {
            $this->getSelect()->order($this->_sort . ' ' . $this->_dir);
        }

        // Initilize filter
        $this->_filter = UrlHelper::getGet($this->_varNameFilter, $this->_filter);
        if (is_string($this->_filter)) {
            $this->_filter = json_decode($this->_filter, true);
        }

        if ($this->_filter != array()) {
            $processedFilterList = array();
            foreach ($this->_filter as $filterKey => $filterValue) {
                if (trim($filterValue) == "") {
                    unset($this->_filter[$filterKey]);
                    continue;
                }
                if (isset($this->_columns[$filterKey]['filterKey'])) {
                    $processedFilterList[$this->_columns[$filterKey]['filterKey']] = $filterValue;
                } else {
                    $processedFilterList['`mt`.'.$filterKey] = $filterValue;
                }
            }
            $this->getSelect()->addFilters($processedFilterList);
        }

        // Set Pagination
        $this->_limit = UrlHelper::getGet($this->_varNameLimit, $this->_limit);
        $this->_limit != $this->_defaultLimit && $this->_setGlobalCookiesValue('limit', $this->_limit);

        $allPages = $this->getLimit() == 999;
        $this->_page = UrlHelper::getGet($this->_varNamePage, $this->_page);
        if ($this->_page <= 0) {
            $this->_page = 1;
        }

        $allPages && $this->_page = 1;

        // Check for click on button Reset
        if (UrlHelper::getGet("submitResetmarketplaces", false)) {
            $this->_reset();
        }

        $this->getSelect()->setCurrentPageNumber($this->getCurrentPage());
        $this->getSelect()->setCountPerPage($this->getLimit());

        // Preload collection
        $this->getSelect()->getItems(false);

        if (!$allPages && $this->_page > $this->getTotalPages()) {
            // Current page more that total number of page reloading collection
            $this->_page = $this->getTotalPages();
            $this->getSelect()->setCurrentPageNumber($this->getCurrentPage());
            $this->getSelect()->getItems(false);
        }

        return $this;
    }

    /**
     * Set grid values based on cookies information.
     *
     * For each grid we store individual cookies and also use grid global
     * cookies
     */
    protected function _restoreCurrentCookiesValue()
    {
        $gridSettings = CookiesHelper::get('prestabay_grid_settings', true);
        if (isset($gridSettings[$this->_gridId])) {
            if (!$this->_shortView) {
                // Grid personal settings
                isset($gridSettings[$this->_gridId]['sort']) && $this->_sort = $gridSettings[$this->_gridId]['sort'];
                isset($gridSettings[$this->_gridId]['dir']) && $this->_dir = $gridSettings[$this->_gridId]['dir'];
            }
        }
        // Grid global settings
        isset($gridSettings['global']['limit']) && $this->_limit = $gridSettings['global']['limit'];
    }

    /**
     * Reset cookies value for current grid
     */
    protected function _resetLocalCookiesSettings()
    {
        $gridSettings = CookiesHelper::get('prestabay_grid_settings', true);
        !is_array($gridSettings) && $gridSettings = array();

        $gridSettings[$this->_gridId] = array();
        CookiesHelper::set('prestabay_grid_settings', $gridSettings);
    }

    /**
     * Set current grid setting
     *
     * @param string $setting
     * @param mixed $value
     */
    protected function _setLocalCookiesValue($setting, $value)
    {
        if ($this->_shortView) {
            return;
        }

        $gridSettings = CookiesHelper::get('prestabay_grid_settings', true);

        if (is_array($gridSettings)) {
            $gridSettings = array();
        }

        if (!isset($gridSettings[$this->_gridId])) {
            $gridSettings[$this->_gridId] = array();
        }

        $gridSettings[$this->_gridId][$setting] = $value;
        CookiesHelper::set('prestabay_grid_settings', $gridSettings);
    }

     /**
     * Set global grid setting
     *
     * @param string $setting
     * @param mixed $value
     */
    protected function _setGlobalCookiesValue($setting, $value)
    {
        $gridSettings = CookiesHelper::get('prestabay_grid_settings', true);
        !is_array($gridSettings) && $gridSettings = array();

        !isset($gridSettings['global']) && $gridSettings['global'] = array();

        $gridSettings['global'][$setting] = $value;
        CookiesHelper::set('prestabay_grid_settings', $gridSettings);
    }

    /**
     *
     * @param bool $cache on true use cache value if exist, else alway make query to db
     */
    public function getItems($cache = true)
    {
        if ($cache && !is_null($this->_loadedItems)) {
            return $this->_loadedItems;
        }

        return $this->_loadedItems = $this->getSelect()->getItems();
    }

    /**
     * @return PrestaSelect
     */
    public function getSelect()
    {
        if (!is_null($this->_selectModel)) {
            return $this->_selectModel->getSelect();
        }

        throw new Exception(L::t('Select object not initialized'));
    }

    /**
     * Get Select Model
     *
     * @return AbstractModel
     */
    public function getSelectModel()
    {
        return $this->_selectModel;
    }

    /**
     * Set Select Model
     *
     * @param $select
     */
    public function setSelect($select)
    {
        $this->_selectModel = $select;
    }

    /**
     * Add column to grid
     */
    public function addColumn($columnId, $columnData)
    {
        if (is_array($columnData)) {
            $columnData['columnId'] = $columnId;

            if (!isset($columnData['index'])) {
                $columnData['index'] = $columnId;
            }
            $this->_columns[$columnId] = $columnData;
        } else {
            throw new Exception(L::t('Wrong column format'));
        }

//        $this->_columns[$columnId]->setId($columnId);
        $this->_lastColumnId = $columnId;

        return $this;
    }

    public function getPrimaryKeyName()
    {
        return $this->_primaryKeyName;
    }

    /**
     * Retrieve grid column by column id
     *
     * @param   string $columnId
     * @return  array || false
     */
    public function getColumn($columnId)
    {
        if (!empty($this->_columns[$columnId])) {
            return $this->_columns[$columnId];
        }
        return false;
    }

    /**
     * Retrieve all grid columns
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->_columns;
    }

    // ############# Defaultes setter
    public function setDefaultLimit($limit)
    {
        $this->_defaultLimit = $limit;
        return $this;
    }

    public function setDefaultPage($page)
    {
        $this->_defaultPage = $page;
        return $this;
    }

    public function setDefaultSort($sort)
    {
        $this->_defaultSort = $sort;
        return $this;
    }

    public function setDefaultDir($dir)
    {
        $this->_defaultDir = $dir;
        return $this;
    }

    public function setDefaultFilter($filter)
    {
        $this->_defaultFilter = $filter;
        return $this;
    }

    // ########## Templates

    public function setHeader($headerText)
    {
        $this->_gridHeader = $headerText;
    }

    public function getHeader()
    {
        return $this->_gridHeader;
    }

    public function getHeaderTemplate()
    {
        return $this->_headerTemplate;
    }

    public function getContentTemplate()
    {
        return $this->_contentTemplate;
    }

    public function getFooterTemplate()
    {
        return $this->_footerTemplate;
    }

    // ############ Main getters

    public function getGridId()
    {
        return $this->_gridId;
    }

    public function getGridUrl()
    {
        return '';
    }

    public function getCurrentPage()
    {
        return $this->_page;
    }

    public function isShortView()
    {
        return $this->_shortView;
    }

    /**
     * Calculate total numbers of page for grid.
     *
     * Page number is total page with limit in each page. Performed based on
     * loaded model object
     *
     * @return int number on page (not record)
     */
    public function getTotalPages()
    {
        return (int) ceil($this->getSelect()->getTotalItemsCount() / $this->getLimit());
    }

    /**
     * Total items in loaded object collection
     *
     * Return actual numbers of items in select.
     *
     * @return int numbers of items
     */
    public function getTotalItemsCount()
    {
        return $this->getSelect()->getTotalItemsCount();
    }

    public function getLimits()
    {
        return $this->_limits;
    }

    public function getLimit()
    {
        return $this->_limit;
    }

    public function getMultiSelect()
    {
        return $this->_multiSelect;
    }

    public function getFilters()
    {
        return!is_null($this->_filter) ? $this->_filter : array();
    }

    /**
     * Retrive filed for witch we sort data in grid
     *
     * @return String row identify with sorted
     */
    public function getSortField()
    {
        return $this->_sort;
    }

    public function getSortDir()
    {
        return $this->_dir;
    }

    /**
     * Adding new button to Grid Header Part.
     *
     * @param int $id Unique button id. Used for identify button on page, and on array
     * @param array $params different html button params
     */
    public function addButton($id, $params, $actionHandler = null)
    {
        if (!isset($params['onclick'])) {
            $params['class'] .= " controll-button";
        }
        $button = new ActionButton($id, $params, $actionHandler);
        $this->_buttonsList[$id] = $button;
    }

    public function getButtons()
    {
        return (!is_null($this->_buttonsList)) ? $this->_buttonsList : array();
    }

    public function addHidden($id, $params = array())
    {
        $button = new HiddenField($id, $params);
        $this->_hiddensList[$id] = $button;
    }
    
    public function getHiddens()
    {
        return (!is_null($this->_hiddensList)) ? $this->_hiddensList : array();
    }

    public function addFooterButton($id, $params, $actionHandler = null)
    {
        $button = new ActionButton($id, $params, $actionHandler);
        $this->_buttonsFooterList[$id] = $button;
    }

    public function getFooterButtons()
    {
        return (!is_null($this->_buttonsFooterList)) ? $this->_buttonsFooterList : array();
    }

    public function addMassaction($label, $url)
    {
        $this->_massactionsList[] = array(
            'label' => $label,
            'url' => $url
        );
    }

    public function getMessactions()
    {
        return $this->_massactionsList;
    }

    public function isMassactions()
    {
        return count($this->_massactionsList) > 0;
    }

    public function getMassactionsType()
    {
        return $this->_massactionType;
    }
    /**
     * Retrieve grid HTML
     *
     * @return string
     */
    public function getHtml($showOutput = true)
    {

        return RenderHelper::view("widget/grid.phtml", array(
                    'grid' => $this
                ), $showOutput);
    }

}