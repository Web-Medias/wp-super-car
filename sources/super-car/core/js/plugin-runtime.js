<<<<<<< HEAD


/* French initialisation for the jQuery UI date picker plugin. */
/* Written by Keith Wood (kbwood{at}iinet.com.au),
			  StÃ©phane Nahmani (sholby@sholby.net),
              StÃ©phane Raimbault <stephane.raimbault@gmail.com>
              StÃ©bastien BrÃ©mond <createck@gmail.com> */

/*
    (function( factory ) {
    	if ( typeof define === "function" && define.amd ) {

    		// AMD. Register as an anonymous module.
    		define([ "../jquery.ui.datepicker" ], factory );
    	} else {

    		// Browser globals
    		factory( jQuery.datepicker );
    	}
    }(function( datepicker ) {
    	datepicker.regional['fr'] = { closeText: 'Fermer',
    		prevText: 'Précédant',
    		nextText: 'Suivant',
    		currentText: 'Aujourd\'hui',
    		monthNames: ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'aoüt', 'septembre', 'octobre', 'novembre', 'décembre'],
    		monthNamesShort: ['janv.', 'févr.', 'mars', 'avril', 'mai', 'juin', 'juil.', 'aoüt', 'sept.', 'oct.', 'nov.', 'déc.'],
    		dayNames: ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'],
    		dayNamesShort: ['dim.', 'lun.', 'mar.', 'mer.', 'jeu.', 'ven.', 'sam.'],
    		dayNamesMin: ['D','L','M','M','J','V','S'],
    		weekHeader: 'Sem.',
    		dateFormat: 'dd/mm/yy',
    		firstDay: 1,
    		isRTL: false,
    		showMonthAfterYear: false,
    		yearSuffix: ''};
    	datepicker.setDefaults( datepicker.regional['fr'] );

    	return datepicker.regional['fr'];

    }));
/**/

/* Add datepicker widget behavior for the two date fields.
 * - Use the French [fr] regional dataset to localise...
 */

/*
    jQuery(function($) {

        var datepicker_args = $.extend( true, $.datepicker.regional['fr'], {
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            dateFormat: 'mm/yy',
            onClose: function(dateText, inst) { 
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(year, month, 1));
            }
        } );

        $( "#dtd_apps__validity" ).datepicker( datepicker_args );

    });
/**/



/* Add the image gallery (WP_Media frame) to allows thumbnail setting.
 * - Pick the thumbnail url from the sizes attachment.
 *   See send method (/wp-includes/js/media-editor.js) and media_attachment properties (media_attachment.sizes.thumbnail).
 */

