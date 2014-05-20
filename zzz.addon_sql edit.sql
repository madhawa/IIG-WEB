ALTER TABLE `ost_ticket` ADD `created_by_noc` tinyint(1) NOT NULL
ALTER TABLE `ost_ticket` ADD `tt_creator` varchar(50) NOT NULL
ALTER TABLE `ost_ticket` ADD `last_editor` varchar(50) NOT NULL DEFAULT ''
ALTER TABLE `ost_ticket` ADD `closer` varchar(50) NOT NULL DEFAULT ''

ALTER TABLE `ost_ticket_message` ADD `client_id` varchar(50) NOT NULL
ALTER TABLE `ost_ticket_response` ADD `staff_id` varchar(50) NOT NULL
ALTER TABLE `ost_ticket_note` ADD `staff_id` varchar(50) NOT NULL
ALTER TABLE `ost_ticket` ADD `internal_cc` TEXT NOT NULL DEFAULT ''