<?php

namespace App\Models;

use MongoDB\Operation\FindOneAndUpdate;

abstract class AbstractModel
{
    public function count($params)
    {
        $params = $this->filter($params);
        $query = \DB::table($this->table);
        if ($params) {
            foreach ($params as $k => $v) {

                if (is_array($v)) {
                    // kiem tra mang nhieu chieu hay 1 chieu
                    if (array_keys($v) !== range(0, count($v) - 1)) { // nhieu chieu
                        foreach ($v as $condition => $v) {
                            switch ($condition) {
                                case 'gt':
                                    //die("OK2");
                                    $query->where($k, '>', $v);
                                    break;
                                case 'gte':
                                    $query->where($k, '>=', $v);
                                    break;
                                case 'lt':
                                    $query->where($k, '<', $v);
                                    break;
                                case 'lte':
                                    $query->where($k, '<=', $v);
                                    break;
                                case 'ne':
                                    $query->where($k, '<>', $v);
                                    break;
                                case 'like':
                                    $query->where($k, 'like', "%" . $v . "%");
                                    break;
                                case 'elemmatch':
                                    foreach ($v as $kk => $vv) {
                                        if (is_array($vv)) {
                                            $v[$kk] = ['$in' => $vv];
                                        }
                                    }
                                    $query->where($k, 'elemmatch', $v);
                                    break;
                                default:
                            }
                        }
                    } else {
                        $query->whereIn($k, $v);
                    }
                } else {
                    $query->where($k, $v);
                }
            }
        }
        return $query->count();
    }

    public function all($params = [], $options = [])
    {
        if (!empty($this->only['lists'])) {
            $arrOnly = array_merge($this->only['lists'], [$this->primaryKey]);
            $params = \Arr::only($params, $arrOnly);
        }
        $params = $this->filter($params);
        $query = \DB::table($this->table);
        if (!empty($options['select'])) {
            $query->select([$options['select']]);
        }
        if ($params) {
            foreach ($params as $k => $v) {

                if (is_array($v)) {
                    // kiem tra mang nhieu chieu hay 1 chieu
                    if (array_keys($v) !== range(0, count($v) - 1)) { // nhieu chieu
                        foreach ($v as $condition => $v) {
                            switch ($condition) {
                                case 'gt':
                                    //die("OK2");
                                    $query->where($k, '>', $v);
                                    break;
                                case 'gte':
                                    $query->where($k, '>=', $v);
                                    break;
                                case 'lt':
                                    $query->where($k, '<', $v);
                                    break;
                                case 'lte':
                                    $query->where($k, '<=', $v);
                                    break;
                                case 'ne':
                                    $query->where($k, '<>', $v);
                                    break;
                                case 'like':
                                    $query->where($k, 'like', "%" . $v . "%");
                                    break;
                                case 'elemmatch':
                                    foreach ($v as $kk => $vv) {
                                        if (is_array($vv)) {
                                            $v[$kk] = ['$in' => $vv];
                                        }
                                    }
                                    $query->where($k, 'elemmatch', $v);
                                    break;
                                default:


                            }
                        }
                    } else {
                        $query->whereIn($k, $v);
                    }
                } else {
                    $query->where($k, $v);
                }

            }
        }
        if (empty($options['order_by'])) {
            $options['order_by'] = [$this->primaryKey, 'DESC'];
        }
        $query->orderBy($options['order_by'][0], $options['order_by'][1] ?? "ASC");
        if (!empty($options['pagination'])) {
            return $query->simplePaginate($options['limit'] ?? config('data.default_limit_pagination'));
        } else {
            return $query->limit($options['limit'] ?? 100)->offset($options['offset'] ?? 0)->get();
        }
    }

    public function create(array $params)
    {
        $params = \Arr::whereNotNull($params);
        if (!empty($this->only['create'])) {
            $params = \Arr::only($params, $this->only['create']);
        }
        if (!empty($this->dataDefault['create'])) {
            $params = array_merge($params, $this->dataDefault['create']);
        }
        $params = $this->filter($params);
        if (!empty($this->idAutoIncrement)) {
            $params[$this->primaryKey] = $this->getNextSequence($this->table);
        }
        if (empty($params['created_by']) && \Auth::id()) {
            $params['created_by'] = (int)\Auth::id();
        }
        $params['created_time'] = time();

        return \DB::table($this->table)->insertGetId($params);
    }

    public function createBatch(array $multiParams)
    {
        foreach ($multiParams as $k => $params) {
            if (!empty($this->only['create'])) {
                $params = \Arr::only($params, $this->only['create']);
            }
            if (!empty($this->dataDefault['create'])) {
                $params = array_merge($params, $this->dataDefault['create']);
            }
            $params = $this->filter($params);
            if ($this->idAutoIncrement) {
                $params[$this->primaryKey] = $this->getNextSequence($this->table);
            }
            $params['created_by'] = (int)\Auth::id();
            $params['created_time'] = time();
            $multiParams[$k] = $params;
        }
        return \DB::table($this->table)->insert($multiParams);
    }

    public function update($id, $params)
    {
        $params = \Arr::whereNotNull($params);

        if (!empty($this->only['update'])) {
            $params = \Arr::only($params, $this->only['update']);
        }
        $params = $this->filter($params);
        if (!empty($this->idAutoIncrement)) {
            $id = (int)$id;
        }
        return \DB::table($this->table)->where($this->primaryKey, $id)->update($params);
        //$query->update($params);
    }

