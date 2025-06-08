(function () {
    var app = angular.module("categoryMapping", []);

    app.directive('dynamic', function ($compile) {
        return {
            restrict: 'A',
            replace: true,
            link: function (scope, ele, attrs) {
                scope.$watch(attrs.dynamic, function(html) {
                    ele.html(html);
                    $compile(ele.contents())(scope);
                });
            }
        };
    });

    app.directive('optionsDisabled', function($parse) {
        var disableOptions = function(scope, attr, element, data, fnDisableIfTrue) {
            // refresh the disabled options in the select element.
            $("option[value!='?']", element).each(function(i, e) {
                var locals = {};
                locals[attr] = data[i];
                $(this).attr("disabled", fnDisableIfTrue(scope, locals));
            });
        };
        return {
            priority: 0,
            require: 'ngModel',
            link: function(scope, iElement, iAttrs, ctrl) {
                // parse expression and build array of disabled options
                var expElements = iAttrs.optionsDisabled.match(/^\s*(.+)\s+for\s+(.+)\s+in\s+(.+)?\s*/);
                var attrToWatch = expElements[3];
                var fnDisableIfTrue = $parse(expElements[1]);
                scope.$watch(attrToWatch, function(newValue, oldValue) {
                    if(newValue)
                        disableOptions(scope, expElements[2], iElement, newValue, fnDisableIfTrue);
                }, true);
                // handle model updates properly
                scope.$watch(iAttrs.ngModel, function(newValue, oldValue) {
                    var disOptions = $parse(attrToWatch)(scope);
                    if(newValue)
                        disableOptions(scope, expElements[2], iElement, disOptions, fnDisableIfTrue);
                });
            }
        };
    });

    app.controller("MappingController", ['$scope', '$http', '$sce', function ($scope, $http, $sce) {
        instance = this;

        instance.fullLoader = true;
        instance.ebayCategoryLoader = false;
        instance.successMessage = "";
        instance.showSuccessMessage = false;
        instance.showErrorMessage = false;
        instance.errorMessage = "";

        instance.name = "";
        instance.marketplace = "";
        /** selected primary category tree */
        instance.selectedPrimaryCategory = [];
        /** selected secondary category tree */
        instance.selectedSecondaryCategory = [];

        instance.trySubmit = false;
        instance.trySubmitAddMapping = false;

        instance.mapping = [];

        /** all ps categories */
        instance.psCategories = [];
        /** selected categories for current mapping */
        instance.categories = [];
        instance.listMappedCategories = [];

        instance.ebayPrimaryCategories = [];
        instance.ebaySecondaryCategories = [];
        instance.ebayCategoryPrimarySelected = false;
        instance.ebayCategorySecondarySelected = false;

        instance.conditionIsShown = false;
        instance.condition = {};
        instance.condition_description = {};
        instance.conditionOptions = [];

        instance.specificHtml = "";
        instance.specific = [];
        instance.specificCustom = [];

        instance.editMode = false;
        instance.editIndex = -1;
        instance.editPrimaryCategoryName = '';


        {
            // Init
            $http.get(routes['categoryMappingInfo'], {
                params: {id: mappingId}
            }).success(function (data) {
                instance.fullLoader = false;

                instance.name = data.name;
                instance.marketplace = data.marketplace;
                instance.mapping = data.mapping;
                instance.psCategories = data.categories;

                // disabled to select already selected categories
                for (var i=0; i < instance.mapping.length; i++) {
                    instance.addUsedPSCategories(instance.mapping[i].categories);
                }

                instance.marketplaceChange();

            });
        }

        this.saveMapping = function(stay) {
            instance.fullLoader = true;
            instance.showSuccessMessage = false;
            instance.successMessage = "";

            instance.showErrorMessage = false;
            instance.errorMessage = "";

            $http.post(routes['saveMapping'], {
                'id': mappingId,
                'name': instance.name,
                'marketplace': instance.marketplace,
                'mapping': instance.mapping,
                'redirect': !stay
            }).success(function (data) {
                instance.fullLoader = false;

                if (data.success && stay) {
                    // Show success message
                    instance.showSuccessMessage = true;
                    instance.successMessage = data.message;
                } else if (!data.success) {
                    instance.showErrorMessage = true;
                    instance.errorMessage = data.message;
                    return;
                }

                if (!stay) {
                    document.location.href = routes['mappingIndex'];
                }
            });
        };

        /**
         * Generate info about single ebay selected category
         */
        this.getSelectedCategoryInfo = function(selectedCategoryTree) {
            var categoryName = "";
            var categoryId = 0;
            var currentCategoryString = "";
            for (var i = 0; i<  selectedCategoryTree.length; i++) {
                var currentCategory = selectedCategoryTree[i];
                if (currentCategoryString != "") {
                    currentCategoryString = currentCategoryString + "->";
                }
                currentCategoryString = currentCategoryString +   currentCategory.label;
                categoryId =  currentCategory.id;
            }

            return {
                'id': categoryId,
                'label': currentCategoryString
            }
        };

        /** Render single category option */
        this.renderCategoryOption = function(category) {
            return $sce.trustAsHtml(category)
        };

        /** Add new mapping to list of mapping */
        this.addMapping = function() {
            if (!$scope.categoryAddMappingForm.$valid) {
                instance.trySubmitAddMapping = true;
                return false;
            }

            var primaryInfo = instance.getSelectedCategoryInfo(instance.selectedPrimaryCategory);
            var secondaryInfo = instance.getSelectedCategoryInfo(instance.selectedSecondaryCategory);

            console.debug(instance.condition_description);
            var newMappingObject = {
                categories: instance.categories,
                ebay_primary_category_name: primaryInfo.label,
                ebay_primary_category_value: primaryInfo.id,
                ebay_secondary_category_name: secondaryInfo.label,
                ebay_secondary_category_value: secondaryInfo.id,
                item_condition: instance.condition,
                item_condition_description: instance.condition_description,
                product_specifics: $.extend({}, instance.specific),
                product_specifics_custom: $.extend({}, instance.specificCustom)
            };

            instance.mapping.push(newMappingObject);

            instance.addUsedPSCategories(instance.categories);

            instance.selectedPrimaryCategory = [];
            instance.selectedSecondaryCategory = [];

            // Reset selected values
            instance.specificHtml = "";
            instance.specific = [];
            instance.specificCustom = [];
            instance.categories = [];
            instance.conditionIsShown = false;
            instance.condition = {};
            instance.condition_description = {};

            var primary = instance.ebayPrimaryCategories[0];
            instance.ebayPrimaryCategories = [];
            instance.ebayPrimaryCategories.push(primary);

            instance.ebaySecondaryCategories.slice(1, instance.ebayPrimaryCategories.length);

            $scope.categoryAddMappingForm.$setPristine(true);
            instance.trySubmitAddMapping = false;

        };

        this.addUsedPSCategories = function(mappedCategoriesList) {
            instance.changedUsedPSCategories(mappedCategoriesList, true);
        };

        this.removeUsedPSCategories = function(mappedCategoriesList) {
            instance.changedUsedPSCategories(mappedCategoriesList, false);
        };

        this.changedUsedPSCategories = function(categoryList, used) {
            for (var i = 0; i< categoryList.length; i++) {
                for(var j = 0; j < instance.psCategories.length; j++) {
                    if(instance.psCategories[j].category_id == categoryList[i].category_id) {
                        instance.psCategories[j].used = used;
                        break;
                    }
                }
            }
        };

        /**
         * Change ebay marketplace
         */
        this.marketplaceChange = function() {
            this.pushCategoryUpdate(instance.marketplace, 0, true);
            this.pushCategoryUpdate(instance.marketplace, 0, false);
        };

        /**
         * Change one of ebay category
         *
         * @param index
         */
        this.changeEbayCategory = function(index, isPrimary) {
            if (isPrimary) {
                var changedCategoryId = instance.selectedPrimaryCategory[index].id;
                instance.ebayPrimaryCategories = instance.ebayPrimaryCategories.slice(0, index + 1);
            } else {
                var changedCategoryId = instance.selectedSecondaryCategory[index].id;
                instance.ebaySecondaryCategories = instance.ebaySecondaryCategories.slice(0, index + 1);
            }

            this.pushCategoryUpdate(instance.marketplace, changedCategoryId, isPrimary);
        };

        /**
         * Add list of available categories to drop-down
         *
         * @param marketplaceId
         * @param categoryId
         */
        this.pushCategoryUpdate = function(marketplaceId, categoryId, isPrimary) {
            if (isPrimary) {
                instance.ebayCategoryPrimarySelected = false;
                var categoryReference = instance.ebayPrimaryCategories;
            } else {
                instance.ebayCategorySecondarySelected = false;
                var categoryReference = instance.ebaySecondaryCategories;
            }

            instance.ebayCategoryLoader = true;
            $http.get(routes['category'], {
                params: {'marketplaceId': marketplaceId, 'parentCategoryId': categoryId}
            }).success(function (data) {
                instance.ebayCategoryLoader = false;
                if (data.success) {
                    if (isPrimary) {
                        instance.conditionIsShown = false;
                        instance.condition = {};
                        instance.conditionOptions = [];
                        instance.condition_description = {};

                        instance.specificHtml = "";
                    }

                    if (!data.data.is_latest) {
                        categoryReference.push(data.data.categories);
                    } else {
                        if (isPrimary) {
                            instance.ebayCategoryPrimarySelected = true;
                            instance.getCategorySpecific(marketplaceId, categoryId);
                        }
                    }
                }
            });
        };


        /**
         * Obtain category specific information
         *
         * @param marketplaceId
         * @param categoryId
         */
        this.getCategorySpecific = function(marketplaceId, categoryId) {

            instance.conditionIsShown = false;
            instance.condition = {};
            instance.condition_description = {};
            instance.conditionOptions = [];

            instance.ebayCategoryLoader = true;
            $http.post(routes['specifics'], {'marketplaceId': marketplaceId, 'id': categoryId, 'angular': true}).success(function (data) {
                instance.ebayCategoryLoader = false;
                instance.conditionOptions = data.data.conditions;
                if (instance.conditionOptions.length > 0) {
                    instance.conditionIsShown = true;
                }
                instance.specificHtml = "<colgroup><col width='150'></colgroup>" +  data.data.specifics_html;
            });
        };

        /**
         * Show custom "Own Value"
         *
         * @param optionKey
         * @returns {boolean}
         */
        this.showSpecificCustom = function(optionKey) {
            if(typeof instance.specific[optionKey] != 'undefined') {
                // does not exist
                return instance.specific[optionKey] == customValueSpecificKey;
            }

            return false;
        };

        this.remove = function(index) {
            removedMapping = instance.mapping[index];
            instance.removeUsedPSCategories(removedMapping.categories);
            instance.mapping.splice(index, 1);
        };

        this.edit = function(index) {
            var existingData = instance.mapping[index];
            instance.editMode = true;
            instance.editIndex = index;
            instance.editPrimaryCategoryName = existingData['ebay_primary_category_name'];
            this.pushCategoryUpdate(instance.marketplace, existingData['ebay_primary_category_value'], true);
        };

        this.saveEdit = function() {
            var index = instance.editIndex;

            instance.mapping[index]['item_condition'] = instance.condition;
            instance.mapping[index]['item_condition_description'] = instance.condition_description;
            instance.mapping[index]['product_specifics'] = $.extend({}, instance.specific);
            instance.mapping[index]['product_specifics_custom'] = $.extend({}, instance.specificCustom);

            // Reset selected values
            instance.specificHtml = "";
            instance.specific = [];
            instance.specificCustom = [];
            instance.categories = [];
            instance.conditionIsShown = false;
            instance.condition = {};
            instance.condition_description = {};

            $scope.categoryAddMappingForm.$setPristine(true);

            instance.editMode = false;
            instance.editIndex = -1;
            instance.editPrimaryCategoryName = '';
        };

    }]);

})();