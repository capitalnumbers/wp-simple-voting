/* 
 * Ajax scripts
 */

function getPostId(pid) {
    var pidArr = pid.split("_");
    var postId = pidArr[pidArr.length-1];
    var voteCount = $("#vote_post_"+postId).attr("votecount");
    if ($("#vote-counts-"+postId).length > 0)
        alert($("#vote-count-"+postId).html());
    /* ================= Voing action ================= */
    if (postId) {
        jQuery.ajax({
            type: 'post',
            url: myAjax.ajaxurl,
            dataType: 'json',
            data: {
                action: 'do_post_vote',
                post_id: postId
            },
            beforeSend: function() {
                var loadHtml  = '<span style="float:left; margin:10px 20px 0 0;">';
                    loadHtml += '<img src="'+pluginurl+'/images/loading-round.gif">';
                    loadHtml += '</span>';

                $("#voting_area_outer_"+postId).html(loadHtml);
            },
            success: function(data, textStatus, XMLHttpRequest) {
                var afterLoadText = '';

                if (data == "s") {
                    afterLoadText = 'Voted';
                } else {
                    afterLoadText = '<strong style="color:#000;">Error</strong>';
                }

                //var afterLoadHtml = '<span id="vote_post_'+postId+'" class="voted">'+afterLoadText+'</span>';
                var afterLoadHtml = '<button class="voted">'+afterLoadText+'</button>';
                $("#voting_area_outer_"+postId).html(afterLoadHtml);
                
                /*if (data == "s") {
                    if ($("#vote-count-"+postId).length > 0) {
                        $("#vote-count-"+postId).html(voteCount);
                    }
                }*/
            },
            error: function (MLHttpRequest, textStatus, errorThrown) {
                alert('ERROR - '+errorThrown);
            }
        })
    }

}


jQuery(function() {
    jQuery(".wsv-reset-button-single").live("click", function() {
        if (confirm("Are you sure want to reset votes for this post?")) {
            var postIdArr = jQuery(this).attr("id");
            postIdArr = postIdArr.split("_");
            var postId = postIdArr[postIdArr.length-1];

            if (postId) {
                jQuery.ajax({
                    type: 'post',
                    url: myAjax.ajaxurl,
                    dataType: 'json',
                    data: {
                        action: 'do_reset_vote_single',
                        post_id: postId
                    },
                    beforeSend: function() {
                        var loadHtml  = '<span style="float:left; margin-left:30px;">';
                            //loadHtml += '<img src="'+siteurl+'/wp-admin/images/loading.gif">';
                            loadHtml += '<img src="'+pluginurl+'/images/loading-round.gif">';
                            loadHtml += '</span>';

                        jQuery("#reset-area-outer-"+postId).html(loadHtml);
                    },
                    success: function(data, textStatus, XMLHttpRequest) {
                        var afterLoadText = '';
                        if (data == "s") {
                            afterLoadText = '<span style="color:#D54E21; font-weight:bold;">Voting has been reset</span>';
                            jQuery("#reset-area-outer-"+postId).html(afterLoadText);
                            setTimeout(function() {
                                window.location.href = window.location.href;
                            }, 2000);
                        } else {
                            jQuery("#reset-area-outer-"+postId).html(afterLoadText);
                            afterLoadText = '<strong style="color:#000;">Error</strong>';
                        }
                    },
                    error: function (MLHttpRequest, textStatus, errorThrown) {
                        alert('ERROR - '+errorThrown);
                    }
                })
            }
        }
    })
    
    jQuery(".wsv-reset-button-all").live("click", function() {
        if (confirm("Are you sure want to reset votes for all post?")) {
            jQuery.ajax({
                type: 'post',
                url: myAjax.ajaxurl,
                dataType: 'json',
                data: {
                    action: 'do_reset_vote_all',
                    called_reset: '1'
                },
                beforeSend: function() {
                    var loadHtml  = '<span style="float:left; margin-left:30px;">';
                        //loadHtml += '<img src="'+siteurl+'/wp-admin/images/loading.gif">';
                        loadHtml += '<img src="'+pluginurl+'/images/loading-round.gif">';
                        loadHtml += '</span>';

                    jQuery("#reset-area-outer-all").html(loadHtml);
                },
                success: function(data, textStatus, XMLHttpRequest) {
                    var afterLoadText = '';
                    if (data == "s") {
                        afterLoadText = '<span style="color:#D54E21; font-weight:bold;">All votes has been reset</span>';
                        jQuery("#reset-area-outer-all").html(afterLoadText);
                        setTimeout(function() {
                            window.location.href = window.location.href;
                        }, 2000);
                    } else {
                        jQuery("#reset-area-outer-all").html(afterLoadText);
                        afterLoadText = '<strong style="color:#000;">Error</strong>';
                    }
                },
                error: function (MLHttpRequest, textStatus, errorThrown) {
                    alert('ERROR - '+errorThrown);
                }
            })
        }
    })
    
});

