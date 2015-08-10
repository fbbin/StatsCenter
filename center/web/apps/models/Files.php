<?php
namespace App\Model;
use Swoole;

class Files extends Swoole\Model
{
    public $table = 'files';

    function add_if_not_exists($files)
    {
        if (!is_array($files))
        {
            $files = array($files);
        }

        $res = $this->gets(array(
            'where' => "url IN ('" . implode("', '", $files) . "')")
        );
        if ($res === false)
        {
            return false;
        }
        $existent_files = array_rebuild($res, 'id', 'url');

        $nonexistent_files = array_diff($files, $existent_files);

        foreach ($nonexistent_files as $file)
        {
            $this->put(array(
                'url' => $file,
            ));
        }

        return true;
    }
}
