<?php
/**
 * Trilhas - Learning Management System
 * Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @author Mohamed Alsharaf
 * @author Abdala Cequeira <abdala.cerqueira@gmail.com>
 *
 */
class Tri_View_Helper_Thumbnail extends Zend_View_Helper_Abstract
{
    private $_name   = null;
    private $_width  = null;
    private $_height = null;
    private $_type   = array('small'  => array('width' => 40, 'height' => 40),
                             'medium' => array('width' => 200, 'height' => 200));

    const IMAGETYPE_GIF  = 'image/png';
    const IMAGETYPE_JPEG = 'image/jpeg';
    const IMAGETYPE_PNG  = 'image/jpg';
    const IMAGETYPE_JPG  = 'image/gif';

    public function thumbnail($path, $type)
    {
        $thumb = UPLOAD_DIR . $path . $type . '.png';
        if (!file_exists($thumb)) {
            $this->_open(UPLOAD_DIR . $path);
            $this->_resize($this->_type[$type]['width'], $this->_type[$type]['height']);
            imagepng($this->_image, $thumb, 0);
        }
        return $path . $type . '.png';
    }

    protected function _setInfo($path)
    {
        $imgSize = @getimagesize($path);
        if(!$imgSize) {
            throw new Exception('Could not extract image size.');
        } elseif ($imgSize[0] == 0 || $imgSize[1] == 0) {
            throw new Exception('Image has dimension of zero.');
        }
        $this->_width    = $imgSize[0];
        $this->_height   = $imgSize[1];
        $this->_mimeType = $imgSize['mime'];
    }

    protected function _setDimension($forDim, $maxWidth, $maxHeight)
    {
        if ($this->_width > $maxWidth) {
            $ration = $maxWidth/$this->_width;
            $newwidth = round($this->_width*$ration);
            $newheight = round($this->_height*$ration);

            if ($newheight > $maxHeight) {
                $ration = $maxHeight/$newheight;
                $newwidth = round($newwidth*$ration);
                $newheight = round($newheight*$ration);

                if ($forDim == 'w') {
                    return $newwidth;
                } else {
                    return $newheight;
                }
            } else {
                if ($forDim == 'w') {
                    return $newwidth;
                } else {
                    return $newheight;
                }
            }
        } else if ($this->_height > $maxHeight) {
            $ration = $maxHeight/$this->_height;
            $newwidth = round($this->_width*$ration);
            $newheight = round($this->_height*$ration);
            if ($newwidth > $maxWidth) {
                $ration = $maxWidth/$newwidth;
                $newwidth = round($newwidth*$ration);
                $newheight = round($newheight*$ration);
                if ($forDim == 'w') {
                    return $newwidth;
                } else {
                    return $newheight;
                }
            } else {
                if ($forDim == 'w') {
                    return $newwidth;
                } else {
                    return $newheight;
                }
            }
        } else {
            if($forDim == 'w') {
                return $this->_width;
            } else {
                return $this->_height;
            }
        }
    }

    protected function _open($path)
    {
        $this->_setInfo($path);
        switch($this->_mimeType) {
            case self::IMAGETYPE_GIF:
                $this->_image = imagecreatefromgif($path);
                break;
            case self::IMAGETYPE_JPEG:
            case self::IMAGETYPE_JPG:
                $this->_image = imagecreatefromjpeg($path);
                break;
            case self::IMAGETYPE_PNG:
                $this->_image = imagecreatefrompng($path);
                break;
            default:
                throw new Exception('Image extension is invalid or not supported.');
                break;
        }
    }

    public function _resize($maxWidth, $maxHeight)
    {
        $newWidth  = $this->_setDimension('w', $maxWidth, $maxHeight);
        $newHeight = $this->_setDimension('h', $maxWidth, $maxHeight);

        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($newImage, $this->_image, 0, 0, 0, 0, $newWidth, $newHeight, $this->_width, $this->_height);

        $this->_image = $newImage;
    }
}
