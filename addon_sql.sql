CREATE TABLE `ost_transmission` (
    `id` int(10) unsigned NOT NULL auto_increment,
    `client_id` varchar(50) NOT NULL DEFAULT '',
    `client_name` varchar(100) NOT NULL DEFAULT '',
    `transmission_data` TEXT NOT NULL DEFAULT '',
    `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    KEY `client_id` (`client_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `ost_services_inhouse` (
    `id` int(10) unsigned NOT NULL auto_increment,
    `client_name` varchar(100) NOT NULL DEFAULT '',
    `service_type` varchar(100) NOT NULL DEFAULT '',
    `circuit_type` varchar(100) NOT NULL DEFAULT '',
    `circuit_id` varchar(100) NOT NULL DEFAULT '',
    `circuit_details` TEXT NOT NULL DEFAULT '',
    `circuit_location` TEXT NOT NULL DEFAULT '',
    `activation_date` varchar(50) NOT NULL DEFAULT '',
    `discontinue_date` varchar(50) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `ost_client_staff` (
    `id` int(10) unsigned NOT NULL auto_increment,
    `client_id` varchar(50) NOT NULL,
    `staff_name` varchar(100) NOT NULL DEFAULT '',
    `phone` varchar(50) NOT NULL DEFAULT '',
    `email` varchar(50) NOT NULL DEFAULT '',
    `designation` varchar(50) NOT NULL DEFAULT '',
    `department` varchar(50) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `ost_user_p` (
    `id` int(10) unsigned NOT NULL auto_increment,
    `user_id` varchar(50) NOT NULL DEFAULT '',
    `p` TEXT NOT NULL DEFAULT '',

    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `ost_1asiaahl` (
    `id` int(11) unsigned NOT NULL auto_increment,
    `texts` text NOT NULL '',
    `updated` datetime NOT NULL default '0000-00-00 00:00:00',
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `ost_custom_settings` (
    `id` int(11) unsigned NOT NULL auto_increment,
    `more_ticket_alert_email` TEXT NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ost_client`;
CREATE TABLE `ost_client` (
    `id` int(11) unsigned NOT NULL auto_increment,
    `client_id` varchar(50) NOT NULL,
    `client_name` varchar(100) NOT NULL DEFAULT '',
    `username` varchar(50) NOT NULL,
    `client_type` varchar(100) NOT NULL DEFAULT '',
    `email` varchar(100) NOT NULL DEFAULT '',
    `phone` varchar(100) NOT NULL DEFAULT '',
    `client_asn` varchar(50) NOT NULL DEFAULT '',
    `password` varchar(100) NOT NULL,
    `created` datetime NOT NULL default '0000-00-00 00:00:00',
    `lastlogin` datetime NOT NULL default '0000-00-00 00:00:00',
    `updated` datetime NOT NULL default '0000-00-00 00:00:00',
    PRIMARY KEY (`client_id`),
    KEY `id` (`id`),
    KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ost_services`;
CREATE TABLE `ost_services` (
    `id` int(30) unsigned NOT NULL auto_increment,
    `client_id` varchar(30) NOT NULL,
    `service_name` varchar(50) NOT NULL DEFAULT '',
    `ip_bw_amount` varchar(50) NOT NULL DEFAULT '',
    `ip_bw_unit` varchar(50) NOT NULL DEFAULT '',
    `ip_bw_1asiaahl_end_ip` varchar(50) NOT NULL DEFAULT '',
    `ip_bw_client_end_ip` varchar(50) NOT NULL DEFAULT '',
    `ip_bw_remarks` varchar(50) NOT NULL DEFAULT '',

    `ip_transit_amount` varchar(50) NOT NULL DEFAULT '',
    `ip_transit_amount_unit` varchar(50) NOT NULL DEFAULT '',
    `ip_transit_1asiaahl_end_ip` varchar(50) NOT NULL DEFAULT '',
    `ip_transit_client_end_ip` varchar(50) NOT NULL DEFAULT '',
    `ip_transit_prefix` varchar(50) NOT NULL DEFAULT '',

    `iplc_fields_level` varchar(50) NOT NULL DEFAULT '',
    `iplc_fields_amount` varchar(50) NOT NULL DEFAULT '',
    `iplc_fields_circuit_type` varchar(50) NOT NULL DEFAULT '',
    `iplc_fields_circuit_diagram` TEXT NOT NULL DEFAULT '',

    `mpls_fields_primary_circuit_level` varchar(50) NOT NULL DEFAULT '',
    `mpls_fields_primary_circuit_amount` varchar(50) NOT NULL DEFAULT '',
    `mpls_fields_primary_circuit_type` varchar(50) NOT NULL DEFAULT '',
    `mpls_fields_primary_circuit_diagram` TEXT NOT NULL DEFAULT '',
    `mpls_fields_secondary_circuit_level` varchar(50) NOT NULL DEFAULT '',
    `mpls_fields_secondary_circuit_amount` varchar(50) NOT NULL DEFAULT '',
    `mpls_fields_secondary_circuit_type` varchar(50) NOT NULL DEFAULT '',
    `mpls_fields_secondary_circuit_diagram` TEXT NOT NULL DEFAULT '',
    `mpls_fields_tertiary_circuit_level` varchar(50) NOT NULL DEFAULT '',
    `mpls_fields_tertiary_circuit_amount` varchar(50) NOT NULL DEFAULT '',
    `mpls_fields_tertiary_circuit_type` varchar(50) NOT NULL DEFAULT '',
    `mpls_fields_tertiary_circuit_diagram` TEXT NOT NULL DEFAULT '',

    `con_details_local_loop` varchar(50) NOT NULL DEFAULT '',

    `con_details_local_loop_nttn_fields_nttn` varchar(50) NOT NULL DEFAULT '',
    `con_details_nttn_odf_tray` varchar(50) NOT NULL DEFAULT '',
    `con_details_nttn_odf_port` varchar(50) NOT NULL DEFAULT '',
    `con_details_nttn_odf_circuit_type` varchar(50) NOT NULL DEFAULT '',

    `con_details_local_loop_mixed_fields_nttn` varchar(50) NOT NULL DEFAULT '',
    `con_details_local_loop_mixed_fields_nttn_point_a` varchar(50) NOT NULL DEFAULT '',
    `con_details_local_loop_mixed_fields_nttn_point_b` varchar(50) NOT NULL DEFAULT '',
    `con_details_local_loop_mixed_fields_overhead` varchar(50) NOT NULL DEFAULT '',
    `con_details_local_loop_mixed_fields_overhead_point_a` varchar(50) NOT NULL DEFAULT '',
    `con_details_local_loop_mixed_fields_overhead_point_b` varchar(50) NOT NULL DEFAULT '',

    `odf_id` TEXT NOT NULL default '',

    `interface_type_router` varchar(50) NOT NULL DEFAULT '',
    `interface_router_name` varchar(50) NOT NULL DEFAULT '',
    `interface_router_port` varchar(50) NOT NULL DEFAULT '',

    `interface_type_mux` varchar(50) NOT NULL DEFAULT '',
    `interface_mux_name` varchar(50) NOT NULL DEFAULT '',
    `interface_mux_port` varchar(50) NOT NULL DEFAULT '',

    `interface_type_mix` varchar(50) NOT NULL DEFAULT '',
    `interface_mixed_router_name` varchar(50) NOT NULL DEFAULT '',
    `interface_mixed_router_port` varchar(50) NOT NULL DEFAULT '',
    `interface_mixed_mux_name` varchar(50) NOT NULL DEFAULT '',
    `interface_mixed_mux_port` varchar(50) NOT NULL DEFAULT '',

    `link_act_date` varchar(50) NOT NULL DEFAULT '',
    `test_alloc_from` varchar(50) NOT NULL DEFAULT '',
    `test_alloc_to` varchar(50) NOT NULL DEFAULT '',
    `billing_statement_date` varchar(50) NOT NULL DEFAULT '',
    `con_details_remarks` TEXT NOT NULL DEFAULT '',

    `created` datetime NOT NULL default '0000-00-00 00:00:00',
    `updated` datetime NOT NULL default '0000-00-00 00:00:00',
    `comissioned` tinyint(1) NOT NULL default 0,

    PRIMARY KEY (`id`),
    UNIQUE KEY (`client_id`),
    KEY `service_name` (`service_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ost_odfs`;
CREATE TABLE `ost_odfs` (
    `id` int(10) unsigned NOT NULL auto_increment,
    `locked` tinyint(1) NOT NULL default 0,
    `lock_user` varchar(50) NOT NULL default '',
    `lock_start` datetime NOT NULL default '0000-00-00 00:00:00',
    `lock_ends` datetime NOT NULL default '0000-00-00 00:00:00',
    `odf_name` VARCHAR(50) NOT NULL,
    `odf_json_obj` TEXT NOT NULL,
    `created` datetime NOT NULL default '0000-00-00 00:00:00',
    `updated` datetime NOT NULL default '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    UNIQUE KEY (`odf_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ost_service_order_logs`;
CREATE TABLE `ost_service_order_logs` (
    `id` int(50) unsigned NOT NULL auto_increment,
    `order_id` varchar(50) NOT NULL,
    `alert_admin` int(1) NOT NULL DEFAULT 1,
    `log_type` enum('accepted','rejected','cancelled','created','updated') NOT NULL,
    `user_id` varchar(50) NOT NULL,
    `client_side` int(1) NOT NULL,
    `log_date` datetime NOT NULL,
    `ip` varchar(50) NOT NULL,
    `log_seen` tinyint(1) NOT NULL default 0,
    PRIMARY KEY (`id`),
    KEY `order_id` (`order_id`),
    KEY `log_type` (`log_type`),
    KEY `log_date` (`log_date`),
    KEY `ip` (`ip`),
    KEY `log_seen` (`log_seen`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ost_order_lock`;
CREATE TABLE `ost_order_lock` (
    `id` int(30) unsigned NOT NULL auto_increment,
    `order_id` varchar(50) NOT NULL,
    `staff_id` varchar(50) NOT NULL,
    `created` datetime NOT NULL,
    `expire` datetime,
    UNIQUE KEY (`order_id`),
    PRIMARY KEY `id` (`id`),
    KEY `staff_id` (`staff_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ost_service_order`;
CREATE TABLE `ost_service_order` (
  `id` int(30) unsigned NOT NULL auto_increment,
  `order_id` varchar(50) NOT NULL,

  `status` enum('pending','accepted','rejected','cancelled') NOT NULL,
  `isupdated` int(1) NOT NULL default 0,
  `client_cancelled` int(1) NOT NULL default 0,
  `dept_id` int(10) NOT NULL default 1,

  `created_by` varchar(50) NOT NULL,

  `assigned_staff_id` varchar(50) NOT NULL default '',
  `locked` int(1) NOT NULL default 0,

  `created_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `lastmsg_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_date` datetime NOT NULL default '0000-00-00 00:00:00',

  `ip_order_created_from` varchar(16),
  `source` varchar(30) default 'web',

  `client_id` varchar(50) NOT NULL,
  `customer_rel_no` varchar(50) NOT NULL,
  `customer_name` varchar(50) NOT NULL,
  `customer_email` varchar(50) NOT NULL,

  `customer_type` varchar(50) NOT NULL,
  `service_type` varchar(50) NOT NULL,
  `circuit_type` varchar(50) NOT NULL,

  `order_creator_name` varchar(50) NOT NULL,
  `order_creator_designation` varchar(50) NOT NULL,
  `order_creator_dept_name` varchar(50) NOT NULL,
  `order_creator_address` varchar(50) NOT NULL,
  `order_creator_city` varchar(50) NOT NULL,
  `order_creator_zip_or_po` int(15) NOT NULL,
  `order_creator_country` varchar(50) NOT NULL,
  `order_creator_office_phone` int(30) NOT NULL,
  `order_creator_fax` int(30) NOT NULL,
  `order_creator_mobile` int(30) NOT NULL,
  `order_creator_service_ready_date` varchar(50) NOT NULL,


  `order_customer_name` varchar(50) NOT NULL,
  `order_customer_designation` varchar(50) NOT NULL,
  `order_customer_dept_name` varchar(50) NOT NULL,
  `order_customer_address` varchar(50) NOT NULL,
  `order_customer_city` varchar(50) NOT NULL,
  `order_customer_zip_or_po` varchar(50) NOT NULL,
  `order_customer_country` varchar(50) NOT NULL,
  `order_customer_phone_office` varchar(50) NOT NULL,
  `order_customer_fax` varchar(50) NOT NULL,
  `order_customer_mobile` varchar(50) NOT NULL,


  `order_customer_backhaul_provider` varchar(50) NOT NULL,
  `order_customer_backhaul_responsibility` varchar(50) NOT NULL,
  `order_customer_equipment_to_be_used` varchar(100) NOT NULL,
  `order_customer_equipment_others` varchar(100) NOT NULL default '',
  `order_customer_equipment_name` varchar(100) NOT NULL,
  `order_customer_equipment_model` varchar(100) NOT NULL,
  `order_customer_equipment_vendor` varchar(100) NOT NULL,
  `order_customer_connectivity_interface` varchar(100) NOT NULL,
  `order_customer_connectivity_interface_others` varchar(100) NOT NULL default '',
  `order_customer_protocol_to_be_used` varchar(100) NOT NULL,
  `order_customer_protocol_others` varchar(100) NOT NULL default '',
  `order_customer_connectivity_capacity` varchar(100) NOT NULL,
  `order_customer_connectivity_capacity_others` varchar(100) NOT NULL,
  `order_customer_special_ins` varchar(500) NOT NULL default '',


  `order_technical_contact_name` varchar(50) NOT NULL,
  `order_technical_contact_mobile` varchar(50) NOT NULL,
  `order_technical_contact_phone` varchar(50) NOT NULL,
  `order_technical_contact_email` varchar(50) NOT NULL,
  `order_technical_contact_messengers` varchar(100) NOT NULL,


  `order_routing_type` varchar(50) NOT NULL,
  `order_customer_as_sys_name` varchar(50) NOT NULL,
  `order_customer_as_sys_num` varchar(50) NOT NULL,
  `order_customer_as_set_num` varchar(50) NOT NULL,
  `order_bgp_routing` varchar(50) NOT NULL,
  `order_router_name` varchar(50) NOT NULL,
  `order_bw_speed_cir` varchar(50) NOT NULL,
  `order_max_burstable_limit` varchar(50) NOT NULL,
  `connectivity_interface` varchar(50) NOT NULL,
  `order_fiber_type` varchar(50) NOT NULL,
  `order_ip_details_for_global` varchar(500) NOT NULL,
  `order_special_routing_comments` varchar(500) NOT NULL default '',


  `order_billing_total_non_recurring_charges` varchar(30) NOT NULL,
  `order_billing_total_monthly_recurring_charges` varchar(30) NOT NULL,
  `order_billing_hw_charges` varchar(30) NOT NULL,
  `order_billing_misc_charges` varchar(30) NOT NULL default '',
  `order_billing_special_discount` varchar(30) NOT NULL default '',
  `order_billing_vat_or_tax` varchar(30) NOT NULL,
  `order_billing_deposit` varchar(30) NOT NULL,
  `order_billing_total_payable_with_sof` varchar(30) NOT NULL,

  `order_special_requests_if_any` varchar(500) NOT NULL default '',
  `applicants_name` varchar(50) NOT NULL,
  `applicants_designation` varchar(20) NOT NULL,
  `application_date` varchar(30) NOT NULL,
  `applicant_sig_with_seal` varchar(500) NOT NULL default '',

  UNIQUE KEY `order_id` (`order_id`),
  PRIMARY KEY (`id`),
  KEY `applicants_name` (`applicants_name`),
  KEY `application_date` (`application_date`),
  KEY `client_id` (`client_id`),
  KEY `customer_rel_no` (`customer_rel_no`),
  KEY `order_creator_name` (`order_creator_name`),
  KEY `created_date` (`created_date`)

) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `%TABLE_PREFIX%ticket`;
CREATE TABLE `%TABLE_PREFIX%ticket` (
  `ticket_id` int(11) unsigned NOT NULL auto_increment,
  `client_id` varchar(50) NOT NULL,
  `ticketID` int(11) unsigned NOT NULL default '0',
  `dept_id` int(10) unsigned NOT NULL default '1',
  `priority_id` int(10) unsigned NOT NULL default '2',
  `topic_id` int(10) unsigned NOT NULL default '0',
  `staff_id` int(10) unsigned NOT NULL default '0',
  `email` varchar(120) NOT NULL default '',
  `alt_email` varchar(50) NOT NULL default '',
  `name` varchar(32) NOT NULL default '',
  `alt_contact_name` varchar(32) NOT NULL default '',
  `subject` varchar(64) NOT NULL default '[no subject]',
  `helptopic` varchar(255) default NULL,
  `phone` varchar(16) default NULL,
  `phone_ext` varchar(8) default NULL,
  `alt_phone_num` varchar(16) default NULL,
  `ip_address` varchar(16) NOT NULL default '',
  `status` enum('open','closed') NOT NULL default 'open',
  `sla_claim` varchar(10) NOT NULL default '',
  `source` enum('Web','Email','Phone','Other') NOT NULL default 'Web',
  `isoverdue` tinyint(1) unsigned NOT NULL default '0',
  `isanswered` tinyint(1) unsigned NOT NULL default '0',
  `duedate` datetime default NULL,
  `reopened` datetime default NULL,
  `closed` datetime default NULL,
  `lastmessage` datetime default NULL,
  `lastresponse` datetime default NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`ticket_id`),
  UNIQUE KEY `email_extid` (`ticketID`,`email`),
  KEY `dept_id` (`dept_id`),
  KEY `staff_id` (`staff_id`),
  KEY `status` (`status`),
  KEY `priority_id` (`priority_id`),
  KEY `created` (`created`),
  KEY `closed` (`closed`),
  KEY `duedate` (`duedate`),
  KEY `topic_id` (`topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
