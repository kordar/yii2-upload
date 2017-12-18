<?php
namespace kordar\upload;

use yii\base\Model;
use yii\web\UploadedFile;

class SingleUploadForm extends Model
{
    /**
     * @var UploadedFile $file
     */
    public $file;

    public $rules = [
        [['file'], 'file'],
    ];

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return $this->rules;
    }
}