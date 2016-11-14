<?php

namespace WPCategoriesMetaboxExtended\Core;

/**
 * Interface Loadie enforces classes to define load() method.
 *
 * All Loadies will be then executed from WPCategoriesMetaboxExtended class.
 *
 * @since 1.0.0
 *
 * @package WPCategoriesMetaboxExtended\Core
 */
interface Loadie {

	public function load();
}