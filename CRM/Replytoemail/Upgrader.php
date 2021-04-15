<?php
use CRM_Replytoemail_ExtensionUtil as E;

/**
 * Collection of upgrade steps.
 */
class CRM_Replytoemail_Upgrader extends CRM_Replytoemail_Upgrader_Base {

  // By convention, functions that look like "function upgrade_NNNN()" are
  // upgrade tasks. They are executed in order (like Drupal's hook_update_N).

  /**
   * Example: Run an external SQL script when the module is installed.
   *
  public function install() {
    $this->executeSqlFile('sql/myinstall.sql');
  }

  /**
   * Example: Work with entities usually not available during the install step.
   *
   * This method can be used for any post-install tasks. For example, if a step
   * of your installation depends on accessing an entity that is itself
   * created during the installation (e.g., a setting or a managed entity), do
   * so here to avoid order of operation problems.
   */
  // public function postInstall() {
  //  $customFieldId = civicrm_api3('CustomField', 'getvalue', array(
  //    'return' => array("id"),
  //    'name' => "customFieldCreatedViaManagedHook",
  //  ));
  //  civicrm_api3('Setting', 'create', array(
  //    'myWeirdFieldSetting' => array('id' => $customFieldId, 'weirdness' => 1),
  //  ));
  // }

  /**
   * Example: Run an external SQL script when the module is uninstalled.
   */
  // public function uninstall() {
  //  $this->executeSqlFile('sql/myuninstall.sql');
  // }

  /**
   * Example: Run a simple query when a module is enabled.
   */
  // public function enable() {
  //  CRM_Core_DAO::executeQuery('UPDATE foo SET is_active = 1 WHERE bar = "whiz"');
  // }

  /**
   * Example: Run a simple query when a module is disabled.
   */
  // public function disable() {
  //   CRM_Core_DAO::executeQuery('UPDATE foo SET is_active = 0 WHERE bar = "whiz"');
  // }

  /**
   * Example: Run a couple simple queries.
   *
   * @return TRUE on success
   * @throws Exception
   */
  // public function upgrade_4200() {
  //   $this->ctx->log->info('Applying update 4200');
  //   CRM_Core_DAO::executeQuery('UPDATE foo SET bar = "whiz"');
  //   CRM_Core_DAO::executeQuery('DELETE FROM bang WHERE willy = wonka(2)');
  //   return TRUE;
  // }


  /**
   * Example: Run an external SQL script.
   *
   * @return TRUE on success
   * @throws Exception
   */
  // public function upgrade_4201() {
  //   $this->ctx->log->info('Applying update 4201');
  //   // this path is relative to the extension base dir
  //   $this->executeSqlFile('sql/upgrade_4201.sql');
  //   return TRUE;
  // }


  /**
   * Example: Run a slow upgrade process by breaking it up into smaller chunk.
   *
   * @return TRUE on success
   * @throws Exception
   */
  // public function upgrade_4202() {
  //   $this->ctx->log->info('Planning update 4202'); // PEAR Log interface

  //   $this->addTask(E::ts('Process first step'), 'processPart1', $arg1, $arg2);
  //   $this->addTask(E::ts('Process second step'), 'processPart2', $arg3, $arg4);
  //   $this->addTask(E::ts('Process second step'), 'processPart3', $arg5);
  //   return TRUE;
  // }
  // public function processPart1($arg1, $arg2) { sleep(10); return TRUE; }
  // public function processPart2($arg3, $arg4) { sleep(10); return TRUE; }
  // public function processPart3($arg5) { sleep(10); return TRUE; }

