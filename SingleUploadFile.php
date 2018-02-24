<?php
namespace kordar\upload;

use kordar\upload\UploadHelper;
use yii\base\Action;
use yii\web\UploadedFile;

class SingleUploadFile extends Action
{
    /**
     * @var \kordar\upload\SingleUploadForm $model
     */
    protected $model;

    /**
     * @var string
     * File extensions that are allowed to be uploaded
     * images eg. jpg,png,gif,jpeg
     * text eg. txt,doc
     * ...
     */
    public $extensions = 'jpg,png,gif,jpeg';

    /**
     * @var int
     * The maximum size of the file to be uploaded.
     * eg. 1024*1024
     */
    public $maxSize = 1024 * 1024;

    /**
     * @var string
     * Root directory, default is "upload"
     */
    public $root = 'uploads';

    /**
     * @var string
     * Categories, eg. "test",
     * generate a directory structure: "uploads/test"
     */
    public $category = '';

    /**
     * @var string
     * like Categorys, recommend formate "Y/m/d"
     */
    public $autoSubDateRoot = '';    // 时间格式

    public function beforeRun()
    {
        $this->setResponseFormat();
        $this->model = new SingleUploadForm();
        $this->model->rules[] = [['file'], 'file', 'extensions' => $this->extensions, 'maxSize' => $this->maxSize];
        return true;
    }

    protected function getPath($filename)
    {
        if (!empty($filename)) {
            $pathParam = explode('?', $filename);
            $filename = $pathParam[0];
            $path = dirname($filename);
        } else {
            if ($this->autoSubDateRoot) {
                $this->autoSubDateRoot = date($this->autoSubDateRoot);
            }
            $path = UploadHelper::getPath([$this->root, $this->category, $this->autoSubDateRoot]);
        }

        if (!file_exists($path)) {
            UploadHelper::createDir($path);
        }

        return empty($filename) ? $path . '/' . $this->uniqid() . '.' . $this->model->file->extension : $filename;
    }

    protected function uniqid()
    {
        /*if (function_exists('session_create_id')) {
            return session_create_id();
        }*/
        return md5(uniqid(md5(microtime(true)),true));
    }

    public function run()
    {
        if (\Yii::$app->request->isPost) {
            $this->model->file = UploadedFile::getInstance($this->model, 'file');
            if ($this->model->file && $this->model->validate()) {
                $fileName = $this->getPath(\Yii::$app->request->post('filename', ''));
                if ($this->model->file->saveAs($fileName)) {
                    return ['status' => 200, 'path' => $fileName . '?' . time(), 'msg' => 'success'];
                }
            } else {
                $err = $this->model->getFirstErrors();
                return ['status' => 201, 'msg' => $err['file'], 'path' => ''];
            }
        }
        return ['status' => 202, 'msg' => \Yii::t('upload', 'Upload Fail'), 'path' => ''];
    }

    protected function setResponseFormat()
    {
        $response = \Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
    }

}