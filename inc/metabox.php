<?php 

// Control core classes for avoid errors
if( class_exists( 'CSF' ) ) {

  //
  // Set a unique slug-like ID
  $prefix = 'sde_metabox_options';

  //
  // Create a metabox
  CSF::createMetabox( $prefix, array(
    'title'     => 'Additional Information',
    'post_type' => 'product',
  ) );

  //
  // Create a section
  CSF::createSection( $prefix, array(
    'title'  => '',
    'fields' => array(
      array(
        'id'    => 'ean',
        'type'  => 'text',
        'title' => 'EAN',
      ),
        array(
            'id'    => 'brand',
            'type'  => 'text',
            'title' => 'Brand',
        ),
        array(
            'id'    => 'color',
            'type'  => 'text',
            'title' => 'Color',
        ),
        array(
            'id'    => 'size',
            'type'  => 'text',
            'title' => 'Size',
        ),
        array(
            'id'    => 'matter',
            'type'  => 'text',
            'title' => 'Matter',
        ),
        array(
            'id'    => 'care',
            'type'  => 'text',
            'title' => 'Care',
        ),
        array(
            'id'    => 'customs-nomenclature',
            'type'  => 'text',
            'title' => 'Customs Nomenclature',
        ),
        array(
            'id'    => 'recommended-retail-price',
            'type'  => 'text',
            'title' => 'Recommended Retail Price',
        ),
        array(
            'id'    => 'weight',
            'type'  => 'text',
            'title' => 'Weight',
        ),
        array(
            'id'    => 'volume',
            'type'  => 'text',
            'title' => 'Volume',
        ),
        array(
            'id'    => 'sustainability',
            'type'  => 'text',
            'title' => 'Sustainability',
        ),
        array(
            'id'    => 'pcb',
            'type'  => 'text',
            'title' => 'PCB',
        ),
        array(
            'id'    => 'pcb-dropshipping',
            'type'  => 'text',
            'title' => 'PCB Dropshipping',
        ),
        array(
            'id'    => 'country-code',
            'type'  => 'text',
            'title' => 'Country Code',
        ),
        array(
            'id'    => 'theme',
            'type'  => 'text',
            'title' => 'Theme',
        ),


    )
  ) );


}