jQuery(document).ready(function($){
 

    // Drives the way how to the rich content editor is displayed.
    if( $( '#post-body-content div.postbox, #postdivrich.postarea, [behaviours="togglable"]' ).length == 3 ){

        var searchClass = 'closed';
        $('#postdivrich.postarea').hide();
        
        var observer = new MutationObserver( 
           function( mutations ) {
              var oldClass;
              mutations.forEach( function( mutation ) {
                 var currentClass = mutation.target.className;
                 if( mutation.attributeName === 'class' ) {
                    if ( currentClass.indexOf( searchClass ) >= 0 ) {
                        console.log( '.' + searchClass + ' was added!' );
                        $('#postdivrich.postarea').hide();
                    }
                    else {
                       console.log( '.' + searchClass + ' was removed!' );
                        $('#postdivrich.postarea').css('display','').css('transform','translateY(0%)' );
                        $('html,body')
                        .animate({ scrollTop: $('html,body').scrollTop() + 0.001 }, 'slow')
                        .animate({ scrollTop: $('html,body').scrollTop() - 0.001 }, 'slow');
                    }
                 }
              });
           });
           
        var $el = $( '#post-body-content div.postbox' );
        observer.observe( $el[0],  {
           attributes: true
        });

    }





    // Behaviours on <select> brand and model.
    
    if( $( 'select[name="_supercar__brand_collection"]' ).find(":selected").val() == "" ){
        
        $('select[name="_supercar__model_collection"] option[data-brand]').hide();
        $('input[name="_supercar__brandmodel_version"]').val('');

    }else{

        _brand = $( 'select[name="_supercar__brand_collection"]' ).find(":selected").data('brand');

        $('select[name="_supercar__model_collection"] option[data-brand]').hide();
        $('select[name="_supercar__model_collection"] option[data-brand="'+_brand+'"]').show();

    }


    s_brand_name = $( 'select[name="_supercar__brand_collection"]' ).find(":selected").val();
    s_model_name = $( 'select[name="_supercar__model_collection"]' ).find(":selected").val();
    if( s_brand_name == "none" || s_model_name == ""){
        $('#selected_brand_model').html( "Identification à préciser" );
        $('input[name="_supercar__brandmodel_version"]').val('');
    }
    else{
        brand_name = $( 'select[name="_supercar__brand_collection"]' ).find(":selected").text();
        model_name = $( 'select[name="_supercar__model_collection"]' ).find(":selected").text();
        bm_version = $('input[name="_supercar__brandmodel_version"]').val();
        $('#selected_brand_model').html( brand_name +' '+ model_name +' '+ bm_version);
    }



    $( 'select[name="_supercar__brand_collection"]' ).off().on("change", function(e){

        // Prevents the default action from occuring.
        e.preventDefault();

        _brand = $( $(this).get(0).selectedOptions ).data('brand');

        $( 'select[name="_supercar__model_collection"]' ).get(0).selectedIndex = 0;

        $('select[name="_supercar__model_collection"] option[data-brand]').hide();
        $('select[name="_supercar__model_collection"] option[data-brand="'+_brand+'"]').show();


        brand_name = $( 'select[name="_supercar__brand_collection"]' ).find(":selected").text();
        $('#selected_brand_model').html( brand_name );


    });

    $( 'select[name="_supercar__model_collection"]' ).off().on("change", function(e){

        // Prevents the default action from occuring.
        e.preventDefault();

        brand_name = $( 'select[name="_supercar__brand_collection"]' ).find(":selected").text();
        model_name = $( 'select[name="_supercar__model_collection"]' ).find(":selected").text();
        bm_version = $('input[name="_supercar__brandmodel_version"]').val();
        $('#selected_brand_model').html( brand_name +' '+ model_name +' '+ bm_version);

    });

    $( 'input[name="_supercar__brandmodel_version"]' ).off().on("change", function(e){

        // Prevents the default action from occuring.
        e.preventDefault();

        brand_name = $( 'select[name="_supercar__brand_collection"]' ).find(":selected").text();
        model_name = $( 'select[name="_supercar__model_collection"]' ).find(":selected").text();
         bm_version = $('input[name="_supercar__brandmodel_version"]').val();
       $('#selected_brand_model').html( brand_name +' '+ model_name +' '+ bm_version);

    });



    $( '#selected_brand_model_btn' ).off().on("click", function(e){

        // Prevents the default action from occuring.
        e.preventDefault();

        s_brand_name = $( 'select[name="_supercar__brand_collection"]' ).find(":selected").val();
        s_model_name = $( 'select[name="_supercar__model_collection"]' ).find(":selected").val();
        if( s_brand_name == "none" || s_model_name == ""){
            alert("Pour appliquer cette sélection comme titre principal, vous devez au préalable préciser l'identification complète du véhicule\n(Marque & Modèle) !");
        }
        else{
            brand_name = $( 'select[name="_supercar__brand_collection"]' ).find(":selected").text();
            model_name = $( 'select[name="_supercar__model_collection"]' ).find(":selected").text();
            bm_version = $.trim( $( 'input[name="_supercar__brandmodel_version"]' ).val() );

            unique_id = $.trim( $( 'input[name="_supercar__unique_id"]' ).val() );
            
            ct_offset = $('html,body').scrollTop();

            $('#title[name="post_title"]').val('').focus().click();
            $('#title[name="post_title"]').val( brand_name +' '+model_name +' '+bm_version ).focus().click();

            slug = String( brand_name +'-'+ model_name +'-'+ bm_version +'-'+ unique_id ).toLowerCase().replace(/(\s|-)+/g, '-');
            $('#sample-permalink a #editable-post-name').html( slug );
            $('#editable-post-name-full').html( slug );
            $('#sample-permalink a').attr( 'href', $('#sample-permalink a').text() );
            $('#post_name[name="post_name"]').val( slug );

            $('html,body').animate({ scrollTop: ct_offset }, 'slow');
            
        }
    });






    $( 'select[name="_supercar__report"]' ).off().on("change", function(e){

        // Prevents the default action from occuring.
        e.preventDefault();

        _report_idx = $(this).get(0).selectedIndex;
        _report     = $( $(this).get(0).selectedOptions );


        if( _report_idx == 0 || _report.val() == 'none' ){

            $( '[name="_supercar__report_url"]' ).val( '' );

        }else{

            $( '[name="_supercar__report_url"]' ).val( _report.val() );

        }

    });





    /* Plays with :
     *
     * - button#upload_select_pdf_btn      => Button that opens the Media Frame Library
     * - [name="_supercar__report_url"]    => Input text box receiving the uploaded/selected file from the Media Frame Library
     * - select[name="_supercar__report"]  => Dropdown <select> who containing all up to date PDFs in the MFL.
     *
     */

    // Instantiates the variable that holds the media library frame.
    var meta_medias_frame;


    // Runs when the image button is clicked (minit thumb).
    $( "button#upload_select_pdf_btn" ).each(function(index) {
        $(this).on("click", function(e){
            
            e.preventDefault();

            if ( meta_medias_frame ) { meta_medias_frame.close(); }

            meta_medias_frame = wp.media.frames.meta_medias_frame = wp.media({
                title: $(this).val(),
                button: { text:  'Associer ce document comme Rapport d\'Expertise' },
                multiple: false,
                library: { type: 'application/pdf' }
            });

            meta_medias_frame.on('select', function(){
     
                var media_attachment = meta_medias_frame.state().get('selection').first().toJSON();

                // Ensure the WP Media Library thats delivering this media allows to purpose the correct image size format here !
                $( '[name="_supercar__report_url"]' ).val( media_attachment.url );


                // Clean (remove) the contained <option> collection, and add dynamically a volatile fixed item option.
                $( 'select[name="_supercar__report"] option' ).remove();

                $( 'select[name="_supercar__report"]' ).append($('<option>', {
                    value:media_attachment.url, 
                    text:'Fraîchement téléversé : '+ media_attachment.name 
                }));


            });

            meta_medias_frame.open();

        });
    });





    /* Plays with :
     *
     * - button#make_select_img_btn      => Button that opens the Media Frame Library
     * - [name="_supercar__gallery_ids"]    => Input text box receiving the uploaded/selected image medias IDs from the Media Frame Library
     *
     */

    // Instantiates the variable that holds the media library frame.
    var meta_medias_frame;


    // Runs when the image button is clicked (minit thumb).
    $( "button#make_select_img_btn" ).each(function(index) {
        $(this).on("click", function(e){
            
            e.preventDefault();

            if ( meta_medias_frame ) { meta_medias_frame.close(); }

            // Just prepares the Media Frame
            meta_medias_frame = wp.media.frames.meta_medias_frame = wp.media({
                title: $(this).val(),
                button: { text:  'Sélectionner ces visuels' },
                multiple: true,
                filterable: 'all',
                library: { type: 'image' }
            });

            // Allows to fetch already stored image IDs, splits all and select them into the library panel
            meta_medias_frame.on('open', function() {
                    var selection = meta_medias_frame.state().get('selection');
                    var library = meta_medias_frame.state('gallery-edit').get('library');
                    var ids = $.trim( $( '[name="_supercar__gallery_ids"]' ).val() );
                    if (ids) {
                            idsArray = ids.split(',');
                            idsArray.forEach(function(id) {
                                    attachment = wp.media.attachment(id);
                                    attachment.fetch();
                                    selection.add( attachment ? [ attachment ] : [] );
                            });
                 }
            });


            /*
            meta_medias_frame.on('ready', function() {
                    jQuery( '.media-modal' ).addClass( 'no-sidebar' );
            });
            /**/


            meta_medias_frame.on('select', function(){
     
                var multiple_media_attachments = meta_medias_frame.state().get('selection').map( 

                    function( attachment ) {

                        attachment.toJSON();
                        return attachment;

                });


                new_selection = [];

                $('[role="gallery_wrapper"]').html('');
                for (var i = 0; i < multiple_media_attachments.length; ++i) {

                    new_selection.push( multiple_media_attachments[i].id );

                    console.log( new_selection , multiple_media_attachments[i] );

                    // Ensure the WP Media Library thats delivering this media allows to purpose the correct image size format here !
                    $('[role="gallery_wrapper"]').append( $( '<img rel="'+ multiple_media_attachments[i].id +'" style="border: solid 3px #0073aa; border-radius: 2px; margin: 0.5em;" src="' + multiple_media_attachments[i].attributes.sizes.thumbnail.url + '" >' ) );

                }


                // Applies the selection to the hidden storage field.
                $( '[name="_supercar__gallery_ids"]' ).val( new_selection.join(',') );

            });

            meta_medias_frame.open();

        });
    });


    // And finally, adds dynamically all thumbnails of the gallery to the gallery wrapper ;)

    // Applies the selection to the hidden storage field.
    var ids = $.trim( $( '[name="_supercar__gallery_ids"]' ).val() );
    if (ids) {
        idsArray = ids.split(',');

        $('[role="gallery_wrapper"]').html('');
        for (var i = 0; i < idsArray.length; ++i) {

            wp.media.attachment( idsArray[i] ).fetch().done( function(attachment){ 
                console.log(attachment);


                // Ensure the WP Media Library thats delivering this media allows to purpose the correct image size format here !
                $('[role="gallery_wrapper"]').append( $( '<img rel="'+ idsArray[i] +'" style="border: solid 3px #0073aa; border-radius: 2px; margin: 0.5em;" src="' + attachment.sizes.thumbnail.url + '" >' ) );

            } );

        }
    };



=======


/* French initialisation for the jQuery UI date picker plugin. */
/* Written by Keith Wood (kbwood{at}iinet.com.au),
			  StÃ©phane Nahmani (sholby@sholby.net),
              StÃ©phane Raimbault <stephane.raimbault@gmail.com>
              StÃ©bastien BrÃ©mond <createck@gmail.com> */

/*
    (function( factory ) {
    	if ( typeof define === "function" && define.amd ) {

    		// AMD. Register as an anonymous module.
    		define([ "../jquery.ui.datepicker" ], factory );
    	} else {

    		// Browser globals
    		factory( jQuery.datepicker );
    	}
    }(function( datepicker ) {
    	datepicker.regional['fr'] = { closeText: 'Fermer',
    		prevText: 'Précédant',
    		nextText: 'Suivant',
    		currentText: 'Aujourd\'hui',
    		monthNames: ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'aoüt', 'septembre', 'octobre', 'novembre', 'décembre'],
    		monthNamesShort: ['janv.', 'févr.', 'mars', 'avril', 'mai', 'juin', 'juil.', 'aoüt', 'sept.', 'oct.', 'nov.', 'déc.'],
    		dayNames: ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'],
    		dayNamesShort: ['dim.', 'lun.', 'mar.', 'mer.', 'jeu.', 'ven.', 'sam.'],
    		dayNamesMin: ['D','L','M','M','J','V','S'],
    		weekHeader: 'Sem.',
    		dateFormat: 'dd/mm/yy',
    		firstDay: 1,
    		isRTL: false,
    		showMonthAfterYear: false,
    		yearSuffix: ''};
    	datepicker.setDefaults( datepicker.regional['fr'] );

    	return datepicker.regional['fr'];

    }));
/**/

/* Add datepicker widget behavior for the two date fields.
 * - Use the French [fr] regional dataset to localise...
 */

/*
    jQuery(function($) {

        var datepicker_args = $.extend( true, $.datepicker.regional['fr'], {
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            dateFormat: 'mm/yy',
            onClose: function(dateText, inst) { 
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(year, month, 1));
            }
        } );

        $( "#dtd_apps__validity" ).datepicker( datepicker_args );

    });
/**/



/* Add the image gallery (WP_Media frame) to allows thumbnail setting.
 * - Pick the thumbnail url from the sizes attachment.
 *   See send method (/wp-includes/js/media-editor.js) and media_attachment properties (media_attachment.sizes.thumbnail).
 */

jQuery(document).ready(function($){
 

    // Drives the way how to the rich content editor is displayed.
    if( $( '#post-body-content div.postbox, #postdivrich.postarea, [behaviours="togglable"]' ).length == 3 ){

        var searchClass = 'closed';
        $('#postdivrich.postarea').hide();
        
        var observer = new MutationObserver( 
           function( mutations ) {
              var oldClass;
              mutations.forEach( function( mutation ) {
                 var currentClass = mutation.target.className;
                 if( mutation.attributeName === 'class' ) {
                    if ( currentClass.indexOf( searchClass ) >= 0 ) {
                        console.log( '.' + searchClass + ' was added!' );
                        $('#postdivrich.postarea').hide();
                    }
                    else {
                       console.log( '.' + searchClass + ' was removed!' );
                        $('#postdivrich.postarea').css('display','').css('transform','translateY(0%)' );
                        $('html,body')
                        .animate({ scrollTop: $('html,body').scrollTop() + 0.001 }, 'slow')
                        .animate({ scrollTop: $('html,body').scrollTop() - 0.001 }, 'slow');
                    }
                 }
              });
           });
           
        var $el = $( '#post-body-content div.postbox' );
        observer.observe( $el[0],  {
           attributes: true
        });

    }





    // Behaviours on <select> brand and model.
    
    if( $( 'select[name="_supercar__brand_collection"]' ).find(":selected").val() == "" ){
        
        $('select[name="_supercar__model_collection"] option[data-brand]').hide();
        $('input[name="_supercar__brandmodel_version"]').val('');

    }else{

        _brand = $( 'select[name="_supercar__brand_collection"]' ).find(":selected").data('brand');

        $('select[name="_supercar__model_collection"] option[data-brand]').hide();
        $('select[name="_supercar__model_collection"] option[data-brand="'+_brand+'"]').show();

    }


    s_brand_name = $( 'select[name="_supercar__brand_collection"]' ).find(":selected").val();
    s_model_name = $( 'select[name="_supercar__model_collection"]' ).find(":selected").val();
    if( s_brand_name == "none" || s_model_name == ""){
        $('#selected_brand_model').html( "Identification à préciser" );
        $('input[name="_supercar__brandmodel_version"]').val('');
    }
    else{
        brand_name = $( 'select[name="_supercar__brand_collection"]' ).find(":selected").text();
        model_name = $( 'select[name="_supercar__model_collection"]' ).find(":selected").text();
        bm_version = $('input[name="_supercar__brandmodel_version"]').val();
        $('#selected_brand_model').html( brand_name +' '+ model_name +' '+ bm_version);
    }



    $( 'select[name="_supercar__brand_collection"]' ).off().on("change", function(e){

        // Prevents the default action from occuring.
        e.preventDefault();

        _brand = $( $(this).get(0).selectedOptions ).data('brand');

        $( 'select[name="_supercar__model_collection"]' ).get(0).selectedIndex = 0;

        $('select[name="_supercar__model_collection"] option[data-brand]').hide();
        $('select[name="_supercar__model_collection"] option[data-brand="'+_brand+'"]').show();


        brand_name = $( 'select[name="_supercar__brand_collection"]' ).find(":selected").text();
        $('#selected_brand_model').html( brand_name );


    });

    $( 'select[name="_supercar__model_collection"]' ).off().on("change", function(e){

        // Prevents the default action from occuring.
        e.preventDefault();

        brand_name = $( 'select[name="_supercar__brand_collection"]' ).find(":selected").text();
        model_name = $( 'select[name="_supercar__model_collection"]' ).find(":selected").text();
        bm_version = $('input[name="_supercar__brandmodel_version"]').val();
        $('#selected_brand_model').html( brand_name +' '+ model_name +' '+ bm_version);

    });

    $( 'input[name="_supercar__brandmodel_version"]' ).off().on("change", function(e){

        // Prevents the default action from occuring.
        e.preventDefault();

        brand_name = $( 'select[name="_supercar__brand_collection"]' ).find(":selected").text();
        model_name = $( 'select[name="_supercar__model_collection"]' ).find(":selected").text();
         bm_version = $('input[name="_supercar__brandmodel_version"]').val();
       $('#selected_brand_model').html( brand_name +' '+ model_name +' '+ bm_version);

    });



    $( '#selected_brand_model_btn' ).off().on("click", function(e){

        // Prevents the default action from occuring.
        e.preventDefault();

        s_brand_name = $( 'select[name="_supercar__brand_collection"]' ).find(":selected").val();
        s_model_name = $( 'select[name="_supercar__model_collection"]' ).find(":selected").val();
        if( s_brand_name == "none" || s_model_name == ""){
            alert("Pour appliquer cette sélection comme titre principal, vous devez au préalable préciser l'identification complète du véhicule\n(Marque & Modèle) !");
        }
        else{
            brand_name = $( 'select[name="_supercar__brand_collection"]' ).find(":selected").text();
            model_name = $( 'select[name="_supercar__model_collection"]' ).find(":selected").text();
            bm_version = $.trim( $( 'input[name="_supercar__brandmodel_version"]' ).val() );

            unique_id = $.trim( $( 'input[name="_supercar__unique_id"]' ).val() );
            
            ct_offset = $('html,body').scrollTop();

            $('#title[name="post_title"]').val('').focus().click();
            $('#title[name="post_title"]').val( brand_name +' '+model_name +' '+bm_version ).focus().click();

            slug = String( brand_name +'-'+ model_name +'-'+ bm_version +'-'+ unique_id ).toLowerCase().replace(/(\s|-)+/g, '-');
            $('#sample-permalink a #editable-post-name').html( slug );
            $('#editable-post-name-full').html( slug );
            $('#sample-permalink a').attr( 'href', $('#sample-permalink a').text() );
            $('#post_name[name="post_name"]').val( slug );

            $('html,body').animate({ scrollTop: ct_offset }, 'slow');
            
        }
    });






    $( 'select[name="_supercar__report"]' ).off().on("change", function(e){

        // Prevents the default action from occuring.
        e.preventDefault();

        _report_idx = $(this).get(0).selectedIndex;
        _report     = $( $(this).get(0).selectedOptions );


        if( _report_idx == 0 || _report.val() == 'none' ){

            $( '[name="_supercar__report_url"]' ).val( '' );

        }else{

            $( '[name="_supercar__report_url"]' ).val( _report.val() );

        }

    });





    /* Plays with :
     *
     * - button#upload_select_pdf_btn      => Button that opens the Media Frame Library
     * - [name="_supercar__report_url"]    => Input text box receiving the uploaded/selected file from the Media Frame Library
     * - select[name="_supercar__report"]  => Dropdown <select> who containing all up to date PDFs in the MFL.
     *
     */

    // Instantiates the variable that holds the media library frame.
    var meta_medias_frame;


    // Runs when the image button is clicked (minit thumb).
    $( "button#upload_select_pdf_btn" ).each(function(index) {
        $(this).on("click", function(e){
            
            e.preventDefault();

            if ( meta_medias_frame ) { meta_medias_frame.close(); }

            meta_medias_frame = wp.media.frames.meta_medias_frame = wp.media({
                title: $(this).val(),
                button: { text:  'Associer ce document comme Rapport d\'Expertise' },
                multiple: false,
                library: { type: 'application/pdf' }
            });

            meta_medias_frame.on('select', function(){
     
                var media_attachment = meta_medias_frame.state().get('selection').first().toJSON();

                // Ensure the WP Media Library thats delivering this media allows to purpose the correct image size format here !
                $( '[name="_supercar__report_url"]' ).val( media_attachment.url );


                // Clean (remove) the contained <option> collection, and add dynamically a volatile fixed item option.
                $( 'select[name="_supercar__report"] option' ).remove();

                $( 'select[name="_supercar__report"]' ).append($('<option>', {
                    value:media_attachment.url, 
                    text:'Fraîchement téléversé : '+ media_attachment.name 
                }));


            });

            meta_medias_frame.open();

        });
    });





    /* Plays with :
     *
     * - button#make_select_img_btn      => Button that opens the Media Frame Library
     * - [name="_supercar__gallery_ids"]    => Input text box receiving the uploaded/selected image medias IDs from the Media Frame Library
     *
     */

    // Instantiates the variable that holds the media library frame.
    var meta_medias_frame;


    // Runs when the image button is clicked (minit thumb).
    $( "button#make_select_img_btn" ).each(function(index) {
        $(this).on("click", function(e){
            
            e.preventDefault();

            if ( meta_medias_frame ) { meta_medias_frame.close(); }

            // Just prepares the Media Frame
            meta_medias_frame = wp.media.frames.meta_medias_frame = wp.media({
                title: $(this).val(),
                button: { text:  'Sélectionner ces visuels' },
                multiple: true,
                filterable: 'all',
                library: { type: 'image' }
            });

            // Allows to fetch already stored image IDs, splits all and select them into the library panel
            meta_medias_frame.on('open', function() {
                    var selection = meta_medias_frame.state().get('selection');
                    var library = meta_medias_frame.state('gallery-edit').get('library');
                    var ids = $.trim( $( '[name="_supercar__gallery_ids"]' ).val() );
                    if (ids) {
                            idsArray = ids.split(',');
                            idsArray.forEach(function(id) {
                                    attachment = wp.media.attachment(id);
                                    attachment.fetch();
                                    selection.add( attachment ? [ attachment ] : [] );
                            });
                 }
            });


            /*
            meta_medias_frame.on('ready', function() {
                    jQuery( '.media-modal' ).addClass( 'no-sidebar' );
            });
            /**/


            meta_medias_frame.on('select', function(){
     
                var multiple_media_attachments = meta_medias_frame.state().get('selection').map( 

                    function( attachment ) {

                        attachment.toJSON();
                        return attachment;

                });


                new_selection = [];

                $('[role="gallery_wrapper"]').html('');
                for (var i = 0; i < multiple_media_attachments.length; ++i) {

                    new_selection.push( multiple_media_attachments[i].id );

                    console.log( new_selection , multiple_media_attachments[i] );

                    // Ensure the WP Media Library thats delivering this media allows to purpose the correct image size format here !
                    $('[role="gallery_wrapper"]').append( $( '<img rel="'+ multiple_media_attachments[i].id +'" style="border: solid 3px #0073aa; border-radius: 2px; margin: 0.5em;" src="' + multiple_media_attachments[i].attributes.sizes.thumbnail.url + '" >' ) );

                }


                // Applies the selection to the hidden storage field.
                $( '[name="_supercar__gallery_ids"]' ).val( new_selection.join(',') );

            });

            meta_medias_frame.open();

        });
    });


    // And finally, adds dynamically all thumbnails of the gallery to the gallery wrapper ;)

    // Applies the selection to the hidden storage field.
    var ids = $.trim( $( '[name="_supercar__gallery_ids"]' ).val() );
    if (ids) {
        idsArray = ids.split(',');

        $('[role="gallery_wrapper"]').html('');
        for (var i = 0; i < idsArray.length; ++i) {

            wp.media.attachment( idsArray[i] ).fetch().done( function(attachment){ 
                console.log(attachment);


                // Ensure the WP Media Library thats delivering this media allows to purpose the correct image size format here !
                $('[role="gallery_wrapper"]').append( $( '<img rel="'+ idsArray[i] +'" style="border: solid 3px #0073aa; border-radius: 2px; margin: 0.5em;" src="' + attachment.sizes.thumbnail.url + '" >' ) );

            } );

        }
    };



>>>>>>> 69d800751f0fdd3e6c15326a85c1175d8e48ca65
});