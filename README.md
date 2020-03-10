# Kirby 3 Vips thumbnail

Highly alpha! This is very basic Kirby CMS thumbnail driver for libvips. It uses [vipsthumbnail](https://libvips.github.io/libvips/API/current/Using-vipsthumbnail.md.html) that is installed with vips. You need recent libvips [installed](https://libvips.github.io/libvips/install.html) on your machine but **you don't need** [Vips-php](https://github.com/libvips/php-vips) PECL extension installed this plugin doesn't use it.

## Why would you want to use this?
Vips is library similar to Imagemagick but uses much less memory and it's faster. This is especially useful if you are dealing with images in 10000x10000+ px range and need to make thumbnails on memory constrained environments ([benchmarks](https://github.com/libvips/libvips/wiki/Speed-and-memory-use)).

## Why use ImageMagick or GD
This library is aimed only at resizing and cropping. Kirby's other thumb functions like grayscale, blur won't work. Cropping right now uses vips "smartcrop" algorythm which might be advantage but you can't set cropping by hand (could be easily implemented i just don't have use for it).

## Output options
Vips has some options that i don't understand (like trellis-quant, overshoot-deringing, optimize-scans) but these options are currently implemented:

- ```strip``` default: true - strips metadata from images
- ```autoOrient``` default: false - aplies orientation meta tag if its present (in vips flag --rotate)
- ```interlace``` default: flase - generate an interlaced (progressive) jpeg
- ```log``` default: flase - There is dumb loging included if you want to debug what is going on and what commands are getting fired
- ```logdir``` default: plugindirectory/logs - connected to above - set where to save logs.


You can set these with normal thumbs kirby config. Kirby options like 'quality' and 'bin' apply.
```
return [
  'thumbs' => [
    'driver'    => 'vipsthumbnail',
    'quality'   => 90,
    'bin'       => '/usr/local/bin/vipsthumbnail',
    'interlace' => true
  ]
];
```
