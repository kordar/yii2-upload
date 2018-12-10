<?php
namespace kordar\upload;

use kordar\upload\oss\OssHelper;
use OSS\Core\OssException;
use yii\web\UploadedFile;

class AliYunOss extends SingleUploadFile
{
    /**
     * @var string
     * Root directory, default is "upload"
     */
    public $bucket = 'uploads';

    public $host = '';


    public function run()
    {
        if (\Yii::$app->request->isPost) {

            $this->model->file = UploadedFile::getInstanceByName($this->name);

            if ($this->model->file && $this->model->validate()) {

                $fileName = $this->getPath(\Yii::$app->request->post('filename', ''));

                try {
                    $oss = OssHelper::getOssInstance();
                    if ($oss->uploadFile($this->bucket, $fileName, $this->model->file->tempName)) {
                        return call_user_func($this->successMessage, $this->host . $fileName);
                    }
                } catch (OssException $e) {
                    return call_user_func($this->errorMessage, $e->getMessage());
                }

            } else {
                $err = $this->model->getFirstErrors();
                return call_user_func($this->errorMessage, $err['file']);
            }
        }
        return call_user_func($this->errorMessage, \Yii::t('upload', 'Upload Fail'));
    }


    protected function getPath($filename)
    {
        if (!empty($filename)) {
            $pathParam = explode('?', $filename);
            $filename = $pathParam[0];
            $path = dirname($filename) . '/';
        } else {
            if ($this->autoSubDateRoot) {
                $this->autoSubDateRoot = date($this->autoSubDateRoot);
            }
            $path = UploadHelper::getPath([$this->category, $this->autoSubDateRoot]);
            if (!empty($path)) {
                $path .= '/';
            }
        }

        return empty($filename) ? $path . $this->uniqid() . '.' . $this->model->file->extension : $filename;
    }

}