<?php
/**
 * @package     RedRad
 * @subpackage  Image
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDRAD') or die;

/**
 * Image library
 *
 * @package     RedRad
 * @subpackage  Image
 * @since       1.0
 */
class RImage extends JImage
{
	/**
	 * @const  integer
	 * @since  12.2
	 */
	const CROP = 4;

	/**
	 * @const  integer
	 * @since  12.3
	 */
	const CROP_RESIZE = 5;

	/**
	 * Method to generate thumbnails from the current image. It allows
	 * creation by resizing or cropping the original image.
	 *
	 * @param   mixed    $thumbSizes      String or array of strings. Example: $thumbSizes = array('150x75','250x150');
	 * @param   integer  $creationMethod  1-3 resize $scaleMethod | 4 create croppping | 5 resize then crop
	 *
	 * @return array
	 *
	 * @throws  LogicException
	 * @throws  InvalidArgumentException
	 *
	 * @since 12.2
	 */
	public function generateThumbs($thumbSizes, $creationMethod = self::SCALE_INSIDE)
	{
		// Make sure the resource handle is valid.
		if (!$this->isLoaded())
		{
			throw new LogicException('No valid image was loaded.');
		}

		// Accept a single thumbsize string as parameter
		if (!is_array($thumbSizes))
		{
			$thumbSizes = array($thumbSizes);
		}

		// Process thumbs
		$generated = array();

		if (!empty($thumbSizes))
		{
			foreach ($thumbSizes as $thumbSize)
			{
				// Desired thumbnail size
				$size = explode('x', strtolower($thumbSize));

				if (count($size) != 2)
				{
					throw new InvalidArgumentException('Invalid thumb size received: ' . $thumbSize);
				}
				$thumbWidth 	= $size[0];
				$thumbHeight	= $size[1];

				switch ($creationMethod)
				{
					// Case for self::CROP
					case 4:
						$thumb = $this->crop($thumbWidth, $thumbHeight, null, null, true);
						break;

					// Case for self::CROP_RESIZE
					case 5:
						$thumb = $this->cropResize($thumbWidth, $thumbHeight, true);
						break;

					default:
						$thumb = $this->resize($thumbWidth, $thumbHeight, true, $creationMethod);
						break;
				}

				// Store the thumb in the results array
				$generated[] = $thumb;
			}
		}

		return $generated;
	}

	/**
	 * Method to create thumbnails from the current image and save them to disk. It allows creation by resizing
	 * or croppping the original image.
	 *
	 * @param   mixed    $thumbSizes      string or array of strings. Example: $thumbSizes = array('150x75','250x150');
	 * @param   integer  $creationMethod  1-3 resize $scaleMethod | 4 create croppping
	 * @param   string   $thumbsFolder    destination thumbs folder. null generates a thumbs folder in the image folder
	 *
	 * @return array
	 *
	 * @throws  LogicException
	 * @throws  InvalidArgumentException
	 *
	 * @since 12.2
	 */
	public function createThumbs($thumbSizes, $creationMethod = self::SCALE_INSIDE, $thumbsFolder = null)
	{
		// Make sure the resource handle is valid.
		if (!$this->isLoaded())
		{
			throw new LogicException('No valid image was loaded.');
		}

		// No thumbFolder set -> we will create a thumbs folder in the current image folder
		if (is_null($thumbsFolder))
		{
			$thumbsFolder = dirname($this->getPath()) . '/thumbs';
		}

		// Check destination
		if (!is_dir($thumbsFolder) && (!is_dir(dirname($thumbsFolder)) || !@mkdir($thumbsFolder)))
		{
			throw new InvalidArgumentException('Folder does not exist and cannot be created: ' . $thumbsFolder);
		}

		// Process thumbs
		$thumbsCreated = array();

		if ($thumbs = $this->generateThumbs($thumbSizes, $creationMethod))
		{
			// Parent image properties
			$imgProperties = self::getImageFileProperties($this->getPath());

			foreach ($thumbs as $thumb)
			{
				// Get thumb properties
				$thumbWidth 	= $thumb->getWidth();
				$thumbHeight 	= $thumb->getHeight();

				// Generate thumb name
				$filename 		= pathinfo($this->getPath(), PATHINFO_FILENAME);
				$fileExtension 	= pathinfo($this->getPath(), PATHINFO_EXTENSION);
				$thumbFileName 	= $filename . '_' . $thumbWidth . 'x' . $thumbHeight . '.' . $fileExtension;

				// Save thumb file to disk
				$thumbFileName = $thumbsFolder . '/' . $thumbFileName;

				if ($thumb->toFile($thumbFileName, $imgProperties->type))
				{
					// Return JImage object with thumb path to ease further manipulation
					$thumb->path = $thumbFileName;
					$thumbsCreated[] = $thumb;
				}
			}
		}
		return $thumbsCreated;
	}