    public function updateBatch($conditions, $params)
    {
        if (!empty($this->only['updateBatchCondition'])) {
            $conditions = \Arr::only($conditions, $this->only['updateBatchCondition']);
        }
        if (!empty($this->only['updateBatch'])) {
            $params = \Arr::only($params, $this->only['updateBatch']);
        }
        $params = $this->filter($params);
        $condition = $this->filter($conditions);
        $query = \DB::table($this->table);
        foreach ($conditions as $k => $v) {
            switch ($k) {
                default:
                    if (is_array($v)) {
                        $query->whereIn($k, $v);
                    } else {
                        $query->where($k, $v);
                    }
                    break;
            }
        }
        $query->update($params);
    }

    public function deleteBatch($conditions)
    {
        if (!empty($this->only['deleteBatch'])) {
            $conditions = \Arr::only($conditions, $this->only['deleteBatch']);
        }
        $conditions = $this->filter($conditions);
        $query = \DB::table($this->table);
        foreach ($conditions as $k => $v) {
            switch ($k) {
                default:
                    if (is_array($v)) {
                        $query->whereIn($k, $v);
                    } else {
                        $query->where($k, $v);
                    }
                    break;
            }
        }
        $query->delete();
    }

    public function detail($id)
    {
        if (!empty($this->idAutoIncrement)) {
            $id = (int)$id;
        }
        return \DB::table($this->table)->where($this->primaryKey, $id)->first();
    }

    public function remove($id)
    {
        if (!empty($this->idAutoIncrement)) {
            $id = (int)$id;
        }
        return \DB::table($this->table)->where($this->primaryKey, $id)->delete();
    }

    public function filter($params)
    {
        if (empty($this->casts) || empty($params)) {
            return $params;
        }
        //$params = \Arr::dot($params);

        $result = [];
        $arrKeys = array_keys($params);
        foreach ($this->casts as $formatType => $v) {
            //////////// CHECK CAC KEY TRUNG //////
            //var_dump($v,$arrKeys);
            // $keysIntersect = array_intersect($v,$arrKeys);
            // if (!$keysIntersect) {
            //     continue;
            // }

            foreach ($v as $key) {
                if (isset($params[$key])) {
                    $isRegex = 0;
                } elseif (strpos($key, '.') !== false) {
                    $arr = explode('.', $key, 2);
                    if (!isset($params[$arr[0]])) {
                        continue;
                    }
                    $data = \Arr::dot($params[$arr[0]]);
                    $isRegex = 1;
                } else {
                    continue;
                }

                switch ($formatType) {
                    case 'integer':
                        if ($isRegex == 0) {
                            $params[$key] = (!is_array($params[$key])) ? (int)$params[$key] : array_map("intval", $params[$key]);
                        } else {

                            foreach ($data as $k => $dt) {
                                $kk = preg_replace('/(\.|^)(\d+)(\.|$)/', '.', $k);
                                $kk = trim($kk, '.');
                                //var_dump($arr[0].'.'.$k,$key);
                                if ($arr[0] . '.' . $kk == $key) {
                                    $data[$k] = (int)$dt;
                                }
                            }
                            $data = \Arr::undot($data);
                            $params[$arr[0]] = $data;
                        }

                        break;
                    case 'string':
                        $params[$key] = (!is_array($params[$key])) ? (string)$params[$key] : array_map("strval", $params[$key]);
                        break;
                    case 'unixtime':

                        if ((!is_array($params[$key])))
                            $params[$key] = (is_numeric($params[$key])) ? (int)$params[$key] : strtotime($params[$key]);
                        else {
                            $params[$key] = array_map(function ($item) {
                                return (is_numeric($item)) ? (int)$item : strtotime($item);
                            }, $params[$key]);
                        }
                        break;
                }
            }
        }
        return $params;
    }

    public function aggregate($aggregate)
    {
        $aggregate = $this->replaceKey($aggregate);
        if (!empty($aggregate[0]['$match'])) {
            $aggregate[0]['$match'] = $this->getMatch($aggregate[0]['$match']);
        }
        return \DB::collection($this->table)->raw(function ($collection) use ($aggregate) {
            return $collection->aggregate(
                $aggregate
            );
        });
    }

    public function getNextSequence($name)
    {
        $seq = \DB::getCollection('counters')->findOneAndUpdate(
            array('_id' => $name),
            array('$inc' => array('seq' => 1)),
            array('new' => true, 'upsert' => true, 'returnDocument' => FindOneAndUpdate::RETURN_DOCUMENT_AFTER)
        );
        return $seq->seq;
    }

    public function replaceKey($arr)
    {
        if (empty($arr)) {
            return $arr;
        }
        $dataEncoded = json_encode($arr);
        $dataEncoded = str_replace('"gt":', '"$gt":', $dataEncoded);
        $dataEncoded = str_replace('"lt":', '"$lt":', $dataEncoded);
        $dataEncoded = str_replace('"lte":', '"$lte":', $dataEncoded);
        $dataEncoded = str_replace('"gte":', '"$gte":', $dataEncoded);
        return json_decode($dataEncoded, TRUE);
    }

    public function getMatch($params)
    {
        $params = $this->filter($params);

        foreach ($params as $k => $v) {
            if (is_array($v)) {
                // kiem tra mang nhieu chieu hay 1 chieu
                if (array_keys($v) === range(0, count($v) - 1)) { // nhieu chieu
                    $params[$k] = ['$in' => $v];
                }
            } else {

            }
        }
        return $params;
    }
}

