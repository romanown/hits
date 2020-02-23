angular.module('apiApp.controllers').controller('HitsHitsIndexController', ['$scope', '$http', '$uibModal', function($scope, $http, $uibModal) {

    $scope.itemsLoading = 1;
    $scope.currentPage = 1;

    $scope.remove = function(id) {
        if (!confirm('Вы уверены, что хотите УДАЛИТЬ салон?')) {
            return;
        }

        $http({
            method: 'POST',
            url: '/api/marketer/shop/delete',
            data: $.param({
                id: id
            }),
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).then(function() {
            $scope.fetchResult();
        });
    };

    $scope.view = function (id) {
        var modalInstance = $uibModal.open({
            templateUrl: '/ng_app/views/HitsHitsView.html',
            controller: 'HitsHitsViewController',
            windowTemplateUrl: '/ng_app/views/window.html',
            resolve: {
                itemId: function () {
                    return id;
                }
            }
        });

        modalInstance.result.then(function () {
            $scope.fetchResult();
        }, function () {
            console.log('Modal dismissed at: ' + new Date());
        });
    };

    $scope.create = function() {
        var modalInstance = $uibModal.open({
            templateUrl: '/ng_app/views/HitsHitsView.html',
            controller: 'HitsHitsViewController',
            windowTemplateUrl: '/ng_app/views/window.html',
            resolve: {
                itmeId: function () {
                    return null;
                }
            }
        });

        modalInstance.result.then(function () {
            $scope.fetchResult();
        }, function () {
            console.log('Modal dismissed at: ' + new Date());
        });
    };

    $scope.pageChanged = function(page) {
        $scope.currentPage = page;
        $scope.fetchResult();
    };

    $scope.fetchResult = function() {
        $scope.itemsLoading = 1;

        var params = {
            offset: ($scope.currentPage - 1) * $scope.pageSize
        };

        $http({
            method: 'GET',
            url: '/api/hits/hits/list',
            params: params
        }).then(function(response) {
        	var data = response.data;
            for (var code in data) if (data.hasOwnProperty(code)) {
                $scope[code] = data[code];
            }
            $scope.itemsLoading = 0;
        });
    };

    $scope.fetchResult();
}]);