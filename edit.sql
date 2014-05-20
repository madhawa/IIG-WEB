ALTER TABLE `ost_staff` DROP `access_level` 
ALTER TABLE `ost_staff` ADD `access_level` tinyint(1) NOT NULL DEFAULT 0 AFTER `dept_id`
ALTER TABLE `ost_staff` DROP `isadmin`


ALTER TABLE `ost_department` ADD `permission_admin_can_view_all_tickets` tinyint(1) NOT NULL DEFAULT 1
ALTER TABLE `ost_department` ADD `permission_any_staff_can_view_all_tickets` tinyint(1) NOT NULL DEFAULT 0
ALTER TABLE `ost_department` ADD `permission_admin_can_create_ticket` tinyint(1) NOT NULL DEFAULT 0
ALTER TABLE `ost_department` ADD `permission_any_staff_can_create_ticket` tinyint(1) NOT NULL DEFAULT 0
ALTER TABLE `ost_department` ADD `permission_admin_can_edit_ticket` tinyint(1) NOT NULL DEFAULT 0
ALTER TABLE `ost_department` ADD `permission_assignee_can_edit_ticket` tinyint(1) NOT NULL DEFAULT 1
ALTER TABLE `ost_department` ADD `permission_admin_can_delete_ticket` tinyint(1) NOT NULL DEFAULT 0
ALTER TABLE `ost_department` ADD `permission_assignee_can_delete_ticket` tinyint(1) NOT NULL DEFAULT 0
ALTER TABLE `ost_department` ADD `permission_admin_can_close_any_tickets` tinyint(1) NOT NULL DEFAULT 0
ALTER TABLE `ost_department` ADD `permission_assignee_can_close_ticket` tinyint(1) NOT NULL DEFAULT 0
ALTER TABLE `ost_department` ADD `permission_admin_can_reply_any_tickets` tinyint(1) NOT NULL DEFAULT 1
ALTER TABLE `ost_department` ADD `permission_assignee_can_reply_ticket` tinyint(1) NOT NULL DEFAULT 1
ALTER TABLE `ost_department` ADD `permission_admin_can_postnote_ticket` tinyint(1) NOT NULL DEFAULT 1
ALTER TABLE `ost_department` ADD `permission_staff_can_postnote_ticket` tinyint(1) NOT NULL DEFAULT 1
ALTER TABLE `ost_department` ADD `permission_can_access_sections` TEXT NOT NULL DEFAULT ''


ALTER TABLE `ost_department` ADD `fixed_id` tinyint(1) NOT NULL DEFAULT 0 AFTER `dept_name`