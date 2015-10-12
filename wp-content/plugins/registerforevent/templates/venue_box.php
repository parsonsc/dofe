<p>
    <label for="venue">
        <strong><?php _e( 'Venue Name', 'registerforevent' ); ?></strong>
    </label>
    <input type="text" id="venue" name="event[venue]" class="widefat" value="<?php echo $venue; ?>" />
</p>
<p>
    <label for="address">
        <strong><?php _e( 'Venue address', 'registerforevent' ); ?></strong>
    </label>
    <input type="text" id="address" name="event[adress]" class="widefat" autocomplete="false" value="<?php 
        echo $address; 
    ?>" />
</p>
<fieldset class="location">    
    <input type="hidden" name="event[address]" id="adress"  value="<?php 
        echo $address; 
    ?>" />
    <input type="hidden" name="event[city]" data-geo="locality"  value="<?php 
        echo $city; 
    ?>" />
    <input type="hidden" name="event[region]" data-geo="administrative_area_level_1"  value="<?php 
        echo $region; 
    ?>" />
    <input type="hidden" name="event[postal_code]" data-geo="postal_code"  value="<?php 
        echo $postal_code; 
    ?>" />
    <input type="hidden" name="event[country_code]" data-geo="country_short"  value="<?php 
        echo $country_code; 
    ?>" />
    <input type="hidden" name="event[country_name]" data-geo="country"  value="<?php 
        echo $country_name; 
    ?>" />
    <input type="hidden" name="event[latitude]" data-geo="lat"  value="<?php 
        echo $latitude; 
    ?>" />
    <input type="hidden" name="event[longitude]" data-geo="lng"  value="<?php 
        echo $longitude; 
    ?>" />
</fieldset>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=places"></script> 
<script type="text/javascript" src="<?php echo WP_PLUGIN_URL . '/' . basename( 'registerforevent' ).'/js/geocode.js'; ?>"></script> 
<script type="text/javascript">
jQuery.noConflict();

(function($){
    $(window).load(function(){
        $(document).ready(function(){ 
            $("#address").geocomplete({
                details: "fieldset.location",
                detailsAttribute: "data-geo",
                types: ["geocode", "establishment"]
            }).bind("geocode:result", function(event, result){
                $('#adress').val(result.address_components[0].long_name+ ' ' +result.address_components[1].long_name);
            });
        });
    });
})(jQuery);  
</script>          