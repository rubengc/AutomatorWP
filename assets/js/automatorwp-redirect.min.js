var automatorwp_redirect_in_progress=!1;function automatorwp_check_for_redirect(){var $=jQuery;var user_id=parseInt(automatorwp_redirect.user_id);if(user_id===0||isNaN(user_id)){return}
if(automatorwp_redirect_in_progress){return}
automatorwp_redirect_in_progress=!0;$.ajax({url:automatorwp_redirect.ajaxurl,method:'POST',data:{action:'automatorwp_check_for_redirect',nonce:automatorwp_redirect.nonce,user_id:automatorwp_redirect.user_id,},success:function(response){if(!automatorwp_is_response_valid_for_redirect(response)){automatorwp_redirect_in_progress=!1;return}
if(!automatorwp_redirect_to_url(response.data.redirect_url)){automatorwp_redirect_in_progress=!1}},error:function(response){if(!automatorwp_is_response_valid_for_redirect(response)){automatorwp_redirect_in_progress=!1;return}
if(!automatorwp_redirect_to_url(response.data.redirect_url)){automatorwp_redirect_in_progress=!1}}})}
function automatorwp_is_response_valid_for_redirect(response){if(response===undefined){return!1}
if(response.data===undefined){return!1}
if(response.data.redirect_url===undefined){return!1}
return!0}
function automatorwp_redirect_to_url(url){if(url===undefined){url=''}
if(!url.length){return!1}
document.location.href=url;return!0}
function automatorwp_redirect_is_url_excluded(url,data){if(url===undefined){url=''}
if(data===undefined){data=url}
if(typeof data!=='string'){data=url}
var excluded_url=!1;automatorwp_redirect.excluded_urls.forEach(function(to_match){if(url!==undefined&&url.includes(to_match)||url===to_match){excluded_url=!0}});if(excluded_url){return!0}
var excluded_data=!1;automatorwp_redirect.excluded_data.forEach(function(to_match){if(data!==undefined&&data.includes(to_match)){excluded_data=!0}});if(excluded_data){return!0}
if(url!==undefined&&url.includes('admin-ajax.php')){var excluded_action=!1;automatorwp_redirect.excluded_ajax_actions.forEach(function(to_match){if(data!==undefined&&data.includes('action='+to_match)){excluded_action=!0}
if(url!==undefined&&url.includes('action='+to_match)){excluded_action=!0}});if(excluded_action){return!0}}
return!1}(function($){$(document).ajaxSuccess(function(event,request,settings){if(automatorwp_redirect_is_url_excluded(settings.url,settings.data)){return}
var status=parseInt(request.status);if(status===200){automatorwp_check_for_redirect()}})})(jQuery)