<?php

namespace app\assets;

use yii\web\AssetBundle;

class LoadFileFormAsset extends AssetBundle
{
	public $basePath = '@webroot';

	public $baseUrl = '@web';

	public $css = [];

    public $js = [
		'scripts/load-file-form.js',
	];

	public $depends = [
		'yii\web\JqueryAsset',
	];
}


