# Flexible Widget Widths

A WordPress plugin to dynamically add classes based off a grid system to widgets. 

## How to enable

Add the `auto_width` argument when registering your sidebar with the value `true`

```
register_sidebar(array(
	'name' => 'Flex Width Sidebar',
	'before_widget' => '<aside id="%1$s" class="%2$s">',
	'after_widget' => '</aside>',
	'before_title' => '<h3>',
	'after_title' => '</h3>',
	'auto_width' => true,
));
```

## Filters

You can adjust the grid classes for each case by filtering `fww_column_widths`

```
add_filter( 'fww_column_widths', 'filter_columns' );

function filter_columns( $args ) {

	$args[2]['sm'] = 6;
	$args[2]['med'] = 4;
	return $args;

}
```
The plugin uses foundation 5 classes by default, but you can apply a different prefix to each of the grid sizes by using the following filter. 

```
add_filter( 'fww_column_widths', 'filter_prefix' );

function filter_prefix( $args ) {

	$args['prefix']['lg'] = 'col-lg-';
	$args['prefix']['med'] = 'col-med-';
	$args['prefix']['sm'] = 'col-sm-';
	return $args;

}
```
If the calculated width doesn't match a preset class set, it will fallback to the default class set. You can filter those too. 
```
add_filter( 'fww_column_widths', 'filter_defaults' );

function filter_defaults( $args ) {
	
	$args['default']['lg'] = 4;
	$args['default']['med'] = 6;
	$args['default']['sm'] = 12;
	return $args;

}
```
The plugin automatically adds a `columns` class to each of the widgets, this can be filtered for another class name. 
```
add_filter( 'fww_column_name', 'filter_column_name' );

function filter_column_name( $args ) {
	
	$args[] = 'cols';
	return $args;

}
```
By default the plugin runs off the assumption you are using a 12 column grid, if that is not the case, you can change this value with a filter
```
add_filter( 'fww_total_columns', 'filter_total_columns' );

funtion filter_total_columns( $args ) {
	
	$args[] = 16;
	return $args;
	
}
```