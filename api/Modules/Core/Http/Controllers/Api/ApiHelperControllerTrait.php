<?php namespace Modules\Core\Http\Controllers\Api;

use Illuminate\Database\Eloquent\Model;

/***
 * Trait ApiHelperControllerTrait
 *
 * @package Modules\Core\Http\Controllers\Api
 */
trait ApiHelperControllerTrait {
    protected function removeNullValueFromArray($input) {
        foreach ($input as $key => $value) {
            if (is_null($value)) {
                unset($input[$key]);
            } else if (is_array($value)) {
                $newValue = $this->removeNullValueFromArray($value);
                if (is_array($newValue) && empty($newValue)) {
                    unset($input[$key]);
                } else {
                    $input[$key] = $newValue;
                }
            }
        }

        return $input;
    }

    /***
     * Get data
     *
     * @return mixed
     */
    protected function getRequestData() {
        return json_decode(urldecode($this->request->get('data')));
    }

    /***
     * Get field by key
     *
     * @param $key
     * @return mixed|null
     */
    protected function getRequestDataField($key) {
        if ($data = $this->getRequestData()) {
            if (isset($data->{$key})) return $data->{$key};
        }

        return null;
    }

    /***
     * Get embed
     *
     * @return array
     */
    protected function getRequestEmbed() {
        if ($data = json_decode(urldecode($this->request->get('data')))) {
            if (!isset($data->embed)) return [];
            $results = [];
            foreach (explode(',', $data->embed) as $embed) {
                $embed = str_replace(' ', '', str_replace('/\s+/', '', $embed));
                if (!empty($embed)) {
                    $items = explode('.', $embed);
                    if (!isset($results[$items[0]])) $results[$items[0]] = [];
                    if (isset($items[1])) $results[$items[0]][] = $items[1];
                }
            }
            // Remove duplicate
            $data = [];
            foreach ($results as $key => $value) $data[$key] = array_unique($value);

            return $data;
        }

        return [];
    }

    /***
     * Get filter
     *
     * @return array
     */
    protected function getRequestFilter() {
        if ($data = json_decode(urldecode($this->request->get('data')))) {
            if (!isset($data->filter)) return [];
            $filter = json_decode(json_encode($data->filter), true);
            if (!is_array($filter)) return [];
            $data = [];
            foreach ($filter as $key => $value) {
                if (!is_null($value) && $value !== '') $data[$key] = $value;
            }
            return $data;
        }
        return [];
    }

    /**
     * Get Input
     * Remove null value, empty value
     */
    protected function getRequestInput() {
        return $this->removeNullValueFromArray($this->request->all());
    }

    /**
     * Get Ids with param
     *
     * @param $paramName
     * @return array|null
     */
    protected function getRequestParamIds($paramName) {
        $input = $this->getRequestInput();
        $result = isset($input[$paramName]) ? $input[$paramName] : null;
        if (is_string($result)) {
            $output = json_decode(urldecode($result), true);
            if (is_null($output)) {
                $output = [];
                foreach (explode(',', $result) as $value) $output[] = $value;
            }
            $result = is_numeric($output) ? [(int)$output] : $output;
        }
        if (is_array($result)) {
            $tmp = [];
            foreach ($result as $id) {
                if (is_array($id)) {
                    foreach ($id as $value) {
                        if ($value != '' && !is_null($value)) $tmp[] = (int)$value;
                    }
                } else {
                    if (is_numeric($id)) {
                        $tmp[] = (int)$id;
                    } else if ($id != '' && !is_null($id)) {
                        $tmp[] = (int)$id;
                    }
                }
            }
            $result = array_unique($tmp);
        } else if (is_numeric($result)) {
            return [(int)$result];
        } else if (is_null($result)) {
            return null;
        }

        return $result;
    }

