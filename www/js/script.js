var Dase = {};

$(document).ready(function() {
	Dase.initDelete('topMenu');
	Dase.initDelete('budget_lines');
	Dase.initDelete('budget_items');
	Dase.initDelete('attachment_types');
	Dase.initDelete('attachments');
	Dase.initDelete('propusers');
	Dase.initDelete('reviewer_comments');
	Dase.initToggle('proposal');
	Dase.initToggle('attachments');
	Dase.initToggle('section_form');
	Dase.initToggle('email');
	//Dase.initSortable('target');
	Dase.initFormPrompt();
	Dase.initFormDelete();
	Dase.initPutStatus();
	Dase.initPendingUpdate('proposal');
});

Dase.initPendingUpdate = function(id) {
	$('#'+id).find('form.section').change(function() {
		var p_alert = $(this).find('.pending');
		p_alert.show();
		$(this).find('input[type="submit"]').click( function() {
			p_alert.hide();
		});
		$(this).find('a').click( function() {
			p_alert.hide();
			return false;
		});
	});	
};

Dase.initToggle = function(id) {
	$('#'+id).find('a[class="toggle"]').click(function() {
		var id = $(this).attr('id');
		var tar = id.replace('toggle','target');
		$('#'+tar).toggle();
		return false;
	});	
};

Dase.initFormDelete = function() {
	$("form[method='delete']").submit(function() {
		if (confirm('are you sure?')) {
			var del_o = {
				'url': $(this).attr('action'),
				'type':'DELETE',
				'success': function() {
					location.reload();
				},
				'error': function() {
					alert('sorry, cannot delete');
				}
			};
			$.ajax(del_o);
		}
		return false;
	});
};

Dase.initFormPrompt = function() {
	$("form[method='post'][class='prompt']").submit(function() {
		var msg = $(this).find("input[name='message']").attr('value');
		if (confirm(msg)) {
			return true;
		}
		return false;
	});
};

Dase.initDelete = function(id) {
	$('#'+id).find("a[class='delete']").click(function() {
		if (confirm('are you sure?')) {
			var del_o = {
				'url': $(this).attr('href'),
				'type':'DELETE',
				'success': function() {
					location.reload();
				},
				'error': function() {
					alert('sorry, cannot delete');
				}
			};
			$.ajax(del_o);
		}
		return false;
	});
};

Dase.initSortable = function(id) {
	$('#'+id).sortable({ 
		cursor: 'crosshair',
		opacity: 0.6,
		revert: true, 
		start: function(event,ui) {
			ui.item.addClass('highlight');
		},	
		stop: function(event,ui) {
			$('#proceed-button').addClass('hide');
			$('#unsaved-changes').removeClass('hide');
			$('#'+id).find("li").each(function(index){
				$(this).find('span.key').text(index+1);
			});	
			ui.item.removeClass('highlight');
		}	
	});
};
 
Dase.initPutStatus = function() {
	$('#deptsList').find("form[method='put']").each(function() {
		$(this).submit(function() {
			var _o = {
				'url': $(this).attr('action'),
				'type':'PUT',
				'data':$(this).find("input[name='is_active']").attr('value'),
				'dataType':'text/plain',
				'success': function() {
					location.reload();
				},
				'error': function() {
					alert('sorry, cannot set status');
				}
			};
			$.ajax(_o);
			return false;
		});
	});
};

