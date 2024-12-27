<?php
class ControllerApiSitemap extends Controller {
    private $pageCount = 1000;
    private $count = 0;
    private $expire = 3600 * 12;
    private $DIR_SITEMAP = DIR_ROOT . 'sitemap/';

    private function cors() {
        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            // may also be using PUT, PATCH, HEAD etc
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) header("Access-Control-Allow-Headers: '{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}'");

            exit(0);
        }
    }

    public function index() {
        $this->cors();

        // Check its a directory
        if (!is_dir(rtrim($this->DIR_SITEMAP, '/'))) mkdir(rtrim($this->DIR_SITEMAP, '/'));
        $data['files'] = [];
        $ffs = scandir(rtrim($this->DIR_SITEMAP, '/'));
        for ($i = 0; $i < count($ffs); $i++) {
            $n = explode('.', $ffs[$i]);
            if (count($n) < 2 || 2 < count($n) || (count($n) == 2 && $n[1] != 'xml')) continue;
            if ($ffs[$i] == '.' || $ffs[$i] == '..' || $ffs[$i] == '.htaccess' || $ffs[$i] == '.gitignore') continue;
            $data['files'][] = $this->config->get('config_url') . str_replace(DIR_ROOT, '', $this->DIR_SITEMAP) . $ffs[$i];
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode(['data' => $data]));
    }

    private function createNode($xml, $root, $link, $lastmodValue = false, $imageValue = [], $changefreqValue = 'weekly', $priorityValue = '1.0') {
        $url = $xml->createElement('url');

        $loc = $xml->createElement('loc');
        $loc->appendChild($xml->createTextNode($link));
        $url->appendChild($loc);

        if ($lastmodValue && is_string($lastmodValue)) {
            $lastmod = $xml->createElement('lastmod');
            $lastmod->appendChild($xml->createTextNode(date('Y-m-d\TH:i:sP', strtotime($lastmodValue))));
            $url->appendChild($lastmod);
        }

        $changefreq = $xml->createElement('changefreq');
        $changefreq->appendChild($xml->createTextNode($changefreqValue));
        $url->appendChild($changefreq);

        $priority = $xml->createElement('priority');
        $priority->appendChild($xml->createTextNode($priorityValue));
        $url->appendChild($priority);

        /**
         * <image:image>
         * <image:loc>http://example.com/photo.jpg</image:loc>
         * <image:title>Grey cat on the table</image:title>
         * <image:license>https://creativecommons.org/licenses/by-sa/2.0/</image:license>
         * <image:geo_location>Berlin, Germany</image:geo_location>
         * <image:caption>Funny cat on the table is looking at photographer.</image:caption>
         * </image:image>
         */
        if (!empty($imageValue)) {
            $image = $xml->createElement('image:image');
            foreach ($imageValue as $k => $v) {
                $prop = $xml->createElement("image:$k");
                $prop->appendChild($xml->createTextNode($v));
                $image->appendChild($prop);
            }
            $url->appendChild($image);
        }

        $root->appendChild($url);
    }

    private function createSiteMapPage() {
        $this->load->model('page/page');

        $xml = new DomDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        $root = $xml->createElement('urlset');
        $root->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $xml->appendChild($root);

        // $results = $this->model_page_page->getPages();
        // foreach ($results as $result) {
        //     $this->createNode($xml, $root, $result['href']);
        // }

        $xml->save(DIR_ROOT . 'sitemap/sitemap' . (++$this->count) . '.xml');
    }

    private function createSiteMapCategoryProduct() {
        $this->load->model('product/category');

        $xml = new DomDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        $root = $xml->createElement('urlset');
        $root->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $xml->appendChild($root);

        $results = $this->model_product_category->getCategories();
        foreach ($results as $result) {
            $this->createNode($xml, $root, $result['href'], $result['updated_at'], [], 'weekly', '0.7');
        }

        $xml->save(DIR_ROOT . 'sitemap/sitemap' . (++$this->count) . '.xml');
    }

    private function createSiteMapProduct() {
        $this->load->model('product/product');
        $total = $this->model_product_product->getTotalProducts();
        $pageTotal = ceil($total / $this->pageCount);
        $count = 0;
        for ($i = 0; $i < $pageTotal; $i++) {
            $results = $this->model_product_product->getProducts([
                'sort'  => 'p.id',
                'order' => 'asc',
                'start' => $i * $this->pageCount,
                'limit' => $this->pageCount,
            ]);
            if ($results) {
                $xml = new DomDocument('1.0', 'UTF-8');
                $xml->formatOutput = true;

                $root = $xml->createElement('urlset');
                $root->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
                $root->setAttribute('xmlns:image', 'http://www.google.com/schemas/sitemap-image/1.1');
                $xml->appendChild($root);

                foreach ($results as $result) {
                    $image = [];
                    if (isset($result['large_url']) && !empty($result['large_url'])) {
                        $image['loc'] = $result['large_url'];
                        $image['caption'] = htmlspecialchars($result['name'], ENT_COMPAT | ENT_XML1);
                        $image['title'] = htmlspecialchars($result['name'], ENT_COMPAT | ENT_XML1);
                        /*if ($result['location']) $image['geo_location'] = $result['location'];*/
                    }
                    $this->createNode($xml, $root, $result['href'], $result['updated_at'], $image);
                    // Product child
                    if (!$result['master_id']) {
                        $childs = $this->model_product_product->getProducts(['filter_master_id' => $result['id']]);
                        if ($childs) {
                            foreach ($childs as $child) {
                                $this->createNode($xml, $root, $child['href'], $child['updated_at'], '', 'weekly', 0.9);
                            }
                        }
                    }
                }

                $xml->save(DIR_ROOT . 'sitemap/products/sitemap' . (++$count) . '.xml');
            }
        }

        // Index
        if ($count > 0) {
            $xml = new DomDocument('1.0', 'UTF-8');
            $xml->formatOutput = true;

            $root = $xml->createElement('sitemapindex');
            $root->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
            $xml->appendChild($root);

            for ($i = 1; $i <= $count; $i++) {
                $sitemap = $xml->createElement('sitemap');

                $loc = $xml->createElement('loc');
                $loc->appendChild($xml->createTextNode($this->config->get('config_url') . 'sitemap/products/sitemap' . $i . '.xml'));
                $sitemap->appendChild($loc);

                $lastmod = $xml->createElement('lastmod');
                $lastmod->appendChild($xml->createTextNode(date('c', time())));
                $sitemap->appendChild($lastmod);

                $root->appendChild($sitemap);
            }

            $xml->save(DIR_ROOT . 'sitemap/sitemap' . (++$this->count) . '.xml');
        }
    }

    private function createSiteMapManufacturer() {
        $this->load->model('catalog/manufacturer');

        $xml = new DomDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        $root = $xml->createElement('urlset');
        $root->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $xml->appendChild($root);

        $results = $this->model_catalog_manufacturer->getManufacturers();
        foreach ($results as $result) {
            if ($result['alias'] && $result['status']) {
                $this->createNode($xml, $root, $result['href'], $result['updated_at'], [], 'weekly', '0.7');
            }
        }

        $xml->save(DIR_ROOT . 'sitemap/sitemap' . (++$this->count) . '.xml');
    }

    /*private function createSiteMapInformations() {
        $this->load->model('catalog/information');

        $xml = new DomDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        $root = $xml->createElement('urlset');
        $root->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $xml->appendChild($root);

        $xml->save(DIR_ROOT . 'sitemap/sitemap' . (++$this->count) . '.xml');
    }*/

    private function createSiteMap() {
        // Check update
        /*if (time() - $this->expire < filemtime($this->DIR_SITEMAP . 'sitemap.xml')) {
            return false;
        }*/
        $this->createSiteMapPage();
        $this->createSiteMapBlogCategory();
        $this->createSiteMapBlog();
        $this->createSiteMapCategoryProduct();
        $this->createSiteMapProduct();
        /*$this->createSiteMapCategoryProject();
        $this->createSiteMapProject();
        $this->createSiteMapManufacturer();
        $this->createSiteMapInformations();*/
    }

    public function build() {
        $this->cors();

        // Check its a directory
        if (!is_dir(rtrim($this->DIR_SITEMAP, '/'))) mkdir(rtrim($this->DIR_SITEMAP, '/'));
        if (!is_dir(rtrim($this->DIR_SITEMAP, '/') . '/blogs')) mkdir(rtrim($this->DIR_SITEMAP, '/') . '/blogs');
        if (!is_dir(rtrim($this->DIR_SITEMAP, '/') . '/products')) mkdir(rtrim($this->DIR_SITEMAP, '/') . '/products');
        //if (!is_dir(rtrim($this->DIR_SITEMAP, '/') . '/projects')) mkdir(rtrim($this->DIR_SITEMAP, '/') . '/projects');

        $this->createSiteMap();

        $xml = new DomDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        $root = $xml->createElement('sitemapindex');
        $root->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $xml->appendChild($root);

        $ffs = scandir(rtrim($this->DIR_SITEMAP, '/'));
        for ($i = 0; $i < count($ffs); $i++) {
            $n = explode('.', $ffs[$i]);
            if (count($n) < 2 || 2 < count($n) || (count($n) == 2 && $n[1] != 'xml')) {
                continue;
            }
            if ($ffs[$i] == '.' || $ffs[$i] == '..' || $ffs[$i] == '.htaccess' || $ffs[$i] == '.gitignore' || $ffs[$i] == 'sitemap.xml') continue;

            $sitemap = $xml->createElement('sitemap');

            $loc = $xml->createElement('loc');
            $loc->appendChild($xml->createTextNode($this->config->get('config_url') . 'sitemap/' . $ffs[$i]));
            $sitemap->appendChild($loc);

            $lastmod = $xml->createElement('lastmod');
            $lastmod->appendChild($xml->createTextNode(date('c', time())));
            $sitemap->appendChild($lastmod);

            $root->appendChild($sitemap);
        }

        $xml->save($this->DIR_SITEMAP . 'sitemap.xml');

        return $this->index();
    }
}
