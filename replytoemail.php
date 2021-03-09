<?php

require_once 'replytoemail.civix.php';
// phpcs:disable
use CRM_Replytoemail_ExtensionUtil as E;
// phpcs:enable

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function replytoemail_civicrm_config(&$config) {
  _replytoemail_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function replytoemail_civicrm_xmlMenu(&$files) {
  _replytoemail_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function replytoemail_civicrm_install() {
  // Edit inbound email activity.
  $inbound = civicrm_api3('OptionValue', 'get', [
    'sequential' => 1,
    'return' => ["id"],
    'option_group_id' => "activity_type",
    'name' => "Inbound Email",
  ]);
  if (!empty($inbound['id'])) {
    civicrm_api3('OptionValue', 'create', [
      'option_group_id' => "activity_type",
      'name' => ts("Inbound Email"),
      'description' => "",
      'is_reserved' => 1,
      'is_active' => 1,
      'icon' => 'crm-i fa-paper-plane-o',
      'id' => $inbound['id'],
    ]);
  }
  // Create new reply status for activity if ti doesn't exist.
  $newReply = civicrm_api3('OptionValue', 'get', [
    'sequential' => 1,
    'return' => ["id"],
    'option_group_id' => "activity_status",
    'name' => "New Reply",
  ]);
  if (empty($newReply['id'])) {
    civicrm_api3('OptionValue', 'create', [
      'option_group_id' => "activity_status",
      'description' => "",
      'is_active' => 1,
      'name' => ts("New Reply"),
    ]);
  }
  // Create report instance.
  $reportInstance = civicrm_api3('ReportInstance', 'create', [
    'title' => "New Email Replies",
    'name' => "New Email Replies",
    'report_id' => "activity",
    'description' => "Easily Track New Replies Received",
    'is_reserved' => 1,
    'form_values' => "a:52:{s:8:\"entryURL\";s:61:\"https://test2-dms.canadahelps.org/dms/report/activity?reset=1\";s:6:\"fields\";a:5:{s:14:\"contact_source\";s:1:\"1\";s:20:\"contact_source_email\";s:1:\"1\";s:16:\"activity_type_id\";s:1:\"1\";s:16:\"activity_subject\";s:1:\"1\";s:18:\"activity_date_time\";s:1:\"1\";}s:17:\"contact_source_op\";s:3:\"has\";s:20:\"contact_source_value\";s:0:\"\";s:19:\"contact_assignee_op\";s:3:\"has\";s:22:\"contact_assignee_value\";s:0:\"\";s:17:\"contact_target_op\";s:3:\"has\";s:20:\"contact_target_value\";s:0:\"\";s:15:\"current_user_op\";s:2:\"eq\";s:18:\"current_user_value\";s:1:\"0\";s:27:\"activity_date_time_relative\";s:0:\"\";s:23:\"activity_date_time_from\";s:0:\"\";s:21:\"activity_date_time_to\";s:0:\"\";s:19:\"activity_subject_op\";s:3:\"has\";s:22:\"activity_subject_value\";s:0:\"\";s:19:\"activity_type_id_op\";s:2:\"in\";s:22:\"activity_type_id_value\";a:1:{i:0;s:2:\"12\";}s:12:\"status_id_op\";s:2:\"in\";s:15:\"status_id_value\";a:1:{i:0;s:2:\"10\";}s:11:\"location_op\";s:3:\"has\";s:14:\"location_value\";s:0:\"\";s:10:\"details_op\";s:3:\"has\";s:13:\"details_value\";s:0:\"\";s:14:\"priority_id_op\";s:2:\"in\";s:17:\"priority_id_value\";a:0:{}s:17:\"street_address_op\";s:3:\"has\";s:20:\"street_address_value\";s:0:\"\";s:14:\"postal_code_op\";s:3:\"has\";s:17:\"postal_code_value\";s:0:\"\";s:7:\"city_op\";s:3:\"has\";s:10:\"city_value\";s:0:\"\";s:13:\"country_id_op\";s:2:\"in\";s:16:\"country_id_value\";a:0:{}s:20:\"state_province_id_op\";s:2:\"in\";s:23:\"state_province_id_value\";a:0:{}s:6:\"gid_op\";s:2:\"in\";s:9:\"gid_value\";a:0:{}s:9:\"order_bys\";a:2:{i:1;a:2:{s:6:\"column\";s:18:\"activity_date_time\";s:5:\"order\";s:3:\"ASC\";}i:2;a:2:{s:6:\"column\";s:16:\"activity_type_id\";s:5:\"order\";s:3:\"ASC\";}}s:11:\"description\";s:34:\"Easily Track New Replies Received \";s:13:\"email_subject\";s:0:\"\";s:8:\"email_to\";s:0:\"\";s:8:\"email_cc\";s:0:\"\";s:9:\"row_count\";s:2:\"10\";s:9:\"view_mode\";s:8:\"criteria\";s:14:\"addToDashboard\";s:1:\"1\";s:13:\"cache_minutes\";s:2:\"15\";s:11:\"is_reserved\";s:1:\"1\";s:10:\"permission\";s:17:\"access CiviReport\";s:9:\"parent_id\";s:0:\"\";s:8:\"radio_ts\";s:0:\"\";s:6:\"groups\";s:0:\"\";s:11:\"instance_id\";s:2:\"71\";}",
    ]);
  if ($reportInstance['id']) {
    // Add to dashboard.
    $dashboard = civicrm_api3('Dashboard', 'create', [
      "name" => "report/" . $reportInstance['id'],
      "label" => "New Email Replies",
      "url" => "civicrm/report/instance/" . $reportInstance['id'] . "?reset=1&section=2&context=dashlet&rowCount=10",
      "permission" => "access CiviReport",
      "fullscreen_url" => "civicrm/report/instance/" . $reportInstance['id'] . "?reset=1&section=2&context=dashletFullscreen&rowCount=10",
      "is_active" => 1,
      "cache_minutes" => 15,
    ]);

    if (!empty($dashboard['id'])) {
      $cids = E::getUsersByRole("client administrator");
      if (!empty($cids)) {
        foreach ($cids as $cid) {
          civicrm_api3('DashboardContact', 'create', [
            'dashboard_id' => $dashboard['id'],
            'contact_id' => $cid,
            'column_no' => 1,
            'is_active' => 1,
          ]);
        }
      }

    }
  }
  _replytoemail_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function replytoemail_civicrm_postInstall() {
  _replytoemail_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function replytoemail_civicrm_uninstall() {
  _replytoemail_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function replytoemail_civicrm_enable() {
  _replytoemail_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function replytoemail_civicrm_disable() {
  _replytoemail_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function replytoemail_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _replytoemail_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function replytoemail_civicrm_managed(&$entities) {
  _replytoemail_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function replytoemail_civicrm_caseTypes(&$caseTypes) {
  _replytoemail_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function replytoemail_civicrm_angularModules(&$angularModules) {
  _replytoemail_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function replytoemail_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _replytoemail_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function replytoemail_civicrm_entityTypes(&$entityTypes) {
  _replytoemail_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_thems().
 */
function replytoemail_civicrm_themes(&$themes) {
  _replytoemail_civix_civicrm_themes($themes);
}

/**
 * Implementation of hook_civicrm_buildForm
 *
 */
function replytoemail_civicrm_buildForm($formName, &$form) {
  if ($formName == 'CRM_Activity_Form_Activity') {
    $activityTypes = CRM_Activity_BAO_Activity::buildOptions('activity_type_id', 'get', ['flip' => TRUE]);
    if ($form->_activityTypeId == $activityTypes['Inbound Email']  && $form->_action & CRM_Core_Action::VIEW) {
      // Add the reply button to the form.
      CRM_Core_Region::instance('page-body')->add(array(
        'template' => 'CRM/Replytoemail/Reply.tpl',
      ));
    }
  }
  if ($formName == 'CRM_Contact_Form_Task_Email' && $form->_context == 'sendReply') {
    $activityId = CRM_Utils_Request::retrieve('activityId', 'Positive');
    $form->add('hidden', 'original_activity_id');
    if (!empty($activityId)) {
      $defaults = ['original_activity_id' => $activityId];
      // Fetch the subject.
      $subject = civicrm_api3('Activity', 'get', [
        'id' => $activityId,
        'return' => ['subject'],
        'sequential' => 1,
      ]);
      if (!empty($subject['values'])) {
        $defaults['subject'] = 'RE: ' . $subject['values'][0]['subject'];
      }
      $form->setDefaults($defaults);
    }
  }
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_postProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postProcess
 */
function replytoemail_civicrm_postProcess($formName, &$form) {
  if ($formName == 'CRM_Contact_Form_Task_Email' && $form->_context == 'sendReply') {
    if (!empty($form->_submitValues['original_activity_id'])) {
      // We set the status of the inbound email activity to completed.
      civicrm_api3('Activity', 'create', [
        'id' => $form->_submitValues['original_activity_id'],
        'source_contact_id' => "user_contact_id",
        'status_id' => "Completed",
      ]);
    }
  }
}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
//function replytoemail_civicrm_navigationMenu(&$menu) {
//  _replytoemail_civix_insert_navigation_menu($menu, 'Mailings', array(
//    'label' => E::ts('New subliminal message'),
//    'name' => 'mailing_subliminal_message',
//    'url' => 'civicrm/mailing/subliminal',
//    'permission' => 'access CiviMail',
//    'operator' => 'OR',
//    'separator' => 0,
//  ));
//  _replytoemail_civix_navigationMenu($menu);
//}
