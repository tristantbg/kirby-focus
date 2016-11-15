<?php

class Focus {

  /**
   * Calculates the image ratio by dividing width / height
   */
  public static function ratio($width, $height) {
    if ($height === 0) {
      return 0;
    }
    return $width / $height;
  }

  /**
   * Correct format, even for localized floats
   */
  public static function numberFormat($number) {
    return number_format($number,2,'.','');
  }


  /**
   * Calculates crop coordinates and width/height to crop and resize the original image
   */
  public static function cropValues($thumb) {
    // get original image dimensions
    $dimensions = clone $thumb->source->dimensions();

	$scale = $thumb->options['scale'];

	$width = floor($dimensions->width() / $scale);
	$height = floor($dimensions->height() / $scale);

	$focusX = floor($dimensions->width() * $thumb->options['focusX']);
	$focusY = floor($dimensions->height() * $thumb->options['focusY']);

	$x1 = $focusX - $width/2;
	// $x1 off canvas?
	$x1 = ($x1 < 0) ? 0 : $x1;
	$x1 = ($x1 + $width > $dimensions->width()) ? $dimensions->width() - $width : $x1;

	$y1 = $focusY - $height/2;
	// $y1 off canvas?
	$y1 = ($y1 < 0) ? 0 : $y1;
	$y1 = ($y1 + $height > $dimensions->height()) ? $dimensions->height() - $height : $y1;

    $x2 = floor($x1 + $width);
    $y2 = floor($y1 + $height);

    return array(
      'x1' => $x1,
      'y1' => $y1,
      'scale' => floor($scale),
      'width' => floor($width),
      'height' => floor($height),
    );
  }


  /**
   * Get the stored coordinates
   */
  public static function coordinates($file, $axis = null) {  
    $focusCoordinates = array(
      'x' => 0.5,
      'y' => 0.5,
    );
    
    $focusFieldKey = c::get('focus.field.key', 'focus');

    if ($file->$focusFieldKey()->isNotEmpty()) {
      $focus = json_decode($file->$focusFieldKey()->value());
      $focusCoordinates = array(
        'x' => focus::numberFormat($focus->x),
        'y' => focus::numberFormat($focus->y),
      );
    }

    if (isset($axis) && isset($focusCoordinates[$axis])) {
      return $focusCoordinates[$axis];
    }

    return $focusCoordinates;
  }

}

