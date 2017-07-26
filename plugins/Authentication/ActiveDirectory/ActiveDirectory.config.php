<?php
/**
Copyright 2011-2016 Nick Korbel

This file is part of Booked Scheduler.

Booked Scheduler is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Booked Scheduler is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Booked Scheduler.  If not, see <http://www.gnu.org/licenses/>.
*/
// See http://adldap.sourceforge.net/wiki/doku.php?id=documentation_configuration for a full list

$conf['settings']['domain.controllers'] = 'ldaps.ad.hhu.de'; // comma separated list of ldap servers such as domaincontroller1,controller2
$conf['settings']['port'] = '636';      // default ldap port 389 or 636 for ssl.
$conf['settings']['username'] = 'SVC_Omero';     // admin user - bind to ldap service with an authorized account user/password
$conf['settings']['password'] = 'HD5ETJo89bYZPFsPtZHJ';     // admin password - corresponding password
$conf['settings']['basedn'] =  'ou=IDMUsers,dc=ad,dc=hhu,dc=de';   // The base dn for your domain. This is generally the same as your account suffix, but broken up and prefixed with DC=. Your base dn can be located in the extended attributes in Active Directory Users and Computers MMC.
$conf['settings']['version'] = '3';		// LDAP protocol version
$conf['settings']['use.ssl'] = 'true'; // 'true' if 636 was used.
$conf['settings']['account.suffix'] = '@ad.hhu.de';	// The full account suffix for your domain. Example: @uidauthent.domain.com.
$conf['settings']['database.auth.when.ldap.user.not.found'] = 'true';	// if ldap auth fails, authenticate against Booked Scheduler database
$conf['settings']['attribute.mapping'] = 'sn=sn,givenname=givenName,mail=mail,telephonenumber=telephonenumber,physicaldeliveryofficename=physicaldeliveryofficename,title=title';	// mapping of required attributes to attribute names in your directory
$conf['settings']['required.groups'] = '';	// Required groups (empty if not necessary) User only needs to belong to at least one listed (eg. Group1,Group2)
$conf['settings']['sync.groups'] = 'true';	// Whether or not groups should be synced into Booked. When true then be sure that the attribute.mapping config value contains a correct map for groups
$conf['settings']['use.sso'] = 'false';	// Whether or not to use single sign on