  public function upgrade_1100() {
      $this->ctx->log->info('Applying update 1100');
      $reportInstance = civicrm_api3('ReportInstance', 'get', [
        'sequential' => 1,
        'name' => "New Email Replies",
        'report_id' => "activity",
      ]);
      $newReply = civicrm_api3('OptionValue', 'get', [
        'sequential' => 1,
        'return' => ["id"],
        'option_group_id' => "activity_status",
        'name' => "New Reply",
      ]);
      $newReplyStatusValue = civicrm_api3('OptionValue', 'getvalue', ['id' => $newReply['id'], 'return' => 'value']);
      $stringLength = strlen($newReplyStatusValue);
      if (!empty($reportInstance['values'])) {
        foreach ($reportInstance['values'] as $instance) {
          $instanceID = $instance['id'];
          civicrm_api3('ReportInstance', 'create', [
            'id' => $instanceID,
            'form_values' => "a:50:{s:6:\"fields\";a:5:{s:14:\"contact_target\";s:1:\"1\";s:20:\"contact_target_email\";s:1:\"1\";s:16:\"activity_type_id\";s:1:\"1\";s:16:\"activity_subject\";s:1:\"1\";s:18:\"activity_date_time\";s:1:\"1\";}s:17:\"contact_source_op\";s:3:\"has\";s:20:\"contact_source_value\";s:0:\"\";s:19:\"contact_assignee_op\";s:3:\"has\";s:22:\"contact_assignee_value\";s:0:\"\";s:17:\"contact_target_op\";s:3:\"has\";s:20:\"contact_target_value\";s:0:\"\";s:15:\"current_user_op\";s:2:\"eq\";s:18:\"current_user_value\";s:1:\"0\";s:27:\"activity_date_time_relative\";s:0:\"\";s:23:\"activity_date_time_from\";s:0:\"\";s:21:\"activity_date_time_to\";s:0:\"\";s:19:\"activity_subject_op\";s:3:\"has\";s:22:\"activity_subject_value\";s:0:\"\";s:19:\"activity_type_id_op\";s:2:\"in\";s:22:\"activity_type_id_value\";a:1:{i:0;s:2:\"12\";}s:12:\"status_id_op\";s:2:\"in\";s:15:\"status_id_value\";a:1:{i:0;s:{$stringLength}:\"{$newReplyStatusValue}\";}s:11:\"location_op\";s:3:\"has\";s:14:\"location_value\";s:0:\"\";s:10:\"details_op\";s:3:\"has\";s:13:\"details_value\";s:0:\"\";s:14:\"priority_id_op\";s:2:\"in\";s:17:\"priority_id_value\";a:0:{}s:17:\"street_address_op\";s:3:\"has\";s:20:\"street_address_value\";s:0:\"\";s:14:\"postal_code_op\";s:3:\"has\";s:17:\"postal_code_value\";s:0:\"\";s:7:\"city_op\";s:3:\"has\";s:10:\"city_value\";s:0:\"\";s:13:\"country_id_op\";s:2:\"in\";s:16:\"country_id_value\";a:0:{}s:20:\"state_province_id_op\";s:2:\"in\";s:23:\"state_province_id_value\";a:0:{}s:6:\"gid_op\";s:2:\"in\";s:9:\"gid_value\";a:0:{}s:9:\"order_bys\";a:2:{i:1;a:2:{s:6:\"column\";s:18:\"activity_date_time\";s:5:\"order\";s:3:\"ASC\";}i:2;a:2:{s:6:\"column\";s:16:\"activity_type_id\";s:5:\"order\";s:3:\"ASC\";}}s:11:\"description\";s:33:\"Easily Track New Replies Received\";s:13:\"email_subject\";s:0:\"\";s:8:\"email_to\";s:0:\"\";s:8:\"email_cc\";s:0:\"\";s:9:\"row_count\";s:0:\"\";s:9:\"view_mode\";s:4:\"view\";s:13:\"cache_minutes\";s:2:\"60\";s:11:\"is_reserved\";s:1:\"1\";s:10:\"permission\";s:17:\"access CiviReport\";s:9:\"parent_id\";s:0:\"\";s:8:\"radio_ts\";s:0:\"\";s:6:\"groups\";s:0:\"\";s:11:\"instance_id\";s:2:\"{$instanceID}\";}",
          ]);
        }
      }
      return TRUE;
    }
}
