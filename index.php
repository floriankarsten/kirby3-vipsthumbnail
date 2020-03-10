<?php
use Kirby\Cms\App;

if(option('thumbs.driver') === 'vipsthumbnail') {

	if (! class_exists('Floriankarsten\Vipsthumbnail')) {
		require_once __DIR__ . '/Vipsthumbnail.php';
	}

	Kirby::plugin('Floriankarsten/Vipsthumbnail', [
		'components' => [
			'thumb' => function (App $kirby, string $src, string $dst, array $options) {
				$vipsThumbnailer = new \Floriankarsten\Vipsthumbnail($src, $dst, $options);
				return $vipsThumbnailer->process();
			}
		]
	]);

}
