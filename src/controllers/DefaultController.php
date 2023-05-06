<?php
namespace safepartner\prolongSession\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

class DefaultController extends Controller
{

	public function actionUpdate() {
		Yii::$app->response->format = Response::FORMAT_JSON;
		if (Yii::$app->user->identity) {
			Yii::$app->session->open();
			return ['success' => true];
		}
		else {
			return ['success' => false];
		}
	}
}