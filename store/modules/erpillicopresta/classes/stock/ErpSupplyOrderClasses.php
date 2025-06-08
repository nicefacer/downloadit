<?php
/**
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    Illicopresta SA <contact@illicopresta.com>
*  @copyright 2007-2015 Illicopresta
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class ErpSupplyOrderClasses extends SupplyOrder
{

	/*
	* @var string Reference Model
	*/
	public static $reference_model = 'SO';

	/*
	 * @var integer number of chiffre of reference Model
	*/
	public static $reference_digit_number = '6';

	/**
	* Returns the next supply order reference
	*
	* @return bool|SupplyOrder
	*/
	public static function getNextSupplyOrderReference()
	{
		//get prefix of supply order reference
		$reference_model  = Configuration::get('PREFIX_REFERENCE_SUPPLY_ORDER');

		if (empty($reference_model))
				$reference_model = self::$reference_model;

		$query = new DbQuery();
		$query->select('reference');
		$query->from('supply_order', 'so');
		$query->where('so.reference LIKE "'.pSQL($reference_model).'%"');
		$query->orderBy('so.reference DESC');
		$ref = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);

                
                
                // Check if there's an order number from a template
                if(count($explodeRef = explode(' ', $ref)) > 0)
                    $ref = $explodeRef[0];
               
		if (!empty($ref))
		{
			preg_match("/[0-9]+$/", (pSQL($ref)) , $matches);
                        if($matches != null)
                        {
                            $new_ref_number = (int)$matches[0] + 1;
                            $new_ref = $reference_model.str_pad($new_ref_number, self::$reference_digit_number, "0", STR_PAD_LEFT);
                            return $new_ref;
                        }
                        else
                            return false;
		}
		else
		{
			$new_ref = $reference_model.str_pad('1', self::$reference_digit_number, "0", STR_PAD_LEFT);
			return $new_ref;
		}
	}

        /**
	 * search product for multi select product pop-up
	 *
	 */
	static public function	searchProduct($id_supplier,$id_category, $id_manufacturer, $id_currency)
	{

		// get supplier id
		$id_supplier = ( !empty($id_supplier)) ? (int)$id_supplier : false;

				// get category id
		$id_category = ( !empty($id_category)) ? (int)$id_category : false;

				// get manufacturer id
				$id_manufacturer = ( !empty($id_manufacturer)) ? (int)$id_manufacturer : false;

		// gets the currency
		$id_currency = ( !empty($id_currency)) ? (int)$id_currency : false;

		// get lang from context
		$id_lang = (int)Context::getContext()->language->id;

				// Get trash category
				$trash_category_id = self::getTrashCategory();

		$query = new DbQuery();
		$query->select('
			CONCAT(p.id_product, \'_\', IFNULL(pa.id_product_attribute, \'0\')) as id,
			ps.product_supplier_reference as supplier_reference,
			IFNULL(pa.reference, IFNULL(p.reference, \'\')) as reference,
			IFNULL(pa.ean13, IFNULL(p.ean13, \'\')) as ean13,
			IFNULL(pa.upc, IFNULL(p.upc, \'\')) as upc,
			md5(CONCAT(\''._COOKIE_KEY_.'\', p.id_product, \'_\', IFNULL(pa.id_product_attribute, \'0\'))) as checksum,
			IFNULL(CONCAT(pl.name, \' : \', GROUP_CONCAT(DISTINCT agl.name, \' - \', al.name ORDER BY agl.name, \' - \', al.name ASC SEPARATOR \', \')), pl.name) as name,
			p.id_supplier as id_default_supplier,
			CASE WHEN (( TRIM(al.name) REGEXP "^[0-9]+$")) THEN LPAD( al.name ,"8", "0") ELSE al.name END as tri_al_name
		');

		$query->from('product', 'p');

		$query->innerJoin('product_lang', 'pl', 'pl.id_product = p.id_product AND pl.id_lang = '.$id_lang);
		$query->leftJoin('product_attribute', 'pa', 'pa.id_product = p.id_product');
		$query->leftJoin('product_attribute_combination', 'pac', 'pac.id_product_attribute = pa.id_product_attribute');
		$query->leftJoin('attribute', 'atr', 'atr.id_attribute = pac.id_attribute');
		$query->leftJoin('attribute_lang', 'al', 'al.id_attribute = atr.id_attribute AND al.id_lang = '.$id_lang);
		$query->leftJoin('attribute_group_lang', 'agl', 'agl.id_attribute_group = atr.id_attribute_group AND agl.id_lang = '.$id_lang);
		$query->leftJoin('product_supplier', 'ps', 'ps.id_product = p.id_product AND ps.id_product_attribute = IFNULL(pa.id_product_attribute, 0)');

		//$query->where('(pl.name LIKE \'%'.$pattern.'%\' OR p.reference LIKE \'%'.$pattern.'%\' OR ps.product_supplier_reference LIKE \'%'.$pattern.'%\')');
		$query->where('p.id_product NOT IN (SELECT pd.id_product FROM `'._DB_PREFIX_.'product_download` pd WHERE (pd.id_product = p.id_product) AND (pd.active = 1))');
		$query->where('p.is_virtual = 0 AND p.cache_is_pack = 0');

                //filter by supplier
		if ($id_supplier)
			$query->where('ps.id_supplier = '.$id_supplier.' OR p.id_supplier = '.$id_supplier);

				if ($id_category || !empty($trash_category_id))
					$query->leftJoin('category_product', 'pc', 'pc.id_product = p.id_product');

				//filter by categorie
		if ($id_category)
					$query->where('p.id_category_default = '.$id_category.' OR pc.id_category = '.$id_category);

				if (!empty($trash_category_id))
					$query->where('p.id_category_default != '.$trash_category_id.' AND pc.id_category != '.$trash_category_id);

				//filter by manufacturer
		if ($id_manufacturer)
					$query->where('p.id_manufacturer = '.$id_manufacturer);

		$query->groupBy('p.id_product, pa.id_product_attribute');

		$query->orderBy(' pl.name  ASC, agl.name ASC, tri_al_name ASC ');


		$items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

		foreach ($items as &$item)
		{
			$ids = explode('_', $item['id']);

						//New - display prices
						$prices = self::getWholesalePrice( $ids[0], $ids[1] , $id_supplier);

						if (!empty($prices))
							$item['unit_price_te'] = Tools::convertPriceFull( $prices, new Currency((int)$id_currency), new Currency($id_currency));
						else
							$item['unit_price_te'] = '';
		}
		if ($items)
					return $items;
	}

	/* Returns the purchase price of a product or a variation */
	public static function getWholesalePrice($id_product, $id_product_attribute = 0, $id_supplier = 0)
	{

		// If there's a supplier
		if (!empty($id_supplier))
		{
			// Fetch supplier's price first
			$prices = ProductSupplierCore::getProductSupplierPrice($id_product, $id_product_attribute, $id_supplier, true);
			if (isset($prices['product_supplier_price_te']))
				$price = $prices['product_supplier_price_te'];
		}

		// If no price for this supplier, or supplier's price null, we look for the price of the product or variation
		if (empty($price) || $price == '0.000000')
		{

			// If no variation, look for product's price
			if ($id_product_attribute == 0)
			{
				$query = new DbQuery();
				$query->select('wholesale_price');
				$query->from('product');
				$query->where('id_product = '.(int)$id_product);
				$price = Db::getInstance()->getValue($query);
			}
			// Variation price
			else
			{
				$query = new DbQuery();
				$query->select('p.wholesale_price as wholesale_price_product, pa.wholesale_price as wholesale_price_product_attribute');
				$query->from('product_attribute','pa');
				$query->where('pa.id_product = '.(int)$id_product);
				$query->where('pa.id_product_attribute = '.(int)$id_product_attribute);
				$query->innerJoin('product', 'p', ' p.id_product = pa.id_product');
				$prices = Db::getInstance()->getRow($query);

				// If variation's price
				if (!empty($prices['wholesale_price_product_attribute']) AND $prices['wholesale_price_product_attribute'] != '0.000000')
					$price = $prices['wholesale_price_product_attribute'];

				// Else product's price
				elseif (!empty($prices['wholesale_price_product']) AND $prices['wholesale_price_product'] != '0.000000')
					$price = $prices['wholesale_price_product'];

				// Else ZERO
				else
					$price = '0.00000';
			}
		}

		return $price;
	}

	/*
	 * Returns the quantity sold of a product between two dates (for x months rolling)
	*/
	static public function getQuantitySales($id_product, $id_product_attribute)
	{
				// Number of months rolling
				$rolling_months_nb = Configuration::get('ERP_ROLLING_MONTHS_NB_SO');

				if (!empty($rolling_months_nb))
				{
					$date_to = date('Y-m-d 00:00:00');

					// Date construction according to the number of months rolling
					$date_from = date('Y-m-d 00:00:00', self::getMonthsAgo(time(), $rolling_months_nb));
				}
				else
					return;


		$sql = 'SELECT  SUM(od.product_quantity)  as quantity_sales 
				FROM `'._DB_PREFIX_.'orders` o 
				LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.id_order = od.id_order 
				WHERE o.date_add >= \''.$date_from.'\' AND o.date_add < \''.$date_to.'\' 
					AND o.valid = 1 
					AND od.product_id = '.(int)$id_product.' AND od.product_attribute_id = '.(int)$id_product_attribute.' 
								GROUP BY od.product_id , od.product_attribute_id ';
      
		$r = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
				return (int)$r['quantity_sales'];
	}

	static public function getProductSalesForecasts($id_product, $id_product_attribute)
	{
		// Recovering the configuration to know the weighting by M
		//init prevision
		$sales_forecasts = 0;
		$coefficients = Configuration::get('ERP_COEFFICIENTS');
                $coefs_sum = 0;

		// calculating the number of order for this product from M-1 to M-6
		for($i = 1; $i<= 6; $i++)
		{
			//M - $1
                        $date_from_m = date('Y-m-d 00:00:00', self::getMonthsAgo(time(), $i));
			$date_to_m   = date('Y-m-d 00:00:00', self::getMonthsAgo(time(), $i - 1));

			// Management harsh sales
			$except_order_limit = Configuration::get('ERP_EXCEPTIONAL_ORDER_LIMIT');

			if (!empty($except_order_limit))
				$order_number = self::countProductsSales($id_product, $id_product_attribute, $date_from_m, $date_to_m, $except_order_limit);
			else
				$order_number = self::countProductsSales($id_product, $id_product_attribute, $date_from_m, $date_to_m);
			// weighting coefficient
			$coef_pond = self::getCoefficient($i, $coefficients);
                        $coefs_sum += $coef_pond;
                        
			$sales_forecasts_init = $order_number * $coef_pond;
			$sales_forecasts += $sales_forecasts_init;

			/*echo 'M - '.$i.'<br/>';
			echo 'coef pond - '.$coef_pond.'<br/>';
			echo $date_from_m.' -> '.$date_to_m.'<br/>';
			echo 'Nombre de commande '.$order_number.'<br/>';
			echo 'Nombre de commande a pres ponderation '.$sales_forecasts_init.'<br/><br/>';*/
		}
                
		// calculating the weighted average value
		$sales_forecasts = $sales_forecasts / $coefs_sum;
                
		return $sales_forecasts;
	}

	static public function getProductSalesForecastsByPeriod($id_product, $id_product_attribute)
	{

		// To recover the amount sold in the same period last year
		$date_old_from = date('Y-m-d 00:00:00', self::getMonthsAgo(time(), 12));
		$date_old_to   = date('Y-m-d 00:00:00', self::getMonthsAgo(time(), 12) + (Configuration::get('ERP_PROJECTED_PERIOD') * 24 * 60 * 60));

		// To recover the amount sold six-month rolling in the same period last year
		$date_old_from_m = date('Y-m-d 00:00:00', self::getMonthsAgo(time(), 12 + Configuration::get('ERP_COMPARISON_PERIOD')));
		$date_old_to_m   = date('Y-m-d 00:00:00', self::getMonthsAgo(time(), 12));

		// To recover the amount sold in the six-month rolling this year
		$date_now_from_m = date('Y-m-d 00:00:00', self::getMonthsAgo(time(), Configuration::get('ERP_COMPARISON_PERIOD')));
		$date_now_to_m   = date('Y-m-d 00:00:00', time());

		$except_order_limit = Configuration::get('ERP_EXCEPTIONAL_ORDER_LIMIT');

		$quantity_sold_old = 0;
		$quantity_sold_old_m = 0;
		$quantity_sold_new_m = 0;

		if (!empty($except_order_limit))
		{
			$quantity_sold_old = self::countProductsSales($id_product, $id_product_attribute, $date_old_from, $date_old_to, $except_order_limit);
			$quantity_sold_old_m = self::countProductsSales($id_product, $id_product_attribute, $date_old_from_m, $date_old_to_m, $except_order_limit);
			$quantity_sold_new_m = self::countProductsSales($id_product, $id_product_attribute, $date_now_from_m, $date_now_to_m, $except_order_limit);
		}
		else
		{
			$quantity_sold_old = self::countProductsSales($id_product, $id_product_attribute, $date_old_from, $date_old_to);
			$quantity_sold_old_m = self::countProductsSales($id_product, $id_product_attribute, $date_old_from_m, $date_old_to_m);
			$quantity_sold_new_m = self::countProductsSales($id_product, $id_product_attribute, $date_now_from_m, $date_now_to_m);
		}
                
		// if (!empty($except_order_limit))
                // {
			// $quantity_sold_old_m = self::countProductsSales($id_product, $id_product_attribute, $date_old_from_m, $date_old_to_m, $except_order_limit);
                // }else{
			// $quantity_sold_old_m = self::countProductsSales($id_product, $id_product_attribute, $date_old_from_m, $date_old_to_m);
                // }
                
		// if (!empty($except_order_limit))
                // {
			// $quantity_sold_new_m = self::countProductsSales($id_product, $id_product_attribute, $date_now_from_m, $date_now_to_m, $except_order_limit);
                // }else{
			// $quantity_sold_new_m = self::countProductsSales($id_product, $id_product_attribute, $date_now_from_m, $date_now_to_m);
                // }
                
		$quantity_forecast = $quantity_sold_old;
		if ($quantity_sold_old_m > 0 && $quantity_sold_new_m > 0)
		{
			$pourcentage = (($quantity_sold_new_m - $quantity_sold_old_m) / $quantity_sold_old_m) * 100;
			$quantity_forecast = $quantity_sold_old + ($quantity_sold_old * $pourcentage / 100);
			return $quantity_forecast;
		}
		return 0;
	}

        /**
	 * Returns progressions of sales between m and m-1
	 *
	 * @param int $id_product
	 * @param int $id_product_attribute
	 *
	 * @return int
	 *
	*/
	static public function getProductSalesGains($id_product, $id_product_attribute)
	{

		//init
		$sales_gains = 0;

		// Quantity sold in the current month
		$query = '  SELECT SUM(product_quantity) as product_total_quantity
		FROM '._DB_PREFIX_.'orders o
		INNER JOIN '._DB_PREFIX_.'order_detail od ON o.id_order = od.id_order
		WHERE od.product_id = '.(int)$id_product.' AND od.`product_attribute_id` = '.(int)$id_product_attribute.'
					AND o.date_add BETWEEN \''.date('Y-m-01 00:00:00').'\' AND \''.date('Y-m-d 23:59:59', strtotime('last day of this month')).'\'
					AND o.valid = 1
					GROUP BY od.product_id , od.product_attribute_id ';


		// Result for the current month
		$result_curent_month =  Db::getInstance()->getValue($query);

		// Amount sold in the last month
		$query = '  SELECT SUM(product_quantity) as product_total_quantity
		FROM '._DB_PREFIX_.'orders o
		INNER JOIN '._DB_PREFIX_.'order_detail od ON o.id_order = od.id_order
		WHERE od.product_id = '.(int)$id_product.' AND od.`product_attribute_id` = '.(int)$id_product_attribute.'
					AND o.date_add BETWEEN \''.date('Y-m-01 00:00:00', self::getMonthsAgo(time(),1)).'\' AND \''.date('Y-m-d 23:59:59', strtotime('last day of this month', self::getMonthsAgo(time(), 1))).'\'
					AND o.valid = 1
					GROUP BY od.product_id , od.product_attribute_id  ';

		// Result for the last month
		$result_prev_month =  Db::getInstance()->getValue($query);

		/*
		 * Calculate sales growth
		 * ( QTY M  -  QTY M-1) / QTY M-1) * 100
		*/


		// If the result for the last month is not null
		if (!empty($result_prev_month))
			$sales_gains =  round(( ( ( $result_curent_month  - $result_prev_month) / $result_prev_month) * 100), 2). ' %';
		else
			$sales_gains =  'ND';

		// Returns value
		return $sales_gains;
	}

	/*
	* Recovery of the trash category
	*
	*/
	static public function getTrashCategory()
	{
		//init var
		$trash_category_id = '';

		// This category is called "Miscellaneous"
		$category = Category::getCategories( false, false, false, " AND cl.name = 'Divers' ");;
		if (!empty($category))
			$trash_category_id = (int)$category[0]['id_category'];

		return $trash_category_id;
	}

	/*
	* Stock level
	* Returns the stock level clor of a product according to its real quantity
	*
	*	Overstock : violet
	*	Normal : black
	*	Warning : orange
	*	Rupture : red
	*
	*	No registered level : grey
	*/
	public static function getStockLevelColor($real_quantity)
	{
		  if ($real_quantity == '0')
				return 'red';

		  // recovery stock level
		 $level_alerte = (int)Configuration::get('ERP_LEVEL_STOCK_ALERT');
		 $level_normal = (int)Configuration::get('ERP_LEVEL_STOCK_NORMAL');

		 if (empty($level_alerte) && empty($level_alerte))
				return 'gray';

		  if ($real_quantity > '0' &&  $real_quantity <=  $level_alerte)
				return 'orange';

		  if ($real_quantity > $level_alerte &&  $real_quantity <=  $level_normal)
				return 'black';

		  if ($real_quantity > $level_normal)
				return 'purple';
	}


	/**
	 * @static
	 * @param $id_supply_order array
	 * @return array collection of OrderInvoice
	 */
	public static function getSupplyOrderCollection($id_supply_order)
	{
		if (!empty( $id_supply_order))
		{
		$supply_order_list = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT so.*
			FROM `'._DB_PREFIX_.'supply_order` so
			WHERE so.id_supply_order = '.(int)$id_supply_order.'
			ORDER BY so.`id_supply_order` DESC');

		return ObjectModel::hydrateCollection('SupplyOrder', $supply_order_list);
		}
	}

        static public function countProductsSales($id_product, $id_product_attribute, $date_from, $date_to, $limit = 0)
        {
            $id_state = Configuration::get('ERP_SO_STATE_TO_PRODUCT_SALES');
            
            $query = '  SELECT SUM(product_quantity) as product_total_quantity 
                        FROM '._DB_PREFIX_.'orders o 
                        INNER JOIN '._DB_PREFIX_.'order_detail od ON o.id_order = od.id_order 
                        WHERE od.product_id = '.(int)$id_product.' AND od.`product_attribute_id` = '.(int)$id_product_attribute.' 
                        AND o.date_add >= \''.pSQL($date_from).'\' AND o.date_add < \''.pSQL($date_to).'\' AND current_state = '.(int)$id_state;
            
                if ($limit > 0)
                    $query .= ' AND od.product_quantity <= '.(int)$limit;



            $result =  Db::getInstance()->executeS($query);

            // Only if we have results on this product between the given dates
            $product_total_quantity = ($result[0]['product_total_quantity'] == NULL) ? 0 : $result[0]['product_total_quantity'];

            return $product_total_quantity;
        }
        
        /*  Calculation of sales forecasts
         *
         *  For a given product : id_product and/or id_product_attribute
         * 
         *      1. fetch for each past month (M-1 M-2 M-3 M-4 M-5 M-6)
         *          a) Number of orders in winch the product is
         *      
         *      2. for each month the weighted actual demand is calculated
         * 
         *                      M-6     M-5       M-4       M-3       M-2       M-1
         *       Coef weighted       60%       80%       100%     100%     120%     140%
         * 
         *      3. after obtaining weighted actual requests, taking their average values
         *          a) Weighted real demands sums / 6
         *      
         *     4. Sales forecast is calculated over 15 days
         *          a) forecast = ( Weighted real demands sums / 6 ) / 2     
         * 
         **/
        
        static public function getCoefficient($number, $coefficients)
        {
            $coeffs = explode(";", $coefficients);
            $i = 1;
            foreach ($coeffs as $coeff)
            {
                if ($i == $number)
                    return $coeff;
                $i = $i + 1;
            }
        }
        
        
        /**
        *Returns timestamp of the specified date minus the number of months.
        *This function is making date to date in possible : march 10th - 1 month = february 10th
        *Else retruns last month day
        *Examples : May 31th - 1 month = April 30th
        *	    May 31th - 2 month = March 31th
        *	    etc.
        *
        *	    March 31th or 30th - 1 mois = February 28th
        *	    March 31th or 30th - 2 mois = January 31th or 30th
        *
        * Can not handle negative numbers month. The result is returned est $initial_date.
        *
        *Parameters : $initial_date : timestamp
        *             $nb_months : int
        */
        static public function getMonthsAgo ($initial_date, $nb_months)
        {
                // day number of $initial_date : from 1 to 31
                $day = date('j', $initial_date);
                // Init variable containing the result
                $final_date = $initial_date;

                /* Hours change management */
                $heure_ete = date ('I', $initial_date);

                // if $day = 31, can't have "date to date", so we will look on the last day of the previous month
                if ($day == 31)
                {
                        // Every month is get one by one since initial date to push the final date with the number of seconds contained in the month
                        for ($i = 1 ; $i <= $nb_months ; $i++)
                        {
                                // Number of days covered in month * number of seconds in a day
                                $final_date -= date('t', $final_date)*24*3600;

                                /* Hours change management */
                                if ($heure_ete == 1 && date('I', $final_date) == 0)
                                {
                                        // One hour is added
                                        $final_date += 3600;
                                        // On repasse en heure d'hiver
                                        $heure_ete = 0;
                                }
                                if ($heure_ete == 0 && date('I', $final_date) == 1)
                                {
                                        // One hour is removed
                                        $final_date -= 3600;
                                        // summer time
                                        $heure_ete = 1;
                                }
                        }
                }
                // Else "date to date" is ok
                else
                {
                        // Every month is get one by one since initial date to push the final date with the number of seconds contained in the month
                        for ($i = 1 ; $i <= $nb_months; $i++)
                        {
                                // Number of days covered in past month
                                $nb_days = date('t', $final_date - $day*24*3600);

                                // If February,
                                // and initial date is 30 (or 29 for non bisextiles years)
                                // can't remove number of days in february month because March 1st or 2nd will be returned
                                // This would shift the result ...
                                if (date('n', $final_date - $day*24*3600) == 2 && $day > $nb_days)
                                {
                                        // Removing as many days as necessary to empty the days of March
                                        $final_date -= $day*24*3600;
                                        //If there are still months to process
                                        if ($i < $nb_months)
                                        {
                                                // February is processed
                                                $final_date -= $nb_days*24*3600;
                                                // Then we continue the calculation recalling the recursive function to get back on the right day of the month
                                                return self::getMonthsAgo($final_date - (31 - $day)*24*3600, $nb_months - ($i+1));
                                        }
                                }
                                else
                                {
                                        // Removing the number of days of the final date of the previous month 
                                        $final_date -= date('t', $final_date - $day*24*3600)*24*3600;
                                }

                                /* Hours change management */
                                if ($heure_ete == 1 && date('I', $final_date) == 0)
                                {
                                        // One hour is added
                                        $final_date += 3600;
                                        // Winter time
                                        $heure_ete = 0;
                                }
                                if ($heure_ete == 0 && date('I', $final_date) == 1)
                                {
                                        // One hour is removed
                                        $final_date -= 3600;
                                        // Summer time
                                        $heure_ete = 1;
                                }
                        }
                }
                return $final_date;
        }

}