<?php
namespace App\Model;
use Swoole;
use App\CdnMgr;

class Files extends Swoole\Model
{
    public $table = 'files';

    function addFiles($files)
    {
        $file_list = array();
        $record_list = array();

        foreach ($files as $file)
        {
            $params = array();
            $params['UrlList.0'] = $file->file;
            $params['Md5'] = $file->md5;

            $file_list[] = "'{$file->file}'";
            $record_list[] = "('{$file->file}', '{$file->md5}')";
        }
        $file_list_str = implode(', ', $file_list);
        $record_list_str = implode(', ', $record_list);

        $sql = "DELETE FROM %s WHERE url IN (%s)";
        $res = $this->db->query(sprintf($sql, $this->table, $file_list_str));
        if (!$res->result)
        {
            return false;
        }

        $sql = "INSERT INTO %s (url, md5) VALUES %s";
        $res = $this->db->query(sprintf($sql, $this->table, $record_list_str));
        if (!$res->result)
        {
            return false;
        }

        return $res->result;
    }
}
