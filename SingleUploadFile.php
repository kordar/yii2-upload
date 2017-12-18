<?php
namespace kordar\upload;

use kordar\upload\helper\UploadHelper;
use yii\base\Action;
use yii\web\UploadedFile;

class SingleUploadFile extends Action
{
    /**
     * @var \kordar\upload\SingleUploadForm $model
     */
    protected $model;

    public $extensions = 'jpg,png,gif,jpeg';
    public $maxSize = 1024 * 1024;

    public $root = 'uploads';

    public $autoSubDateRoot = false;    // 时间格式

    public function beforeRun()
    {
        $this->model = new SingleUploadForm();
        $this->model->rules[] = [['file'], 'file', 'extensions' => $this->extensions, 'maxSize' => $this->maxSize];
        return true;
    }

    protected function getPath()
    {
        $path = $this->root . '/';
        if ($this->autoSubDateRoot) {
            $path .= date($this->autoSubDateRoot) . '/';
        }
        return $path;
    }

    protected function uniqid()
    {
        if (function_exists('session_create_id')) {
            return session_create_id();
        }
        return md5(uniqid(md5(microtime(true)),true));
    }

    public function run()
    {
        if (\Yii::$app->request->isPost) {
            $this->model->file = UploadedFile::getInstance($this->model, 'file');
            if ($this->model->file && $this->model->validate()) {
                $path = $this->getPath();
                if (!file_exists($path)) {
                    UploadHelper::createDir($path);
                }
                $fileName = $path . $this->uniqid() . '.' . $this->model->file->extension;
                $this->model->file->saveAs($fileName);
                return json_encode(['status' => 'success', 'path' => $fileName]);
            } else {
                $err = $this->model->getFirstErrors();
                return json_encode(['status' => 'fail', 'msg' => $err['file']]);
            }
        }
        return json_encode(['status' => 'fail', 'msg' => 'Upload Fail']);
    }

}