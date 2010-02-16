<?php
/*****************************************************************************
 *
 * GlobalMapCfg.php - Class for handling the map configuration files of NagVis
 *
 * Copyright (c) 2004-2008 NagVis Project (Contact: lars@vertical-visions.de)
 *
 * License:
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2 as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 *
 *****************************************************************************/
 
/**
 * @author	Lars Michelsen <lars@vertical-visions.de>
 */
class GlobalMapCfg {
	private $CORE;
	public $BACKGROUND;
	private $CACHE;
	
	protected $name;
	protected $type = 'map';
	protected $mapConfig;
	
	private $configFile = '';
	protected $cacheFile = '';
	
	// Array for config validation
	protected $validConfig;
	
	/**
	 * Class Constructor
	 *
	 * @param	GlobalCore	$CORE
	 * @param	String			$name		Name of the map
	 * @author	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function __construct($CORE, $name='') {
		$this->CORE = $CORE;
		$this->name	= $name;
		
		$this->validConfig = Array(
			'global' => Array('type' => Array('must' => 0,
					'match' => MATCH_OBJECTTYPE,
					'field_type' => 'hidden'),
				'object_id' => Array('must' => 0,
					'match' => MATCH_INTEGER,
					'field_type' => 'hidden'),
				'allowed_for_config' => Array('must' => 0,
					'deprecated' => 1,
					'match' => MATCH_STRING),
				'allowed_user' => Array('must' => 0,
					'deprecated' => 1,
					'match' => MATCH_STRING),
				'map_image' => Array('must' => 0,
					'match' => MATCH_PNG_GIF_JPG_FILE_OR_URL_NONE,
					'field_type' => 'dropdown'),
				'alias' => Array('must' => 0,
					'default' => $name,
					'match' => MATCH_STRING),
				'backend_id' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'backend'),
					'match' => MATCH_STRING_NO_SPACE),
				'background_color' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'backgroundcolor'),
					'match' => MATCH_COLOR),
				'default_params' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('automap', 'defaultparams'),
					'match' => MATCH_STRING_URL),
				
				'context_menu' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'contextmenu'),
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'context_template' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'contexttemplate'),
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown',
					'depends_on' => 'context_menu',
					'depends_value' => '1'),
				
				'event_background' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'eventbackground'),
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				
				'event_highlight' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'eventhighlight'),
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'event_highlight_interval' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'eventhighlightinterval'),
					'match' => MATCH_INTEGER,
					'depends_on' => 'event_highlight',
					'depends_value' => '1'),
				'event_highlight_duration' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'eventhighlightduration'),
					'match' => MATCH_INTEGER,
					'depends_on' => 'event_highlight',
					'depends_value' => '1'),
				
				'event_log' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'eventlog'),
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'event_log_level' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'eventloglevel'),
					'match' => MATCH_STRING_NO_SPACE,
					'depends_on' => 'event_log',
					'depends_value' => '1'),
				'event_log_height' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'eventlogheight'),
					'match' => MATCH_INTEGER,
					'depends_on' => 'event_log',
					'depends_value' => '1'),
				'event_log_hidden' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'eventloghidden'),
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean',
					'depends_on' => 'event_log',
					'depends_value' => '1'),
				
				'event_scroll' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'eventscroll'),
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'event_sound' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'eventsound'),
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				
				'grid_show' => Array('must' => 0,
					'default' => intval($this->CORE->getMainCfg()->getValue('wui', 'grid_show')),
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'grid_color' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('wui', 'grid_color'),
					'match' => MATCH_COLOR,
					'depends_on' => 'grid_show',
					'depends_value' => '1'),
				'grid_steps' => Array('must' => 0,
					'default' => intval($this->CORE->getMainCfg()->getValue('wui', 'grid_steps')),
					'match' => MATCH_INTEGER,
					'depends_on' => 'grid_show',
					'depends_value' => '1'),
				
				'header_menu' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'headermenu'),
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'header_template' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'headertemplate'),
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown',
					'depends_on' => 'header_menu',
					'depends_value' => '1'),
				
				'hover_menu' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'hovermenu'),
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'hover_delay' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'hoverdelay'),
					'match' => MATCH_INTEGER,
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_template' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'hovertemplate'),
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown',
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_timeout' => Array('must' => 0,
					'deprecated' => '1',
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'hovertimeout'),
					'match' => MATCH_INTEGER,
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_childs_show' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'hoverchildsshow'),
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean',
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_childs_limit' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'hoverchildslimit'),
					'match' => MATCH_INTEGER_PRESIGN,
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_childs_order' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'hoverchildsorder'),
					'match' => MATCH_ORDER,
					'field_type' => 'dropdown',
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_childs_sort' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'hoverchildssort'),
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown',
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				
				'iconset' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'icons'),
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown'),
				'in_maintenance' => Array('must' => 0,
					'default' => '0',
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				
				'label_show' => Array('must' => 0,
					'default' => '0',
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'label_text' => Array('must' => 0,
					'default' => '[name]',
					'match' => MATCH_ALL,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_x' => Array('must' => 0,
					'default' => '-20',
					'match' => MATCH_INTEGER_PRESIGN,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_y' => Array('must' => 0,
					'default' => '+20',
					'match' => MATCH_INTEGER_PRESIGN,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_width' => Array('must' => 0,
					'default' => 'auto',
					'match' => MATCH_INTEGER,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_background' => Array('must' => 0,
					'default' => 'transparent',
					'match' => MATCH_COLOR,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_border' => Array('must' => 0,
					'default' => '#000000',
					'match' => MATCH_COLOR,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_style' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_STYLE,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				
				'only_hard_states' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'onlyhardstates'),
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'recognize_services' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'recognizeservices'),
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'show_in_lists' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'showinlists'),
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'stylesheet' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'stylesheet'),
					'match' => MATCH_STRING_NO_SPACE),
				'url_target' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'urltarget'),
					'match' => MATCH_STRING_NO_SPACE),
				'usegdlibs' => Array('must' => 0,
					'deprecated' => '1',
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'usegdlibs'),
          'deprecated' => 1,
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean')),
			
			'host' => Array('type' => Array('must' => 0,
					'match' => MATCH_OBJECTTYPE,
					'field_type' => 'hidden'),
				'object_id' => Array('must' => 0,
					'match' => MATCH_INTEGER,
					'field_type' => 'hidden'),
				'host_name' => Array('must' => 1,
					'match' => MATCH_STRING,
					'field_type' => 'dropdown'),
				'x' => Array('must' => 1,
					'match' => MATCH_FLOAT),
				'y' => Array('must' => 1,
					'match' => MATCH_FLOAT),
				'z' => Array('must' => 0,
					'default' => 1,
					'match' => MATCH_INTEGER),
				'backend_id' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown'),
				
				'view_type' => Array('must' => 0,
					'default' => 'icon',
					'match' => MATCH_VIEW_TYPE,
					'field_type' => 'dropdown'),
				'iconset' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown',
					'depends_on' => 'view_type',
					'depends_value' => 'icon'),
				'line_type' => Array('must' => 0,
					'match' => MATCH_LINE_TYPE,
					'field_type' => 'dropdown',
					'depends_on' => 'view_type',
					'depends_value' => 'line'),
				'line_width' => Array('must' => 0,
					'default' => '3',
					'match' => MATCH_INTEGER,
					'depends_on' => 'view_type',
					'depends_value' => 'line'),
				
				'context_menu' => Array('must' => 0,
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'context_template' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown',
					'depends_on' => 'context_menu',
					'depends_value' => '1'),
				
				'hover_menu' => Array('must' => 0,
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'hover_delay' => Array('must' => 0,
					'match' => MATCH_INTEGER,
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_template' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown',
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_timeout' => Array('must' => 0,
					'default' => '',
					'deprecated' => '1',
					'match' => MATCH_INTEGER,
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_url' => Array('must' => 0,
					'match' => MATCH_STRING_URL,
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_childs_show' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean',
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_childs_sort' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown',
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_childs_order' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_ORDER,
					'field_type' => 'dropdown',
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_childs_limit' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_INTEGER_PRESIGN,
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				
				'label_show' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'label_text' => Array('must' => 0,
					'default' => '[name]',
					'match' => MATCH_ALL,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_x' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_INTEGER_PRESIGN,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_y' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_INTEGER_PRESIGN,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_width' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_INTEGER,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_background' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_COLOR,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_border' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_COLOR,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_style' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_STYLE,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				
				'only_hard_states' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'recognize_services' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				
				'url' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'hosturl'),
					'match' => MATCH_STRING_URL_EMPTY),
				'url_target' => Array('must' => 0,
					'match' => MATCH_STRING_NO_SPACE),
				
				'use' => Array('must' => 0,
					'match' => MATCH_STRING_NO_SPACE)),
			
			'hostgroup' => Array('type' => Array('must' => 0,
					'match' => MATCH_OBJECTTYPE,
					'field_type' => 'hidden'),
				'object_id' => Array('must' => 0,
					'match' => MATCH_INTEGER,
					'field_type' => 'hidden'),
				'hostgroup_name' => Array('must' => 1,
					'match' => MATCH_STRING,
					'field_type' => 'dropdown'),
				'x' => Array('must' => 1,
					'match' => MATCH_FLOAT),
				'y' => Array('must' => 1,
					'match' => MATCH_FLOAT),
				'z' => Array('must' => 0,
					'default' => 1,
					'match' => MATCH_INTEGER),
				'backend_id' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown'),
				
				'view_type' => Array('must' => 0,
					'default' => 'icon',
					'match' => MATCH_VIEW_TYPE,
					'field_type' => 'dropdown'),
				'iconset' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown',
					'depends_on' => 'view_type',
					'depends_value' => 'icon'),
				'line_type' => Array('must' => 0,
					'match' => MATCH_INTEGER,
					'field_type' => 'dropdown',
					'depends_on' => 'view_type',
					'depends_value' => 'line'),
				'line_width' => Array('must' => 0,
					'default' => '3',
					'match' => MATCH_INTEGER,
					'depends_on' => 'view_type',
					'depends_value' => 'line',
					'field_type' => 'dropdown'),
				
				'context_menu' => Array('must' => 0,
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'context_template' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown',
					'depends_on' => 'context_menu',
					'depends_value' => '1'),
				
				'hover_menu' => Array('must' => 0,
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'hover_delay' => Array('must' => 0,
					'match' => MATCH_INTEGER,
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_template' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown',
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_timeout' => Array('must' => 0,
					'default' => '',
					'deprecated' => '1',
					'match' => MATCH_INTEGER,
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_url' => Array('must' => 0,
					'match' => MATCH_STRING_URL,
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_childs_show' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean',
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_childs_sort' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown',
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_childs_order' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_ORDER,
					'field_type' => 'dropdown',
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_childs_limit' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_INTEGER_PRESIGN,
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				
				'label_show' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'label_text' => Array('must' => 0,
					'default' => '[name]',
					'match' => MATCH_ALL,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_x' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_INTEGER_PRESIGN,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_y' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_INTEGER_PRESIGN,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_width' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_INTEGER,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_background' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_COLOR,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_border' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_COLOR,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_style' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_STYLE,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				
				'only_hard_states' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'recognize_services' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				
				'url' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'hostgroupurl'),
					'match' => MATCH_STRING_URL_EMPTY),
				'url_target' => Array('must' => 0,
					'match' => MATCH_STRING_NO_SPACE),
				
				'use' => Array('must' => 0,
					'match' => MATCH_STRING_NO_SPACE)),
			
			'service' => Array('type' => Array('must' => 0,
					'match' => MATCH_OBJECTTYPE,
					'field_type' => 'hidden'),
				'object_id' => Array('must' => 0,
					'match' => MATCH_INTEGER,
					'field_type' => 'hidden'),
				'host_name' => Array('must' => 1,
					'match' => MATCH_STRING,
					'field_type' => 'dropdown'),
				'service_description' => Array('must' => 1,
					'match' => MATCH_STRING,
					'field_type' => 'dropdown'),
				'x' => Array('must' => 1,
					'match' => MATCH_FLOAT),
				'y' => Array('must' => 1,
					'match' => MATCH_FLOAT),
				'z' => Array('must' => 0,
					'default' => 1,
					'match' => MATCH_INTEGER),
				'backend_id' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown'),
				
				'view_type' => Array('must' => 0,
					'default' => 'icon',
					'match' => MATCH_VIEW_TYPE_SERVICE,
					'field_type' => 'dropdown'),
				'iconset' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown',
					'depends_on' => 'view_type',
					'depends_value' => 'icon'),
				'line_type' => Array('must' => 0,
					'match' => MATCH_INTEGER,
					'field_type' => 'dropdown',
					'depends_on' => 'view_type',
					'depends_value' => 'line'),
				'line_width' => Array('must' => 0,
					'default' => '3',
					'match' => MATCH_INTEGER,
					'depends_on' => 'view_type',
					'depends_value' => 'line'),
				'gadget_url' => Array('must' => 0,
					'match' => MATCH_STRING_URL,
					'field_type' => 'dropdown',
					'depends_on' => 'view_type',
					'depends_value' => 'gadget'),
				'gadget_scale' => Array('must' => 0,
					'default' => 100,
					'match' => MATCH_INTEGER,
					'depends_on' => 'view_type',
					'depends_value' => 'gadget'),
				'gadget_opts' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_GADGET_OPT,
					'depends_on' => 'view_type',
					'depends_value' => 'gadget'),
				
				'context_menu' => Array('must' => 0,
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'context_template' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown',
					'depends_on' => 'context_menu',
					'depends_value' => '1'),
				
				'hover_menu' => Array('must' => 0,
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'hover_template' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown',
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_timeout' => Array('must' => 0,
					'default' => '',
					'deprecated' => '1',
					'match' => MATCH_INTEGER,
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_delay' => Array('must' => 0,
					'match' => MATCH_INTEGER,
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_url' => Array('must' => 0,
					'match' => MATCH_STRING_URL,
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_childs_show' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean',
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_childs_sort' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown',
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_childs_order' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_ORDER,
					'field_type' => 'dropdown',
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_childs_limit' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_INTEGER_PRESIGN,
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				
				'label_show' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'label_text' => Array('must' => 0,
					'default' => '[name] [service_description]',
					'match' => MATCH_ALL,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_x' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_INTEGER_PRESIGN,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_y' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_INTEGER_PRESIGN,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_width' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_INTEGER,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_background' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_COLOR,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_border' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_COLOR,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_style' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_STYLE,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				
				'only_hard_states' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				
				'url' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'serviceurl'),
					'match' => MATCH_STRING_URL_EMPTY),
				'url_target' => Array('must' => 0,
					'match' => MATCH_STRING_NO_SPACE),
				
				'use' => Array('must' => 0,
					'match' => MATCH_STRING_NO_SPACE)),
			
			'servicegroup' => Array('type' => Array('must' => 0,
					'match' => MATCH_OBJECTTYPE,
					'field_type' => 'hidden'),
				'object_id' => Array('must' => 0,
					'match' => MATCH_INTEGER,
					'field_type' => 'hidden'),
				'servicegroup_name' => Array('must' => 1,
					'match' => MATCH_STRING,
					'field_type' => 'dropdown'),
				'x' => Array('must' => 1,
					'match' => MATCH_FLOAT),
				'y' => Array('must' => 1,
					'match' => MATCH_FLOAT),
				'z' => Array('must' => 0,
					'default' => 1,
					'match' => MATCH_INTEGER),
				'backend_id' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown'),
				
				'view_type' => Array('must' => 0,
					'default' => 'icon',
					'match' => MATCH_VIEW_TYPE,
					'field_type' => 'dropdown'),
				'iconset' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown',
					'depends_on' => 'view_type',
					'depends_value' => 'icon'),
				'line_type' => Array('must' => 0,
					'match' => MATCH_INTEGER,
					'field_type' => 'dropdown',
					'depends_on' => 'view_type',
					'depends_value' => 'line'),
				'line_width' => Array('must' => 0,
					'default' => '3',
					'match' => MATCH_INTEGER,
					'depends_on' => 'view_type',
					'depends_value' => 'line'),
				
				'context_menu' => Array('must' => 0,
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'context_template' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown',
					'depends_on' => 'context_menu',
					'depends_value' => '1'),
				
				'hover_menu' => Array('must' => 0,
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'hover_delay' => Array('must' => 0,
					'match' => MATCH_INTEGER,
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_template' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown',
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_timeout' => Array('must' => 0,
					'default' => '',
					'deprecated' => '1',
					'match' => MATCH_INTEGER,
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_url' => Array('must' => 0,
					'match' => MATCH_STRING_URL,
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_childs_show' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean',
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_childs_sort' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown',
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_childs_order' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_ORDER,
					'field_type' => 'dropdown',
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_childs_limit' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_INTEGER_PRESIGN,
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				
				'label_show' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'label_text' => Array('must' => 0,
					'default' => '[name]',
					'match' => MATCH_ALL,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_x' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_INTEGER_PRESIGN,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_y' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_INTEGER_PRESIGN,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_width' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_INTEGER,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_background' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_COLOR,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_border' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_COLOR,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_style' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_STYLE,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				
				'only_hard_states' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				
				'url' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'servicegroupurl'),
					'match' => MATCH_STRING_URL_EMPTY),
				'url_target' => Array('must' => 0,
					'match' => MATCH_STRING_NO_SPACE),
				
				'use' => Array('must' => 0,
					'match' => MATCH_STRING_NO_SPACE)),
			
			'map' => Array('type' => Array('must' => 0,
					'match' => MATCH_OBJECTTYPE,
					'field_type' => 'hidden'),
				'object_id' => Array('must' => 0,
					'match' => MATCH_INTEGER,
					'field_type' => 'hidden'),
				'map_name' => Array('must' => 1,
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown'),
				'x' => Array('must' => 1,
					'match' => MATCH_FLOAT),
				'y' => Array('must' => 1,
					'match' => MATCH_FLOAT),
				'z' => Array('must' => 0,
					'default' => 1,
					'match' => MATCH_INTEGER),
				
				'view_type' => Array('must' => 0,
					'default' => 'icon',
					'match' => MATCH_VIEW_TYPE,
					'field_type' => 'dropdown'),
				'iconset' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown'),
				'line_type' => Array('must' => 0,
					'match' => MATCH_INTEGER,
					'field_type' => 'dropdown',
					'depends_on' => 'view_type',
					'depends_value' => 'line'),
				'line_width' => Array('must' => 0,
					'default' => '3',
					'match' => MATCH_INTEGER,
					'depends_on' => 'view_type',
					'depends_value' => 'line'),
				
				'context_menu' => Array('must' => 0,
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'context_template' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown',
					'depends_on' => 'context_menu',
					'depends_value' => '1'),
				
				'hover_menu' => Array('must' => 0,
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'hover_template' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown',
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_timeout' => Array('must' => 0,
					'default' => '',
					'deprecated' => '1',
					'match' => MATCH_INTEGER,
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_delay' => Array('must' => 0,
					'match' => MATCH_INTEGER,
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_url' => Array('must' => 0,
					'match' => MATCH_STRING_URL,
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_childs_show' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean',
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_childs_sort' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_NO_SPACE,
					'field_type' => 'dropdown',
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_childs_order' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_ORDER,
					'field_type' => 'dropdown',
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				'hover_childs_limit' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_INTEGER_PRESIGN,
					'depends_on' => 'hover_menu',
					'depends_value' => '1'),
				
				'label_show' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'label_text' => Array('must' => 0,
					'default' => '[name]',
					'match' => MATCH_ALL,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_x' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_INTEGER_PRESIGN,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_y' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_INTEGER_PRESIGN,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_width' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_INTEGER,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_background' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_COLOR,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_border' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_COLOR,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				'label_style' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_STYLE,
					'depends_on' => 'label_show',
					'depends_value' => '1'),
				
				'only_hard_states' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				
				'url' => Array('must' => 0,
					'default' => $this->CORE->getMainCfg()->getValue('defaults', 'mapurl'),
					'match' => MATCH_STRING_URL_EMPTY),
				'url_target' => Array('must' => 0,
					'match' => MATCH_STRING_NO_SPACE),
				
				'use' => Array('must' => 0,
					'match' => MATCH_STRING_NO_SPACE)),
			
			'line' => Array('type' => Array('must' => 0,
					'match' => MATCH_OBJECTTYPE,
					'field_type' => 'hidden'),
				'object_id' => Array('must' => 0,
					'match' => MATCH_INTEGER,
					'field_type' => 'hidden'),
				'x' => Array('must' => 1,
					'match' => MATCH_FLOAT),
				'y' => Array('must' => 1,
					'match' => MATCH_FLOAT),
				'z' => Array('must' => 0,
					'default' => 1,
					'match' => MATCH_INTEGER),
				'line_type' => Array('must' => 1,
					'match' => MATCH_INTEGER,
					'field_type' => 'dropdown'),
				'line_width' => Array('must' => 1,
					'default' => '3',
					'match' => MATCH_INTEGER),
				'line_color' => Array('must' => 0,
					'default' => '#ffffff',
					'match' => MATCH_COLOR),
				'line_color_border' => Array('must' => 0,
					'default' => '#000000',
					'match' => MATCH_COLOR),
				
				'hover_menu' => Array('must' => 0,
					'default' => 0,
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'hover_url' => Array('must' => 0,
					'match' => MATCH_STRING_URL),
				'hover_delay' => Array('must' => 0,
					'match' => MATCH_INTEGER),
				
				'url' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_URL_EMPTY),
				'url_target' => Array('must' => 0,
					'match' => MATCH_STRING_NO_SPACE),
				
				'use' => Array('must' => 0,
					'match' => MATCH_STRING_NO_SPACE)),
			
			'textbox' => Array('type' => Array('must' => 0,
					'match' => MATCH_OBJECTTYPE,
					'field_type' => 'hidden'),
				'object_id' => Array('must' => 0,
					'match' => MATCH_INTEGER,
					'field_type' => 'hidden'),
				'text' => Array('must' => 1,
					'match' => MATCH_ALL),
				'x' => Array('must' => 1,
					'match' => MATCH_INTEGER),
				'y' => Array('must' => 1,
					'match' => MATCH_INTEGER),
				'z' => Array('must' => 0,
					'default' => 1,
					'match' => MATCH_INTEGER),
				'background_color' => Array('must' => 0,
					'default' => '#C0C0C0',
					'match' => MATCH_COLOR),
				'border_color' => Array('must' => 0,
					'default' => '#000000',
					'match' => MATCH_COLOR),
				'style' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_STYLE),
				'use' => Array('must' => 0,
					'match' => MATCH_STRING_NO_SPACE),
				'h' => Array('must' => 0,
					'default' => 'auto',
					'match' => MATCH_TEXTBOX_HEIGHT),
				'w' => Array('must' => 1,
					'match' => MATCH_TEXTBOX_WIDTH)),
			
			'shape' => Array('type' => Array('must' => 0,
					'match' => MATCH_OBJECTTYPE,
					'field_type' => 'hidden'),
				'object_id' => Array('must' => 0,
					'match' => MATCH_INTEGER,
					'field_type' => 'hidden'),
				'icon' => Array('must' => 1,
					'match' => MATCH_PNG_GIF_JPG_FILE_OR_URL,
					'field_type' => 'dropdown'),
				'x' => Array('must' => 1,
					'match' => MATCH_INTEGER),
				'y' => Array('must' => 1,
					'match' => MATCH_INTEGER),
				'z' => Array('must' => 0,
					'default' => 1,
					'match' => MATCH_INTEGER),
				'enable_refresh' => Array('must' => 0,
					'default' => 0,
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				
				'hover_menu' => Array('must' => 0,
					'default' => 0,
					'match' => MATCH_BOOLEAN,
					'field_type' => 'boolean'),
				'hover_url' => Array('must' => 0,
					'match' => MATCH_STRING_URL),
				'hover_delay' => Array('must' => 0,
					'match' => MATCH_INTEGER),
				
				'url' => Array('must' => 0,
					'default' => '',
					'match' => MATCH_STRING_URL_EMPTY),
				'url_target' => Array('must' => 0,
					'match' => MATCH_STRING_NO_SPACE),
				
				'use' => Array('must' => 0,
					'match' => MATCH_STRING_NO_SPACE)),
			'template' => Array('type' => Array('must' => 0,
					'match' => MATCH_OBJECTTYPE),
				'name' => Array('must' => 1,
					'match' => MATCH_STRING_NO_SPACE),
				'object_id' => Array('must' => 0,
					'match' => MATCH_INTEGER,
					'field_type' => 'hidden')));
		
		// Define the map configuration file when no one set until here
		if($this->configFile === '') {
			$this->setConfigFile($this->CORE->getMainCfg()->getValue('paths', 'mapcfg').$name.'.cfg');
		}

		if($this->cacheFile === '') {
			$this->cacheFile = $this->CORE->getMainCfg()->getValue('paths','var').$name.'.cfg-'.CONST_VERSION.'-cache';
		}
		
		// Initialize the map configuration cache
		$this->initCache();
	}
	
	/**
	 * Gets the default values for the objects 
	 *
	 * @author	Lars Michelsen <lars@vertical-visions.de>
	 */
	private function getObjectDefaults() {
		$aVars = Array('recognize_services',
			'only_hard_states',
			'backend_id',
			'iconset',
			'context_menu',
			'context_template',
			'hover_menu',
			'hover_template',
			'hover_timeout',
			'hover_delay',
			'hover_url',
			'label_show',
			'label_text',
			'label_x',
			'label_y',
			'label_width',
			'label_background',
			'label_border',
			'label_style',
			'url_target',
			'hover_childs_show',
			'hover_childs_sort',
			'hover_childs_order',
			'hover_childs_limit');
		foreach($aVars As $sVar) {
			$sTmp = $this->getValue('global', 0, $sVar);
			$this->validConfig['host'][$sVar]['default'] = $sTmp;
			$this->validConfig['hostgroup'][$sVar]['default'] = $sTmp;
			
			// Handle exceptions for servicegroups
			if($sVar != 'recognize_services') {
				$this->validConfig['servicegroup'][$sVar]['default'] = $sTmp;
			}
			
			// Handle exceptions for services
			if($sVar != 'recognize_services') {
				$this->validConfig['service'][$sVar]['default'] = $sTmp;
			}
			
			// Handle exceptions for maps
			if($sVar != 'recognize_services' && $sVar != 'backend_id') {
				$this->validConfig['map'][$sVar]['default'] = $sTmp;
			}
			
			// Handle exceptions for shapes
			if($sVar == 'url_target' || $sVar == 'hover_delay') {
				$this->validConfig['shape'][$sVar]['default'] = $sTmp;
			}
			
			// Handle exceptions for lines
			if($sVar == 'url_target' || $sVar == 'hover_delay') {
				$this->validConfig['line'][$sVar]['default'] = $sTmp;
			}
		}
	}
	
