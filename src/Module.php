<?php
namespace safepartner\prolongSession;

use safepartner\prolongSession\interfaces\ProlongSessionInterface;
use Yii;
use yii\base\BootstrapInterface;
use yii\helpers\Url;
use yii\web\View;

class Module extends \yii\base\Module implements BootstrapInterface
{

	public $modalView;

	/**
	 * php session maxlifetime in seconds
	 * default ini_get('session.gc_maxlifetime')*60
	 * @var integer
	 */
	public $phpSessionLifetime;

	/**
	 * Timeout before logout in seconds
	 * @var integer
	 */
	public $timeout = 120;

	/**
	 * Timeout Send Request logout
	 * @var integer
	 */
	public $timeoutSendRequest = 10;

	/**
	 * @var string
	 */
	public $logoutUrl;

	/**
	 * @var string
	 */
	public $dialogId = 'session-control-modal';

	/**
	 * @var string
	 * localStorage seccion key
	 */
	public $localStorageKey = 'session_control';

	/**
	 * Id button prolon user session
	 * @var string
	 */
	public $prolongButtonId = 'prolong_session';

	/**
	 * session maxlifetime in milliseconds
	 * @var integer
	 */
	private $sessionMaxlifetime;

	/**
	 * Url prolong session
	 * @var string
	 */
	private $prolongUrl;

	/**
	 * @inheritdoc
	 */
	public function bootstrap($app)
	{
		if (Yii::$app->user->identity
			&& Yii::$app->user->identity instanceof ProlongSessionInterface
			&& Yii::$app->user->identity->isEnabledProlongSession()
		) {

			if ($this->phpSessionLifetime === null) {
				$this->phpSessionLifetime = ini_get('session.gc_maxlifetime') * 60;
			}

			$this->sessionMaxlifetime = ($this->phpSessionLifetime - ($this->timeout + $this->timeoutSendRequest)) * 1000;
			$this->prolongUrl         = Url::toRoute(['/prolong-session/update']);
			$this->logoutUrl          = Yii::$app->user->identity->getProlongSessionLogoutUrl();

			$app->getView()->on(View::EVENT_END_BODY, function ($event) {
				if (!Yii::$app->request->isAjax) {
					if ($this->modalView) {
						echo Yii::$app->view->render($this->modalView, ['timeout'=> $this->timeout]);
					} else {
						echo Yii::$app->view->renderFile($this->getViewPath() . DIRECTORY_SEPARATOR . 'modal.php', ['timeout'=> $this->timeout]);
					}
				}
			});

			$this->registerJs();
		}
	}

	private function registerJs()
	{
		$js = <<<JS
			localStorage.setItem("$this->localStorageKey", new Date().getTime() + $this->sessionMaxlifetime);
JS;

		if (!Yii::$app->request->isAjax) {
			$js .= <<<JS
				$(document).ready(function () {
					sessionControl($this->sessionMaxlifetime);
				});
				function sessionControl(timeout) {
				    if (typeof sendRequestInterval !== 'undefined') {
				        clearInterval(sendRequestInterval);
				    }
					setTimeout(function () {
						var now = new Date().getTime();
						if (localStorage.getItem("$this->localStorageKey") < now) {
							$("#$this->dialogId").modal('show');
							let sendRequestInterval = setInterval(function() {
								let dateNow = new Date().getTime();
								if(localStorage.getItem("$this->localStorageKey") > dateNow) {
								    clearInterval(sendRequestInterval);
									$("#$this->dialogId").modal('hide');
									sessionControl(localStorage.getItem("$this->localStorageKey") - dateNow);
								}
								else if((dateNow - localStorage.getItem("$this->localStorageKey")) >= ($this->timeout * 1000)) {
									window.location.href = "$this->logoutUrl";
								}
							}, 1000);
						}
						else {
							sessionControl(localStorage.getItem("$this->localStorageKey") - now);
						}
					}, timeout);
				}
				$(document).on('click', "#$this->prolongButtonId", function () {
					$.ajax({
						url: "$this->prolongUrl"
					}).done(function (data) {
						if (data.success) {
							localStorage.setItem("$this->localStorageKey", new Date().getTime() + $this->sessionMaxlifetime);
							sessionControl($this->sessionMaxlifetime);
						}
					});
					$("#$this->dialogId").modal('hide');
					return false;
				});
JS;
		}

		Yii::$app->getView()->registerJs($js);

	}

}
