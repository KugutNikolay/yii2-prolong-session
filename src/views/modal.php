<?php

use Yii;
use yii\helpers\Url;

?>

<div id="session-control-modal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close ml-5" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title"><?= Yii::t('app','Session expire control'); ?></h4>
			</div>
			<div class="modal-body">
                <h3 class="text-center"><?= Yii::t('app','Your session will expire in {timeout} minutes and you will be automatically logged out.', ['timeout'=> $timeout/60]); ?></h3>
			</div>
			<div class="modal-footer">
				<a id="prolong_session" class="btn btn-red pull-left" href="<?= Url::toRoute(['/prolong-session/update'])?>"><?= Yii::t('app','Prolong Session'); ?></a>
				<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>


