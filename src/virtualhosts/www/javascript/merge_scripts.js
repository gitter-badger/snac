/**
 * SNAC Merge Scripts
 *
 * Scripts used in merging and diffing identity constellations in the UI
 *
 * @author Robbie Hott
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @copyright 2015 the Rector and Visitors of the University of Virginia, and
 *            the Regents of the University of California
 */

jQuery.fn.exists = function(){return this.length>0;}

var pieceCache = new Array();

// Scripts to run when the page finishes loading...
$(document).ready(function() {
    console.log(pieceCache);

    $("#preview_button").click(function() {
        var form = $("#merged_identity");
        // empty out the form
        form.html("");

        // for each "both" pane, copy it into the form and then submit the form!
        $(".content-both").each(function() {
            var copy = $(this).html();
            form.append(copy);
        });

        form.attr('action', '?command=preview').attr('method', 'post').attr('target', '_blank');
        form.submit();

        return false;
    });

    $("#cancel_button").click(function() {
        bootbox.confirm({
            title: "Cancel",
            message: "Any changes you've made will be lost.  Are you sure you want to cancel?",
            buttons: {
                cancel: {
                    label: '<i class="fa fa-times"></i> Continue Working'
                },
                confirm: {
                    label: '<i class="fa fa-check"></i> Cancel Merge'
                }
            },
            callback: function (result) {
                if (result) {
                    var form = $("#merged_identity");
                    // empty out the form
                    form.html("");

                    // for each "both" pane, copy it into the form and then submit the form!
                    $("#constellation_data").each(function() {
                        var copy = $(this).html();
                        form.append(copy);
                    });

                    $.post("?command=merge_cancel", $("#merged_identity").serialize(), function (data) {
                        // Check the return value from the ajax. If success, then alert the
                        // user and make appropriate updates.
                        if (data.result == "success") {
                            parent.history.back();
                        } else {
                            // Display an error
                        }
                    });
                }
            }
        });

        return false;
    });

    $("#hrt_preview_button").click(function() {
        bootbox.confirm({
            title: "HRT Preview Notice",
            message: "The HRT does not evidence all portions of the Identity Constellation.  Some data, such as places, sources, SCMs, and more, are not currently visible on the HRT.  This preview should only be used for aesthetic purposes.  Use the main \"Preview\" button to see the full potential-merged Constellation.",
            buttons: {
                cancel: {
                    label: '<i class="fa fa-times"></i> Cancel'
                },
                confirm: {
                    label: '<i class="fa fa-check"></i> Preview Anyway'
                }
            },
            callback: function (result) {
                if (result) {
                    var form = $("#merged_identity");
                    // empty out the form
                    form.html("");

                    // for each "both" pane, copy it into the form and then submit the form!
                    $(".content-both").each(function() {
                        var copy = $(this).html();
                        form.append(copy);
                    });

                    form.attr('action', '?command=preview&view=hrt').attr('method', 'post').attr('target', '_blank');
                    form.submit();
                }
            }
        });

        return false;
    });


    $("#merge_button").click(function() {
        bootbox.confirm({
            title: "Merge Constellation",
            message: "A new \"merged\" Constellation will be created from the elements in the \"Merge Area\" sections.  Any elements not moved to those sections will not be considered as part of the \"merged\" Constellation and will be tombstoned with the original Constellations.  This operation can not be undone.  Are you sure you want to continue?",
            buttons: {
                cancel: {
                    label: '<i class="fa fa-times"></i> Cancel'
                },
                confirm: {
                    label: '<i class="fa fa-check"></i> Confirm'
                }
            },
            callback: function (result) {
                if (result) {
                    $("#please_wait_modal").modal("show");

                    var form = $("#merged_identity");
                    // empty out the form
                    form.html("");

                    // for each "both" pane, copy it into the form and then submit the form!
                    $(".content-both").each(function() {
                        var copy = $(this).html();
                        form.append(copy);
                    });

                    // Put the other constellation data into the form
                    var copy = $("#constellation_data").html();
                    form.append(copy);

                    form.attr('action', '?command=merge').attr('method', 'post').attr('target', '_self');
                    form.submit();
                }
            }
        });

        return false;
    });


    $("#automated_merge_button").click(function() {

        bootbox.confirm({
            title: "Automatic Merge",
            message: "This action automatically combines all data elements from both Constellations to create a merged version.  This operation cannot be undone.  Are you sure you want to continue?",
            buttons: {
                cancel: {
                    label: '<i class="fa fa-times"></i> Cancel'
                },
                confirm: {
                    label: '<i class="fa fa-check"></i> Confirm'
                }
            },
            callback: function (result) {
                if (result) {
                    $("#please_wait_modal").modal("show");

                    var form = $("#merged_identity");
                    // empty out the form
                    form.html("");

                    // for all data panes (both, a, and b), copy all data into the form and then submit the form!
                    $(".content-both").each(function() {
                        var copy = $(this).html();
                        form.append(copy);
                    });
                    $(".content-a").each(function() {
                        var copy = $(this).html();
                        form.append(copy);
                    });
                    $(".content-b").each(function() {
                        var copy = $(this).html();
                        form.append(copy);
                    });

                    // Put the other constellation data into the form
                    var copy = $("#constellation_data").html();
                    form.append(copy);

                    form.attr('action', '?command=merge').attr('method', 'post').attr('target', '_self');
                    form.submit();
                }
            }
        });



        return false;
    });

    pieceCache.forEach(function(piece, i) {
        $("#icon_" + i).popover({
                title: piece.title,
                content : $(piece.identifier).html(),
                html: true,
                trigger: 'hover',
                container: 'body',
                placement: 'bottom'
        });

        $("#icon_" + i).on("click", function() {
            $(".data-component").each(function() {
                $(this).removeClass("data-component-selected").removeClass("disabled");
                $(this).popover('enable');
            });
            $(".preview").each(function() {
                $(this).html("");
            })
            $(".move-button-div").each(function() {
                $(this).addClass("move-button-div-disabled");
                $(this).find(".move-button").off("click");
                $(this).find(".split-button").off("click");
            })
            var obj = $(this);
            $(this).popover('disable');
            obj.addClass("data-component-selected").addClass("disabled");

            obj.closest(".diff-content-panel").find(".preview").html($("#data_" + i).html());
            obj.closest(".diff-content-panel").find(".move-button-div").removeClass("move-button-div-disabled");
            obj.closest(".diff-content-panel").find(".move-button").on("click", function() {
                var both = $("#icon_" + i).closest(".tab-pane").find(".merge-panel").find(".data-components");
                var copy = $("#icon_" + i).detach();
                copy.appendTo(both);


                $(".data-component").each(function() {
                    $(this).removeClass("data-component-selected").removeClass("disabled");
                    $(this).popover('enable');
                });
                $(".preview").each(function() {
                    $(this).html("");
                })
                $(".move-button-div").each(function() {
                    $(this).addClass("move-button-div-disabled");
                })

                $(this).closest(".diff-content-panel").find(".move-button").off("click");

            });

            obj.closest(".diff-content-panel").find(".split-button").on("click", function() {
                var innerObj = $(this);
                var move = null;
                if (innerObj.hasClass("split-button-left"))
                    move = $("#icon_" + i).closest(".tab-pane").find(".content-a").find(".data-components");
                else
                    move = $("#icon_" + i).closest(".tab-pane").find(".content-b").find(".data-components");

                if (move != null) {
                    var copy = $("#icon_" + i).detach();
                    copy.appendTo(move);

                    $(".data-component").each(function() {
                        $(this).removeClass("data-component-selected").removeClass("disabled");
                        $(this).popover('enable');
                    });
                    $(".preview").each(function() {
                        $(this).html("");
                    })
                    $(".move-button-div").each(function() {
                        $(this).addClass("move-button-div-disabled");
                    });

                    $(this).closest(".diff-content-panel").find(".split-button").off("click");

                }

            });
        });
    });

    $(".move-all-button").each(function() {
        var button = $(this);
        button.on("click", function() {
            var button = $(this);
            button.closest(".diff-content-panel").find(".data-component").each(function() {
                var both = $(this).closest(".tab-pane").find(".merge-panel").find(".data-components");
                var copy = $(this).detach();
                copy.appendTo(both);
            });

            $(".data-component").each(function() {
                $(this).removeClass("data-component-selected").removeClass("disabled");
                $(this).popover('enable');
            });

            $(".preview").each(function() {
                $(this).html("");
            })
            $(".move-button-div").each(function() {
                $(this).addClass("move-button-div-disabled");
            })

            $(this).closest(".diff-content-panel").find(".move-button").off("click");
        })
    });
});