	/**
	 * Initializes the map configuration file caching
	 *
	 * @param   String   Path to the configuration file
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	protected function setConfigFile($file) {
		$this->configFile = $file;
	}
	
	/**
	 * Initializes the map configuration file caching
	 *
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	protected function initCache() {
		if($this->cacheFile !== '') {
			$this->CACHE = new GlobalFileCache($this->CORE, $this->configFile, $this->cacheFile);
		}
	}
	
	/**
	 * Initializes the background image
	 *
	 * @return	GlobalBackground
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	private function getBackground() {
		$RET = new GlobalBackground($this->CORE, $this->getValue('global', 0, 'map_image'));
		return $RET;
	}
	
	/**
	 * Creates a new Configfile
	 *
	 * @return	Boolean	Is Successful?
	 * @author Lars Michelsen <lars@vertical-visions.de>
	 */
	public function createMapConfig() {
		// does file exist?
		if(!$this->checkMapConfigReadable(FALSE)) {
			if($this->checkMapCfgFolderWriteable(TRUE)) {
				// create empty file
				$fp = fopen($this->configFile, 'w');
				fclose($fp); 
				// set permissions
				chmod($this->configFile,0666);
				
					return TRUE;
				} else {
					return FALSE;
				}
		} else {
			// file exists & is readable
			return FALSE;
		}
	}
	