    /***
     * Get fields
     *
     * @param string $fieldName
     * @return array
     */
    protected function getRequestFields($fieldName = 'fields') {
        if ($data = json_decode(urldecode($this->request->get('data')))) {
            if (!isset($data->{$fieldName})) return [];
            $values = str_replace(' ', '', str_replace('/\s+/', '', (string)$data->{$fieldName}));
            $arr = [];
            foreach (explode(',', $values) as $v) {
                $v = trim($v);
                if ($v) $arr[] = $v;
            }

            return array_unique($arr);
        }

        return [];
    }

    /** Get Request File
     *
     * @param string $field_name
     * @param string $allowed_type
     * @return array
     */
    protected function getRequestFile($field_name = 'file', $allowed_type = '*') {
        if (!$field_name) $field_name = 'file';
        $file = $this->request->file($field_name);
        if ($file) {
            $mimeType = $file->getMimeType();
            list($type, $subtype) = explode('/', $mimeType);
            //=== Check file size
            $fileSize = $file->getSize();
            if ($type == 'video') {
                if ($fileSize > config('media.config.video-max-total-size')) return [$file, 'file.video_max_size'];
            } else if ($type == 'image') {
                if ($fileSize > config('media.config.max-total-size')) return [$file, 'file.max'];
            } else {
                if ($fileSize > config('media.config.file-max-total-size')) return [$file, 'file.max'];
            }
            //=== Check extension
            if ($allowed_type != '*') {
                if ($allowed_type && $allowed_type != '*' && $allowed_type != $type) return [$file, 'file.mime'];
                if (in_array($type, ['image', 'video'])) {
                    $mimeTypes = [];
                    foreach (explode(',', config('media.config.allowed-types')) as $subtype) $mimeTypes[] = str_replace('.', $type . '/', $subtype);
                    if (!in_array($mimeType, $mimeTypes)) return [$file, 'file.mime'];
                } else {
                    return [$file, 'file.mime'];
                }

            }
            return [$file, null];
        }
        return [null, null];
    }

    /** Get Request Files
     *
     * @param string $field_name
     * @param string $allowed_type
     * @return array
     */
    protected function getRequestFiles($field_name = 'files', $allowed_type = '*') {
        if (!$field_name) $field_name = 'files';
        $f = [];
        $errorKeys = [];
        $files = $this->request->file($field_name);
        if (!empty($files)) foreach ($files as $file) {
            $mimeType = $file->getMimeType();
            list($type, $subtype) = explode('/', $mimeType);
            //=== Check file size
            $fileSize = $file->getSize();
            if ($type == 'video') {
                if ($fileSize > config('media.config.video-max-total-size')) {
                    $errorKeys[] = 'file.video_max_size';
                    continue;
                }
            } else if ($type == 'image') {
                if ($fileSize > config('media.config.max-total-size')) {
                    $errorKeys[] = 'file.max';
                    continue;
                }
            } else {
                if ($fileSize > config('media.config.file-max-total-size')) {
                    $errorKeys[] = 'file.max';
                    continue;
                }
            }
            //=== Check extension
            if ($allowed_type != '*') {
                if (in_array($type, ['image', 'video'])) {
                    $mimeTypes = [];
                    foreach (explode(',', config('media.config.allowed-types')) as $subtype) $mimeTypes[] = str_replace('.', $type . '/', $subtype);
                    if (!in_array($mimeType, $mimeTypes)) {
                        $errorKeys[] = 'file.mime';
                        continue;
                    }
                } else {
                    $errorKeys[] = 'file.mime';
                    continue;
                }
            }
            $f[] = $file;
        }

        return [$f, $errorKeys];
    }