	/**
	 * Method to crop the current image.
	 *
	 * @param   mixed    $width      The width of the image section to crop in pixels or a percentage.
	 * @param   mixed    $height     The height of the image section to crop in pixels or a percentage.
	 * @param   integer  $left       The number of pixels from the left to start cropping.
	 * @param   integer  $top        The number of pixels from the top to start cropping.
	 * @param   bool     $createNew  If true the current image will be cloned, cropped and returned; else
	 *                               the current image will be cropped and returned.
	 *
	 * @return  JImage
	 *
	 * @since   11.3
	 * @throws  LogicException
	 */
	public function crop($width, $height, $left = null, $top = null, $createNew = true)
	{
		// Make sure the resource handle is valid.
		if (!$this->isLoaded())
		{
			throw new LogicException('No valid image was loaded.');
		}

		// Sanitize width.
		$width = $this->sanitizeWidth($width, $height);

		// Sanitize height.
		$height = $this->sanitizeHeight($height, $width);

		// Autocrop offsets
		if (is_null($left))
		{
			$left = round(($this->getWidth() - $width) / 2);
		}
		if (is_null($top))
		{
			$top = round(($this->getHeight() - $height) / 2);
		}

		// Sanitize left.
		$left = $this->sanitizeOffset($left);

		// Sanitize top.
		$top = $this->sanitizeOffset($top);

		// Create the new truecolor image handle.
		$handle = imagecreatetruecolor($width, $height);

		// Allow transparency for the new image handle.
		imagealphablending($handle, false);
		imagesavealpha($handle, true);

		if ($this->isTransparent())
		{
			// Get the transparent color values for the current image.
			$rgba = imageColorsForIndex($this->handle, imagecolortransparent($this->handle));
			$color = imageColorAllocate($this->handle, $rgba['red'], $rgba['green'], $rgba['blue']);

			// Set the transparent color values for the new image.
			imagecolortransparent($handle, $color);
			imagefill($handle, 0, 0, $color);

			imagecopyresized($handle, $this->handle, 0, 0, $left, $top, $width, $height, $width, $height);
		}
		else
		{
			imagecopyresampled($handle, $this->handle, 0, 0, $left, $top, $width, $height, $width, $height);
		}

		// If we are cropping to a new image, create a new JImage object.
		if ($createNew)
		{
			// @codeCoverageIgnoreStart
			$new = new JImage($handle);

			return $new;

			// @codeCoverageIgnoreEnd
		}
		// Swap out the current handle for the new image handle.
		else
		{
			// Free the memory from the current handle
			$this->destroy();

			$this->handle = $handle;

			return $this;
		}
	}

	/**
	 * Method to load a file into the JImage object as the resource.
	 *
	 * @param   string  $path  The filesystem path to load as an image.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 * @throws  InvalidArgumentException
	 * @throws  RuntimeException
	 */
	public function loadFile($path)
	{
		// Destroy the current image handle if it exists
		$this->destroy();

		return parent::loadFile($path);
	}

