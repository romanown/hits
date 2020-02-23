<?php
/**
 * @var $this \HitsController
 */

$this->ngApp = 'apiApp';
$this->ngController = 'HitsHitsIndexController';
$this->pageTitle = Yii::app()->name.' - Хиты';
?>

<style>
	.item-image {
		max-width: 200px;
		max-height: 200px;
	}
</style>

<div class="ajax-loader" ng-show="itemsLoading"></div>

<br>
<button class="buttonS bGreen" ng-click="create()" ng-cloak ng-show="true">Добавить</button>

<div class="widget" ng-cloak ng-show="!itemsLoading && items && items.length">
	<table class="tDefault shops-table" width="100%">
		<thead>
		<tr>
			<th>ИД</th>
			<th>Изображение</th>
			<th>Название</th>
			<th>Активность</th>
			<th>Город</th>
			<th>Адрес</th>
			<th>Режим работы</th>
			<th>Контакты</th>
			<th></th>
		</tr>
		</thead>
		<tbody>
		<tr ng-repeat="item in items">
			<td><a ng-click="view(item.id)">{{ item.id }}</a></td>
			<td><a ng-click="view(item.id)"><img ng-src="{{ item.photos && item.photos.length ? item.photos[0]['url'] : '' }}" class="item-image"></a></td>
			<td><a ng-click="view(item.id)">{{ item.name }}</a></td>
			<td>{{ item.is_active == 1 ? 'Да' : 'Нет'}}</td>
			<td>{{ item.city }}</td>
			<td>{{ item.address }}</td>
			<td>{{ item.work_time }}</td>
			<td>{{ item.contacts }}</td>
			<td>
				<a ng-href="{{ item.site_url }}" target="_blank">Просмотр на сайте</a>
				<br>
				<a ng-click="remove(item.id)" ng-show="false">Удалить</a>
			</td>
		</tr>
		</tbody>
	</table>
</div>