    /**
     * Get Where Raw Queries
     *
     * @param string $table
     * @return array
     */
    protected function getWhereRawQueries($table = '') {
        $whereRaw = [];
        $filters = [];
        // Get Request Filter
        // $filter = $this->getRequestFilter();
        if ($data = json_decode(urldecode($this->request->get('data')))) {
            if (isset($data->filter)) {
                $f = json_decode(json_encode($data->filter), true);
                if (is_array($f)) foreach ($f as $k => $value) {
                    if (is_array($value) &&
                        isset($value['operator']) && in_array(strtolower($value['operator']), ['=', '<', '<=', '>', '>=', 'like', 'in']) &&
                        isset($value['value']) && !is_null($value['value']) && $value['value'] !== '')
                        $filters[$k] = $value;
                }
            }
        }
        if (!empty($filters)) foreach ($filters as $k => $f) {
            $operator = strtolower($f['operator']);
            $value = is_string($f['value']) ? trim($f['value']) : $f['value'];
            $fieldName = $table ? "$table.$k" : "$k";
            if ($k == 'phone_number') {
                list($calling_code, $phone_number) = calling2phone($value);
                if ($phone_number) {
                    $whereRaw[] = ["$fieldName $operator ?", ["%" . $phone_number . "%"]];
                } else {
                    $whereRaw[] = ["$fieldName $operator ?", ["%" . $value . "%"]];
                }
            } else if ($operator == 'like') {
                $whereRaw[] = ["lower($fieldName) $operator ?", ["%" . utf8_strtolower($value) . "%"]];
            } else if ($operator == 'in') {
                if (is_array($value) && !empty($value)) {
                    $temp = [];
                    foreach ($value as $v) $temp[] = "'$v'";
                    $whereRaw[] = ["$fieldName in (" . implode(', ', $temp) . ")"];
                }
            } else {
                $whereRaw[] = ["$fieldName $operator ?", [$value]];
            }
        }

        return $whereRaw;
    }

