<?php

add_action('acf/include_fields', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_66156456639fc',
        'title' => 'Sowing Calendar',
        'fields' => array(
            array(
                'key' => 'field_661e527bd3fc9',
                'label' => '',
                'name' => 'enable_sowing_calendar',
                'aria-label' => '',
                'type' => 'true_false',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'message' => 'enable',
                'default_value' => 0,
                'ui' => 0,
                'ui_on_text' => '',
                'ui_off_text' => '',
            ),
            array(
                'key' => 'field_661e4f69161eb',
                'label' => 'Sow',
                'name' => '',
                'aria-label' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_661e4d448dcfd',
                'label' => 'Sow in months',
                'name' => 'sow_months',
                'aria-label' => '',
                'type' => 'group',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_661e4da48dcfe',
                        'label' => 'Start (1-12)',
                        'name' => 'start_month',
                        'aria-label' => '',
                        'type' => 'number',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '50',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'min' => '',
                        'max' => '',
                        'placeholder' => '',
                        'step' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                    array(
                        'key' => 'field_661e4dd38dcff',
                        'label' => 'End (1-12)',
                        'name' => 'end_month',
                        'aria-label' => '',
                        'type' => 'number',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '50',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'min' => '',
                        'max' => '',
                        'placeholder' => '',
                        'step' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                ),
            ),
            array(
                'key' => 'field_661e4ffa42368',
                'label' => 'Plant',
                'name' => '',
                'aria-label' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_661e503842369',
                'label' => 'Plant in months',
                'name' => 'plant_months',
                'aria-label' => '',
                'type' => 'group',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_661e50384236a',
                        'label' => 'Start (1-12)',
                        'name' => 'start_month',
                        'aria-label' => '',
                        'type' => 'number',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '50',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'min' => '',
                        'max' => '',
                        'placeholder' => '',
                        'step' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                    array(
                        'key' => 'field_661e50384236b',
                        'label' => 'End (1-12)',
                        'name' => 'end_month',
                        'aria-label' => '',
                        'type' => 'number',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '50',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'min' => '',
                        'max' => '',
                        'placeholder' => '',
                        'step' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                ),
            ),
            array(
                'key' => 'field_661e50c5576b8',
                'label' => 'Harvest',
                'name' => '',
                'aria-label' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_661e50f6576b9',
                'label' => 'Harvest in months',
                'name' => 'harvest_months',
                'aria-label' => '',
                'type' => 'group',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_661e50f6576ba',
                        'label' => 'Start (1-12)',
                        'name' => 'start_month',
                        'aria-label' => '',
                        'type' => 'number',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '50',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'min' => '',
                        'max' => '',
                        'placeholder' => '',
                        'step' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                    array(
                        'key' => 'field_661e50f6576bb',
                        'label' => 'End (1-12)',
                        'name' => 'end_month',
                        'aria-label' => '',
                        'type' => 'number',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '50',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'min' => '',
                        'max' => '',
                        'placeholder' => '',
                        'step' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                ),
            ),
            array(
                'key' => 'field_661e511c576bc',
                'label' => 'Other',
                'name' => '',
                'aria-label' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'placement' => 'top',
                'endpoint' => 0,
            ),
            // array(
            //     'key' => 'field_66156457449b3',
            //     'label' => 'Sowing start date',
            //     'name' => 'sowing_start_date',
            //     'aria-label' => '',
            //     'type' => 'date_picker',
            //     'instructions' => '',
            //     'required' => 0,
            //     'conditional_logic' => 0,
            //     'wrapper' => array(
            //         'width' => '',
            //         'class' => '',
            //         'id' => '',
            //     ),
            //     'display_format' => 'd/m/Y',
            //     'return_format' => 'd/m/Y',
            //     'first_day' => 1,
            // ),
            // array(
            //     'key' => 'field_661564c4449b4',
            //     'label' => 'Sowing end date',
            //     'name' => 'sowing_end_date',
            //     'aria-label' => '',
            //     'type' => 'date_picker',
            //     'instructions' => '',
            //     'required' => 0,
            //     'conditional_logic' => 0,
            //     'wrapper' => array(
            //         'width' => '',
            //         'class' => '',
            //         'id' => '',
            //     ),
            //     'display_format' => 'd/m/Y',
            //     'return_format' => 'd/m/Y',
            //     'first_day' => 1,
            // ),
            // array(
            //     'key' => 'field_6616b219f9eb6',
            //     'label' => 'Plant start date',
            //     'name' => 'plant_start_date',
            //     'aria-label' => '',
            //     'type' => 'date_picker',
            //     'instructions' => '',
            //     'required' => 0,
            //     'conditional_logic' => 0,
            //     'wrapper' => array(
            //         'width' => '',
            //         'class' => '',
            //         'id' => '',
            //     ),
            //     'display_format' => 'd/m/Y',
            //     'return_format' => 'd/m/Y',
            //     'first_day' => 1,
            // ),
            // array(
            //     'key' => 'field_6616b238f9eb7',
            //     'label' => 'Plant end date',
            //     'name' => 'plant_end_date',
            //     'aria-label' => '',
            //     'type' => 'date_picker',
            //     'instructions' => '',
            //     'required' => 0,
            //     'conditional_logic' => 0,
            //     'wrapper' => array(
            //         'width' => '',
            //         'class' => '',
            //         'id' => '',
            //     ),
            //     'display_format' => 'd/m/Y',
            //     'return_format' => 'd/m/Y',
            //     'first_day' => 1,
            // ),
            // array(
            //     'key' => 'field_6616b25ff9eb8',
            //     'label' => 'Harvest start date',
            //     'name' => 'harvest_start_date',
            //     'aria-label' => '',
            //     'type' => 'date_picker',
            //     'instructions' => '',
            //     'required' => 0,
            //     'conditional_logic' => 0,
            //     'wrapper' => array(
            //         'width' => '',
            //         'class' => '',
            //         'id' => '',
            //     ),
            //     'display_format' => 'd/m/Y',
            //     'return_format' => 'd/m/Y',
            //     'first_day' => 1,
            // ),
            // array(
            //     'key' => 'field_6616b285f9eb9',
            //     'label' => 'Harvest end date',
            //     'name' => 'harvest_end_date',
            //     'aria-label' => '',
            //     'type' => 'date_picker',
            //     'instructions' => '',
            //     'required' => 0,
            //     'conditional_logic' => 0,
            //     'wrapper' => array(
            //         'width' => '',
            //         'class' => '',
            //         'id' => '',
            //     ),
            //     'display_format' => 'd/m/Y',
            //     'return_format' => 'd/m/Y',
            //     'first_day' => 1,
            // ),
            // array(
            //     'key' => 'field_661e49e7653a4',
            //     'label' => 'Sowing months',
            //     'name' => 'sowing_months',
            //     'aria-label' => '',
            //     'type' => 'range',
            //     'instructions' => '',
            //     'required' => 0,
            //     'conditional_logic' => 0,
            //     'wrapper' => array(
            //         'width' => '',
            //         'class' => '',
            //         'id' => '',
            //     ),
            //     'default_value' => '',
            //     'min' => 0,
            //     'max' => 12,
            //     'step' => '',
            //     'prepend' => '',
            //     'append' => '',
            // ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'product',
                ),
            ),
            // array(
            //     array(
            //         'param' => 'post_type',
            //         'operator' => '==',
            //         'value' => 'vegetable',
            //     ),
            // ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_rest' => 1,
    ));
});