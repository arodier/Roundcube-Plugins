/*
 +-----------------------------------------------------------------------+
 | Vacation Module for RoundCube, using Sieve                            |
 |                                                                       |
 | Copyright (C) 2011 André Rodier <andre.rodier@gmail.com>              |
 | Licensed under the GNU GPL                                            |
 +-----------------------------------------------------------------------+
 */

if (window.rcmail)
{
	rcmail.addEventListener('init', function(evt) {
		var tab = $('<span>').attr('id', 'settingstabpluginvacation_sieve').addClass('tablink');
		var button = $('<a>').attr('href',
				rcmail.env.comm_path + '&_action=plugin.vacation_sieve').html(
				rcmail.gettext('vacation', 'vacation')).appendTo(tab);
		button.bind('click', function(e) {
			return rcmail.command('plugin.vacation_sieve', this);
		});
		rcmail.add_element(tab, 'tabs');
		rcmail.register_command('plugin.vacation_sieve', function() {
			rcmail.goto_url('plugin.vacation_sieve')
		}, true);
		rcmail.register_command('plugin.vacation_sieve-save', function() {
			rcmail.gui_objects.vacationsieveform.submit();
		}, true);
	})

    // Adjust the eight of the identities list
    $(function () {
        $("select#identities").css("height", 1.25 * parseInt($("#identities option").length) + "em");
    });

    // Datepicker for the vacation dates
    $(function() {
        var dates = $('#vacation_start, #vacation_end').datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 1,
            onSelect: function( selectedDate ) {
                var option = this.id == "vacation_start" ? "minDate" : "maxDate",
                    instance = $( this ).data( "datepicker" ),
                    date = $.datepicker.parseDate(
                        instance.settings.dateFormat ||
                        $.datepicker._defaults.dateFormat,
                        selectedDate, instance.settings );
                dates.not( this ).datepicker( "option", option, date );
            }
        });
    });
}