    /***
     * SetUpQueryBuilder
     *
     * @param $query_builder
     * @param array $queries
     * @param bool $is_count
     * @param array $default_fields
     * @param string $table
     * @param string $whereRawTable
     * @return \Illuminate\Database\Eloquent\Builder|Model
     * $data = {"filter":{"type":{"operator":"=","value":""}},"embed":""}
     * $queries = [
     *               ['user_id', '=',1],
     *               'and' => [
     *                  ['is_read', '=', false],
     *               ],
     *               'or'  => [
     *                  ['client_id', '=', 1],
     *                ],
     *               'orOr'  => [
     *                ],
     *               'orAnd'  => [
     *                ],
     *               'in'  => [
     *                ],
     *                'notIn' => [
     *                  ['key', []]
     *                ],
     *               'withHas'  => [
     *                  'job' => [
     *                    ['status', '=', '1'],
     *                   ],
     *                ],
     *               'whereRaw' => [
     *                  ["modified_at IS NOT NULL"],
     *               ],
     *               'orWhereRaw' => [
     *               ],
     *               'orOrWhereRaw' => [
     *               ],
     *            ];
     */
    protected function setUpQueryBuilder($query_builder, $queries = [], $is_count = false, $default_fields = [], $table = '', $whereRawTable = '') {
        if ($query_builder instanceof Model) $query_builder = $query_builder->newQuery();
        // Query is not count
        if ($is_count !== true) {
            // Select fields
            $fields = $this->getRequestFields();
            if (!empty($fields) && $table) {
                $new_fields = [];
                foreach ($fields as $f) $new_fields[] = "$table.$f";
                $fields = $new_fields;
            }
            $fields = array_unique(array_merge($fields, $default_fields));
            if (empty($fields)) $fields = ['*'];
            $query_builder = $query_builder->select($fields);
            // Select embed
            $embed = $this->getRequestEmbed();
            if (!empty($embed)) foreach ($embed as $key => $select) {
                $query_builder = $query_builder->with([$key => function($query) use ($select) {
                    if (!empty($select)) {
                        if (array_search('id', $select) === false) $select = array_merge($select, ['id']);
                        $query->select($select);
                    }
                }]);
            }
        }
        // Query
        $whereRaw = $this->getWhereRawQueries($whereRawTable);
        if ($whereRaw) {
            if (!isset($queries['whereRaw'])) $queries['whereRaw'] = [];
            $queries['whereRaw'] = array_merge($queries['whereRaw'], $whereRaw);
        }
        foreach ($queries as $key => $value) {
            if ($key === 'and') {
                $query_builder = $query_builder->where(function($query) use ($value) {
                    foreach ($value as $and) $query = $query->where($and[0], $and[1], $and[2]);
                });
            } else if ($key === 'or') {
                if (count($value) == 1) {
                    $query_builder = $query_builder->orWhere($value[0][0], $value[0][1], $value[0][2]);
                } else {
                    $query_builder = $query_builder->where(function($query) use ($value) {
                        $key = 0;
                        foreach ($value as $and) {
                            if ($key === 0) {
                                $query = $query->where($and[0], $and[1], $and[2]);
                            } else {
                                $query = $query->orWhere($and[0], $and[1], $and[2]);
                            }
                            $key++;
                        }
                    });
                }
            } else if ($key === 'orOr') {
                $query_builder = $query_builder->orWhere(function($query) use ($value) {
                    foreach ($value as $and) {
                        $query = $query->orWhere($and[0], $and[1], $and[2]);
                    }
                });
            } else if ($key === 'orAnd') {
                $query_builder = $query_builder->orWhere(function($query) use ($value) {
                    foreach ($value as $and) $query = $query->where($and[0], $and[1], $and[2]);
                });
            } else if ($key === 'in') {
                $query_builder = $query_builder->where(function($query) use ($value) {
                    foreach ($value as $in) $query = $query->whereIn($in[0], $in[1]);
                });
            } else if ($key === 'notIn') {
                $query_builder = $query_builder->where(function($query) use ($value) {
                    foreach ($value as $in) $query = $query->whereNotIn($in[0], $in[1]);
                });
            } else if ($key === 'withHas') {
                $query_builder = $query_builder->where(function($query) use ($value) {
                    foreach ($value as $with => $withValues) {
                        if (is_array($withValues)) {
                            $query = $query->whereHas($with, function($query) use ($withValues) {
                                if (!empty($withValues)) $query->where(function($query) use ($withValues) {
                                    foreach ($withValues as $withValue) $query = $query->where($withValue[0], $withValue[1], $withValue[2]);
                                });
                            });
                        } else {
                            $query = $query->has($withValues);
                        }
                    }
                });
            } else if ($key === 'has') {
                $query_builder = $query_builder->where(function($query) use ($value) {
                    foreach ($value as $and) {
                        if (is_array($and)) {
                            $query = $query->has($and[0], $and[1], $and[2]);
                        } else {
                            $query = $query->has($and);
                        }
                    }
                });
            } else if ($key === 'whereRaw') {
                $query_builder = $query_builder->where(function($query) use ($value) {
                    foreach ($value as $raw) {
                        if (isset($raw[1])) {
                            $query = $query->whereRaw($raw[0], $raw[1]);
                        } else {
                            $query = $query->whereRaw($raw[0]);
                        }
                    }
                });
            } else if ($key === 'orWhereRaw') {
                $query_builder = $query_builder->where(function($query) use ($value) {
                    foreach ($value as $raw) {
                        if (isset($raw[1])) {
                            $query = $query->orWhereRaw($raw[0], $raw[1]);
                        } else {
                            $query = $query->orWhereRaw($raw[0]);
                        }
                    }
                });
            } else if ($key === 'orOrWhereRaw') {
                $query_builder = $query_builder->orWhere(function($query) use ($value) {
                    foreach ($value as $raw) {
                        if (isset($raw[1])) {
                            $query = $query->orWhereRaw($raw[0], $raw[1]);
                        } else {
                            $query = $query->orWhereRaw($raw[0]);
                        }
                    }
                });
            } else {
                $query_builder = $query_builder->where($value[0], $value[1], $value[2]);
            }
        }

        return $query_builder;
    }

    /**
     * Get Session Id
     *
     * @return string|null
     */
    protected function getSessionId() {
        $session_id = $this->request->get('session_id');
        if (!$session_id) $session_id = requestValue('OCSESSID');
        return $session_id;
    }

    /**
     * Get Currency
     *
     * @param string $default
     * @return string|null
     */
    protected function getCurrency($default = 'VND') {
        $currency = $this->request->get('currency');
        if (!$currency) $currency = requestValue('currency');
        return $currency ? $currency : $default;
    }
}
