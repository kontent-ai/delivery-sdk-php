<?php

namespace KenticoCloud\Delivery\Models;


class Pagination extends Model {

	public $skip = null;
	public $limit = null;
	public $count = null;
	public $next_page = null;

}