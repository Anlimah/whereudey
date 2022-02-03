<?php

namespace Src\TableGateways;

use Src\System\DatabaseMethods;

class VideoGateway
{
    private $from = null;
    private $to = null;

    private $dm = null;

    public function __construct()
    {
        $this->dm = new DatabaseMethods();
    }

    private function setDates($when)
    {
        $when = (int) $when;
        if ($when == 1) {
            $this->from = date("Y-m-d");
            $this->to = date("Y-m-d");
        } else if ($when == 7) {
            $this->from = date("Y-m-d");
            $to = date_create($this->from);
            date_sub($to, date_interval_create_from_date_string("7 days"));
            $this->to = date_format($to, "Y-m-d");
        } else if ($when == 30) {
            $this->from = date("Y-m-d");
            $to = date_create($this->from);
            date_sub($to, date_interval_create_from_date_string("30 days"));
            $this->to = date_format($to, "Y-m-d");
        }
    }

    private function getDateFrom()
    {
        if ($this->from != null)
            return $this->from;
    }

    private function getDateTo()
    {
        if ($this->to != null)
            return $this->to;
    }

    public function findAll($where, $when)
    {
        $this->setDates($when);
        $stmt = "CALL procMost(:c, :s, :e);";
        $params = array(':c' => $where, ':s' => $this->getDateFrom(), ':e' => $this->getDateTo());
        try {
            return $this->dm->getData($stmt, $params);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function find($id)
    {
        $stmt = "SELECT * FROM `music_data` WHERE `id` = :id;";
        $params = array(':id' => $id);

        try {
            return $this->dm->getData($stmt, $params);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function insert(array $input)
    {
        $stmt = "
            INSERT INTO links (url, title, pub, dur, ch_name, ch_url, ch_subs, added_at)
            VALUES (:url, :title, :pub, :dur, :ch_name, :ch_url, :ch_subs, :ad_at);
        ";
        $params = array(
            'url' => $input['url'],
            'title' => $input['title'],
            'pub' => $input['pub'],
            'dur' => $input['dur'],
            'ch_name' => $input['ch_name'],
            'ch_url' => $input['ch_url'],
            'ch_subs' => $input['ch_subs'],
            'added_at' => $input['ad_at'],
        );
        try {
            $stmt = $this->dm->inputData($stmt, $params);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function update($id, array $input)
    {
        $stmt = "
            UPDATE links 
            SET 
                url = :url, title = :title, pub = :pub, dur = :dur, 
                ch_name = :ch_name, ch_url = :ch_url, ch_subs = :ch_subs, 
                updated_at = :up_at 
            WHERE id = :id;
        ";
        $params = array(
            'id' => (int) $id,
            'url' => $input['url'],
            'title' => $input['title'],
            'pub' => $input['pub'],
            'dur' => $input['dur'],
            'ch_name' => $input['ch_name'],
            'ch_url' => $input['ch_url'],
            'ch_subs' => $input['ch_subs'],
            'updated_at' => $input['up_at'],
        );
        try {
            $stmt = $this->dm->inputData($stmt, $params);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function delete($id)
    {
        $stmt = "DELETE FROM links WHERE id = :id;";
        $params = array(':id' => $id);
        try {
            $stmt = $this->dm->inputData($stmt, $params);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
}
