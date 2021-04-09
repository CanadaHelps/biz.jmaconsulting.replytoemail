<div class="additional-actions" style="float:right">
<div class="archiveemail" style="float:left;padding-right:10px;">
    {capture assign=archiveUrl}{crmURL p="civicrm/activity/archive" q="action=edit&reset=1&cid=`$contactId`&selectedChild=activity&atype=3&context=archive&activityId=`$activityId`"}{/capture}
    {capture assign=link}class="action-item crm-popup" style="text-decoration:none;color:#FFF" href="{$archiveUrl}"{/capture}
    {ts 1=$link}<a %1><span><i class="crm-i fa-times"></i> Archive</span></a>{/ts}
</div>
<div class="replytoemail" style="float:left">
    {capture assign=replyUrl}{crmURL p="civicrm/activity/email/add" q="action=add&reset=1&cid=`$contactId`&selectedChild=activity&atype=3&context=sendReply&activityId=`$activityId`"}{/capture}
    {capture assign=link}class="action-item crm-popup" style="text-decoration:none;color:#FFF" href="{$replyUrl}"{/capture}
    {ts 1=$link}<a %1><span><i class="crm-i fa-paper-plane-o"></i> Reply</span></a>{/ts}
</div>
</div>

{literal}
<script type="text/javascript">
    CRM.$(function($) {
        $('.crm-activity-view-block .crm-submit-buttons').append($('.additional-actions'));
        $( document ).ajaxComplete(function( event, xhr, settings ) {
            var url = settings.url;
            if (url.indexOf('contact/view/activity') != -1) {
                $('.ui-dialog-buttonset').append($('.additional-actions'));
                $('.crm-activity-view-block .crm-submit-buttons').hide();
            }
        });
    });
</script>
{/literal}

{literal}
    <style type="text/css">
        .archiveemail a, .replytoemail a {
            color: #fff !important;
            background-color: #0071bd !important;
            border-color: #0062a4 !important;
            float: right !important;
            font-size: 13px !important;
            font-family: 'Lato' !important;
            border-radius: 3px !important;
            padding: 9px 19px !important;
        }

        .archiveemail a:hover, .replytoemail a:hover {
            background: #005c99;
            color: #fff;
            outline: none;
            text-shadow: none;
        }
</style>
{/literal}