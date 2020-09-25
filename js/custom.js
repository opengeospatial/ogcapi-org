(function($) {
  "use strict"; // Start of use strict


//    SHOW MORE BUTTON FOR DOCUMENTS
$('div#document_list .list-group-item:gt(3)').hide();

const l = $('#document_list .list-group-item').length;
if (l > 3) {
    $('span').show();
} else {
    $('span').hide();
}

$('.show_button').click(function () {
    $('div#document_list .list-group-item:gt(3)').toggle('slide');
    $(this).text() === 'Show more +' ? $(this).text('Show less -') : $(this).text('Show more +');
});

})(jQuery); // End of use strict
