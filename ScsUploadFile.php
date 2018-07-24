<?php
namespace kordar\upload;

use kordar\upload\scs\SCS;
use kordar\upload\scs\ScsHelper;
use yii\web\UploadedFile;

class ScsUploadFile extends SingleUploadFile
{
    /**
     * @var string
     * Root directory, default is "upload"
     */
    public $bucket = 'uploads';

    public $host = 'http://sinacloud.net/';


    public function run()
    {
        if (\Yii::$app->request->isPost) {

            $this->model->file = UploadedFile::getInstanceByName($this->name);

            if ($this->model->file && $this->model->validate()) {

                $scs = ScsHelper::getScsInstance();

                $fileName = $this->getPath(\Yii::$app->request->post('filename', ''));

                if ($scs->putObjectFile($this->model->file->tempName, $this->bucket, $fileName, SCS::ACL_PUBLIC_READ)) {
                    return call_user_func($this->successMessage, $this->host . $this->bucket . '/' . $fileName);
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