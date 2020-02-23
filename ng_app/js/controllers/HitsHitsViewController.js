angular.module('apiApp.controllers').controller('HitsHitsViewController', ['$scope', '$http', 'Upload', '$uibModalInstance', 'itemId', function($scope, $http, Upload, $uibModalInstance, itemId) {
    $scope.itemId = itemId;
    $scope.message = '';
    $scope.item = {};
    $scope.uploadingNew = false;
	$scope.uploading = {};
    $scope.types = [];

    $scope.close = function () {
        $uibModalInstance.close();
    };

    $scope.fetch = function() {
        $http({method: 'GET', url: '/api/hits/hits/view', params: {id: itemId}}).then(function(response) {
            $scope.item = response.data;

            $scope.refreshWindow();
        });
    };
	
	$scope.create = function() {
        $http({method: 'GET', url: '/api/hits/hits/create', params: {id: null}}).then(function(response) {
            $scope.item = response.data;

            $scope.refreshWindow();
        });
    };

    $scope.refreshWindow = function() {
        $('.ui-dialog-content').css('max-height', parseInt($(window).height()) - 200);
    };

    $scope.save = function() {
        $http({
            method: 'POST',
            url: '/api/hits/hits/save',
            data: $.param($scope.item),
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).then(function(response) {
            $scope.item = response.data;
            $scope.itemId = response.data['id'];

            $scope.refreshWindow();
        });
    };

	$scope.remove = function(fileId) {
		$http({
			method: 'POST',
			url: '/api/hits/hits/deletePhoto',
			data: $.param({
				id: itemId,
				file_id: fileId
			}),
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded'
			}
		}).then(function(response) {
			$scope.item = response.data;

			$scope.refreshWindow();
		});
	};

    $scope.upload = function (files, fileId) {
        if (files && files.length) {

			if (fileId) {
				$scope.uploading[fileId] = true;
			} else {
				$scope.uploadingNew = true;
			}

            for (var i = 0; i < files.length; i++) {
                var file = files[i];

				var fields = {'id': $scope.item.id};
				if (fileId) {
					fields['file_id'] = fileId;
				}

                Upload.upload({
                    url: '/api/hits/hits/upload',
                    fields: fields,
                    file: file
                }).progress(function (evt) {
                    var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
                    console.log('progress: ' + progressPercentage + '% ' + evt.config.file.name);
                }).then(function (response) {
                    $scope.item = response.data;

					if (fileId) {
						$scope.uploading[fileId] = false;
					} else {
						$scope.uploadingNew = false;
					}

                    $scope.refreshWindow();
                });
            }
        }
    };

    if ($scope.itemId) {
        $scope.fetch();
    }
	else
	{
		$scope.create();
	}
}]);