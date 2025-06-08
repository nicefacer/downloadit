<?php


abstract class AjaxGrid extends Grid
{
    const TYPE_INFO = 'info';
    const TYPE_BUTTON_ACTION = 'buttonAction';
    const TYPE_CUSTOM_ACTION = 'customAction';

    protected $rendererCache = array();

    /**
     * Get Grid configuration array
     *
     * @return array
     */
    public function getConfig()
    {
        return array(
            'gridUrl' => $this->getGridUrl(),
            'gridButtons' => $this->getButtons(),
            'footerButtons' => $this->getFooterButtons(),
        );
    }

    /**
     * Get main grid information
     *
     * @return array
     */
    protected function getInfo()
    {
        return array(
            'gridId' => $this->getGridId(),
            'header' => $this->getHeader(),
            'multiSelect' => $this->getMultiSelect(),
            'columns' => array_values($this->getColumns()),
            'sortField' => $this->getSortField(),
            'sortDir' => $this->getSortDir(),
            'filters' => $this->getFilters(),
            'items' => $this->getRenderedItems(),
            'primaryKeyName' => $this->getPrimaryKeyName(),
            'totalItemsCount' => $this->getTotalItemsCount(),
            'page' => $this->getCurrentPage(),
            'totalPages' => $this->getTotalPages(),
            'massactions' => $this->getMessactions(),
            'massactionType' => $this->getMassactionsType(),
            'limit' => $this->getLimit(),
            'limits' => $this->getLimits()
        );
    }

    /**
     * Get rendered items for grid
     *
     * @return array list of items in grid already rendered in html
     */
    protected function getRenderedItems()
    {
        $columns = $this->getColumns();
        $items = $this->getItems();

        $itemsHtml = array();
        $index = 0;

        $primaryKeyName = $this->getPrimaryKeyName();
        foreach ($items as $row) {

            foreach ($columns AS $columnId => $params) {
                $render = $this->getRenderer(isset($params['type']) ? $params['type'] : "text");
                $key = isset($params['index']) ? $params['index'] : $columnId;

                $itemsHtml[$index][$key] = $render->render($key, $row, $params, $this);
            }

            if (!isset($itemsHtml[$index][$primaryKeyName])) {
                $itemsHtml[$index][$primaryKeyName] = $row[$primaryKeyName];
            }
            $itemsHtml[$index]['checked'] = 0;

            $index++;
        }

        return $itemsHtml;
    }

    /**
     * Return renderer for specific column type
     *
     * @param string $columnType column type
     *
     * @return Grid_AbstractRenderer
     */
    protected function getRenderer($columnType)
    {
        $renderClassName = "Grid_" . ucfirst(strtolower($columnType)) . "Renderer";
        if (!isset($this->rendererCache[$renderClassName])) {
            $this->rendererCache[$renderClassName] = new $renderClassName();
        }

        return $this->rendererCache[$renderClassName];
    }

    /**
     * Handle grid control request
     *
     * @param array $getRequest
     * @param array $postRequest
     *
     * @return bool|array result of request handling
     */
    public function handleRequest($getRequest = array(), $postRequest = array())
    {
        try {
            if (!empty($getRequest['type'])) {
                return $this->handleGetRequest($getRequest);
            }

            if (!empty($postRequest)) {
                return $this->handlePostRequest($postRequest);
            }
        } catch (Exception $ex) {
            return array('success' => false, 'message' => $ex->getMessage());
        }

        return false;
    }

    /**
     * Handle GET request for ajax grid
     * Each request processing to grid should have type
     *
     * @param $getRequest
     * @return array result of execution
     *
     * @throws Exception
     */
    protected function handleGetRequest($getRequest)
    {
        if (empty($getRequest['type'])) {
            throw new Exception('Empty request type');
        }

        switch ($getRequest['type']) {
            case self::TYPE_INFO:
                return $this->getInfo();
            default:
                throw new Exception('Unknown request type');

        }
    }

    /**
     * Handle POST request for ajax grid
     *
     * @param array $postRequest
     * @return array result of execution
     *
     * @throws Exception
     */
    protected function handlePostRequest($postRequest)
    {
        if (empty($postRequest['type'])) {
            throw new Exception('Empty request type');
        }

        switch ($postRequest['type']) {
            case self::TYPE_BUTTON_ACTION:
                return $this->buttonAction(
                    $postRequest['buttonName'],
                    $postRequest['selectedIds'],
                    isset($postRequest['extraValue']) ? $postRequest['extraValue'] : false
                );

            case self::TYPE_CUSTOM_ACTION:
                if (!isset($postRequest['actionName'])) {
                    throw new Exception('Please define action name');
                }
                return $this->customAction($postRequest['actionName'], $postRequest);

            default:
                throw new Exception('Unknown request type');
        }
    }

    /**
     * @param $buttonId
     * @param array $selectedIds selected ids items
     * @param mixed $extraValue additional value passed to request
     *
     * @return array result of execution
     */
    protected function buttonAction($buttonId, $selectedIds, $extraValue = false)
    {
        $buttonsList = $this->getButtons();
        $buttonsList = array_merge($buttonsList, $this->getFooterButtons());

        if (!isset($buttonsList[$buttonId])) {
            return array('success' => false, 'message' => L::t('Unknown button handler'));
        }
        /** @var ActionButton $actionButton */
        $actionButton = $buttonsList[$buttonId];
        $result = $actionButton->execute($selectedIds, $extraValue);

        return array('success' => true, 'result' => $result);
    }

    /**
     * Handle custom action
     *
     * @param string $actionName
     * @param array $postRequest request array
     *
     * @return mixed execution result
     * @throws Exception
     */
    protected function customAction($actionName, $postRequest)
    {
        $methodName = $actionName . 'Action';
        if (!method_exists($this, $methodName)) {
            throw new Exception('Custom handler does not exist');
        }

        return call_user_func(array($this, $methodName), $postRequest);
    }
}
