<?php namespace Modules\Media\Services;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Queue\Jobs\Job;
use Modules\Media\Helpers\FileHelper;
use Modules\Media\Repositories\FileRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileService {
    /**
     * @var FileRepository
     */
    private $file;
    /**
     * @var Repository
     */
    private $config;
    /**
     * @var Queue
     */
    private $queue;

    public function __construct(FileRepository $file, Repository $config, Queue $queue) {
        $this->file = $file;
        $this->config = $config;
        $this->queue = $queue;
    }

    /**
     * @param UploadedFile $file
     * @param array $optional
     * @return mixed|string
     */
    public function store(UploadedFile $file, $optional = []) {
        $prefix = str_random_not_cap(4);
        $filename = FileHelper::slug($prefix . "-" . FileHelper::limit($file->getClientOriginalName(), 59));
        //$filename = FileHelper::slug($file->getClientOriginalName());

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file);
        finfo_close($finfo);

        list($type, $subtype) = explode('/', $mimeType);
        $aspectRatio = 1.78;
        if ($type == 'image' && in_array($subtype, ['gif', 'png', 'jpeg'])) {
            $info = getimagesize($file);
            $width = $info[0];
            $height = $info[1];
            $optional['width'] = $width;
            $optional['height'] = $height;
            if ($width && $height) $aspectRatio = round($width / $height, 2);
        }

        $configFilePath = $this->config->get('media.config.files-path');
        $filePath = $configFilePath;
        //if (isset($optional['user_id'])) $filePath = rtrim($filePath, '/') . '/uid' . $optional['user_id'];
        if (isset($optional['sub'])) $filePath = rtrim($filePath, '/') . '/' . trim($optional['sub']);
        $filePath = rtrim($filePath, '/') . '/' . date('Ymd');
        $path = rtrim($filePath, '/') . '/' . $filename;

        $ok = \Storage::disk($this->config->get('filesystems.default'))->getDriver()->put($path, fopen($file, 'r+'), ['visibility' => 'public', 'ContentType' => $mimeType]);
        if (!$ok) return ("Can not upload file to service");
        // Save to origin
        $originPath = str_replace(rtrim($configFilePath, '/'), rtrim($configFilePath, '/') . '/origin', $path);
        \Storage::disk($this->config->get('filesystems.default'))->getDriver()->put($originPath, fopen($file, 'r+'), ['visibility' => 'public', 'ContentType' => $mimeType]);
        // Save the file info to db
        try {
            $optional = array_merge($optional, ['filename' => $filename, 'path' => $path]);
            $savedFile = $this->file->createFromFile($file, $optional);
        } catch (\InvalidArgumentException $e) {
            return $e->getMessage();
        }

        // Move the uploaded file to files path
        //$file->move(public_path() . $this->config->get('media.config.files-path'), $savedFile->filename);
        //@chmod(public_path() . $this->config->get('media.config.files-path') . $savedFile->filename, 0666);
        //dispatch(new \Modules\Media\Jobs\JobThumbs($aspectRatio, $savedFile->path));

        if ($type == 'image' && !in_array($subtype, ['gif', 'svg'])) {
            if ($aspectRatio == 0.56) { //9:16
                app('imagy')->createAll_9by16($savedFile->path);
            } else if ($aspectRatio == 1.33) { //4:3
                app('imagy')->createAll_4by3($savedFile->path);
            } else if ($aspectRatio == 0.75) { //3:4
                app('imagy')->createAll_3by4($savedFile->path);
            } else if ($aspectRatio == 1) { //1:1
                app('imagy')->createAll_1by1($savedFile->path);
            } else if ($aspectRatio == 1.78) { //16:9
                app('imagy')->createAll($savedFile->path);
            } else {
                app('imagy')->createDefaultThumbs($savedFile->path);
            }
        }

        return $savedFile;
    }
}
