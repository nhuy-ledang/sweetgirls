<?php
class ControllerCommonPagination extends Controller {
	public function index($setting) {
		if (isset($setting['total'])) {
			$total = $setting['total'];
		} else {
			$total = 0;
		}

		if (isset($setting['page']) && $setting['page'] > 0) {
			$page = (int)$setting['page'];
		} else {
			$page = 1;
		}

		if (isset($setting['limit'])) {
			$limit = (int)$setting['limit'];
		} else {
			$limit = 10;
		}

		if (isset($setting['url'])) {
			$url = str_replace('%7Bpage%7D', '{page}', (string)$setting['url']);
		} else {
			$url = '';
		}

		$num_links = 5;
		$num_pages = ceil($total / $limit);

		$data['page'] = $page;

		if ($page > 1) {
			$data['first'] = str_replace(array('&amp;page={page}', '?page={page}', '&page={page}'), '', $url);

			if ($page - 1 === 1) {
				$data['prev'] = str_replace(array('&amp;page={page}', '?page={page}', '&page={page}'), '', $url);
			} else {
				$data['prev'] = str_replace('{page}', $page - 1, $url);
			}
		} else {
			$data['first'] = '';
			$data['prev'] = '';
		}

		$data['links'] = array();

		if ($num_pages > 1) {
			if ($num_pages <= $num_links) {
				$start = 1;
				$end = $num_pages;
			} else {
				$start = $page - floor($num_links / 2);
				$end = $page + floor($num_links / 2);

				if ($start < 1) {
					$end += abs($start) + 1;
					$start = 1;
				}

				if ($end > $num_pages) {
					$start -= ($end - $num_pages);
					$end = $num_pages;
				}
			}

            if ($start > 2) {
                $data['links'][] = array(
                    'page' => 1,
                    'href' => str_replace('{page}', 1, $url)
                );
                $data['links'][] = array(
                    'page' => '...',
                    'href' => ''
                );
            }
            if ($start == 2) {
                $start = $page - floor(($num_links + 1) / 2);
                $end = $page + floor(($num_links - 2) / 2);
            } else if ($end == $num_pages - 1) {
                $start = $page - floor(($num_links - 2) / 2);
                $end = $page + floor(($num_links + 1) / 2);
            }

			for ($i = $start; $i <= $end; $i++) {
				$data['links'][] = array(
					'page' => $i,
					'href' => str_replace('{page}', $i, $url)
				);
			}

            if ($end < $num_pages) {
                $data['links'][] = array(
                    'page' => '...',
                    'href' => ''
                );

                $data['links'][] = array(
                    'page' => $num_pages,
                    'href' => str_replace('{page}', $num_pages, $url)
                );
            }
		}

		if ($num_pages > $page) {
			$data['next'] = str_replace('{page}', $page + 1, $url);
			$data['last'] = str_replace('{page}', $num_pages, $url);
		} else {
			$data['next'] = '';
			$data['last'] = '';
		}

		if ($num_pages > 1) {
			return $this->load->view('common/pagination', $data);
		} else {
			return '';
		}
	}
}