	/**
	 * Reads the map config file (copied from readFile->readNagVisCfg())
	 *
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function readMapConfig($onlyGlobal = 0, $resolveTemplates = true, $useCache = true) {
		if($this->name != '') {
			// Only use cache when there is
			// a) The cache should be used
			// b) When whole config file should be read
			// c) Some valid cache file
			// d) Some valid main configuration cache file
			// e) This cache file newer than main configuration cache file
			if($onlyGlobal == 0
			   && $useCache === true
			   && $this->CACHE->isCached() !== -1
				 && $this->CORE->getMainCfg()->isCached() !== -1
				 && $this->CACHE->isCached() >= $this->CORE->getMainCfg()->isCached()) {
				$this->mapConfig = $this->CACHE->getCache();
				
				/**
				 * The default values refer to global settings in the validConfig 
				 * array - so they have to be defined here and mustn't be defined
				 * in the array at creation.
				 * Because the default values should refer to the truly defined
				 * settings in global area they have to be read here.
				 */
				$this->getObjectDefaults();
				
				$this->BACKGROUND = $this->getBackground();
				
				return TRUE;
			} else {
				if($this->checkMapConfigExists(TRUE) && $this->checkMapConfigReadable(TRUE)) {
					$this->mapConfig = Array();
					// Array for counting objects
					$types = Array('global' => 0,
									'host' => 0,
									'service' => 0,
									'hostgroup' => 0,
									'servicegroup' => 0,
									'map' => 0,
									'textbox' => 0,
									'shape' => 0,
									'line' => 0,
									'template' => 0);
					
					// Read file in array (Don't read empty lines and ignore new line chars)
					$file = file($this->configFile, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
					
					// Create an array for these options
					$createArray = Array('use' => 1);
										
					// Don't read these keys
					$ignoreKeys = Array('object_id' => 0,
					                    'type' => 0);
					
					$l = 0;
					
					// These variables set which object is currently being filled
					$sObjType = '';
					$iObjTypeId = 0;
					$iObjId = 0;
					
					// Loop each line
					$iNumLines = count($file);
					for($l = 0; $l < $iNumLines; $l++) {
						// Remove spaces, newlines, tabs, etc. (http://de.php.net/rtrim)
						$file[$l] = rtrim($file[$l]);
						// Don't recognize empty lines
						if($file[$l] != '') {
							// Don't recognize comments and empty lines, do nothing with ending delimiters
							$sFirstChar = substr($file[$l], 0, 1);
							if($sFirstChar != ';' && $sFirstChar != '#' && $sFirstChar != '}') {
								// Determine if this is a new object definition
								if(strpos($file[$l], 'define') !== FALSE) {
									// If only the global section should be read break the loop after the global section
									if($onlyGlobal == 1 && $types['global'] == 1) {
										break;
									}
									
									$iDelimPos = strpos($file[$l], '{', 8);
									$sObjType = substr($file[$l], 7, ($iDelimPos - 8));
									
									if(isset($sObjType) && isset($this->validConfig[$sObjType])) {
										// This is a new definition and it's a valid one
										
										// Get the type index
										$iObjTypeId = $types[$sObjType];
										
										$this->mapConfig[$sObjType][$iObjTypeId] = Array(
										  'type' => $sObjType,
										  'object_id' => $iObjId
										);
										
										// increase type index
										$types[$sObjType]++;
										
										// Increase the map object id to identify the object on the map
										$iObjId++;
									} else {
										// unknown object type
										new GlobalMessage('ERROR', $this->CORE->getLang()->getText('unknownObject',Array('TYPE' => $sObjType, 'MAPNAME' => $this->name)));
										return FALSE;
									}
								} else {
									// This is another attribute
									$iDelimPos = strpos($file[$l], '=');
									$sKey = trim(substr($file[$l],0,$iDelimPos));
									$sValue = trim(substr($file[$l],($iDelimPos+1)));
									
									if(!isset($ignoreKeys[$sKey])) {
										if(isset($createArray[$sKey])) {
											$this->mapConfig[$sObjType][$iObjTypeId][$sKey] = explode(',', $sValue);
										} else {
											$this->mapConfig[$sObjType][$iObjTypeId][$sKey] = $sValue;
										}
									}
								}
							}
						}
					}
					
					/**
					 * The default values refer to global settings in the validConfig 
					 * array - so they have to be defined here and mustn't be defined
					 * in the array at creation.
					 * Because the default values should refer to the truly defined
					 * settings in global area they have to be read here.
					 */
					if($onlyGlobal != 1) {
						$this->getObjectDefaults();
						
						if(isset($this->mapConfig['template']) && $resolveTemplates == true) {
							// Merge the objects with the linked templates
							$this->mergeTemplates();
						}
					}                               
					
					if($this->checkMapConfigIsValid(1)) {
						if($onlyGlobal == 0) { 
							// Build cache
							if($useCache === true) {
								$this->CACHE->writeCache($this->mapConfig, 1);
							}
							
							// The automap also uses this method, so handle the different type
							if($this->type === 'automap') {
								$mod = 'AutoMap';
							} else {
								$mod = 'Map';
							}
							
							// Trigger the autorization backend to create new permissions when needed
							$AUTHORIZATION = $this->CORE->getAuthorization();
							if($AUTHORIZATION !== null) {
								$this->CORE->getAuthorization()->createPermission($mod, $this->getName());
							}
						}
						
						$this->BACKGROUND = $this->getBackground();
						
						return TRUE;
					} else {
						return FALSE;
					}
				} else {
					return FALSE;
				}
			}
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Gets the numeric index of a template by the name
	 *
	 * @param   String   Name of the template
	 * @return  Integer  ID of the template
	 * @author  Lars Michelsen <lars@vertical-visions.de>
	 */
	public function getTemplateIdByName($name) {
		foreach($this->mapConfig['template'] AS $id => $arr) {
			if(isset($arr['name']) && $arr['name'] === $name) {
				return $id;
			} 
		}
		
		return false;
	}
	
	/**
	 * Merges the object which "use" a template with the template values
	 *
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	private function mergeTemplates() {
		// Loop all objects
		foreach($this->mapConfig AS $type => &$elements) {
			// Except global and templates (makes no sense)
			if($type != 'global') {
				// Loop all objects of that type
				foreach($elements AS $id => &$element) {
					// Check for "use" value
					if(isset($element['use']) && is_array($element['use'])) {
						// loop all given templates
						foreach($element['use'] AS &$templateName) {
							$index = $this->getTemplateIdByName($templateName);
							
							if(isset($this->mapConfig['template'][$index]) && is_array($this->mapConfig['template'][$index])) {
								// merge object array with template object array (except type and name attribute)
								$tmpArray = $this->mapConfig['template'][$index];
								unset($tmpArray['type']);
								unset($tmpArray['name']);
								unset($tmpArray['object_id']);
								$this->mapConfig[$type][$id] = array_merge($tmpArray, $element);
							}
						}
					}
				}
			}
		}
		
		// Everything is merged: The templates are not relevant anymore
		unset($this->mapConfig['template']);
	}
	
	/**
	 * Checks for existing config file
	 *
	 * @param	Boolean $printErr
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function checkMapConfigExists($printErr) {
		if($this->name != '') {
			if(file_exists($this->configFile)) {
				return TRUE;
			} else {
				if($printErr == 1) {
					new GlobalMessage('ERROR', $this->CORE->getLang()->getText('mapCfgNotExists', Array('MAP' => $this->configFile)));
				}
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
	
	/**
	 * PROTECTED  checkMapConfigReadable()
	 *
	 * Checks for readable config file
	 *
	 * @param	Boolean $printErr
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	protected function checkMapConfigReadable($printErr) {
		if($this->name != '') {
			if(is_readable($this->configFile)) {
				return TRUE;
			} else {
				if($printErr == 1) {
					new GlobalMessage('ERROR', $this->CORE->getLang()->getText('mapCfgNotReadable', Array('MAP' => $this->configFile)));
				}
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Checks if the config file is valid
	 *
	 * @param	Boolean $printErr
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	private function checkMapConfigIsValid($printErr) {
		// check given objects and attributes
		foreach($this->mapConfig AS $type => $elements) {
			foreach($elements AS $id => $element) {
				// loop validConfig for checking: => missing "must" attributes
				foreach($this->validConfig[$type] AS $key => $val) {
					if(isset($val['must']) && $val['must'] == '1') {
						// value is "must"
						if(!isset($element[$key]) || $element[$key] == '') {
							// a "must" value is missing or empty
							new GlobalMessage('ERROR',$this->CORE->getLang()->getText('mapCfgMustValueNotSet', Array('MAPNAME' => $this->name, 'ATTRIBUTE' => $key, 'TYPE' => $type, 'ID' => $id)));
						}
					}
				}
				
				// Don't check values in templates
				if($type !== 'template') {
					// loop given elements for checking: => all given attributes valid
					foreach($element AS $key => $val) {
						// check for valid attributes
						if(!isset($this->validConfig[$type][$key])) {
							// unknown attribute
							if($printErr == 1) {
								new GlobalMessage('ERROR', $this->CORE->getLang()->getText('unknownAttribute', Array('MAPNAME' => $this->name, 'ATTRIBUTE' => $key, 'TYPE' => $type)));
							}
							return FALSE;
						} elseif(isset($this->validConfig[$type][$key]['deprecated']) && $this->validConfig[$type][$key]['deprecated'] == 1) {
							// deprecated option
							if($printErr) {
								new GlobalMessage('ERROR', $this->CORE->getLang()->getText('mapDeprecatedOption', Array('MAP' => $this->getName(), 'ATTRIBUTE' => $key, 'TYPE' => $type)));
							}
							return FALSE;
						} else {
							// The object has a match regex, it can be checked
							if(isset($this->validConfig[$type][$key]['match'])) {
								if(is_array($val)) {
									// This is an array
									
									// Loop and check each element
									foreach($val AS $key2 => $val2) {
										if(!preg_match($this->validConfig[$type][$key]['match'], $val2)) {
											// wrong format
											if($printErr) {
												new GlobalMessage('ERROR', $this->CORE->getLang()->getText('wrongValueFormatMap', Array('MAP' => $this->getName(), 'TYPE' => $type, 'ATTRIBUTE' => $key)));
											}
											return FALSE;
										}
									}
								} else {
									// This is a string value
									
									if(!preg_match($this->validConfig[$type][$key]['match'],$val)) {
										// Wrong format
										if($printErr) {
											new GlobalMessage('ERROR', $this->CORE->getLang()->getText('wrongValueFormatMap', Array('MAP' => $this->getName(), 'TYPE' => $type, 'ATTRIBUTE' => $key)));
										}
										return FALSE;
									}
								}
							}
							
							// Check wether a object has line_type set and not view_type=line
							// Update: Only check this when not in WUI!
							// Update: Don't check this for stateless lines
							// FIXME: This check should be removed in 1.5 or 1.6
							if($type != 'line' && $key == 'line_type' && !isset($element['view_type']) && !$this instanceof WuiMapCfg) {
								new GlobalMessage('ERROR', $this->CORE->getLang()->getText('lineTypeButViewTypeNotSet', Array('MAP' => $this->getName(), 'TYPE' => $type)));
							}
							
							// Check gadget options when object view type is gadget
							// Update: Only check this when not in WUI!
							if($key == 'view_type' && $val == 'gadget' && !isset($element['gadget_url']) && !$this instanceof WuiMapCfg) {
								new GlobalMessage('ERROR', $this->CORE->getLang()->getText('viewTypeGadgetButNoGadgetUrl', Array('MAP' => $this->getName(), 'TYPE' => $type)));
							}
							
							// Check if the configured backend is defined in main configuration file
							if($key == 'backend_id' && !in_array($val, $this->CORE->getDefinedBackends())) {
								new GlobalMessage('ERROR', $this->CORE->getLang()->getText('backendNotDefined', Array('BACKENDID' => $val)));
							}
						}
					}
				}
			}
		}
		return TRUE;
	}
	
	/**
	 * Gets valid keys for a specific object type
	 *
	 * @param   String  Specific object type
	 * @return  Array   Valid object keys
	 * @author  Lars Michelsen <lars@vertical-visions.de>
	 */
	public function getValidTypeKeys($sType) {
		$aRet = Array();
		foreach($this->validConfig[$sType] AS $key => $arr) {
			$aRet[] = $key;
		}
		return $aRet;
	}
	
	/**
	 * Gets all valid object types
	 *
	 * @return  Array  Valid object types
	 * @author  Lars Michelsen <lars@vertical-visions.de>
	 */
	public function getValidObjectTypes() {
		$aRet = Array();
		foreach($this->validConfig AS $key => $arr) {
			$aRet[] = $key;
		}
		return $aRet;
	}
	
	/**
	 * Gets all definitions of type $type
	 *
	 * @param	String	$type
	 * @return	Array	All elements of this type
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function getDefinitions($type) {
		if(isset($this->mapConfig[$type]) && count($this->mapConfig[$type]) > 0) {
			return $this->mapConfig[$type];
		} else {
			return Array();
		}
	}
	
	/**
	 * Gets a list of available templates with optional regex filtering
	 * the templates are listed as keys
	 *
	 * @param   String  Filter regex
	 * @return  Array	  List of templates as keys
	 * @author  Lars Michelsen <lars@vertical-visions.de>
	 */
	public function getTemplateNames($strMatch = NULL) {
		$a = Array();
		foreach($this->getDefinitions('template') AS $id => $aOpts) {
			if($strMatch == NULL || ($strMatch != NULL && preg_match($strMatch, $aOpts['name']))) {
				$a[$aOpts['name']] = true;
			}
		}
		
		return $a;
	}
	
	/**
	 * Gets the last modification time of the configuration file
	 *
	 * @return	Integer Unix timestamp with last modification time
	 * @author	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function getFileModificationTime() {
		if($this->checkMapConfigReadable(1)) {
			$time = filemtime($this->configFile);
			return $time;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Checks for writeable MapCfgFolder
	 *
	 * @param	Boolean $printErr
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	function checkMapCfgFolderWriteable($printErr) {
		if(file_exists(substr($this->CORE->getMainCfg()->getValue('paths', 'mapcfg'),0,-1)) && is_writable(substr($this->CORE->getMainCfg()->getValue('paths', 'mapcfg'),0,-1))) {
			return TRUE;
		} else {
			if($printErr == 1) {
				new GlobalMessage('ERROR', $this->CORE->getLang()->getText('mapCfgDirNotWriteable', Array('MAPPATH' => $this->CORE->getMainCfg()->getValue('paths', 'mapcfg'))), $this->CORE->getMainCfg()->getValue('paths','htmlbase'));
			}
			return FALSE;
		}
	}
	
	/**
	 * Deletes an element of the specified type from the config array
	 *
	 * @param	String	$type
	 * @param	Integer	$id
	 * @return	Boolean	TRUE
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function deleteElement($type,$id) {
		$this->mapConfig[$type][$id] = '';
		return TRUE;
	}
	
	/**
	 * Adds an element of the specified type to the config array
	 *
	 * @param	String	$type
	 * @param	Array	$properties
	 * @return	Integer	Id of the Element
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function addElement($type,$properties) {
		$this->mapConfig[$type][] = $properties;
		return count($this->mapConfig[$type])-1;
	}
	
	/**
	 * Sets a config value in the array
	 *
	 * @param	String	$type
	 * @param	Integer	$id
	 * @param	String	$key
	 * @param	String	$value
	 * @return	Boolean	TRUE
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function setValue($type, $id, $key, $value) {
		$this->mapConfig[$type][$id][$key] = $value;
		return TRUE;
	}
	
	/**
	 * Gets a config value from the array
	 *
	 * @param	String	$type
	 * @param	Integer	$id
	 * @param	String	$key
	 * @param	Boolean	$ignoreDefault
	 * @return	String	Value
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function getValue($type, $id, $key, $ignoreDefault=FALSE) {
		if(isset($this->mapConfig[$type][$id]) && isset($this->mapConfig[$type][$id][$key])) {
			return $this->mapConfig[$type][$id][$key];
		} elseif(!$ignoreDefault) {
			if(isset($this->validConfig[$type][$key]['default'])) {
				return $this->validConfig[$type][$key]['default'];
			}
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Gets the mapName
	 *
	 * @return	String	MapName
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function getName() {
		return $this->name;	
	}
	
	/**
	 * Gets the map alias
	 *
	 * @return	String	Map alias
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function getAlias() {
		return $this->getValue('global', 0, 'alias');	
	}
}
?>
