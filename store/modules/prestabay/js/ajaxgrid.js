(function () {
    var app = angular.module("ajaxGrid", []);

    app.filter('unsafe', ['$sce', function($sce){
        return function(text) {
            return $sce.trustAsHtml('' + text);
        };
    }]);

    app.directive('ngEnter', function() {
        return function(scope, element, attrs) {
            element.bind("keydown keypress", function(event) {
                if(event.which === 13) {
                    scope.$apply(function(){
                        scope.$eval(attrs.ngEnter, {'event': event});
                    });

                    event.preventDefault();
                }
            });
        };
    });

    app.controller("AjaxGridController", ['$scope', '$http', '$sce', function ($scope, $http, $sce) {
        instance = this;

        instance.isLoading = true;
        instance.gridHeader = "";
        instance.colums = {};
        instance.items = {};
        instance.multiSelect = false;
        instance.sortField = false;
        instance.sortDir = false;
        instance.primaryKeyName = false;
        instance.totalItemsCount = 0;
        instance.page = 1;
        instance.totalPages = 0;
        instance.paginationStartPage = 0;
        instance.paginationEndPage = 0;
        instance.limit = 20;
        instance.limits= {};
        instance.selectedAll = false;
        instance.filters = {};

        instance.progressActive = false;
        instance.progressPercent = '0%';

        instance.hasError = false;
        instance.errorMessage = '';

        instance.hasWarning = false;
        instance.warningMessage = '';

        instance.massactions = {};
        instance.massactionType = false;
        instance.selectedMassaction = false;

        // Reload grid
        this.reload = function() {
            instance.isLoading = true;
            instance.hasWarning = false;
            instance.warningMessage = '';
            instance.hasError = false;
            instance.errorMessage = '';

            $http.get(routes['gridUrl'], {
                params: {
                    type: 'info',
                    'paginator-page': instance.page,
                    pagination: instance.limit,
                    filter: instance.filters,
                    'sort-field': instance.sortField,
                    'sort-dir': instance.sortDir
                }
            }).success(function(data) {
                instance.isLoading = false;

                instance.gridHeader = data.header;
                instance.columns = data.columns;
                instance.multiSelect = data.multiSelect;
                instance.sortField = data.sortField;
                instance.sortDir = data.sortDir;
                instance.primaryKeyName = data.primaryKeyName;
                instance.items = data.items;


                instance.totalItemsCount = data.totalItemsCount;
                instance.page = data.page;
                instance.totalPages = data.totalPages;
                instance.limit = data.limit;
                instance.limits = data.limits;

                instance.paginationStartPage = 1;
                instance.paginationEndPage = instance.totalPages;


                if (instance.page > 3) {
                    instance.paginationStartPage = instance.page - 2;
                }
                if (instance.page < instance.totalPages - 3 ) {
                    instance.paginationEndPage = instance.page + 2;
                }
                if (instance.paginationEndPage > instance.totalPages) {
                    instance.paginationEndPage = instance.totalPages;
                }

                instance.massactions = data.massactions;
                instance.massactionType = data.massactionType;
            });

        };

        {
            // Init
            this.reload();
        }

        this.filter = function() {
            instance.reload();
        };

        this.sortBy = function(field, direction) {
            instance.sortField = field;
            instance.sortDir = direction;
            instance.reload();
        }

        this.reset = function() {
            instance.page = 1;
            instance.filters = {};
            instance.sortField = false;
            instance.sortDir = false;

            instance.reload();
        };

        this.toggleAllCheckboxes = function() {
            if (instance.selectedAll) {
                instance.selectedAll = false;
            } else {
                instance.selectedAll = true;
            }

            angular.forEach(instance.items, function (item) {
                item.checked = instance.selectedAll;
            });
        };

        // Change to other page
        this.gotoPage = function(newPage) {
            instance.page = newPage;
            instance.reload();
        };

        // change limit
        this.changeLimit = function(newLimit) {
            instance.limit = newLimit;
            instance.reload();
        };

        this.getSelectedItems = function() {
            var selected = [];
            angular.forEach(instance.items, function (item) {
                if (item.checked) {
                    selected.push(item[instance.primaryKeyName]);
                }
            });

            return selected;
        };

        this.setHtmlValue = function(value) {
            return $sce.trustAsHtml(value);
        }

        this.buttonAction = function(buttonName, extraValue) {
            instance.hasError = false;
            instance.errorMessage = '';

            instance.isLoading = true;

            var selectedItemsIds = instance.getSelectedItems();
            $http.post(routes['gridUrl'], {
                'type': 'buttonAction',
                'buttonName': buttonName,
                'selectedIds': selectedItemsIds,
                'extraValue': extraValue
            }).success(function (data) {
                instance.isLoading = false;

                if (data.success) {
                    if (data.result['jshandler'] != undefined) {
                        var fn = data.result['jshandler'];

                        if(typeof window[fn] === 'function') {
                            window[fn](instance, selectedItemsIds, extraValue);
                        }
                    }
                } else {
                    instance.hasError = true;
                    if (data.message != undefined) {
                        instance.errorMessage = data.message;
                    } else {
                        instance.errorMessage = 'Button action failed';
                    }
                }
            });
        };

        this.submitMassaction = function() {
            instance.hasError = false;
            instance.errorMessage = "";
            if (instance.selectedMassaction != "") {
                instance.isLoading = true;
                window.location = instance.selectedMassaction;
            } else {
                instance.errorMessage = "Mass-action: Please select action from drop-down";
                instance.hasError = true;
            }
        };

        this.refresh = function() {
            $scope.$apply();
        };

        $scope.range = function(min, max, step) {
            step = step || 1;
            var input = [];
            for (var i = min; i <= max; i += step) {
                input.push(i);
            }
            return input;
        };
    }]);

})();