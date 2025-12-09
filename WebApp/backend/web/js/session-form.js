/**
 * Script to manage the Dependent Dropdown (Event -> Venues)
 */
$(document).ready(function() {
    $('#select-evento-id').change(function(){
        var eventId = $(this).val();

        // Blocks the field while loading
        var venueSelect = $('#select-venue-id');
        venueSelect.html('<option>A carregar salas...</option>').prop('disabled', true);

        // TODO: Se usar PrettyUrls no futuro, verifique este caminho
        $.get('index.php?r=session/list-venues', { id: eventId }, function(data){
            venueSelect.html(data).prop('disabled', false);
        });
    });
});