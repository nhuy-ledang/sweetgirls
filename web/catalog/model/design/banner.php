<?php
class ModelDesignBanner extends Model {
    private $table = DB_PREFIX . 'media__banners';
    private $image_table = DB_PREFIX . 'media__banner_images';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['image', 'sort_order', 'status', 'banner_id'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'         => 'integer',
        'sort_order' => 'integer',
    ];

    protected function getTransformer($row) {
        $row = array_merge($row, [
            'title'     => $this->trans->T($row['title']),
//            'caption'   => $this->trans->T($row['caption']),
            'linkname'  => $this->trans->T($row['linkname']),
            'caption'   => nl2br($this->trans->T($row['caption'])),
            'thumb_url' => media_url_file(html_entity_decode($row['image'], ENT_QUOTES, 'UTF-8'), 'thumb'),
            'raw_url'   => media_url_file(html_entity_decode($row['image'], ENT_QUOTES, 'UTF-8')),
            'href'      => (!$row['link'] || $row['link'] == '#') ? 'javascript:void(0)' : $row['link']
        ]);

        if (!empty($row['table_images'])) {
            $row['table_images'] = json_decode($row['table_images'], true);
            // Images
            $row['table_imagess'] = [];
            if (isset($row['table_images'])) {
                foreach($row['table_images'] as $table_images) {
                    $row['table_imagess'][] = array_merge($table_images, [
                        'name'        => isset($table_images[$this->config->get('config_language')]['title'])?$table_images[$this->config->get('config_language')]['title']:'',
                        'description' => html_entity_decode(isset($table_images[$this->config->get('config_language')]['description'])?$table_images[$this->config->get('config_language')]['description']:'', ENT_QUOTES, 'UTF-8'),
                        'image'       => $table_images['image'] ? media_url_file(html_entity_decode($table_images['image'], ENT_QUOTES, 'UTF-8')) : false,
//                        'thumb' => $this->model_tool_image->resize(html_entity_decode($table_images['image'], ENT_QUOTES, 'UTF-8'), 100, 100),
//                        'thumb_icon' => $this->model_tool_image->resize(html_entity_decode($table_images['icon'], ENT_QUOTES, 'UTF-8'), 100, 100),
                        'link'       => isset($table_images[$this->config->get('config_language')]['link'])?$table_images[$this->config->get('config_language')]['link']:'',
                    ]);
                }
            }
            unset($row['table_images']);

        }

        return $this->transform($row);
    }

    public function getBanner($id) {
        $keyCache = 'banner.' . $id . $this->config->get('config_language');
        $data = $this->cache->get($keyCache);
        if (!$data) {
            $query = $this->db->query("SELECT * FROM " . $this->table . " b LEFT JOIN " . $this->image_table . " bi ON (b.id = bi.banner_id) WHERE b.id = '" . (int)$id . "' AND b.status = '1' ORDER BY bi.sort_order ASC");
            $data = [];
            foreach ($query->rows as $row) {
                $data[] = $this->getTransformer($row);
            }

            $this->cache->set($keyCache, $data);
        }

        return $data;
    }
}