	/**
	 * Method to resize the current image.
	 *
	 * @param   mixed    $width        The width of the resized image in pixels or a percentage.
	 * @param   mixed    $height       The height of the resized image in pixels or a percentage.
	 * @param   bool     $createNew    If true the current image will be cloned, resized and returned; else
	 *                                 the current image will be resized and returned.
	 * @param   integer  $scaleMethod  Which method to use for scaling
	 *
	 * @return  JImage
	 *
	 * @since   11.3
	 * @throws  LogicException
	 */
	public function resize($width, $height, $createNew = true, $scaleMethod = self::SCALE_INSIDE)
	{
		// Make sure the resource handle is valid.
		if (!$this->isLoaded())
		{
			throw new LogicException('No valid image was loaded.');
		}

		// Sanitize width.
		$width = $this->sanitizeWidth($width, $height);

		// Sanitize height.
		$height = $this->sanitizeHeight($height, $width);

		// Prepare the dimensions for the resize operation.
		$dimensions = $this->prepareDimensions($width, $height, $scaleMethod);

		// Create the new truecolor image handle.
		$handle = imagecreatetruecolor($dimensions->width, $dimensions->height);

		// Allow transparency for the new image handle.
		imagealphablending($handle, false);
		imagesavealpha($handle, true);

		if ($this->isTransparent())
		{
			// Get the transparent color values for the current image.
			$rgba = imageColorsForIndex($this->handle, imagecolortransparent($this->handle));
			$color = imageColorAllocate($this->handle, $rgba['red'], $rgba['green'], $rgba['blue']);

			// Set the transparent color values for the new image.
			imagecolortransparent($handle, $color);
			imagefill($handle, 0, 0, $color);

			imagecopyresized($handle, $this->handle, 0, 0, 0, 0, $dimensions->width, $dimensions->height, $this->getWidth(), $this->getHeight());
		}
		else
		{
			imagecopyresampled($handle, $this->handle, 0, 0, 0, 0, $dimensions->width, $dimensions->height, $this->getWidth(), $this->getHeight());
		}

		// If we are resizing to a new image, create a new JImage object.
		if ($createNew)
		{
			// @codeCoverageIgnoreStart
			$new = new JImage($handle);

			return $new;

			// @codeCoverageIgnoreEnd
		}
		// Swap out the current handle for the new image handle.
		else
		{
			// Free the memory from the current handle
			$this->destroy();

			$this->handle = $handle;

			return $this;
		}
	}

	/**
	 * Method to crop an image after resizing it to maintain
	 * proportions without having to do all the set up work.
	 *
	 * @param   integer  $width      The desired width of the image in pixels or a percentage.
	 * @param   integer  $height     The desired height of the image in pixels or a percentage.
	 * @param   integer  $createNew  If true the current image will be cloned, resized, cropped and returned.
	 *
	 * @return  object  JImage Object for chaining.
	 *
	 * @since   12.3
	 */
	public function cropResize($width, $height, $createNew = true)
	{
		$width   = $this->sanitizeWidth($width, $height);
		$height  = $this->sanitizeHeight($height, $width);

		if (($this->getWidth() / $width) < ($this->getHeight() / $height))
		{
			$this->resize($width, 0, false);
		}
		else
		{
			$this->resize(0, $height, false);
		}

		return $this->crop($width, $height, null, null, $createNew);
	}

	/**
	 * Method to rotate the current image.
	 *
	 * @param   mixed    $angle       The angle of rotation for the image
	 * @param   integer  $background  The background color to use when areas are added due to rotation
	 * @param   bool     $createNew   If true the current image will be cloned, rotated and returned; else
	 *                                the current image will be rotated and returned.
	 *
	 * @return  JImage
	 *
	 * @since   11.3
	 * @throws  LogicException
	 */
	public function rotate($angle, $background = -1, $createNew = true)
	{
		// Make sure the resource handle is valid.
		if (!$this->isLoaded())
		{
			throw new LogicException('No valid image was loaded.');
		}

		// Sanitize input
		$angle = (float) $angle;

		// Create the new truecolor image handle.
		$handle = imagecreatetruecolor($this->getWidth(), $this->getHeight());

		// Allow transparency for the new image handle.
		imagealphablending($handle, false);
		imagesavealpha($handle, true);

		// Copy the image
		imagecopy($handle, $this->handle, 0, 0, 0, 0, $this->getWidth(), $this->getHeight());

		// Rotate the image
		$handle = imagerotate($handle, $angle, $background);

		// If we are resizing to a new image, create a new JImage object.
		if ($createNew)
		{
			// @codeCoverageIgnoreStart
			$new = new JImage($handle);

			return $new;

			// @codeCoverageIgnoreEnd
		}
		// Swap out the current handle for the new image handle.
		else
		{
			// Free the memory from the current handle
			$this->destroy();

			$this->handle = $handle;

			return $this;
		}
	}

	/**
	 * Method to write the current image out to a file.
	 *
	 * @param   string   $path     The filesystem path to save the image.
	 * @param   integer  $type     The image type to save the file as.
	 * @param   array    $options  The image type options to use in saving the file.
	 *
	 * @return  boolean
	 *
	 * @see     http://www.php.net/manual/image.constants.php
	 * @since   11.3
	 * @throws  LogicException
	 */
	public function toFile($path, $type = IMAGETYPE_JPEG, array $options = array())
	{
		// Make sure the resource handle is valid.
		if (!$this->isLoaded())
		{
			throw new LogicException('No valid image was loaded.');
		}

		switch ($type)
		{
			case IMAGETYPE_GIF:
				return imagegif($this->handle, $path);
				break;

			case IMAGETYPE_PNG:
				return imagepng($this->handle, $path, (array_key_exists('quality', $options)) ? $options['quality'] : 0);
				break;

			case IMAGETYPE_JPEG:
			default:
				return imagejpeg($this->handle, $path, (array_key_exists('quality', $options)) ? $options['quality'] : 100);
		}
	}

