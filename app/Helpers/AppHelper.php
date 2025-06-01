<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AppHelper
{

    // Is Super Admin
    public static function isSuperAdmin()
    {
        $roles = Auth::guard('admin-api')->user()->roles;
        $roleFilter = collect($roles)->where('name', 'Super Admin')->all();
        return count($roleFilter) > 0 ? true : false;
    }
    
    // Is Super Developer
    public static function isDeveloper()
    {
        $roles = Auth::guard('admin-api')->user()->roles;
        $roleFilter = collect($roles)->where('name', 'Developer')->all();
        return count($roleFilter) > 0 ? true : false;
    }
    
    /**
     * @param $permission
     * @param $action
     * @return bool
     */
    public static function isPermissionAction($permission, $action): bool
    {
        if (auth()->user()->can($permission,$action)) {
            return true;
        }
        return false;
    }

    
	public static function createPath($path)
	{
		$paths = explode('/', $path);
		if (count($paths) > 0) {
			$newPath = '';
			foreach ($paths as $_path) {
				$newPath .= $_path . '/';
				if (!File::exists($newPath)) {
					File::makeDirectory($newPath);
				}
			}
			return $newPath;
		}
		return $path;
	}

	public static function uploadImage($requestFile, $path)
	{
		if ($requestFile) {
			$format = $requestFile->getClientOriginalExtension();
			$imgPath = uniqid() . '.' . $format;
			$fullPath = $requestFile->storeAs($path, $imgPath);
		} else {
			$format = "";
			$imgPath = null;
		}
		return $fullPath;
	}

    

    // public static function createPath($path)
    // {
    //     $paths = explode('/', $path);
    //     if (count($paths) > 0) {
    //         $newPath = '';
    //         foreach ($paths as $_path) {
    //             $newPath .= $_path . '/';
    //             if (!File::exists($newPath)) {
    //                 File::makeDirectory($newPath);
    //             }
    //         }
    //         return $newPath;
    //     }
    //     return $path;
    // }
    
    public static function deletePath($path)
    {
        if (File::exists($path)) {
            File::delete($path);
        }
        return null;
    }
    
    // public static function uploadImage($requestFile, $path)
    // {
    //     if ($requestFile) {
    //         $format = $requestFile->getClientOriginalExtension();
    //         // ->resize(1280, 1280)
    //         $img = Image::make($requestFile);
    //         $imgPath = $path . uniqid() . '.' . $format;
    //         $img->save($imgPath, 50);
    //     } else {
    //         $format = "";
    //         $imgPath = null;
    //     }
    //     return $imgPath;
    // }
    
    public static function uploadTextEditorImage($requestFile, $path){
        if ($requestFile) {
            $uuid = uniqid();
            $format = $requestFile->getClientOriginalExtension();
            // ->resize(1280, 1280)
            $img = Image::make($requestFile);
            $imgPath = $path . $uuid . '.' . $format;
            $imgName = $uuid . '.' . $format;
            $img->save($imgPath, 50);
        } else {
            $imgPath = null;
        }
        return $imgName;
    }
        
    // public static function uploadImage($requestFile, $path)
    // {
    //     if (!$requestFile) {
    //         return null;
    //     } 

    //     return $requestFile->store($path);
    // }
    public static function cleanText($text){
        $specialCharacters = ['+', '&', '&&', '||', '!', '(', ')', '{', '}', '[', ']', '^', '"', '~', '?', ':', '\\', '/'];
        return str_replace($specialCharacters, '', $text);
    }
    
}