	/**
	 * Method to get the new dimensions for a resized image.
	 *
	 * @param   integer  $width        The width of the resized image in pixels.
	 * @param   integer  $height       The height of the resized image in pixels.
	 * @param   integer  $scaleMethod  The method to use for scaling
	 *
	 * @return  stdClass
	 *
	 * @since   11.3
	 * @throws  InvalidArgumentException  If width, height or both given as zero
	 */
	protected function prepareDimensions($width, $height, $scaleMethod)
	{
		// Instantiate variables.
		$dimensions = new stdClass;

		switch ($scaleMethod)
		{
			case self::SCALE_FILL:
				$dimensions->width = (int) round($width);
				$dimensions->height = (int) round($height);
				break;

			case self::SCALE_INSIDE:
			case self::SCALE_OUTSIDE:
				$rx = ($width > 0) ? ($this->getWidth() / $width) : 0;
				$ry = ($height > 0) ? ($this->getHeight() / $height) : 0;

				if ($scaleMethod == self::SCALE_INSIDE)
				{
					$ratio = ($rx > $ry) ? $rx : $ry;
				}
				else
				{
					$ratio = ($rx < $ry) ? $rx : $ry;
				}

				$dimensions->width = (int) round($this->getWidth() / $ratio);
				$dimensions->height = (int) round($this->getHeight() / $ratio);
				break;

			default:
				throw new InvalidArgumentException('Invalid scale method.');
				break;
		}

		return $dimensions;
	}

	/**
	 * Method to sanitize a height value.
	 *
	 * @param   mixed  $height  The input height value to sanitize.
	 * @param   mixed  $width   The input width value for reference.
	 *
	 * @return  integer
	 *
	 * @since   11.3
	 */
	protected function sanitizeHeight($height, $width)
	{
		// If no height was given we will assume it is a square and use the width.
		$height = ($height === null) ? $width : $height;

		// If we were given a percentage, calculate the integer value.
		if (preg_match('/^[0-9]+(\.[0-9]+)?\%$/', $height))
		{
			$height = (int) round($this->getHeight() * (float) str_replace('%', '', $height) / 100);
		}
		// Else do some rounding so we come out with a sane integer value.
		else
		{
			$height = (int) round((float) $height);
		}

		return $height;
	}

	/**
	 * Method to sanitize an offset value like left or top.
	 *
	 * @param   mixed  $offset  An offset value.
	 *
	 * @return  integer
	 *
	 * @since   11.3
	 */
	protected function sanitizeOffset($offset)
	{
		return (int) round((float) $offset);
	}

	/**
	 * Method to sanitize a width value.
	 *
	 * @param   mixed  $width   The input width value to sanitize.
	 * @param   mixed  $height  The input height value for reference.
	 *
	 * @return  integer
	 *
	 * @since   11.3
	 */
	protected function sanitizeWidth($width, $height)
	{
		// If no width was given we will assume it is a square and use the height.
		$width = ($width === null) ? $height : $width;

		// If we were given a percentage, calculate the integer value.
		if (preg_match('/^[0-9]+(\.[0-9]+)?\%$/', $width))
		{
			$width = (int) round($this->getWidth() * (float) str_replace('%', '', $width) / 100);
		}
		// Else do some rounding so we come out with a sane integer value.
		else
		{
			$width = (int) round((float) $width);
		}

		return $width;
	}

	/**
	 * Method to destroy an image handle and
	 * free the memory associated with the handle
	 *
	 * @return  boolean  True on success, false on failure or if no image is loaded
	 *
	 * @since 12.3
	 */
	public function destroy()
	{
		if ($this->isLoaded())
		{
			return imagedestroy($this->handle);
		}

		return false;
	}

	/**
	 * Method to call the destroy() method one last time
	 * to free any memory when the object is unset
	 *
	 * @see     JImage::destroy()
	 */
	public function __destruct()
	{
		$this->destroy();
	}
}
